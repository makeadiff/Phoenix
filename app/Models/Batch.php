<?php
namespace App\Models;

use App\Models\Common;
use App\Models\Center;

final class Batch extends Common
{
    protected $table = 'Batch';
    public $timestamps = false;
    protected $fillable = ['day','class_time','batch_head_id','center_id','status','year'];

    public function center()
    {
        $center = $this->belongsTo('App\Models\Center', 'center_id');
        return $center->first();
    }

    public function search($data) {
        $search_fields = ['id', 'day', 'class_time', 'center_id', 'status'];
        $q = app('db')->table('Batch');
        $q->select('Batch.id', 'day', 'class_time', 'batch_head_id', 'Batch.center_id', 'Batch.status');
        if(!isset($data['status'])) $data['status'] = '1';
        if(!isset($data['year'])) $data['year'] = $this->year;

        foreach ($search_fields as $field) {
            if(empty($data[$field])) continue;

            else $q->where("Batch." . $field, $data[$field]);
        }

        if(!empty($data['level_id'])) {
            $q->join('BatchLevel', 'Batch.id', '=', 'BatchLevel.batch_id');
            $q->join("Level", 'Level.id', '=', 'BatchLevel.level_id');
            $q->where("Level.year", $this->year)->where('Level.status', '1');
            $q->where('BatchLevel.level_id', $data['level_id']);
        }

        $q->orderBy('day', 'class_time');
        $results = $q->get();
        foreach ($results as $key => $row) {
            $results[$key]->name = $this->getName($row->day, $row->class_time);
        }

        return $results;
    }

    public function fetch($id) {
        $this->id = $id;
        $this->item = $this->where('status', '1')->where('year', $this->year)->find($id);
        $this->item->name = $this->getName($this->item->day, $this->item->class_time);
        $this->item->center = $this->item->center()->name;
        return $this->item;
    }

    public function inCenter($center_id) {
        return $this->search(['center_id' => $center_id]);
    }

    public function add($data)
    {
        $batch = Batch::create([
            'day'       => $data['day'],
            'class_time'=> $data['class_time'],
            'center_id' => $data['center_id'],
            'batch_head_id' => isset($data['batch_head_id']) ? $data['batch_head_id'] : '',
            'year'      => $this->year,
            'status'    => isset($data['status']) ? $data['status'] : '1'
        ]);

        return $batch;
    }

    public function getName($day, $time) {
        $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        return $days[$day] . ' ' . date('h:i A', strtotime('2018-01-21 ' . $time));
    }

}
