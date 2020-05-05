<?php
namespace App\Models;

use App\Models\Common;

final class Link extends Common
{
    protected $table = 'Link';
    public $timestamps = true;
    const CREATED_AT = 'added_on';
    const UPDATED_AT = 'updated_on';
    protected $fillable = ['name', 'url', 'vertical_id', 'city_id', 'group_id', 'center_id', 'sort_order', 'status'];

    public static function search($data)
    {
        $search_fields = ['id', 'name', 'url', 'vertical_id', 'city_id', 'group_id', 'center_id', 'sort_order', 'status'];
        $q = app('db')->table('Link');
        $q->select('id', 'name', 'url', 'vertical_id', 'city_id', 'group_id', 'center_id', 'sort_order', 'status');

        foreach ($search_fields as $field) {
            if (empty($data[$field])) {
                continue;
            }

            $q->where($field, $data[$field]);
        }
        $results = $q->get();

        return $results;
    }


}
