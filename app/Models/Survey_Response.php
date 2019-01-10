<?php
namespace App\Models;

use App\Models\Common;
// use App\Models\Survey_Question;
// use App\Models\Survey_Choice;

final class Survey_Response extends Common  
{
    protected $table = 'Survey_Response';
    const CREATED_AT = 'added_on';
    public $timestamps = true;

	public function question()
    {
        return $this->belongsTo('App\Models\Survey_Question');
    }

    public function survey()
    {
        return $this->belongsTo('App\Models\Survey');
    }

    public function choice()
    {
        return $this->belongsTo('App\Models\Survey_Choice');
    }

    public function responder()
    {
        // $responder = $this->morphTo();
        // return $responder->first();
    }


    public static function search($data) 
    {
        $q = app('db')->table('Survey_Response');

        $q->select("id", "survey_id", "responder_id", 'survey_question_id', 'survey_choice_id', 'response', 'added_on', 'added_by_user_id');

        if(!empty($data['id'])) $q->where('id', $data['id']);
        if(!empty($data['response_id'])) $q->where('id', $data['response_id']);
        if(!empty($data['survey_id'])) $q->where('survey_id', $data['survey_id']);
        if(!empty($data['question_id'])) $q->where('survey_question_id', $data['question_id']);
        if(!empty($data['responder_id'])) $q->where('responder_id', $data['responder_id']);
        if(!empty($data['added_by_user_id'])) $q->where('added_by_user_id', $data['added_by_user_id']);
        
        $results = $q->get();

        $choice_model = new Survey_Choice;
        foreach ($results as $index => $response) {
            if($response->survey_choice_id) {
                $response->choice = $choice_model->fetch($response->survey_choice_id)->name;
            }
        }
        return $results;
    }

    public static function inSurvey($survey_id) {
        $q = app('db')->table('Survey_Response');
        $q->select("id", "survey_id", "responder_id", 'survey_question_id', 'survey_choice_id', 'response', 'added_on', 'added_by_user_id');
        $q->where('survey_id', $survey_id);
        $q->orderBy("responder_id");
        $responses = $q->get();

        // :TODO: Paging
        
        $last_responder_id = 0;
        $return = [];
        foreach ($responses as $index => $row) {
            if($row->responder_id != $last_responder_id) {
                $return[$row->responder_id] = [];
            }

            $return[$row->responder_id][] = $row;
        }

        return $return;
    }

    public function fetch($response_id) {
        if(!$response_id) return false;

        $response = Survey_Response::find($response_id);
        if($response) {
            $response->choice = (new Survey_Choice)->fetch($response->survey_choice_id)->name;
        }
        return $response;
    }

}

