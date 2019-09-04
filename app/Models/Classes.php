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
        $q = app('db')->table('Class');
        return $this->baseSearch($data, $q);
    }

    /// This is a seperate function because even Project->classes() uses almost the exact same thing.
    public function baseSearch($search, $q = false)
    {
        if(!$q) $q = app('db')->table('Class');
        
        // teacher_id: Int, status: String, batch_id: Int, level_id: Int, project_id: Int, class_date: Date, direction: String)
        $search_fields = ['teacher_id', 'substitute_id', 'batch_id', 'level_id', 'center_id', 'project_id', 'status', 'class_date', 'class_date_to','class_date_from', 'class_status', 'direction', 'from_date', 'limit'];
        $q->select('Class.id', 'Class.batch_id', 'Class.level_id', 'Class.class_on', 'Class.class_type', 'Class.class_satisfaction', 'Class.cancel_option', 'Class.cancel_reason',
                        'Class.status AS class_status', 'UserClass.id AS user_class_id', 'UserClass.substitute_id', 'UserClass.zero_hour_attendance', 'UserClass.status AS status'); // ->distinct('Class.id');
        $q->join("UserClass", 'UserClass.class_id', '=', 'Class.id');
        $q->join("Batch", 'Batch.id', '=', 'Class.batch_id');
        $q->join("Level", 'Level.id', '=', 'Class.level_id');

        $q->where("Batch.year", '=', $this->year);
        $q->where("Batch.status", '=', '1');
        $q->where("Level.year", '=', $this->year);
        $q->where("Level.status", '=', '1');

        foreach ($search_fields as $field) {
            if(empty($search[$field])) {
                continue;
            } elseif($field == 'class_date') {
                $q->whereDate("Class.class_on", $search[$field]);
            } elseif($field == 'class_date_to') {
                $q->whereDate("Class.class_on", '<=', $search[$field]);
            } elseif($field == 'class_date_from') {
                $q->whereDate("Class.class_on", '>=', $search[$field]);

            } elseif($field == 'class_status') {
                $q->whereDate("Class.status", $search[$field]);

            } elseif($field == 'teacher_id') {
                $q->where("UserClass.user_id", $search[$field]);

            } elseif($field == 'center_id') {
                $q->where("Batch.center_id", $search[$field]);

            } elseif($field == 'substitute_id') {
                $q->where("UserClass.substitute_id", $search[$field]);
                
            } elseif($field == 'limit') {
                // Limit by one day - This will only show the classes of the given batch for the next one day - weather its a + or - direction.
                if($search['limit'] == 'day' and isset($search['batch_id'])) {
                    $next_class_day_query = app('db')->table('Class')->select('class_on');
                    if(isset($search['direction']) and isset($search['from_date'])) {
                        if($search['direction'] == '+') {
                            $next_class_day_query->where("class_on", '>', date('Y-m-d', strtotime($search['from_date'])) . ' 23:59:59');
                            $next_class_day_query->orderBy("class_on", "ASC");
                        } elseif($search['direction'] == '-') {
                            $next_class_day_query->where("class_on", '<', date('Y-m-d', strtotime($search['from_date'])) . ' 00:00:00');
                            $next_class_day_query->orderBy("class_on", "DESC");
                        }
                    }
                    $next_class_day_query->where('Class.batch_id', $search['batch_id']);
                    $next_class_day_query->limit(1);
                    $next_class_day = $next_class_day_query->value('class_on');

                    if($next_class_day) {
                        $q->where("Class.class_on", $next_class_day);
                    }

                // Limit by a number.
                } else {
                    $q->limit($search['limit']);
                }

            } elseif($field == 'direction' and isset($search['from_date'])) {
                if($search['direction'] == '+') {
                    $q->where("Class.class_on", '>', date('Y-m-d', strtotime($search['from_date'])) . ' 23:59:59');
                } elseif($search['direction'] == '-') {
                    $q->where("Class.class_on", '<', date('Y-m-d', strtotime($search['from_date'])) . ' 00:00:00');
                }

            } elseif($field == 'from_date') {
                continue; // Ignore - only used with 'direction'

            } else {
                $q->where("Class." . $field, $search[$field]);
            }
        }

        $q->where("Class.class_on", '>=', $this->year_start_time);
        $q->orderBy("Class.class_on", "ASC");
        $q->groupBy("Class.id");

        // dd($q->toSql(), $q->getBindings(), $search);

        return $q;
    }

    // /// Find the next class in the given batch from the given date in either direction.
    // function get_next_class($batch_id, $level_id, $from_date, $direction) {
    //     if($direction == "+") {
    //         $where = '>';
    //         $order = 'ASC';
    //         $time  = '23:59:59';
    //     } else {
    //         $where = '<';
    //         $order = 'DESC';
    //         $time  = '00:00:00';
    //     }

    //     $level_check = '';
    //     if($level_id) $level_check = " AND level_id=$level_id";

    //     $next_class = $this->db->query("SELECT * FROM Class WHERE class_on $where '$from_date $time' $level_check AND batch_id=$batch_id ORDER BY class_on $order LIMIT 0,1")->row();

    //     return $next_class;
    // }

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
