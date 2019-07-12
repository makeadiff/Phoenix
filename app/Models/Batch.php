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
        return $this->belongsTo('App\Models\Center', 'center_id');
    }
    public function levels()
    {
        return $this->belongsToMany("App\Models\Level", 'BatchLevel'); // ->where('BatchLevel.year', '=', $this->year);
    }
    public function teachers()
    {
        return $this->belongsToMany("App\Models\User", 'UserBatch');
    }

    public function search($data) 
    {
        $q = app('db')->table('Batch');
        $results = $this->baseSearch($data, $q)->get();

        foreach ($results as $key => $row) {
            $results[$key]->name = $this->getName($row->day, $row->class_time);
            $results[$key]->vertical_id = $this->getVerticalIdFromProjectId($results[$key]->project_id);
        }

        return $results;
    }

    public function baseSearch($data, $q)
    {
        $search_fields = ['id', 'day', 'center_id', 'level_id',  'project_id', 'year', 'teacher_id'];
        $q = app('db')->table('Batch');
        $q->select('Batch.id', 'day', 'class_time', 'batch_head_id', 'Batch.center_id', 'Batch.status', 'Batch.project_id')->distinct();
        if(!isset($data['status'])) $data['status'] = '1';
        if(!isset($data['year'])) $data['year'] = $this->year;

        if(isset($data['teacher_id'])) {
            $q->join("UserBatch", 'Batch.id', '=', 'UserBatch.batch_id');
        }

        if(isset($data['level_id'])) {
            $q->join('BatchLevel', 'Batch.id', '=', 'BatchLevel.batch_id');
            $q->join("Level", 'Level.id', '=', 'BatchLevel.level_id');
            $q->where("Level.year", $this->year)->where('Level.status', '1');
        }

        foreach ($search_fields as $field) {
            if(empty($data[$field])) continue;

            else if($field == 'teacher_id') {
                $q->where("UserBatch.user_id", $data[$field]);

            } else if($field == 'level_id') {
                $q->where('BatchLevel.level_id', $data['level_id']);

            } else {
                $q->where("Batch." . $field, $data[$field]);
            }
        }

        $q->orderBy('day')->orderBy('class_time');

        // dd($results);

        return $q;
    }

    public function fetch($id, $is_active = true) {
        $this->id = $id;
        if($is_active)
            $this->item = $this->where('status', '1')->where('year', $this->year)->find($id);
        else 
            $this->item = $this->find($id);
        $this->item->name = $this->getName($this->item->day, $this->item->class_time);
        $this->item->center = $this->item->center()->first()->name;
        $this->item->vertical_id = $this->getVerticalIdFromProjectId($this->item->project_id);
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
        return $days[$day] . ' ' . date('h:i A', strtotime('2018-01-21 ' . $time)); // Random date. No relavence to the result.
    }

    public function name() {
        if(!$this->id) return false;

        return $this->getName($this->day, $this->class_time);
    }

    private function getVerticalIdFromProjectId($project_id)
    {
        $project_vertical_mapping = [
            1   => 3,
            2   => 19,
            4   => 5,
            5   => 18
        ];

        return $project_vertical_mapping[$project_id];
    }

}
