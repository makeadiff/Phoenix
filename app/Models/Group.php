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
        return $this->belongsToMany('App\Models\User', 'UserGroup')->where('User.status', '=', '1')->where('User.user_type', '=', 'volunteer')->wherePivot('year', $this->year);
    }

    public function vertical()
    {
        return $this->belongsTo('App\Models\Vertical', 'vertical_id');
    }

    public function parent()
    {
        return $this->belongsTo('App\Models\Group', 'parent_group_id')->where('parent_group_id', 0);
    }

    public function children()
    {
        return $this->hasMany('App\Models\Group', 'parent_group_id');
    }

    public static function search($data)
    {
        $search_fields = ['id', 'name','type','vertical_id', 'type_in'];
        $q = app('db')->table('Group');
        $q->select('id', 'name', 'type', 'vertical_id');
        $q->where('group_type', 'normal')->where('status', '1');

        foreach ($search_fields as $field) {
            if (empty($data[$field])) {
                continue;
            }

            if ($field === 'name') {
                $q->where($field, 'like', '%' . $data[$field] . '%');
            } elseif ($field === 'type_in') {
                $q->whereIn('type', explode(",", $data[$field]));
            } else {
                $q->where($field, $data[$field]);
            }
        }
        $q->orderBy('type')->orderBy('name');
        $results = $q->get();

        return $results;
    }

    public function permissions()
    {
        // First, get all the permissions that the parent has.
        $parent = $this->parent();
        $parent_permissions = [];
        if ($parent->first()) { // Only supports 1 level nesting for now.
            $parent_group_id = $parent->first()->id;
            $parent_permissions = $this->find($parent_group_id)->permissions();
        }

        // Get all the permissions of the current Group.
        $permissions = app('db')->table('Permission')->select('Permission.name')->join('GroupPermission', 'Permission.id', '=', 'GroupPermission.permission_id')->where('GroupPermission.group_id', $this->id)->get()->toArray();
        $permissions_arr = json_decode(json_encode($permissions), true); // Making it an array.

        // Merge both permission sets together - all permissions of current group + parent group.
        $all_permissions = [];
        if ($permissions_arr) {
            $all_permissions = array_column($permissions_arr, 'name');
        }
        if ($parent_permissions) {
            $all_permissions = array_merge($all_permissions, $parent_permissions);
        }

        return $all_permissions;
    }

    public static function getAll()
    {
        return Group::select('id', 'name', 'type', 'vertical_id')->where('group_type', 'normal')->where('status', '1')->orderBy('type', 'name')->get();
    }


    public static function getTypes(){
        return Group::select('type')->where('group_type','normal')->where('status','1')->distinct()->get();
    }
}
