<?php
namespace App\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
// use App\Models\Allocation;
use App\Models\Level;
use App\Exceptions\GraphQLException;

class assignLevelStudents
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
        $level_id = $args['level_id'];
        $student_ids = $args['student_ids'];
        $level = new Level;

        if (!is_array($student_ids)) {
            $student_ids = [$student_ids];
        }

        $insert_count = 0;
        foreach ($student_ids as $student_id) {
            $response = $level->assignStudent($level_id, $student_id);
            if ($response) {
                $insert_count++;
            }
        }

        return "Assigned " . $insert_count . " of " . count($student_ids) . " students to the given class section";
    }
}
