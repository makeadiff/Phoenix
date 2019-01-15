<?php
namespace App\Models;

use App\Models\Common;
use Validator;
// use App\Models\Survey_Question;
// use App\Models\Survey_Choice;

final class Survey_Response extends Common  
{
    protected $table = 'Survey_Response';
    const CREATED_AT = 'added_on';
    const UPDATED_AT = null;
    public $timestamps = true;
    protected $fillable = ['survey_id', 'responder_id', 'survey_question_id', 'survey_choice_id', 'response', 'added_by_user_id'];

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


    public function add($fields, $survey_id = 0, $question_id = 0)
    {
        if(empty($fields['survey_id']) and $survey_id) $fields['survey_id'] = $survey_id;
        if(empty($fields['survey_question_id']) and $question_id) $fields['survey_question_id'] = $question_id;

        $validator = Validator::make($fields, [
            'survey_id'             => 'required|integer|exists:Survey,id',
            'survey_question_id'    => 'required|integer|exists:Survey_Question,id',
            'survey_choice_id'      => 'required_without:response|integer|exists:Survey_Question,id',
            'response'              => 'required_without:survey_choice_id',
            'responder_id'          => 'required|integer' 
            // :TODO: Additional validation rules - responder_id should map to the valid table, question_id and choice_id should have status=1 in their table
        ]);
        $response = false;
        if ($validator->fails()) {
            return $this->error($validator->errors());
        } else {
            $response = Survey_Response::create($fields);
        }

        return $response;
    }

    public function addMany($data, $survey_id = 0, $question_id)
    {
        $responses = [];
        foreach ($data as $row) {
            $response = $this->add($row, $survey_id, $question_id);
            if($response) $responses[] = $response;
        }

        return $responses;
    }

}

