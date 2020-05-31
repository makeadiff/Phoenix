<?php
namespace App\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Models\Classes;

class cancelClass
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
        $mentor_id = isset($args['mentor_id']) ? $args['mentor_id'] : 0;
        $reason = isset($args['reason']) ? $args['reason'] : "";
        $reason_other = isset($args['reason_other']) ? $args['reason_other'] : "";

        $class = $class_model->find($class_id);
        if (!$class) {
            return 0;
        } // No class with given id

        $class->cancel($class_id, $reason, $reason_other, $mentor_id);

        return 1;
    }
}

/**
 * Sample call...
mutation {
    cancelClass(class_id: 431153, reason: IN_VOLUNTEER_UNAVAILABLE, mentor_id: 1)
}
 */
