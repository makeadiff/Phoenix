<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Group;
use Illuminate\Support\Facades\Hash;

final class User extends Model  
{
    protected $table = 'User';
    public $timestamps = false;
    protected $fillable = ['email','mad_email','phone','name','sex','password','address','bio','source','birthday','city_id','credit','status','user_type', 'joined_on', 'left_on'];

    public $year;
    // private $id = 0;
    // private $data = false;

    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);
        $this->year = 2017; // :TODO:
    }

    public function search($data)
    {
        $q = app('db')->table($this->table);

        $q->select("User.id","User.name","User.email","User.phone","User.mad_email","User.credit","User.joined_on","User.left_on",
                    "User.user_type","User.address","User.sex", app('db')->raw("City.name AS city_name"));
        $q->join("City", "City.id", '=', 'User.city_id');

        if(!isset($data['status'])) $data['status'] = 1;
        if($data['status'] !== false) $q->where('User.status', $data['status']); // Setting status as 'false' gets you even the deleted users
        
        if(isset($data['city_id']) and $data['city_id'] != 0) $q->where('User.city_id', $data['city_id']);
        
        if(empty($data['user_type'])) $data['user_type'] = 'volunteer';
        if($data['user_type'] !== false) $q->where('User.user_type', $data['user_type']);

        if(!empty($data['not_user_type'])) $q->where_not_in('User.user_type', $data['not_user_type']);
        if(!empty($data['id'])) $q->where('User.id', $data['id']);
        if(!empty($data['user_id'])) $q->where('User.id', $data['user_id']);
        if(!empty($data['name'])) $q->where('User.name', 'like', '%' . $data['name'] . '%');
        if(!empty($data['phone'])) $q->where('User.phone', $data['phone']);
        
        if(!empty($data['email'])) $q->where('User.email', $data['email']);
        if(!empty($data['mad_email'])) $q->where('User.mad_email', $data['mad_email']);
        if(!empty($data['any_email'])) $q->where('User.email', $data['any_email'])->orWhere("User.mad_email", $data['any_email']);

        if(!empty($data['left_on'])) $q->where('DATE_FORMAT(User.left_on, "%Y-%m")', date('Y-m', strtotime($data['left_on'])));
        
        if(!empty($data['user_group'])) {
            $q->join('UserGroup', 'User.id', '=', 'UserGroup.user_id');
            $q->whereIn('UserGroup.group_id', $data['user_group']);
            $q->where('UserGroup.year', $this->year);
        }
        if(!empty($data['user_group_type'])) {
            $q->join('UserGroup', 'User.id', '=', 'UserGroup.user_id');
            $q->join('Group', 'Group.id', '=', 'UserGroup.group_id');
            $q->whereIn('Group.type', $data['user_group_type']);
            $q->where('UserGroup.year', $this->year);
        }
        if(!empty($data['user_group_vertical_id'])) {
            $q->join('UserGroup', 'User.id', '=', 'UserGroup.user_id');
            $q->join('Group', 'Group.id', '=', 'UserGroup.group_id');
            $q->whereIn('Group.vertical_id', $data['user_group_vertical_id']);
            $q->where('UserGroup.year', $this->year);
        }
        if(!empty($data['center_id'])) {
            // $q->select("DISTINCT User.id");
            $q->join('UserClass', 'User.id', '=', 'UserClass.user_id');
            $q->join('Class', 'Class.id', '=', 'UserClass.class_id');
            $q->join('Level', 'Class.level_id', '=', 'Level.id');
            $q->where('Level.center_id', $data['center_id']);
            $q->distinct();
        }

        // Sorting
        if(!empty($data['user_type'])) {
            if($data['user_type'] == 'applicant') {
                $q->orderBy('User.joined_on','desc');
            } elseif($data['user_type'] == 'let_go') {
                $q->orderBy('User.left_on','desc');
            }
        }
        $q->orderby('User.name');

        // :TODO: Pagination

        // dd($q->toSql(), $q->getBindings());

        $results = $q->get();
        
        return $results;
    }

    public function fetch($user_id) {
        $data = $this->select('id', 'name', 'email', 'mad_email','phone', 'sex', 'photo', 'joined_on', 'address', 'birthday', 'left_on', 
                                'reason_for_leaving', 'user_type', 'status', 'credit', 'city_id')->find($user_id);
        if(!$data) return false;
        
        $data->groups = $data->groups();
        $data->city = $data->city()[0]->name;
        return $data;
    }

    public function groups() 
    {
        $groups = $this->belongsToMany('App\Models\Group', 'UserGroup', 'user_id', 'group_id')->wherePivot('year',$this->year)->select('Group.id','Group.vertical_id', 'Group.name');
        return $groups->get();
    }

    public function city()
    {
         $city = $this->belongsTo('App\Models\City', 'city_id');
         return $city->get();
    }

    public function add($data)
    {
        $user = User::create([
            'email'     => $data['email'],
            'mad_email' => isset($data['mad_email']) ? $data['mad_email'] : '',
            'phone'     => User::correctPhoneNumber($data['phone']),
            'name'      => $data['name'],
            'sex'       => isset($data['sex']) ? $data['sex'] : 'f',
            'password'  => Hash::make($data['password']),
            'address'   => isset($data['address']) ? $data['address'] : '',
            'bio'       => isset($data['bio']) ? $data['bio'] : '',
            'source'    => isset($data['source']) ? $data['source'] : 'other',
            'birthday'  => isset($data['birthday']) ? $data['birthday'] : '',
            'city_id'   => $data['city_id'],
            'credit'    => isset($data['credit']) ? $data['credit'] : '3',
            'status'    => isset($data['status']) ? $data['status'] : '1',
            'user_type' => isset($data['user_type']) ? $data['user_type'] : 'applicant',
            'joined_on' => date('Y-m-d H:i:s'),
        ]);

        return $data;
    }

    public function edit($data, $user_id)
    {
        $user = $this->find($user_id);
        foreach ($this->fillable as $key) {
            if(!isset($data[$key])) continue;

            if($key == 'phone') $data[$key] = $this->correctPhoneNumber($data[$key]);
            if($key == 'password') $data[$key] = Hash::make($data[$key]);

            $user->$key = $data[$key];
        }
        return $user->save();
    }
    
    /// Changes the phone number format from +91976068565 to 9746068565. Remove the 91 at the starting.
    private function correctPhoneNumber($phone) {
        if(strlen($phone) > 10) {
            return preg_replace('/^\+?91\D?/', '', $phone);
        }
        return $phone;
    }

}
