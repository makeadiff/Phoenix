<?php
namespace App\Models;

use App\Models\Common;
use Validator;

final class Survey extends Common  
{
    protected $table = 'Survey';
    const CREATED_AT = 'added_on';
    const UPDATED_AT = null;
    public $timestamps = true;
    protected $fillable = ['survey_template_id', 'added_by_user_id', 'name'];

    public function template()
    {
         return $this->belongsTo('App\Models\Survey_Template', 'survey_template_id');
    }
    public function responses()
    {
        return $this->hasMany('App\Models\Survey_Response');
    }

    public static function search($data)
    {
        $q = app('db')->table('Survey');

        $q->select("Survey.id", "Survey.name", "Survey_Template.description", "Survey_Template.responder", 'Survey_Template.vertical_id', 
                    'Survey_Template.options', 'Survey.survey_template_id', app('db')->raw("Survey_Template.name AS template_name"));
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

    public function fetch($survey_id)
    {
        if(!$survey_id) return false;

        $survey = Survey::find($survey_id);
        if($survey) {
            $template = $survey->template()->first();
            $survey->template_name = $template->name;
            $survey->description = $template->description;
            $survey->responder = $template->responder;
            $survey->options = $template->options;
            return $survey;
        }
        return false;
    }

    public function add($survey_template_id, $name, $added_by_user_id)
    {
        $fields = ['survey_template_id' => $survey_template_id, 'added_by_user_id' => $added_by_user_id, 'name' => $name];
        $validator = Validator::make($fields, [
            'survey_template_id'    => 'required|integer|exists:Survey_Template,id',
            'added_by_user_id'      => 'required|integer|exists:User,id'
        ]);
        if ($validator->fails()) {
            return $this->error($validator->errors());
        } else {
            return $this->create($fields);
        }
    }
}

