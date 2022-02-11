<?php
namespace App\Models;

use App\Models\Group;
use App\Models\UserGroup;
use App\Models\Log;
use App\Models\Common;
use App\Models\Classes;
use App\Models\Data;

use App\Libraries\Email;
use Illuminate\Support\Facades\Hash;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Tymon\JWTAuth\Contracts\JWTSubject;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

use Illuminate\Foundation\Auth\User as Authenticatable;

// :TODO: Don't return password as plain text. Esp on /users/<ID> GET or /users/<ID> POST. Later Comment: Huh? How is this even possible? We only store hashs.

class User extends Authenticatable implements JWTSubject
{
    use Common;
    
    protected $table = 'User';
    public $timestamps = true;
    const CREATED_AT = 'added_on';
    const UPDATED_AT = 'updated_on';
    protected $fillable = ['email','mad_email','phone','name','sex','password_hash','address','bio','source','birthday','city_id','center_id',
                            'credit','applied_role','applied_role_secondary','status','user_type', 'joined_on', 'added_on', 'left_on', 'campaign', 
                            'job_status','edu_institution', 'edu_course', 'edu_year', 'company','why_mad', 'volunteering_experience', 
                            'zoho_sync_status', 'zoho_user_id'];
    public $enable_logging = false; // Used to disable logging the basic auth authentications for API Calls

    public function groups()
    {
        $groups = $this->belongsToMany('App\Models\Group', 'UserGroup', 'user_id', 'group_id')
                            ->where('Group.status', '1')->wherePivot('year', $this->year())
                            ->select('Group.id', 'Group.vertical_id', 'Group.name', 'Group.type', 'UserGroup.main');
        $groups->orderByRaw("FIELD(Group.type, 'executive', 'national', 'strat', 'fellow', 'volunteer')");
        return $groups;
    }

    public function pastGroups()
    {
        $past_groups = $this->belongsToMany('App\Models\Group', 'UserGroup', 'user_id', 'group_id')
                            ->where('Group.status', '1')
                            ->select('Group.id', 'Group.vertical_id', 'Group.name', 'Group.type', 'UserGroup.main', 'UserGroup.year');
        $past_groups->orderBy("UserGroup.year");
        return $past_groups;
    }

    public function mainGroup()
    {
        $group = $this->hasOneThrough('App\Models\Group', 'App\Models\UserGroup', 'user_id', 'id', 'id', 'group_id')
                            ->where('Group.status', '1')->where('UserGroup.year', $this->year())->where('UserGroup.main', '1')
                            ->select('Group.id', 'Group.vertical_id', 'Group.name', 'Group.type', 'UserGroup.main');
        return $group;
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function center()
    {
        return $this->belongsTo(Center::class);
    }

    public function classes($status = '')
    {
        $classes = $this->belongsToMany("App\Models\Classes", 'UserClass', 'user_id', 'class_id')->where('Class.class_on', '>=', $this->yearStartTime());
        if ($status) {
            $classes->where('Class.status', $status);
        }
        $classes->orderBy("Class.class_on");
        return $classes;
    }
    
    public function pastClasses($status = '')
    {
        $classes = $this->belongsToMany("App\Models\Classes", 'UserClass', 'user_id', 'class_id');
        if ($status) {
            $classes->where('Class.status', $status);
        }
        $classes->orderBy("Class.class_on");
        return $classes;
    }

    /// Connects to all the batches the current user mentors.
    public function mentoredBatches($status = false)
    {
        $batches = $this->belongsToMany("App\Models\Batch", 'UserBatch', 'user_id', 'batch_id');
        $batches->select("Batch.id", "Batch.class_time", "Batch.day", "Batch.batch_head_id");
        $batches->join("Class", "Class.batch_id", '=', 'Batch.id');
        $batches->where('Batch.year', '=', $this->year())->where("Class.class_on", '>', date('Y-m-d H:i:s'))
                ->where("Batch.status", '=', '1')->where('UserBatch.role', 'mentor');
        if ($status) {
            $batches->where('Class.status', $status);
        }
        $batches->orderBy("Class.class_on")->distinct();
        return $batches;
    }

    /// All the batches the current user teaches at
    public function batches()
    {
        $batches = $this->belongsToMany("App\Models\Batch", 'UserBatch', 'user_id', 'batch_id');
        $batches->where('Batch.year', '=', $this->year())->where("Batch.status", '=', '1')->where('UserBatch.role', 'teacher');
        return $batches;
    }

    /// All the levels the current user teaches at
    public function levels()
    {
        $levels = $this->belongsToMany("App\Models\Level", 'UserBatch', 'user_id', 'level_id');
        $levels->where('Level.year', '=', $this->year())->where("Level.status", '=', '1');
        return $levels;
    }


    public function donations()
    {
        $donations = $this->hasMany("App\Models\Donation", 'fundraiser_user_id');
        $donations->where("added_on", '>=', $this->yearStartDate());
        $donations->orderBy("added_on", 'desc');
        return $donations;
    }

    public function pastDonations()
    {
        $donations = $this->hasMany("App\Models\Donation", 'fundraiser_user_id');
        $donations->orderBy("added_on", 'desc');
        return $donations;
    }

    public function devices()
    {
        $devices = $this->hasMany("App\Models\Device", 'user_id');
        $devices->where("status", '=', "1");
        return $devices;
    }

    public function conversations()
    {
        $conversations = $this->hasMany("App\Models\Conversation", 'user_id');
        $conversations->where("added_on", '>=', $this->yearStartDate());
        $conversations->orderBy("scheduled_on", 'desc');
        return $conversations;
    }

    public function links()
    {
        $groups = $this->groups();
        $group_ids = $groups->pluck('id')->unique();
        $vertical_ids = $groups->pluck('vertical_id')->unique();
        $city_ids = [0, $this->city()->first()->id];
        $batches = $this->batches()->get();

        // All arrays have 0 value to make sure those match too - if the Link.<field> value is 0
        $group_ids[] = 0;
        $vertical_ids[] = 0;
        $center_ids = [0];
        foreach ($batches as $i => $b) {
            $center_ids[] = $b->center()->first()->id;
        }

        $links = $this->hasMany('App\Models\Link', 'status', 'status'); // :UGLY: This is a HORRIBLE thing to get tables linked that wouldn't otherwise link easily.
        $links->whereIn("Link.center_id", $center_ids);
        $links->whereIn("Link.group_id", $group_ids);
        $links->whereIn("Link.vertical_id", $vertical_ids);
        // $links->select('id','name','url','text', 'sort_order');

        return $links;
    }

    // Both functions needed for JWT authentication(https://blog.pusher.com/laravel-jwt/)
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    // public function data()
    // {
    // 	return $this->morphMany(Data::class, 'item', 'item_id');
    // }

    public function search($data, $pagination = false)
    {
        $q = app('db')->table('User');
        if ($pagination) {
            $results = $this->baseSearch($data, $q)->paginate(50, ['User.*']);
        } else {
            $results = $this->baseSearch($data, $q)->get();
        }

        // Add groups to each volunter that was returned.
        for ($i=0; $i<count($results); $i++) {
            $results[$i]->groups = [];
            if (!isset($data['user_type']) or $data['user_type'] == 'volunteer') {
                $this_user = User::fetch($results[$i]->id);
                if ($this_user and $this_user->groups) {
                    $results[$i]->groups = $this_user->groups;
                }
            }
        }
        
        return $results;
    }

    public function baseSearch($data, $q = false)
    {
        if (!$q) {
            $q = app('db')->table($this->table);
        }

        $q->select(
            "User.id",
            "User.name",
            "User.email",
            "User.phone",
            "User.mad_email",
            "User.credit",
            "User.joined_on",
            "User.left_on",
            "User.user_type",
            "User.address",
            "User.sex",
            "User.status",
            "User.city_id",
            "User.center_id",
            app('db')->raw("City.name AS city_name")
        );
        $this->joinOnce($q, "City", "City.id", '=', 'User.city_id');

        // Aliases.
        if (isset($data['group_id']) and !isset($data['user_group'])) {
            $data['user_group'] = $data['group_id'];
        }
        if (isset($data['group_type']) and !isset($data['user_group_type'])) {
            $data['user_group_type'] = $data['group_type'];
        }

        if (!isset($data['status'])) {
            $data['status'] = '1';
        }
        if ($data['status'] !== false) {
            $q->where('User.status', $data['status']);
        } // Setting status as '0' gets you even the deleted users

        if (isset($data['city_id']) and $data['city_id'] != 0) {
            $q->where('User.city_id', $data['city_id']);
        }
        if (!empty($data['city_in'])) {
            $q->whereIn('User.city_id', $data['city_in']);
        }

        if (empty($data['user_type'])) {
            $data['user_type'] = 'volunteer';
        }
        if (!empty($data['not_user_type'])) {
            $q->whereNotIn('User.user_type', $data['not_user_type']);
        } elseif ($data['user_type'] !== false) {
            $q->where('User.user_type', $data['user_type']);
        }

        if (!empty($data['id'])) {
            $q->where('User.id', $data['id']);
        }
        if (!empty($data['user_id'])) {
            $q->where('User.id', $data['user_id']);
        }
        if (!empty($data['name'])) {
            $q->where('User.name', 'like', '%' . $data['name'] . '%');
        }
        if (!empty($data['phone'])) {
            // :TODO: Phone search might need more things - +91 should be not considered, etc.
            $q->where('User.phone', $data['phone']);
        }

        if (!empty($data['email'])) {
            $q->where('User.email', $data['email']);
        }
        if (!empty($data['mad_email'])) {
            $q->where('User.mad_email', $data['mad_email']);
        }
        if (!empty($data['any_email'])) {
            $q->where('User.email', $data['any_email'])->orWhere("User.mad_email", $data['any_email']);
        }

        if (isset($data['credit'])) {
            $q->where('User.credit', $data['credit']);
        }
        if (isset($data['credit_lesser_than'])) {
            $q->where('User.credit', '<', $data['credit_lesser_than']);
        }
        if (isset($data['credit_greater_than'])) {
            $q->where('User.credit', '>', $data['credit_greater_than']);
        }

        if (!empty($data['identifier'])) {
            $q->where(function ($query) use ($data) {
                $query->where('User.email', $data['identifier'])
                    ->orWhere("User.mad_email", $data['identifier'])
                    ->orWhere("User.phone", $data['identifier'])
                    ->orWhere("User.id", $data['identifier']);
            });
        }

        if (!empty($data['left_on'])) {
            $q->where('DATE_FORMAT(User.left_on, "%Y-%m")', date('Y-m', strtotime($data['left_on'])));
        }

        if (!empty($data['user_group'])) {
            if (!is_array($data['user_group'])) {
                $data['user_group'] = array($data['user_group']);
            }
            $this->joinOnce($q, 'UserGroup', 'User.id', '=', 'UserGroup.user_id');
            $q->whereIn('UserGroup.group_id', $data['user_group']);
            $q->where('UserGroup.year', $this->year());
            if(!empty($data['only_main_group'])) {
                $q->where('UserGroup.main', $data['only_main_group']);
            }
            // $q->distinct();
            $q->groupBy("User.id"); // Using group by instead of distinct because distinct does not work well with paginate.
        }
        if (!empty($data['user_group_type'])) {
            $this->joinOnce($q, 'UserGroup', 'User.id', '=', 'UserGroup.user_id');
            $this->joinOnce($q, 'Group', 'Group.id', '=', 'UserGroup.group_id');
            $q->where('Group.type', $data['user_group_type']);
            $q->where('UserGroup.year', $this->year());
            if(!empty($data['only_main_group'])) {
                $q->where('UserGroup.main', $data['only_main_group']);
            }
            // $q->distinct();
            $q->groupBy("User.id");
        }
        if (!empty($data['vertical_id'])) {
            $this->joinOnce($q, 'UserGroup', 'User.id', '=', 'UserGroup.user_id');
            $this->joinOnce($q, 'Group', 'Group.id', '=', 'UserGroup.group_id');
            $q->where('Group.vertical_id', $data['vertical_id']);
            $q->where('UserGroup.year', $this->year());
            if(!empty($data['only_main_group'])) {
                $q->where('UserGroup.main', $data['only_main_group']);
            }
            // $q->distinct();
            $q->groupBy("User.id");
        }
        if (!empty($data['teaching_in_center_id'])) {
            $mentor_group_id = 8; // :HARDCODE:

            if (isset($data['user_group']) and in_array($mentor_group_id, $data['user_group'])) { // Find the mentors
                $this->joinOnce($q, "Batch", 'User.id', '=', 'Batch.batch_head_id');
                $q->where('Batch.center_id', $data['teaching_in_center_id']);
                $q->where('Batch.year', $this->year());
                if (isset($data['project_id'])) {
                    $q->where('Batch.project_id', $data['project_id']);
                }
            } else { // Find the teachers
                $this->joinOnce($q, 'UserClass', 'User.id', '=', 'UserClass.user_id');
                $this->joinOnce($q, 'Class', 'Class.id', '=', 'UserClass.class_id');
                $this->joinOnce($q, 'Level', 'Class.level_id', '=', 'Level.id');
                $q->where('Level.center_id', $data['teaching_in_center_id']);
                if (isset($data['project_id'])) {
                    $q->where('Level.project_id', $data['project_id']);
                }
            }
            // $q->distinct();
            $q->groupBy("User.id");
        }

        if(!empty($data['center_id'])) {
            $q->where('User.center_id', $data['center_id']);
        }
        if(isset($data['without_center_id'])) {
            $q->where('User.center_id', '0');
        }

        if (!empty($data['batch_id'])) {
            if(!$this->isTableJoined($q, 'UserBatch'))
            $this->joinOnce($q, 'UserBatch', 'User.id', '=', 'UserBatch.user_id');
            $q->addSelect("UserBatch.level_id");
            $q->where('UserBatch.batch_id', $data['batch_id']);

            if (!empty($data['level_id'])) {
                $q->where('UserBatch.level_id', $data['level_id']);
            }

            if (!empty($data['batch_role'])) {
                $q->where('UserBatch.role', $data['batch_role']);
            }
        }

        // Sorting
        if (!empty($data['user_type'])) {
            if ($data['user_type'] == 'applicant') {
                $q->orderBy('User.joined_on', 'desc');
            } elseif ($data['user_type'] == 'let_go') {
                $q->orderBy('User.left_on', 'desc');
            }
        }
        $q->orderBy('User.name');

        // dd($q->toSql(), $q->getBindings(), $data);

        return $q;
    }

    public function inCity($city_id)
    {
        return $this->search(['city_id' => $city_id]);
    }

    public function fetch($user_id, $only_volunteers = true)
    {
        if (!$user_id) {
            return false;
        }

        $user = User::select(
            'id',
            'name',
            'email',
            'mad_email',
            'phone',
            'sex',
            'photo',
            'joined_on',
            'address',
            'birthday',
            'left_on',
            'reason_for_leaving',
            'user_type',
            'status',
            'credit',
            'city_id',
            'center_id'
        )->where('status', '1');
        if ($only_volunteers) {
            $user = $user->where('user_type', 'volunteer');
        }

        $data = $user->find($user_id);
        if (!$data) {
            return false;
        }
        $this->item_id = $user_id;
        $this->item = $data;

        $data->groups = $data->groups()->get();
        $data->city = $data->city()->first()->name;
        $center = $data->center()->first();
        if($center) {
            $data->center = $center->name;
        } else {
            $data->center = "";
        }

        return $data;
    }

    public function add($data)
    {
        $q = app('db')->table($this->table);
        $q->select('id', 'email', 'phone', 'user_type');
        $q->where('user_type', '<>', 'volunteer')->where('user_type', '<>', 'applicant');
        $q->where('email', $data['email'])->orWhere('phone', $data['phone']);

        if (isset($data['mad_email'])) {
            $q->where('mad_email', $data['mad_email']);
        }

        $results = $q->first();
        $zoho_user_id = isset($data['zoho_user_id']) ? $data['zoho_user_id'] : 0;
        $madapp_user_id = 0;

        // Backward compatibility
        if(!empty($data['profile']) and empty($data['applied_role'])) {
            $data['applied_role'] = $data['profile'];
        }

        // Didn't find the user in the database - create now row.
        if (empty($results)) {
            $user = User::create([
                'email'     => $data['email'],
                'mad_email' => isset($data['mad_email']) ? $data['mad_email'] : '',
                'phone'     => User::correctPhoneNumber($data['phone']),
                'name'      => $data['name'],
                'sex'       => isset($data['sex']) ? $data['sex'] : 'not-given',
                'password_hash' => Hash::make($data['password']),
                'address'   => isset($data['address']) ? $data['address'] : '',
                'bio'       => isset($data['bio']) ? $data['bio'] : '',
                'source'    => isset($data['source']) ? $data['source'] : 'other',
                'birthday'  => isset($data['birthday']) ? $data['birthday'] : null,
                
                'job_status'=> isset($data['job_status']) ? $data['job_status'] : 'student',
                'edu_institution'  => isset($data['edu_institution']) ? $data['edu_institution'] : null,
                'edu_course'=> isset($data['edu_course']) ? $data['edu_course'] : null,
                'edu_year'  => isset($data['edu_year']) ? $data['edu_year'] : null,
                'company'   => isset($data['company']) ? $data['company'] : null,

                'city_id'   => $data['city_id'],
                'applied_role'=>isset($data['applied_role']) ? $data['applied_role'] : null,
                'applied_role_secondary'=>isset($data['applied_role_secondary']) ? $data['applied_role_secondary'] : null,

                'why_mad'   =>isset($data['why_mad']) ? $data['why_mad'] : null,
                'volunteering_experience'   => isset($data['volunteering_experience']) ? $data['volunteering_experience'] : null,

                'credit'    => isset($data['credit']) ? $data['credit'] : '3',
                'status'    => isset($data['status']) ? $data['status'] : '1',
                'user_type' => isset($data['user_type']) ? $data['user_type'] : 'applicant',
                'joined_on' => isset($data['joined_on']) ? $data['joined_on'] : date('Y-m-d H:i:s'),
                'campaign'  => isset($data['campaign']) ? $data['campaign'] : '',
                'zoho_user_id'=>$zoho_user_id,
                'zoho_sync_status' => 'insert-pending'
            ]);
            $madapp_user_id = $user->id;

        // Found a matching user with same email/phone - so update the profile to mark new joining date.
        } else {
            $user = User::where('id', $results->id)->first();
            $madapp_user_id = $user->id;
            $user->email        = $data['email'];
            $user->mad_email    = isset($data['mad_email']) ? $data['mad_email'] : '';
            $user->phone        = User::correctPhoneNumber($data['phone']);
            $user->name         = $data['name'];
            $user->sex          = isset($data['sex']) ? $data['sex'] : 'f';
            $user->password_hash= Hash::make($data['password']);
            $user->address      = isset($data['address']) ? $data['address'] : '';
            $user->bio          = isset($data['bio']) ? $data['bio'] : '';
            $user->source       = isset($data['source']) ? $data['source'] : 'other';
            $user->birthday     = isset($data['birthday']) ? $data['birthday'] : null;
            $user->city_id      = $data['city_id'];

            $user->job_status   = isset($data['job_status']) ? $data['job_status'] : '';
            $user->edu_institution = isset($data['edu_institution']) ? $data['edu_institution'] : '';
            $user->edu_course   = isset($data['edu_course']) ? $data['edu_course'] : '';
            $user->edu_year     = isset($data['edu_year']) ? $data['edu_year'] : null;
            $user->company      = isset($data['company']) ? $data['company'] : '';

            $user->applied_role = isset($data['applied_role']) ? $data['applied_role'] : '';
            $user->applied_role_secondary = isset($data['applied_role_secondary']) ? $data['applied_role_secondary'] : '';
            $user->credit       = isset($data['credit']) ? $data['credit'] : '3';
            $user->status       = isset($data['status']) ? $data['status'] : '1';
            $user->user_type    = isset($data['user_type']) ? $data['user_type'] : 'applicant';
            $user->joined_on    = isset($data['joined_on']) ? $data['joined_on'] : date('Y-m-d H:i:s');

            $user->zoho_sync_status = 'update-pending';
            $user->save();
        }

        // Send Welcome Email. Do this ONLY if zoho sync is disabled.
        $mail = new Email;
        $mail->from     = "noreply@makeadiff.in";
        $mail->to       = $data['email'];
        $mail->subject  = "Your Journey to Make a Difference begins now!";

        $base_path = app()->basePath();
        $base_url = url('/');

        $email_html = file_get_contents($base_path . '/resources/email_templates/applicant_welcome_template.html');
        $mail->html = str_replace(
            array('%BASE_FOLDER%','%BASE_URL%', '%NAME%', '%DATE%'),
            array($base_path, $base_url,$data['name'], date('d/m/Y')),
            $email_html
        );

        $images = [
            $base_path . '/public/assets/welcome_header.png',
            $base_path . '/public/assets/recruitment-email/attributes-of-madsters.jpeg',
            $base_path . '/public/assets/recruitment-email/recruitment-process.jpeg',
        ];
        $mail->images = $images;
        $mail->send(); // $mail->queue();

        return $user;
    }


    public function edit($data, $user_id = false)
    {
        $this->chain($user_id);

        foreach ($this->fillable as $key) {
            if (!isset($data[$key])) {
                continue;
            }

            if ($key == 'phone') {
                $data[$key] = $this->correctPhoneNumber($data[$key]);
            }
            if ($key == 'password') {
                $data['password_hash'] = Hash::make($data[$key]);
                $key = 'password_hash'; // Otherwise its going to store as cleartext.
            }

            $this->item->$key = $data[$key];
        }

        // :TODO: Special cases that should be handled.
        //  - User moved to Volunteer from applicant
        //      - joined_on date update
        //  - User moved to Alumnai / Let_go
        //      - left_on date update
        //      - delete future class.
        //      - delete teacher allocations
        $this->item->save();

        return $this->item;
    }

    private function unsetMainGroup($user_id = false)
    {
        $user_id = $this->chain($user_id);
        app('db')->table('UserGroup')->where('main','1')->where('user_id', $user_id)->where('year',$this->year())->update(['main' => '0']);
    }
    public function setMainGroup($group_id, $main ='1', $user_id = false)
    {
        $user_id = $this->chain($user_id);
        app('db')->table("UserGroup")->where('group_id',$group_id)->where('user_id', $user_id)->where('year',$this->year())->update(['main' => $main]);
    }
    private function unsetAllGroups($user_id = false)
    {
        $user_id = $this->chain($user_id);
        app('db')->table('UserGroup')->where('user_id', $user_id)->where('year',$this->year())->delete();
    }

    /// $groups should be in the format of [{group_id: 12, main: "0"}, {group_id: 13, main: "1"}]
    public function setGroups($groups, $user_id = false)
    {
        $user_id = $this->chain($user_id);

        $new_groups = [];
        foreach($groups as $g) {
            $new_groups[$g->group_id] = $g->main;
        }
        $existing_groups_raw = $this->item->groups()->get();
        $existing_groups = [];
        foreach($existing_groups_raw as $g) {
            $existing_groups[$g->id] = $g->main;
        }

        // Any difference between the currently given group list and the existing groups for the user?
        $diff = array_intersect_assoc($existing_groups, $new_groups);
        if(count($diff) == count($existing_groups) and count($diff) == count($new_groups))  {
            return $existing_groups; // No, its the same. Nothing to be done.
        }

        $this->unsetAllGroups($user_id);
        foreach($new_groups as $group_id => $main) {
            $this->addGroup($group_id, $main, $user_id);
        }

        return $new_groups;
    }

    public function addGroup($group_id, $main=0, $user_id = false)
    {
        $user_id = $this->chain($user_id);

        // Check if the user has the group already.
        $existing_groups = $this->item->groups()->get();
        $group_found = false;
        foreach ($existing_groups as $grp) {
            if ($grp->id == $group_id) {
                $group_found = $grp;
                break;
            }
        }

        if ($group_found) {
            if($group_found->main == $main) return false; // No change required
            else { // If the main group is not correctly, do that.
                $this->unsetMainGroup($user_id);
                $this->setMainGroup($group_found->id, $main, $user_id);
                return false;
            }
        }

        if($main) { // If a new group is getting the main tag, remove main tag from other group -if any.
            $this->unsetMainGroup($user_id);
        }

        app('db')->table("UserGroup")->insert([
            'user_id'   => $user_id,
            'group_id'  => $group_id,
            'year'      => $this->year(),
            'main'      => (string) $main,
            'added_on'  => date('Y-m-d H:i:s')
        ]);

        return $this->item->groups();
    }

    public function removeGroup($group_id, $user_id = false)
    {
        $user_id = $this->chain($user_id);

        // Check if the user has the group.
        $existing_groups = $this->item->groups()->get();
        $group_found = false;
        foreach ($existing_groups as $grp) {
            if ($grp->id == $group_id) {
                $group_found = true;
                break;
            }
        }

        if (!$group_found) {
            return false;
        }
        // :TODO: What happens if the 'main' Group is deleted. Assign them the highest?

        app('db')->table("UserGroup")->where('user_id', $user_id)->where('group_id',$group_id)->where('year',$this->year())->delete();

        return $this->item->groups();
    }

    public function setCredit($credit, $user_id = false)
    {
        $this->chain($user_id);

        $this->item->credit = $credit;
        return $this->item->save();
    }

    public function editCredit($new_credit, $credit_assigned_by_user_id, $reason, $user_id = false)
    {
        $this->chain($user_id);

        app('db')->table('UserCredit')->insert([
            'user_id'   => $this->id,
            'credit'    => $new_credit,
            'credit_assigned_by_user_id' => $credit_assigned_by_user_id,
            'comment'   => $reason,
            'added_on'  => date('Y-m-d H:i:s'),
            'year'      => $this->year()
        ]);

        return $this->find($this->id)->setCredit($new_credit);
    }

    public function login($email_or_phone, $password, $auth_token='')
    {
        $user = User::where('status', '1')->where('user_type', 'volunteer');
        $user->where(function ($q) use ($email_or_phone) {
            $q->where('email', $email_or_phone)->orWhere('phone', $email_or_phone)->orWhere('mad_email', $email_or_phone);
        });
        $data = $user->first();

        if ($data) {
            $is_correct = false;
            if ($password) {
                $is_correct = Hash::check($password, $data->password_hash);
            } elseif ($auth_token) {
                $is_correct = ($data->auth_token == $auth_token);
            } // :TODO: elseif($jwt_token) { $token = JWTAuth::attempt($credentials) }

            if (!$is_correct) { // Incorrect password / Auth key
                $data = null;
                $this->errors[] = "Incorrect password provided";
            } else {
                $user_id = $data->getKey();

                if ($this->enable_logging) {
                    // Log the login
                    $login_type = 'Username/Password';
                    if ($auth_token) {
                        $login_type = 'Auth Token';
                    }
                    Log::add(['name' => 'user_login', 'user_id' => $user_id, 'data' => ['entry_point' => 'Phoenix', 'login_type' => $login_type]]);
                }

                $user_data = $this->fetch($user_id);
                $user_data->auth_token = $data->auth_token;

                // Create JWT token.
                $user_data->jwt_token = JWTAuth::fromUser($user_data);
                $user_data->permissions = $this->permissions($user_id);

                return $user_data;
            }
        } else {
            $this->errors[] = "Can't find any user with the given email/phone";
        }
        return false;
    }

    public function permissions($user_id = false)
    {
        $user_id = $this->chain($user_id);

        $groups = app('db')->table("UserGroup")->where('user_id', $user_id)->where('year', $this->year())->select('group_id')->get()->pluck('group_id');

        $parent_groups = app('db')->table("Group")->distinct('parent_group_id')
            ->whereIn('id', $groups)->where('status', '1')->where('parent_group_id', '!=', '0')->get()->pluck('parent_group_id');
        $groups = $groups->merge($parent_groups);

        if (!$groups->count()) { // If he has no group, he is volunteer group.
            $groups = collect([9]); //:HARD-CODE: 9 is the teacher group.
        }

        $permissions = app('db')->table("Permission")->join("GroupPermission", 'GroupPermission.permission_id', '=', 'Permission.id')
            ->distinct('Permission.name')->select('Permission.name')->whereIn("GroupPermission.group_id", $groups)->get()->pluck('name');

        return $permissions;
    }

    // Each user has a unique Sourcing campaign ID that's stored in the Data table.
    public function getSourcingCampaignId($user_id = null)
    {
        $user_id = $this->chain($user_id);

        $campaign_id = (new Data)->get('User', $user_id, 'sourcing_campaign_id')->getData();
        return $campaign_id;
    }

    // They can give a registeartion link with that ID to new people - if they register using that link, we store the campaign 
    // ID in the User table. This function will return all the people who came in thru the user
    public function getSourcedApplicants($user_id = null)
    {
        $user_id = $this->chain($user_id);
        $campaign_id = $this->getSourcingCampaignId($user_id);
        if(!$campaign_id) return null;
        $applicants = app('db')->table("User")->select('id','name')->where('campaign', $campaign_id)->get();
        return $applicants;
    }

    /// Changes the phone number format from +91976063565 to 9746063565. Remove the 91 at the starting.
    private function correctPhoneNumber($phone)
    {
        if (strlen($phone) > 10) {
            return preg_replace('/^\+?91\D?/', '', $phone);
        }
        return $phone;
    }
}
