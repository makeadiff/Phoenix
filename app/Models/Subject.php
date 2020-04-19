<?php
namespace App\Models;

use App\Models\Common;

final class Subject extends Common
{
    protected $table = 'Subject';
    public $timestamps = false;

    public static function getAll()
    {
        return Subject::where('status', '1')->orderBy('name')->get();
    }

    public static function search($data)
    {
        $search_fields = ['id', 'name'];
        $q = app('db')->table('Subject');
        $q->select('id', 'name');
        $q->where('status', '1');

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
}
