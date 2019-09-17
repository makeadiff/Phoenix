<?php
use App\Models\Survey;
use App\Models\Survey_Template;
use App\Models\Survey_Question;
use App\Models\Survey_Choice;
use App\Models\Survey_Response;

use App\Http\Controllers\SurveyController;
use Illuminate\Http\Request;

/// Surveys Routes / API Calls...

Route::get('/survey_templates', function (Request $request) {
    $search = array_filter($request->only('id', 'name', 'vertical_id', 'responder', 'status'));
    $survey_templates = Survey_Template::search($search);

    return JSend::success("Survey Templates", ['templates' => $survey_templates]);
});

Route::get('/survey_templates/{survey_template_id}', function ($survey_template_id) {
    $survey_template = (new Survey_Template)->fetch($survey_template_id);

    return JSend::success("Survey Template", ['templates' => $survey_template]);
});

Route::get('/survey_templates/{survey_template_id}/surveys', function ($survey_template_id) {
    $surveys = Survey::search(['survey_template_id' => $survey_template_id]);

    return JSend::success("Surveys in Template $survey_template_id", ['surveys' => $surveys]);
});

Route::get('/survey_templates/{survey_template_id}/questions', function ($survey_template_id, Request $request) {
    $request->merge(['survey_template_id' => $survey_template_id]);
    $questions = Survey_Question::search($request->all());

    return JSend::success("Questions in Template: $survey_template_id", ['questions' => $questions]);
});

Route::get('/survey_templates/{survey_template_id}/categorized_questions', function ($survey_template_id) {
    $questions = (new Survey_Question)->inCategorizedFormat($survey_template_id);

    return JSend::success("Questions in Template: $survey_template_id", ['questions' => $questions]);
});

Route::get('/survey_templates/{survey_template_id}/questions/{survey_question_id}', function ($survey_template_id, $survey_question_id) {
    $question = (new Survey_Question)->fetch($survey_question_id);

    return JSend::success("Question ID : $survey_question_id", ['questions' => $question]);
});
Route::get('/survey_templates/{survey_template_id}/questions/{survey_question_id}/choices', function ($survey_template_id, $survey_question_id) {
    $choices = Survey_Choice::inQuestion($survey_question_id);

    return JSend::success("Choices for question ID : $survey_question_id", ['choices' => $choices]);
});

Route::get('/survey_templates/{survey_template_id}/questions/{survey_question_id}/choices/{survey_choice_id}', function ($survey_template_id, $survey_question_id, $survey_choice_id) {
    $choice = (new Survey_Choice)->fetch($survey_choice_id);

    return JSend::success("Survey Choice ID : $survey_choice_id", ['choices' => $choice]);
});

Route::get('/surveys', function (Request $request) {
    $search = array_filter($request->only('id', 'name', 'vertical_id', 'responder', 'survey_template_id'));
    $surveys = Survey::search($search);
    return JSend::success("Survey", ['surveys' => $surveys]);
});

Route::post('/surveys', function (Request $request) {
    $survey_model = new Survey;
    $survey = $survey_model->add($request->survey_template_id, $request->name, $request->added_by_user_id);

    if (!$survey) {
        return JSend::error("Error creating survey instance", $survey_model->errors);
    }
    
    return JSend::success("Survey Instance Created", ['surveys' => $survey]);
});

Route::get('/surveys/{survey_id}', function ($survey_id) {
    $survey = (new Survey)->fetch($survey_id);

    return JSend::success("Survey Template", ['surveys' => $survey]);
});

Route::get('/surveys/{survey_id}/questions/{question_id}/responses', function ($survey_id, $question_id) {
    $responses = Survey_Response::search(['survey_id' => $survey_id, 'question_id' => $question_id]);

    return JSend::success("Responses for question ID: $question_id", ['responses' => $responses]);
});

Route::get('/surveys/{survey_id}/questions/{question_id}/responses/{response_id}', function ($survey_id, $question_id, $response_id) {
    $response = (new Survey_Response)->fetch($response_id);

    return JSend::success("Response ID: $response_id", ['responses' => $response]);
});

Route::get('/surveys/{survey_id}/responses', function ($survey_id) {
    $responses = Survey_Response::inSurvey($survey_id);

    return JSend::success("Responses for Survey ID: $survey_id", ['responses' => $responses]);
});
