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
        return $this->belongsTo('App\Models\Center', 'center_id');
    }
    public function students()
    {
        return $this->belongsToMany('App\Models\Student', 'StudentLevel', 'level_id', 'student_id');
    }
    public function batches()
    {
        return $this->belongsToMany('App\Models\Batch', 'BatchLevel', 'level_id', 'batch_id')->where('BatchLevel.year', $this->year);
    }

    public function search($data) {
        $search_fields = ['id', 'name', 'grade', 'center_id', 'project_id', 'year', 'status'];
        $q = app('db')->table('Level');
        $q->select('Level.id', 'name', 'grade', 'Level.center_id', 'Level.status');
        if(!isset($data['status'])) $data['status'] = '1';
        if(!isset($data['year'])) $data['year'] = $this->year;

        foreach ($search_fields as $field) {
            if(empty($data[$field])) continue;

            else $q->where("Level." . $field, $data[$field]);
        }

        if(!empty($data['batch_id'])) {
            $q->join('BatchLevel', 'Level.id', '=', 'BatchLevel.level_id');
            $q->join("Batch", 'Batch.id', '=', 'BatchLevel.batch_id');
            $q->where("Batch.year", $this->year)->where('Batch.status', '1');

            $q->where('BatchLevel.batch_id', $data['batch_id']);
        }

        $q->orderBy('grade', 'asc')->orderBy('name', 'asc');
        // dd($q->toSql(), $q->getBindings(), $data);
        $results = $q->get();

        foreach ($results as $key => $row) {
            $results[$key]->name = $row->grade . ' ' . $row->name;
        }

        return $results;
    }

    public function fetch($id, $is_active = true) {
        $this->id = $this->item_id = $id;

        if($is_active)
            $this->item = $this->where('status', '1')->where('year', $this->year)->find($id);
        else 
            $this->item = $this->find($id);
        if(!$this->item) return false;
        
        $this->item->name = $this->item->grade . ' ' . $this->item->name;
        $this->item->center = $this->item->center()->first()->name;
        return $this->item;
    }

    public function inCenter($center_id) {
        return $this->search(['center_id' => $center_id]);
    }

    public function add($data)
    {
        $batch = Level::create([
            'name'      => $data['name'],
            'grade'     => $data['grade'],
            'center_id' => $data['center_id'],
            'year'      => $this->year,
            'status'    => isset($data['status']) ? $data['status'] : '1'
        ]);

        return $batch;
    }
}
