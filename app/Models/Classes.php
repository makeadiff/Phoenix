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
    protected $fillable = ['batch_id', 'level_id', 'project_id', 'class_on', 'class_type', 'class_satisfaction',
                            'cancel_option','cancel_reason','updated_by_mentor','updated_by_teacher','status'];

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
        return $this->belongsToMany('App\Models\Student', 'StudentClass', 'class_id', 'student_id')
                ->withPivot('present', 'participation', 'check_for_understanding');
    }
    public function teachers()
    {
        return $this->belongsToMany('App\Models\User', 'UserClass', 'class_id', 'user_id')
                ->withPivot('substitute_id', 'zero_hour_attendance', 'status');
    }
    public function substitutes()
    {
        return $this->belongsToMany('App\Models\User', 'UserClass', 'class_id', 'substitute_id')
                ->withPivot('substitute_id', 'user_id', 'zero_hour_attendance', 'status');
    }

    // public function subject()
    // {
    //     return $this->hasOne('App\Models\Subject')
    //                 ->join("UserBatch", "UserBatch.batch_id", "=", "Class.batch_id")
    //                 ->where("UserBatch.level_id", "=", "Class.level_id");
    // }

    // public function allocation()
    // {
    //     return $this->hasMany('App\Models\Allocation')
    //             ->where('UserBatch.batch_id', '=', 'Class.batch_id')
    //             ->where('UserBatch.level_id', '=', 'Class.level_id');
    //     // return $this->hasManyThrough('App\Models\Allocation')
    // }

    public function search($data)
    {
        $q = app('db')->table('Class');
        return $this->baseSearch($data, $q)->get();
    }

    /// This is a seperate function because even Project->classes() uses almost the exact same thing.
    public function baseSearch($search, $q = false)
    {
        if (!$q) {
            $q = app('db')->table('Class');
        }
        
        // teacher_id: Int, status: String, batch_id: Int, level_id: Int, project_id: Int, class_date: Date, direction: String)
        $search_fields = ['teacher_id', 'substitute_id', 'batch_id', 'level_id', 'center_id', 'project_id', 'status', 
                        'class_date', 'class_on', 'class_date_to','class_date_from', 'class_status', 'direction', 'from_date', 'limit'];
        $q->select(
            'Class.id',
            'Class.batch_id',
            'Class.level_id',
            'Class.class_on',
            'Class.class_type',
            'Class.class_satisfaction',
            'Class.cancel_option',
            'Class.cancel_reason',
            'Class.status AS class_status',
            'UserClass.id AS user_class_id',
            'UserClass.substitute_id',
            'UserClass.zero_hour_attendance',
            'UserClass.status AS status'
        ); // ->distinct('Class.id');
        $q->join("UserClass", 'UserClass.class_id', '=', 'Class.id');
        $q->join("Batch", 'Batch.id', '=', 'Class.batch_id');
        $q->join("Level", 'Level.id', '=', 'Class.level_id');

        $q->where("Batch.year", '=', $this->year);
        $q->where("Batch.status", '=', '1');
        $q->where("Level.year", '=', $this->year);
        $q->where("Level.status", '=', '1');

        foreach ($search_fields as $field) {
            if (empty($search[$field])) {
                continue;
            } elseif ($field == 'class_date') {
                $q->whereDate("Class.class_on", $search[$field]);
            } elseif ($field == 'class_date_to') {
                $q->whereDate("Class.class_on", '<=', $search[$field]);
            } elseif ($field == 'class_date_from') {
                $q->whereDate("Class.class_on", '>=', $search[$field]);
            } elseif ($field == 'class_status') {
                $q->where("Class.status", $search[$field]);
            } elseif ($field == 'status') {
                $q->where("UserClass.status", $search[$field]);
            } elseif ($field == 'teacher_id') {
                $q->where("UserClass.user_id", $search[$field]);
            } elseif ($field == 'center_id') {
                $q->where("Batch.center_id", $search[$field]);
            } elseif ($field == 'substitute_id') {
                $q->where("UserClass.substitute_id", $search[$field]);
            } elseif ($field == 'limit') {
                // Limit by one day - This will only show the classes of the given batch for the next one day - weather its a + or - direction.
                if ($search['limit'] == 'day' and isset($search['batch_id'])) {
                    $next_class_day_query = app('db')->table('Class')->select('class_on');
                    if (isset($search['direction']) and isset($search['from_date'])) {
                        if ($search['direction'] == '+') {
                            $next_class_day_query->where("class_on", '>', date('Y-m-d', strtotime($search['from_date'])) . ' 23:59:59');
                            $next_class_day_query->orderBy("class_on", "ASC");
                        } elseif ($search['direction'] == '-') {
                            $next_class_day_query->where("class_on", '<', date('Y-m-d', strtotime($search['from_date'])) . ' 00:00:00');
                            $next_class_day_query->orderBy("class_on", "DESC");
                        }
                    }
                    $next_class_day_query->where('Class.batch_id', $search['batch_id']);
                    $next_class_day_query->limit(1);
                    $next_class_day = $next_class_day_query->value('class_on');

                    if ($next_class_day) {
                        $q->where("Class.class_on", $next_class_day);
                    }

                    // Limit by a number.
                } else {
                    $q->limit($search['limit']);
                }
            } elseif ($field == 'direction') {
                if (!isset($search['from_date'])) {
                    $search['from_date'] = date('Y-m-d H:i:s');
                } // If no from date is specified, from date is today.

                if ($search['direction'] == '+') {
                    $q->where("Class.class_on", '>', date('Y-m-d', strtotime($search['from_date'])) . ' 23:59:59');
                } elseif ($search['direction'] == '-') {
                    $q->where("Class.class_on", '<', date('Y-m-d', strtotime($search['from_date'])) . ' 00:00:00');
                }
            } elseif ($field == 'from_date') {
                continue; // Ignore - only used with 'direction'
            } else {
                $q->where("Class." . $field, $search[$field]);
            }
        }

        $q->where("Class.class_on", '>=', $this->year_start_time);
        if (isset($search['direction']) and $search['direction'] == '-' and isset($search['limit'])) {
            $q->orderBy("Class.class_on", "DESC"); // If we are trying to find the latest class...
        } else {
            $q->orderBy("Class.class_on", "ASC");
        }
        $q->groupBy("Class.id");
        // dd($q->toSql(), $q->getBindings(), $search);

        return $q;
    }

    // Not tested.
    public function add($data)
    {
        if (empty($data['batch_id']) or empty($data['level_id']) or empty($data['teacher_id'])) {
            return false;
        }
        
        $class = $this->search(['batch_id' => $data['batch_id'], 'level_id' => $data['level_id'], 'class_on' => $data['class_on']]);

        if (!$class) {
            if (!isset($data['project_id'])) {
                $batch_model = new Batch;
                $project_id = $batch_model->find($data['batch_id'])->project_id;
                $data['project_id'] = $project_id;
            }
            $data['class_on'] = date('Y-m-d H:i:s', strtotime($data['class_on']));

            if (!in_array($data['class_type'], ['scheduled', 'extra'])) {
                $data['class_type'] = 'scheduled';
            }
            if (!in_array($data['status'], ['projected', 'happened', 'cancelled'])) {
                $data['status'] = 'projected';
            }

            $class = Classes::create($data);
        } else {
            $class = $class[0];
        }

        app('db')->table('UserClass')->insert([
            'class_id'      => $class->id,
            'user_id'       => $data['teacher_id'],
            'substitute_id' => 0,
            'status'        => 'projected'
        ]);

        return $class;
    }

    // Not tested.
    public function saveStudentAttendance($class_id, $student_id, $class_details, $class_satisfaction, $teacher_id = 0)
    {
        // Clear existing data
        app('db')->table('StudentClass')->where('class_id', $class_id)->where('student_id', $student_id)->delete();

        // Insert new data...
        $participation = isset($class_details['participation']) ? $class_details['participation'] : 0;
        $present = ($participation) ? "1" : "0";
        $check_for_understanding = isset($class_details['check_for_understanding']) ? $class_details['check_for_understanding'] : 0;

        $class_data = app('db')->table('StudentClass')->insert([
            'class_id'      => $class_id,
            'student_id'    => $student_id,
            'participation' => $participation,
            'present'       => $present,
            'check_for_understanding' => $check_for_understanding
        ]);
        $this->edit([
            'status' => 'happened', 
            'updated_by_teacher' => $teacher_id,
            'class_satisfaction' => $class_satisfaction
        ], $class_id);

        return $class_data;
    }

    public function cancel($class_id, $reason, $reason_other = "", $mentor_id = 0)
    {
        $cls = $this->find($class_id);
        $cls->status = 'cancelled';
        $cls->cancel_option = $reason;
        $cls->cancel_reason = $reason_other;
        $cls->updated_by_mentor = $mentor_id;
        $cls->save();

        app('db')->table('UserClass')->where('class_id', $class_id)->update(['status'    => 'cancelled']);

        return $cls;
    }

    public function saveTeacherAttendance($class_id, $teacher_id, $class_details, $mentor_id = 0)
    {
        $this->revertCredits($class_id, $teacher_id); // If this call is an edit of existing class data, revert Credits awarded earlier

        // Clear existing data
        app('db')->table('UserClass')->where('class_id', $class_id)->where('user_id', $teacher_id)->delete();

        // Insert new data...
        $substitute_id = isset($class_details['substitute_id']) ? $class_details['substitute_id'] : 0;
        $zero_hour_attendance = isset($class_details['zero_hour_attendance']) ? $class_details['zero_hour_attendance'] : 1;
        $status = isset($class_details['status']) ? $class_details['status'] : 'projected';

        $class_data = app('db')->table('UserClass')->insert([
            'class_id'      => $class_id,
            'user_id'       => $teacher_id,
            'substitute_id' => $substitute_id,
            'zero_hour_attendance' => (string)$zero_hour_attendance,
            'status'        => $status
        ]);

        $cls_status = $status;
        if ($status == 'attended' or $status == 'absent') {
            $cls_status = 'happened';
        }
        $this->edit(['status' => $cls_status, 'updated_by_mentor' => $mentor_id], $class_id);

        // Award credits for this class
        $this->awardCredits($class_id, $teacher_id, $status, $substitute_id, $zero_hour_attendance, ['mentor_id' => $mentor_id]);

        return $class_data;
    }

    public function awardCredits($class_id, $teacher_id, $status, $substitute_id, $zero_hour_attendance, $options = [])
    {
        $options_template = [
            'mentor_id' => 0, 
            'revert'    => false,
            'class'     => false
        ];
        $options = array_merge($options_template, $options);
        extract($options);

        if(!$class) $class = $this->find($class_id);
        if(!$class) return false;
        $project_id = $class->project_id;

        $credit_options = [
            'item'      => 'Class',
            'item_id'   => $class_id,
            'added_by_user_id'  => $mentor_id,
            'revert'    => $revert // This will reset credits that used to exist.
        ];

        if($project_id == 1) {
            $param_for_substituting = 1;
            $param_for_finding_a_substitute = 2;
            $param_for_missing_class = 4; 
            $param_for_missing_zero_hour = 3;
        
        } elseif($project_id == 2) {
            $param_for_substituting = 5;
            $param_for_finding_a_substitute = 6;
            $param_for_missing_class = 8; 
            $param_for_missing_zero_hour = 7;
        }

        // :TODO: TR and Aftercare

        $credit = new Credit;

        // MADApp/Class_model.php:367
        if($status == 'attended') {
            if($substitute_id) {
                $credit->assign($substitute_id, $param_for_substituting, $credit_options);
                $credit->assign($teacher_id, $param_for_finding_a_substitute, $credit_options);
  
                if(!$zero_hour_attendance) {
                    $credit->assign($substitute_id, $param_for_missing_zero_hour, $credit_options);
                }
            } else {
                if(!$zero_hour_attendance) {
                    $credit->assign($substitute_id, $param_for_missing_zero_hour, $credit_options);
                }
            }
            
        } elseif($status == 'absent') {
            if($substitute_id) {
                $credit->assign($substitute_id, $param_for_missing_class, $credit_options);
                $credit->assign($teacher_id, $param_for_finding_a_substitute, $credit_options);
            } else {
                $credit->assign($teacher_id, $param_for_missing_class, $credit_options);
            }
        }

        return 1;
    }

    public function revertCredits($class_id, $teacher_id)
    {
        $user_class = app('db')->table('UserClass')
            ->where('class_id', $class_id)->where('user_id', $teacher_id)
            ->get();

        // No pre-existing class data. No need to revert credits.
        if(!count($user_class)) return false;
        elseif($user_class[0]->status === "projected") return false;

        $this->awardCredits($class_id, $teacher_id, $user_class[0]->status, $user_class[0]->substitute_id, 
                        $user_class[0]->zero_hour_attendance, ['revert' => true]);
    }

}
