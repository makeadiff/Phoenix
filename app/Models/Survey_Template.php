<?php
namespace App\Models;

use App\Models\Common;

final class Survey_Template extends Common
{
    protected $table = 'Survey_Template';
    const CREATED_AT = 'added_on';
    const UPDATED_AT = null;
    public $timestamps = true;
    protected $fillable = ['name', 'description', 'vertical_id', 'responder', 'status'];

    public function surveys()
    {
        return $this->hasMany('App\Models\Survey', 'survey_template_id')->where('Survey.status', '=', '1');
    }
    public function questions()
    {
        return $this->hasMany('App\Models\Survey_Question', 'survey_template_id')->where('Survey_Question.status', '=', '1')->orderBy("sort_order");
    }

    public static function search($data)
    {
        $q = app('db')->table('Survey_Template');

        $q->select("id", "name", "description", "responder", 'vertical_id', "options", 'status');

        if (!isset($data['status'])) {
            $data['status'] = '1';
        }
        if ($data['status'] !== false) {
            $q->where('status', $data['status']);
        } // Setting status as '0' gets you even the deleted question
        
        if (!empty($data['id'])) {
            $q->where('id', $data['id']);
        }
        if (!empty($data['name'])) {
            $q->where('name', 'like', '%' . $data['name'] . '%');
        }
        if (!empty($data['description'])) {
            $q->where('description', 'like', '%' . $data['description'] . '%');
        }
        if (!empty($data['vertical_id'])) {
            $q->where('vertical_id', $data['vertical_id']);
        }
        if (!empty($data['responder'])) {
            $q->where('responder', $data['responder']);
        }
        // dd($q->toSql(), $q->getBindings());

        $results = $q->get();
        return $results;
    }

    public static function add($fields)
    {
        if($fields['responder'] == 'User' and empty($fields['options'])) {
            $fields['options'] = json_encode(["responder_list" => "self"]);
        }
        
        return Survey_Template::create($fields);
    }
}
