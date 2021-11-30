<?php
namespace App\Models;

use App\Models\Common;
use App\Models\Center;
use Illuminate\Database\Eloquent\Model;

final class Batch extends Model
{
    use Common;
    
    protected $table = 'Batch';
    public $timestamps = true;
    const CREATED_AT = 'added_on';
    const UPDATED_AT = 'updated_on';
    protected $fillable = ['day','class_time','center_id','project_id','status','year', 'batch_head_id'];

    public function center()
    {
        return $this->belongsTo('App\Models\Center', 'center_id');
    }
    public function levels()
    {
        return $this->belongsToMany("App\Models\Level", 'BatchLevel'); // ->where('BatchLevel.year', '=', $this->year());
    }
    public function teachers()
    {
        return $this->belongsToMany("App\Models\User", 'UserBatch')->where('UserBatch.role', 'teacher');
    }
    public function mentors()
    {
        return $this->belongsToMany("App\Models\User", "UserBatch")->where("UserBatch.level_id", 0)->where('UserBatch.role', 'mentor');
    }

    public function classes()
    {
        return $this->hasMany("App\Models\Classes");
    }
    public function allocations()
    {
        return $this->hasMany("App\Models\Allocation");
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
        $search_fields = ['id', 'day', 'center_id', 'level_id', 'batch_id', 'project_id', 'year', 'teacher_id', 'mentor_id', 'direction', 'from_date', 'limit', 'class_status', 'city_id'];
        $q->select('Batch.id', 'Batch.day', 'Batch.class_time', 'Batch.batch_head_id', 'Batch.center_id', 'Batch.status', 'Batch.project_id')->distinct();
        if (!isset($data['status'])) {
            $data['status'] = '1';
        }
        if (!isset($data['year'])) {
            $data['year'] = $this->year();
        }

        if (isset($data['teacher_id'])) {
            $q->join("UserBatch", 'Batch.id', '=', 'UserBatch.batch_id');
        }

        if (isset($data['level_id'])) {
            $q->join('UserBatch', 'Batch.id', '=', 'UserBatch.batch_id');
            $q->join("Level", 'Level.id', '=', 'UserBatch.level_id');
            $q->where("Level.year", $this->year())->where('Level.status', '1');
        }

        if (isset($data['city_id'])) {
            $q->join("Center", 'Center.id', '=', 'Batch.center_id');
        }

        foreach ($search_fields as $field) {
            if (empty($data[$field])) {
                continue;
            } elseif ($field == 'batch_id') {
                $q->where("Batch.id", $data[$field]);
            } elseif ($field == 'teacher_id') {
                $q->where("UserBatch.user_id", $data[$field]);
            } elseif ($field == 'mentor_id') {
                $q->where("Batch.batch_head_id", $data[$field]);
            } elseif ($field == 'level_id') {
                $q->where('UserBatch.level_id', $data[$field]);
            } elseif ($field === 'city_id') {
                $q->where('Center.city_id', $data[$field]);
            } elseif ($field == 'direction' and isset($data['from_date'])) {
                $q->join("Class", 'Class.batch_id', '=', 'Batch.id');
                $q->orderBy("Class.class_on", "ASC");

                if ($data['direction'] == '+') {
                    $q->where("Class.class_on", '>', date('Y-m-d', strtotime($search['from_date'])) . ' 23:59:59');
                } elseif ($data['direction'] == '-') {
                    $q->where("Class.class_on", '<', date('Y-m-d', strtotime($search['from_date'])) . ' 00:00:00');
                }
            } elseif ($field == 'limit') {
                $q->limit($data['limit']);
            } elseif ($field == 'class_status') { // You can use this to get batches with projected classes in them.
                $q->join("Class", 'Class.batch_id', '=', 'Batch.id');
                $q->where("Class.status", $data[$field]);
                $q->where("Class.class_on", "<=", date('Y-m-d H:i:s')); // Only search for this in classes that should be over. not future classes
            } elseif ($field == 'from_date') {
                continue; // Ignore - only used with 'direction'
            } else {
                $q->where("Batch." . $field, $data[$field]);
            }
        }

        $q->orderBy('day')->orderBy('class_time');
        //dd($q->toSql(), $q->getBindings(), $data);

        return $q;
    }

    public function fetch($id, $is_active = true)
    {
        $this->id = $id;
        if ($is_active) {
            $this->item = $this->where('status', '1')->where('year', $this->year())->find($id);
        } else {
            $this->item = $this->find($id);
        }
        if (!$this->item) {
            return false;
        }

        $this->item->name = $this->getName($this->item->day, $this->item->class_time);
        $this->item->center = $this->item->center()->first()->name;
        $this->item->vertical_id = $this->getVerticalIdFromProjectId($this->item->project_id);
        return $this->item;
    }

    public function inCenter($center_id)
    {
        return $this->search(['center_id' => $center_id]);
    }

    public function add($data)
    {
        $batch_data = [
            'day'       => (string) $data['day'],
            'class_time'=> $data['class_time'],
            'center_id' => $data['center_id'],
            'project_id'=> $data['project_id'],
            'year'      => isset($data['year']) ? $data['year'] : $this->year(),
            'status'    => isset($data['status']) ? $data['status'] : '1',
            'batch_head_id' => '0'
        ];
        $batch = Batch::create($batch_data); // Should be working - but doesn't - getting a "array_key_exists(): The first argument should be either a string or an integer" error
        $batch_id = $batch->id;

        if (!empty($data['batch_head_id'])) {
            $this->assignMentor($batch_id, $data['batch_head_id']);
        }

        return $batch;
    }

    public function getName($day, $time)
    {
        $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        return $days[$day] . ' ' . date('h:i A', strtotime('2018-01-21 ' . $time)); // Random date. No relavence to the result.
    }

    public function name()
    {
        if (!$this->id) {
            return false;
        }

        return $this->getName($this->day, $this->class_time);
    }

    private function getVerticalIdFromProjectId($project_id)
    {
        $project_vertical_mapping = [
            1   => 3,
            2   => 19,
            4   => 5,
            5   => 5,
            6   => 18
        ];

        return $project_vertical_mapping[$project_id];
    }
}
