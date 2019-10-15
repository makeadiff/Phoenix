<?php
namespace App\Models;

use App\Models\Group;
use App\Models\Common;

final class Permission extends Common
{
    protected $table = 'Permission';
    public $timestamps = false;
    protected $hidden = ['pivot'];

    public static function search($data)
    {
        $search_fields = ['id', 'name'];
        $q = app('db')->table('Permission');
        $q->select('id', 'name');

        foreach ($search_fields as $field) {
            if (empty($data[$field])) {
                continue;
            }

            if ($field === 'name') {
                $q->where($field, 'like', '%' . $data[$field] . '%');
            } else {
                $q->where($field, $data[$field]);
            }
        }
        $q->orderBy('name');
        $results = $q->get();

        return $results;
    }

    public static function getAll()
    {
        return Permission::select('id', 'name')->orderBy('name')->get();
    }
}
