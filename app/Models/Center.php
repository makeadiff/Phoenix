<?php
namespace App\Models;

use App\Models\Common;

final class Center extends Common
{
    protected $table = 'Center';
    public $timestamps = true;
    const CREATED_AT = null;
    const UPDATED_AT = 'updated_on';

    public function users()
    {
        return $this->hasMany('App\Models\User')->where('User.status', '=', '1')->where('User.user_type', '=', 'volunteer');
    }

    public function city()
    {
        return $this->belongsTo('App\Models\City', 'city_id');
    }
    // public function projects()
    // {
    //     $projects = $this->belongsToMany('App\Models\Project', 'CenterProject', 'center_id', 'project_id');
    //     $projects->wherePivot('year', $this->year);
    //     return $projects;
    // }

    public function projects()
    {
        $projects = $this->hasMany('App\Models\CenterProject');
        // $projects->wherePivot('year', $this->year);
        return $projects;
    }

    public function batches($project_id = 1)
    {
        return $this->hasMany('App\Models\Batch')->where('Batch.status', '1')->where('Batch.year', $this->year)
                    ->where('Batch.project_id', $project_id)->orderBy("Batch.day");
    }
    public function levels($project_id = 1)
    {
        return $this->hasMany('App\Models\Level')->where('Level.status', '1')->where('Level.year', $this->year)
                    ->where('Level.project_id', $project_id)->orderBy("Level.grade")->orderBy("Level.name");
    }
    public function students()
    {
        return $this->hasMany('App\Models\Student')->where('Student.status', '1')->orderBy("Student.name");
    }

    public static function getAll()
    {
        return Center::where('status', '1')->orderBy('name')->get();
    }

    public static function search($data)
    {
        $search_fields = ['id', 'name', 'city_id'];
        $q = app('db')->table('Center');
        $q->select('id', 'name', 'city_id', 'center_head_id', 'class_starts_on');
        $q->where('status', '1');

        foreach ($search_fields as $field) {
            if (empty($data[$field])) {
                continue;
            }

            if ($field === 'name') {
                $q->where($field, 'like', '%' . $data[$field] . '%');
            } else {
                $q->where($field, $data[$field]);
            }
        }
        $q->orderBy('name');
        $results = $q->get();

        return $results;
    }

    public static function inCity($city_id)
    {
        return Center::search(['city_id' => $city_id]);
    }
}
