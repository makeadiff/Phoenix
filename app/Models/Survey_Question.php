<?php
namespace App\Models;

use App\Models\Common;
use App\Models\Survey_Question_Category;
use App\Models\Survey_Choice;
use Validator;

final class Survey_Question extends Common  
{
    protected $table = 'Survey_Question';
    public $timestamps = false;
    protected $fillable = ['question', 'survey_question_category_id', 'survey_template_id', 'response_type', 'required', 'sort_order'];

	public function responses()
    {
        return $this->hasMany('App\Models\Survey_Response');
    }

    public function choices()
    {
        return $this->hasMany('App\Models\Survey_Choice');
    }

    public function survey_template()
    {
         $template = $this->belongsTo('App\Models\Survey_Template', 'survey_template_id');
         return $template->first();
    }

    public function survey_question_category()
    {
         $category = $this->belongsTo('App\Models\Survey_Question_Category', 'survey_question_category_id');
         return $category->first();
    }

    public static function search($data) 
    {
        $q = app('db')->table('Survey_Question');

        $q->select("id", "question", "survey_question_category_id", 'response_type', 'required', 'sort_order', app('db')->raw("'question' AS type"));

        if(!isset($data['status'])) $data['status'] = 1;
        if($data['status'] !== false) $q->where('status', $data['status']); // Setting status as '0' gets you even the deleted question
        
        if(isset($data['survey_template_id']) and $data['survey_template_id'] != 0) $q->where('survey_template_id', $data['survey_template_id']);
        if(isset($data['survey_question_category_id'])) $q->where('survey_question_category_id', $data['survey_question_category_id']);
        if(!empty($data['id'])) $q->where('id', $data['id']);
        if(!empty($data['question_id'])) $q->where('id', $data['question_id']);
        if(!empty($data['response_type'])) $q->where('response_type', $data['response_type']);
        if(!empty($data['required'])) $q->where('required', $data['required']);
        
        if(!empty($data['survey_id'])) {
            $survey = Survey::fetch($data['survey_id']);
            if($survey) $q->where("survey_template_id", $survey->survey_template_id);
        }
        $q->orderby('sort_order');
        // dd($q->toSql(), $q->getBindings(), $data);

        $results = $q->get();

        foreach ($results as $index => $question) {
            if($question->response_type == 'choice') {
                $results[$index]->choices = Survey_Choice::inQuestion($question->id);
            }
        }
        return $results;
    }

    /// Returns all the questions with categories as well. The questions/category linking and hirachy will be precerved. Use this by default.
    public function inCategorizedFormat($survey_template_id, $survey_id=0)
    {
        if(!$survey_template_id and $survey_id) {
            $survey = Survey::fetch($survey_id);
            $survey_template_id = $survey->survey_template_id;
        }

        if(!$survey_template_id) {
            return $this->error("Can't find Survey Template ID. Make sure its passed as an argument like this - $survey_question->inCategorizedFormat(3)");
        }

        $questions = Survey_Question::search(['survey_template_id' => $survey_template_id, 'survey_question_category_id' => 0]);

        $categories = Survey_Question_Category::inSurveyTemplate($survey_template_id);
        foreach ($categories as $category) {
            $category->type = 'category';
            $category->questions = Survey_Question::search(['survey_template_id' => $survey_template_id, 'survey_question_category_id' => $category->id]);

            $questions[] = $category;
        }

        return $questions;
    }

     public function addMany($data, $survey_template_id = 0)
    {
        $questions = [];
        $choice_model = new Survey_Choice;
        $sort_order  = 0;
        foreach ($data as $index => $fields) {
            if(empty($fields['survey_template_id']) and $survey_template_id) $fields['survey_template_id'] = $survey_template_id;

            // Validation...
            $validator = Validator::make($fields, [
                'question'              => 'required',
                'survey_template_id'    => 'required|integer|exists:Survey_Template,id',
                'response_type'         => 'required|in:text,choice,number,1-10,1-5,yes-no,date,datetime,file'
            ]);
            if ($validator->fails()) {
                $this->error($validator->errors());
            } else {
                $sort_order += 10;
                $fields['sort_order'] = $sort_order;
                $last_question = json_decode(json_encode(Survey_Question::create($fields))); // I was not getting the ID without doing this. Because it was protected.
                $questions[] = $last_question;
                
                if($fields['response_type'] == 'choice' and isset($fields['choices']) and is_array($fields['choices'])) {
                    $status = $choice_model->addMany($fields['choices'], $last_question->id);
                    if(!$status) {
                        $this->error($choice_model->errors);
                    }
                }
            }
        }

        return $questions;
    }

}

