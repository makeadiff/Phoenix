<?php
namespace App\Models;

use App\Models\Common;

final class Survey extends Common  
{
    protected $table = 'Survey';
    const CREATED_AT = 'added_on';
    public $timestamps = true;
    protected $fillable = ['survey_template_id', 'added_by_user_id'];

    public function template()
    {
         return $this->belongsTo('App\Models\Survey_Template', 'survey_template_id')->first();
    }
    public function responses()
    {
        return $this->hasMany('App\Models\Survey_Response');
    }

    public static function search($data)
    {
        $q = app('db')->table('Survey');

        $q->select("Survey.id", "Survey_Template.name", "Survey_Template.description", "Survey_Template.responder", 'Survey_Template.vertical_id', 'Survey.survey_template_id');
        $q->join("Survey_Template", "Survey_Template.id", '=', 'Survey.survey_template_id');

        if(!empty($data['id'])) $q->where('Survey.id', $data['id']);
        if(!empty($data['survey_id'])) $q->where('Survey.id', $data['survey_id']);
        if(!empty($data['name'])) $q->where('Survey_Template.name', 'like', '%' . $data['name'] . '%');
        if(!empty($data['description'])) $q->where('Survey_Template.description', 'like', '%' . $data['description'] . '%');
        if(!empty($data['vertical_id'])) $q->where('Survey_Template.vertical_id', $data['vertical_id']);
        if(!empty($data['responder'])) $q->where('Survey_Template.responder', $data['responder']);
        if(!empty($data['survey_template_id'])) $q->where('Survey.survey_template_id', $data['survey_template_id']);
        // dd($q->toSql(), $q->getBindings());

        $results = $q->get();
        return $results;
    }

    public function fetch($survey_id) {
        if(!$survey_id) return false;

        $survey = Survey::find($survey_id);
        $survey->template_name = $survey->template()->name;
        $survey->template_id = $survey->template()->id;
        return $survey;
    }
}

