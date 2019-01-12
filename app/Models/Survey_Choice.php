<?php
namespace App\Models;

use App\Models\Common;

final class Survey_Choice extends Common  
{
    protected $table = 'Survey_Choice';
    public $timestamps = false;
    protected $fillable = ['name', 'description', 'survey_question_id', 'sort_order', 'value', 'status'];

    public function question()
    {
         $question = $this->belongsTo('App\Models\Survey_Question', 'survey_question_id');
         return $question->first();
    }

    public static function search($data) 
    {
        $q = app('db')->table('Survey_Choice');

        $q->select("id", "name", "description", "value", 'sort_order');

        if(!isset($data['status'])) $data['status'] = 1;
        if($data['status'] !== false) $q->where('status', $data['status']); // Setting status as '0' gets you even the deleted question
        
        if(isset($data['survey_question_id']) and $data['survey_question_id'] != 0) $q->where('survey_question_id', $data['survey_question_id']);
        if(!empty($data['id'])) $q->where('id', $data['id']);
        if(!empty($data['choice_id'])) $q->where('id', $data['choice_id']);
        
        $q->orderby('sort_order');
        // dd($q->toSql(), $q->getBindings());

        $results = $q->get();
        return $results;
    }

    public static function inQuestion($survey_question_id)
    {
        return Survey_Choice::search(['survey_question_id' => $survey_question_id]);
    }

    public static function add($fields)
    {
        return Survey_Choice::create($fields);
    }

}

