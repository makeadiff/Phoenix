<?php
namespace App\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Models\Survey_Response;
use App\Models\Survey;

class saveSurveyResponses
{
    /**
     * Return a value for the field.
     *
     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  mixed[]  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     */
    public function resolve($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $survey_model = new Survey;
        $survey = $survey_model->find($args['survey_id']);
        if (!$survey) {
            return 0;
        } // No survey with given id

        $survey_response_model = new Survey_Response;
        $insert_count = 0;

        foreach ($args['responses'] as $response) {
            $data = $response;

            if (empty($data['survey_id']) and !empty($args['survey_id'])) {
                $data['survey_id'] = $args['survey_id'];
            }
            if (empty($data['responder_id']) and !empty($args['responder_id'])) {
                $data['responder_id'] = $args['responder_id'];
            }
            if (empty($data['added_by_user_id']) and !empty($args['added_by_user_id'])) {
                $data['added_by_user_id'] = $args['added_by_user_id'];
            }

            $data['survey_question_id'] = $data['question_id'];
            $data['survey_choice_id'] = $data['choice_id'];
            
            $return = $survey_response_model->add($data, $args['survey_id']);
            if (!$return) {
                dump($survey_response_model->errors);
            } else {
                $insert_count ++ ;
            }
        }

        return $insert_count;
    }
}

/*
 * Sample call
mutation {
    saveStudentAttendance(class_id:429626, students: [{
        student_id: 11450,
        participation: 5,
        check_for_understanding: 4
    }, {
        student_id: 16966,
        participation: 0,
        check_for_understanding: 0
    }])
}
 */
