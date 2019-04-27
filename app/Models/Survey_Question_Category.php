<?php
namespace App\Models;

use App\Models\Common;

final class Survey_Question_Category extends Common  
{
    protected $table = 'Survey_Question_Category';
    public $timestamps = false;

	public function questions()
    {
        return $this->hasMany('App\Models\Survey_Question');
    }

    public function survey_template()
    {
        return $this->belongsTo('App\Models\Survey_Template', 'survey_template_id');
    }

    public static function search($data) 
    {
        $q = app('db')->table('Survey_Question_Category');

        $q->select("id", "name");

        if(!isset($data['status'])) $data['status'] = '1';
        if($data['status'] !== false) $q->where('status', $data['status']); // Setting status as '0' gets you even the deleted question
        
        if(isset($data['survey_template_id']) and $data['survey_template_id'] != 0) $q->where('survey_template_id', $data['survey_template_id']);
        if(!empty($data['id'])) $q->where('id', $data['id']);
        if(!empty($data['category_id'])) $q->where('id', $data['category_id']);
        
        if(!empty($data['survey_id'])) {
            $survey = Survey::fetch($data['survey_id']);
            if($survey) $q->where("survey_template_id", $survey->survey_template_id);
        }
        $q->orderby('sort_order');
        // dd($q->toSql(), $q->getBindings());

        $results = $q->get();
        return $results;
    }

    public static function inSurveyTemplate($survey_template_id)
    {
        return Survey_Question_Category::search(['survey_template_id' => $survey_template_id]);
    }

}

