<?php
namespace App\Models;

use App\Models\Common;

final class Data extends Common  
{
    protected $table = 'Data';
    public $timestamps = true;
    const CREATED_AT = 'added_on';
    const UPDATED_AT = false;
    protected $fillable = ['name', 'item', 'item_id', 'year', 'added_by_user_id', 'data'];

    public static function search($data)
    {
        $search_fields = ['id', 'item','item_id','name', 'year'];
        $q = app('db')->table('Data');
        $q->select('id', 'item','item_id', 'name', 'data');

        foreach ($search_fields as $field) {
            if(empty($data[$field])) continue;

            $q->where($field, $data[$field]);
        }
        $results = $q->get();

        return $results;
    }

    public function getData()
    {
        $item = ( $this->item ) ? $this->item : $this->item_copy;
        if(!$item) return false;

        if($item and isset($item->data)) return $item->data;
        return false;
    }

    public function remove()
    {
        if($this->item) Data::destroy($this->item->id);
    }

    public function setData($data)
    {
        // Clear current data
        $item = ( $this->item ) ? $this->item : $this->item_copy;
        if(!$item) return false;
        $this->remove();

        app('db')->table('Data')->insert([
            'item'      => $item->item,
            'item_id'   => $item->item_id,
            'name'      => $item->name,
            'data'      => $data,
            'year'      => $this->year,
            'added_on'  => date('Y-m-d H:i:s')
        ]);
    }

    public function get($item, $item_id, $name, $year = false)
    {
        $q = app('db')->table('Data');
        $q->where('item', $item)->where('item_id', $item_id)->where('name', $name);
        if($year) $q->where('year', $year);

        $this->item = $q->first();
        $this->item_copy = (object) ['item' => $item, 'item_id' => $item_id, 'name' => $name, 'year' => $year];

        return $this;
    }
}
