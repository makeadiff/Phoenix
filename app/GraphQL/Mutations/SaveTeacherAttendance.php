<?php
namespace App\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Models\Classes;
use App\Models\User;

class saveTeacherAttendance
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

        foreach ($args['teachers'] as $teacher) {
            $class_model->saveTeacherAttendance($class_id, $teacher['user_id'], $teacher);
        }

        return 1;
    }
}

/**
 * Sample call...
mutation {
    saveTeacherAttendance(class_id: 431153, teachers: [{
      user_id: 1,
      substitute_id: 0,
      zero_hour_attendance: "1",
      status: "attended"
    }, {
      user_id: 117733,
      substitute_id: 0,
      zero_hour_attendance: "0",
      status: "attended"
    }])
}
 */