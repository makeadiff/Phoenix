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
        $survey_template = Survey_Template::add($fields);

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
                foreach ($fields['choices'] as $choice) {
                    $choice['survey_question_id'] = $last_question->id;
                    Survey_Choice::add($choice);
                }
            }
        }

        if(count($questions) === 1) $questions = $questions[0];
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

        $choices = [];
        foreach ($data as $index => $fields) {
            if(empty($fields['survey_question_id'])) $fields['survey_question_id'] = $question_id;

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

        if(count($choices) === 1) $choices = $choices[0];
        return JSend::success("Added Choices for question ID : $question_id", $choices);
    }

}
