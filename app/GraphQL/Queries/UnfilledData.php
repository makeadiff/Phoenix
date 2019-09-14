<?php
namespace App\GraphQL\Queries;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Models\Batch;
use App\Models\Classes;

class unfilledData
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
        if(isset($args['mentor_id'])) {
            $batch_model = new Batch;
            $class_model = new Classes;

            $unfilled_batch = $batch_model->search(['mentor_id' => $args['mentor_id'], 'class_status' => 'projected']);
            foreach ($unfilled_batch as $batch) {
                $classes = $class_model->search(['batch_id' => $batch->id, 'class_status' => 'projected'])->get();

                // NOW bunch up the classes by date, return only unique classes.  :TODO:
                dump($classes);
            }
        }

        return [];
    }
}
