<?php
namespace App\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Models\Classes;
use App\Models\Student;

class saveStudentAttendance
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
        $class_id = $args['class_id'];
        $class_model = new Classes;

        $class = $class_model->find($class_id);
        if(!$class) return 0; // No class with given id

        foreach ($args['students'] as $student) {
            $class_model->saveStudentAttendance($class_id, $student['student_id'], $student);
        }

        return 1;
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