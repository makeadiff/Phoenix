<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Center;

final class Student extends Model  
{
    protected $table = 'Student';
    public $timestamps = false;
    protected $fillable = ['name','sex','birthday','center_id','status','added_on', 'description', 'photo'];
    public $errors = array();

    public $year;
    private $id = 0;
    private $student = false;

    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);
        $this->year = 2017; // :TODO:
    }

    public function search($data)
    {
        $q = app('db')->table($this->table);

        $q->select("Student.id","Student.name","Student.added_on","Student.sex", "Student.status", "Student.center_id", "Student.birthday", "Student.description",
                        app('db')->raw("Center.name AS center_name"));
        $q->join("Center", "Center.id", '=', 'Student.center_id');

        if(!isset($data['status'])) $data['status'] = 1;
        if($data['status'] !== false) $q->where('Student.status', $data['status']); // Setting status as '0' gets you even the deleted students
        
        if(isset($data['center_id']) and $data['center_id'] != 0) $q->where('Student.center_id', $data['center_id']);
        
        if(!empty($data['id'])) $q->where('Student.id', $data['id']);
        if(!empty($data['student_id'])) $q->where('Student.id', $data['user_id']);
        if(!empty($data['city_id'])) $q->where('Center.city_id', $data['city_id']);
        if(!empty($data['name'])) $q->where('Student.name', 'like', '%' . $data['name'] . '%');
        if(!empty($data['sex'])) $q->where('Student.sex', $data['sex']);

        $results = $q->get();
        
        return $results;
    }

    public function fetch($student_id) {
        $data = Student::where('status','1')->find($student_id);
        if(!$data) return false;

        $this->id = $student_id;
        $this->student = $data;

        $data->center = $data->center()[0]->name;
        return $data;
    }


    public function center()
    {
        $center = $this->belongsTo('App\Models\Center', 'center_id');
        return $center->get();
    }

    public function add($data)
    {
        $student = Student::create([
            'name'      => $data['name'],
            'sex'       => isset($data['sex']) ? $data['sex'] : 'u',
            'birthday'  => isset($data['birthday']) ? $data['birthday'] : '',
            'center_id' => $data['center_id'],
            'description'   => isset($data['description']) ? $data['description'] : '',
            'photo'     => isset($data['photo']) ? $data['photo'] : '',
            'status'    => isset($data['status']) ? $data['status'] : '1',
            'added_on'  => isset($data['added_on']) ? $data['added_on'] : date('Y-m-d H:i:s')
        ]);

        return $student;
    }

    public function edit($data, $student_id = false)
    {
        $this->chain($student_id);

        foreach ($this->fillable as $key) {
            if(!isset($data[$key])) continue;

            $this->student->$key = $data[$key];
        }
        $this->student->save();

        return $this->student;
    }

    public function remove($student_id = false)
    {
        $this->chain($student_id);

        $this->student = Student::find($student_id);
        $this->student->status = 0;
        $this->student->save();

        return $this->student;
    }

    /// This is necessary to make the methord chaining work. With this, you can do stuff like - $student->find(3)->remove();
    private function chain($student_id) {
        if($student_id) {
            $this->id = $student_id;
        }
        if(!$this->id and $this->attributes['id']) {
            $this->id = $this->attributes['id'];
        }

        if(!$this->student) {
            $this->student = $this->find($this->id);
        }
    }
}
