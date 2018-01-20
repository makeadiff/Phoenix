<?php
namespace App\Models;

use App\Models\Common;
use App\Models\Center;

final class Level extends Common
{
    protected $table = 'Level';
    public $timestamps = false;
    protected $fillable = ['name','grade','center_id','status','year'];

    public function center()
    {
        $center = $this->belongsTo('App\Models\Center', 'center_id');
        return $center->first();
    }

    public function search($data) {
        $search_fields = ['id', 'name', 'grade', 'center_id', 'status'];
        $q = app('db')->table('Level');
        $q->select('id', 'name', 'grade', 'center_id', 'status');
        if(!isset($data['status'])) $data['status'] = '1';
        if(!isset($data['year'])) $data['year'] = $this->year;

        foreach ($search_fields as $field) {
            if(empty($data[$field])) continue;

            else $q->where($field, $data[$field]);
        }
        $q->orderBy('grade', 'name');
        $results = $q->get();
        foreach ($results as $key => $row) {
            $results[$key]->name = $row->grade . ' ' . $row->name;
        }

        return $results;
    }

    public function fetch($id) {
        $this->id = $id;
        $this->item = $this->where('status', '1')->where('year', $this->year)->find($id);
        $this->item->name = $this->item->grade . ' ' . $this->item->name;
        $this->item->center = $this->item->center()->name;
        return $this->item;
    }

    public function inCenter($center_id) {
        return $this->search(['center_id' => $center_id]);
    }

    public function add($data)
    {
        $batch = Level::create([
            'name'       => $data['name'],
            'grade'=> $data['grade'],
            'center_id' => $data['center_id'],
            'year'      => $this->year,
            'status'    => isset($data['status']) ? $data['status'] : '1'
        ]);

        return $batch;
    }
}
