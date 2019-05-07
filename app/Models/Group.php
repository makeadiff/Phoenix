<?php
namespace App\Models;

use App\Models\Common;

final class Group extends Common  
{
    protected $table = 'Group';
    public $timestamps = false;    
    protected $hidden = ['pivot'];

	public function users()
    {
        return $this->belongsToMany('App\Models\User')->where('User.status', '=', '1')->where('User.user_type', '=', 'volunteer')->wherePivot('year',$this->year);
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
        $q->orderBy('type')->orderBy('name');
        $results = $q->get();

        return $results;
    }

    public static function getAll()
    {
        return Group::select('id', 'name', 'type', 'vertical_id')->where('group_type','normal')->where('status', '1')->orderBy('type', 'name')->get();
    }
}

