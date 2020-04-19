<?php
namespace App\GraphQL\Queries;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Models\Batch;
use App\Models\Classes;
use App\Models\Center;

class mentorClassConnection
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
        if (isset($args['mentor_id'])) {
            $batch_model = new Batch;
            $class_model = new Classes;
            $center_model = new Center;

            $users_batch = $batch_model->search(['mentor_id' => $args['mentor_id']]);
            $conected_batches = [];

            foreach ($users_batch as $batch) {
                $cls = $class_model->search(['batch_id' => $batch->id, 'class_date_to' => date('Y-m-d H:i:s')])->last();
                
                if ($cls) {
                    $batch_info = [
                        'batch_id'      => $batch->id,
                        'day'           => $batch->day,
                        'batch_name'    => $batch->name,
                        'class_time'    => date('H:i:s', strtotime($cls->class_on)),
                        'class_on'      => $cls->class_on,
                        'center_id'     => $batch->center_id,
                        'center_name'   => $center_model->find($batch->center_id)->name,
                    ];
                    $conected_batches[] = $batch_info;
                }
            }

            return $conected_batches;
        }

        return [];
    }
}
