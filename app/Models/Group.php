<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class Group extends Model  
{
    protected $table = 'Group';
    public $timestamps = false;

    public $year;

	public function __construct(array $attributes = array())
	{
	    parent::__construct($attributes);
	    $this->year = 2017; // :TODO:
	}
	
	public function users()
    {
        return $this->belongsToMany('App\Models\User')->wherePivot('year',$this->year);
    }

    public function vertical()
    {
         return $this->belongsTo('App\Models\Vertical', 'vertical_id');
    }
    
    public static function search($data)
    {
        $search_fields = ['id', 'name','type','vertical_id'];
        $q = app('db')->table('Group');
        $q->select('id', 'name', 'type', 'vertical_id');
        $q->where('group_type','normal')->where('status', '1');

        foreach ($search_fields as $field) {
            if(empty($data[$field])) continue;

            if($field === 'name') $q->where($field, 'like', '%' . $data[$field] . '%');
            else $q->where($field, $data[$field]);
        }
        $results = $q->get();

        return $results;
    }

    public static function getAll()
    {
        return Group::select('id', 'name', 'type', 'vertical_id')->where('group_type','normal')->where('status', '1')->orderBy('type', 'name')->get();
    }

    public static function fetch($group_id)
    {
        return Group::select('id', 'name', 'type', 'vertical_id')->where('id', $group_id)->first();
    }
}

