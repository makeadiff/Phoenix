<?php
namespace App\Models;

use App\Models\Common;
use App\Models\Batch;
use App\Models\Level;
use App\Models\Student;
use App\Models\User;

// This is named 'Classes' - going against the convention in other classes - because Class is a reserved keyword in PHP

final class Classes extends Common
{
    protected $table = 'Class';
    public $timestamps = false;
    protected $fillable = ['batch_id', 'level_id', 'project_id', 'class_on', 'class_type', 'class_satisfaction','cancel_option','cancel_reason','updated_by_mentor','updated_by_teacher','status'];

    public function batch()
    {
        return $this->belongsTo('App\Models\Batch', 'batch_id');
    }
    public function level()
    {
        return $this->belongsTo('App\Models\Level', 'level_id');
    }
    public function students()
    {
        return $this->belongsToMany('App\Models\Student','StudentClass','class_id', 'student_id')->withPivot('present', 'participation', 'check_for_understanding');
    }
    public function teachers()
    {
        return $this->belongsToMany('App\Models\User', 'UserClass', 'class_id', 'user_id')->withPivot('substitute_id', 'zero_hour_attendance', 'status');
    }
    public function substitutes()
    {
        return $this->belongsToMany('App\Models\User', 'UserClass', 'class_id', 'substitute_id')->withPivot('substitute_id', 'user_id', 'zero_hour_attendance', 'status');
    }

    public function search($data) {
        $search_fields = ['teacher_id', 'substitute_id', 'batch_id', 'level_id', 'project_id', 'status', 'class_date', 'direction'];
        $q = app('db')->table('Class');
        $q->select('Class.id', 'Class.batch_id', 'Class.level_id', 'Class.class_on', 'Class.class_type', 'Class.class_satisfaction', 'Class.cancel_option', 'Class.cancel_reason', 'Class.status');

        foreach ($search_fields as $field) {
            if(empty($data[$field])) {
                continue;
            } elseif($field == 'class_date') {
                $q->whereDate("Class.class_on", $data[$field]);

            } elseif($field == 'teacher_id') {
                $q->join("UserClass", "Class.id", '=', 'UserClass.class_id');
                $q->where("UserClass.user_id", $data[$field]);

            } elseif($field == 'substitute_id') {
                $q->join("UserClass", "Class.id", '=', 'UserClass.class_id');
                $q->where("UserClass.substitute_id", $data[$field]);
                
            } elseif($field == 'direction') {
                // :TODO
            } else {
                $q->where("Class." . $field, $data[$field]);
            }
        }

        $q->where("Class.class_on", '>=', $this->year_start_time);

        $q->orderBy('class_on');
        $results = $q->get();

        return $results;
    }

    // public function fetch($id, $is_active = true) {
    //     $this->id = $id;
    //     if($is_active)
    //         $this->item = $this->where('status', '1')->where('year', $this->year)->find($id);
    //     else 
    //         $this->item = $this->find($id);
    //     $this->item->name = $this->getName($this->item->day, $this->item->class_time);
    //     $this->item->center = $this->item->center()->first()->name;
    //     $this->item->vertical_id = $this->getVerticalIdFromProjectId($this->item->project_id);
    //     return $this->item;
    // }

    // public function add($data)
    // {
    //     $batch = Class::create([
    //         'day'       => $data['day'],
    //         'class_time'=> $data['class_time'],
    //         'center_id' => $data['center_id'],
    //         'batch_head_id' => isset($data['batch_head_id']) ? $data['batch_head_id'] : '',
    //         'year'      => $this->year,
    //         'status'    => isset($data['status']) ? $data['status'] : '1'
    //     ]);

    //     return $batch;
    // }
}
