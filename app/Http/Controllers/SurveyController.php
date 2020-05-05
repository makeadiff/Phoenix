<?php
namespace App\Http\Controllers;

use App\Models\Survey;
use App\Models\Survey_Template;
use App\Models\Survey_Question;
use App\Models\Survey_Choice;
use App\Models\Survey_Response;

use Illuminate\Http\Request;
use Validator;
use JSend;

class SurveyController extends Controller
{
    public function addSurveyTemplate(Request $request)
    {
        $fields = array_filter($request->only('name', 'description', 'vertical_id', 'responder', 'questions', 'options'));

        // Validation...
        $validator = Validator::make($fields, [
            'name'      => 'required',
            'responder' => 'required|in:User,Student,Center'
        ]);
        if ($validator->fails()) {
            return JSend::fail("Survey Template insert validation failed", $validator->errors());
        }

        $survey_template = Survey_Template::add($fields);
        if (!empty($fields['questions'])) {
            $questions = json_decode($fields['questions'], true);
            $question_model = new Survey_Question;
            $question_model->addMany($questions, $survey_template->id);
        }

        // :TODO: - Maybe create an instance along with the template. Because an instance is what people want.

        return JSend::success("Added a Survey Template", ['templates' => $survey_template]);
    }

    public function addQuestion($survey_template_id, Request $request)
    {
        $body = $request->getContent();
        if ($body) { // If you want to add multiple questions together.
            $data = json_decode($body, true);
        } else {
            $data = [array_filter($request->only('question', 'survey_question_category_id', 'survey_template_id', 'response_type', 'required', 'sort_order', 'choices'))];
        }

        $question_model = new Survey_Question;
        $questions = $question_model->addMany($data, $survey_template_id);

        if (count($questions) == 1) {
            $questions = $questions[0];
        }
        if ($questions) {
            return JSend::success("Added Questions to Survey Template ID : $survey_template_id", ['questions' => $questions]);
        } else {
            return JSend::fail("Error adding questions", $question_model->errors);
        }
    }

    public function addChoice($survey_template_id, $question_id, Request $request)
    {
        // This can create multiple Choices - or a single choice depending on the input - https://apihandyman.io/api-design-tips-and-tricks-getting-creating-updating-or-deleting-multiple-resources-in-one-api-call/#create-multiple-resources
        $body = $request->getContent();
        if ($body) {
            $data = json_decode($body, true);
        } else {
            $data = [array_filter($request->only('name', 'description', 'value', 'survey_question_id', 'sort_order'))];
        }

        $choice_model = new Survey_Choice;
        $choices = $choice_model->addMany($data, $question_id);

        if (count($choices) == 1) {
            $choices = $choices[0];
        }
        if ($choices) {
            return JSend::success("Added Choices for question ID : $question_id", ['choices' => $choices]);
        } else {
            return JSend::fail("Error adding choice", $choice_model->errors);
        }
    }

    public function addResponse($survey_id, Request $request)
    {
        return $this->addQuestionResponse($survey_id, false, $request);
    }

    public function addQuestionResponse($survey_id, $question_id, Request $request)
    {
        $body = $request->getContent();
        if ($body) {
            $data = json_decode($body, true);
        } else {
            $data = [array_filter($request->only('responder_id', 'survey_question_id', 'survey_choice_id', 'response', 'added_by_user_id'))];
        }

        $response_model = new Survey_Response;

        $responses = $response_model->addMany($data, $survey_id, $question_id);
        if (count($responses) == 1) {
            $responses = $responses[0];
        }

        if (!$responses) {
            return JSend::fail("Error adding response", $response_model->errors);
        }
        return JSend::success("Added Responses for Survey ID : $survey_id", ['responses' => $responses]);
    }
}
