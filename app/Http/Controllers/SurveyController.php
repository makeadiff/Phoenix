<?php
namespace App\Http\Controllers;

use App\Models\Survey;
use App\Models\Survey_Template;
use App\Models\Survey_Question;
use App\Models\Survey_Choice;

use Illuminate\Http\Request;
use Validator;
use JSend;

class SurveyController extends Controller
{
    public function addSurveyTemplate(Request $request)
    {
        $fields = array_filter($request->only('name', 'description', 'vertical_id', 'responder', 'questions'));

        // Validation...
        $validator = Validator::make($fields, [
            'name'      => 'required',
            'responder' => 'required|in:User,Student,Center'
        ]);
        if ($validator->fails()) {
            return JSend::fail("Survey Template insert validation failed", $validator->errors());
        }
        $survey_template = Survey_Template::add($fields);
        if(!empty($fields['questions'])) {
            $questions = json_decode($fields['questions'], true);
            if($questions) $this->addQuestions($survey_template->id, $questions);
        }

        return JSend::success("Added a Survey Template", $survey_template);
    }

    public function addSurveyQuestion($survey_template_id, Request $request)
    {
        $body = $request->getContent();
        if($body) { // If you want to add multiple questions together.
            $data = json_decode($body, true);
        } else {
            $data = [array_filter($request->only('question', 'survey_question_category_id', 'survey_template_id', 'response_type', 'required', 'sort_order', 'choices'))];
        }

        $questions = $this->addQuestions($survey_template_id, $data);
        return JSend::success("Added Questions to Survey Template ID : $survey_template_id", $questions);
    }

    public function addSurveyChoice($survey_template_id, $question_id, Request $request) 
    {
        // This can create multiple Choices - or a single choice depending on the input - https://apihandyman.io/api-design-tips-and-tricks-getting-creating-updating-or-deleting-multiple-resources-in-one-api-call/#create-multiple-resources
        $body = $request->getContent();
        if($body) {
            $data = json_decode($body, true);
        } else {
            $data = [array_filter($request->only('name', 'description', 'value', 'survey_question_id', 'sort_order'))];
        }

        $this->addChoices($question_id, $data);
        return JSend::success("Added Choices for question ID : $question_id", $choices);
    }

    public function addQuestions($survey_template_id, $data)
    {
        $questions = [];
        foreach ($data as $index => $fields) {
            if(empty($fields['survey_template_id'])) $fields['survey_template_id'] = $survey_template_id;

            // Validation...
            $validator = Validator::make($fields, [
                'question'              => 'required',
                'survey_template_id'    => 'required|integer|exists:Survey_Template,id',
                'response_type'         => 'required|in:text,choice,number,1-10,1-5,yes-no,date,datetime,file'
            ]);
            if ($validator->fails()) {
                return JSend::fail("Survey Question insert validation failed", $validator->errors());
            }

            $questions[] = Survey_Question::add($fields);
            if($fields['response_type'] == 'choice' and isset($fields['choices']) and is_array($fields['choices'])) {
                $last_question = end($questions);
                $this->addChoices($last_question->id, $fields['choices']);
            }
        }

        if(count($questions) === 1) return $questions[0];
        return $questions;
    }

    public function addChoices($survey_question_id, $data)
    {
        $choices = [];
        foreach ($data as $index => $fields) {
            if(empty($fields['survey_question_id'])) $fields['survey_question_id'] = $survey_question_id;

            // Validation...
            $validator = Validator::make($fields, [
                'name'                  => 'required',
                'survey_question_id'    => 'required|integer|exists:Survey_Question,id'
            ]);
            if ($validator->fails()) {
                return JSend::fail("Survey Choice insert validation failed", $validator->errors());
            }

            $choices[] = Survey_Choice::add($fields);
        }

        if(count($choices) === 1) return $choices[0];
        return $choices;
    }

}
