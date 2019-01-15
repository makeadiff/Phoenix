<?php
use App\Models\Survey;
use App\Models\Survey_Template;
use App\Models\Survey_Question;
use App\Models\Survey_Choice;
use App\Models\Survey_Response;

use App\Http\Controllers\SurveyController;
use Illuminate\Http\Request;

/// Surveys Routes / API Calls...

$app->get('/survey_templates', function(Request $request) use ($app) {
	$search = array_filter($request->only('id', 'name', 'vertical_id', 'responder', 'status'));
	$survey_templates = Survey_Template::search($search);

	return JSend::success("Survey Templates", $survey_templates);
});

$app->get('/survey_templates/{survey_template_id}', function($survey_template_id) use ($app) {
	$survey_template = (new Survey_Template)->fetch($survey_template_id);

	return JSend::success("Survey Template", $survey_template);
});

$app->get('/survey_templates/{survey_template_id}/surveys', function($survey_template_id) use ($app) {
	$surveys = Survey::search(['survey_template_id' => $survey_template_id]);

	return JSend::success("Surveys in Template $survey_template_id", $surveys);
});

$app->get('/survey_templates/{survey_template_id}/questions', function($survey_template_id, Request $request) use ($app) {
	$request->merge(['survey_template_id' => $survey_template_id]);
	$questions = Survey_Question::search($request->all());

	return JSend::success("Questions in Template: $survey_template_id", $questions);
});

$app->get('/survey_templates/{survey_template_id}/categorized_questions', function($survey_template_id) use ($app) {
	$questions = (new Survey_Question)->inCategorizedFormat($survey_template_id);

	return JSend::success("Questions in Template: $survey_template_id", $questions);
});

$app->get('/survey_templates/{survey_template_id}/questions/{question_id}', function($survey_template_id, $question_id) use ($app) {
	$question = (new Survey_Question)->fetch($question_id);

	return JSend::success("Question ID : $question_id", $question);
});
$app->get('/survey_templates/{survey_template_id}/questions/{question_id}/choices', function($survey_template_id, $question_id) use ($app) {
	$choices = Survey_Choice::inQuestion($question_id);

	return JSend::success("Choices for question ID : $question_id", $choices);
});

$app->get('/survey_templates/{survey_template_id}/questions/{question_id}/choices/{choice_id}', function($survey_template_id, $question_id, $choice_id) use ($app) {
	$choice = (new Survey_Choice)->fetch($choice_id);

	return JSend::success("Survey Choice ID : $choice_id", $choice);
});

$app->get('/surveys', function(Request $request) use ($app) {
	$search = array_filter($request->only('id', 'name', 'vertical_id', 'responder', 'survey_template_id'));

	$surveys = Survey::search($search);

	return JSend::success("Survey", $surveys);
});

$app->get('/surveys/{survey_id}', function($survey_id) use ($app) {
	$survey = (new Survey)->fetch($survey_id);

	return JSend::success("Survey Template", $survey);
});

$app->get('/surveys/{survey_id}/questions/{question_id}/responses', function($survey_id, $question_id) use ($app) {
	$responses = Survey_Response::search(['survey_id' => $survey_id, 'question_id' => $question_id]);

	return JSend::success("Responses for question ID: $question_id", $responses);
});
$app->get('/surveys/{survey_id}/questions/{question_id}/responses/{response_id}', function($survey_id, $question_id, $response_id) use ($app) {
	$response = (new Survey_Response)->fetch($response_id);

	return JSend::success("Response ID: $response_id", $response);
});
$app->get('/surveys/{survey_id}/responses', function($survey_id) use ($app) {
	$responses = Survey_Response::inSurvey($survey_id);

	return JSend::success("Responses for Survey ID: $survey_id", $responses);
});



/*
	GET /survey_templates
	POST /survey_templates
	GET /survey_templates/{survey_template_id}
POST /survey_templates/{survey_template_id}
DELETE /survey_templates/{survey_template_id}
	GET /survey_templates/{survey_template_id}/surveys - Alias

	GET /surveys
		?survey_template_id
	GET /surveys/{survey_id}
POST /surveys/{survey_id}

	GET /survey_templates/{survey_template_id}/questions
	GET /survey_templates/{survey_template_id}/categorized_questions - Returns Questions using the category format. 
	GET /survey_templates/{survey_template_id}/questions/{question_id}/choices
	POST /survey_templates/{survey_template_id}/questions/{question_id}/choices
	GET /survey_templates/{survey_template_id}/questions/{question_id}/choices/{choice_id}
POST /survey_templates/{survey_template_id}/questions/{question_id}/choices/{choice_id}
	POST /survey_templates/{survey_template_id}/questions - Create new question
POST /survey_templates/{survey_template_id}/questions/{question_id} - Edit Existing Question
	GET /survey_templates/{survey_template_id}/questions/{question_id}

	GET /surveys/{survey_id}/questions - alias
	GET /surveys/{survey_id}/categorized_questions - Returns Questions using the category format  - alias

	GET /surveys/{survey_id}/responses
	POST /surveys/{survey_id}/responses
		?question_id,responder_id
	GET /surveys/{survey_id}/questions/{question_id}/responses
	POST /surveys/{survey_id}/questions/{question_id}/responses


INSERT INTO Survey_Question(id,question,survey_template_id,response_type,sort_order,status,required) SELECT id,question,survey_id,type,sort_order,status,'1' as requried FROM SurveyQuestion 

INSERT INTO Survey_Response(id,survey_id,responder_id,survey_question_id,survey_choice_id,response,added_on,added_by_user_id)
	SELECT id,'1' AS survey_id,user_id, question_id,answer_id,answer,added_on,user_id FROM SurveyResponse
 */
