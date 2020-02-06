<?php
namespace App\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
// use App\Models\Allocation;
// use App\Models\Batch;
use App\Http\Controllers\BatchController;
use Illuminate\Http\Request;
use App\Exceptions\GraphQLException;

class assignBatchTeachers
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
        $batch_id = $args['batch_id'];
        $level_id = $args['level_id'];
        $subject_id = isset($args['subject_id']) ? $args['subject_id'] : 0;
        $teacher_ids = $args['teacher_ids'];
        $batch = new BatchController;
        $request_dummy = new Request;
        $response = $batch->assignTeachers($request_dummy, $batch_id, $level_id, $subject_id, $teacher_ids);

        // Controller returns JSON Response - parse it, read the json to see if the call succeded.
        list($headers, $content) = explode("\r\n\r\n", $response, 2);
        $data = json_decode($content);

        if($data->status == "success") return 1;
        else {
            // Shows validation error.
            throw new GraphQLException($data->status, $data->{$data->status});
            return 0;
        }
    }
}
