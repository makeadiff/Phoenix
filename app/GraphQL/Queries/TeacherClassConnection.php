<?php
namespace App\GraphQL\Queries;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Models\Batch;
use App\Models\Classes;
use App\Models\Level;
use App\Models\Center;

class teacherClassConnection
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
        if (isset($args['teacher_id'])) {
            $class_model = new Classes;
            $level_model = new Level;
            $batch_model = new Batch;
            $center_model = new Center;

            $classes = $class_model->search(['teacher_id' => $args['teacher_id'], 'status' => 'projected'])->get();
            $return = [];
            foreach ($classes as $cls) {
                if ($cls) {
                    $level = $level_model->find($cls->level_id);
                    $batch = $batch_model->find($cls->batch_id);
                    $class_info = [
                        'batch_id'      => $cls->batch_id,
                        'level_id'      => $cls->level_id,
                        'level'         => $level->name,
                        'day'           => $batch->day,
                        'batch_name'    => $batch->name(),
                        'class_time'    => date('H:i:s', strtotime($cls->class_on)),
                        'class_on'      => $cls->class_on,
                        'class_id'      => $cls->id,
                        'center_id'     => $batch->center_id,
                        'center_name'   => $center_model->find($batch->center_id)->name,
                    ];
                    $return[] = $class_info;
                }
            }

            /*
                "batch_id": "2652",
                "level_id": "7794",
                "level": "8 A",
                "day": "0",
                "class_time": "16:00:00",
                "class_id": "418035",
                "class_on": "2019-09-01 16:00:00",
                "center_id": "184",
                "center_name": "Test Level",
                "batch_name": "Sunday, 4 PM"


                classSearch(teacher_id:1, status: "projected") {
                id
                class_on
                status
                batch {
                  id
                  batch_name
                  day
                  center {
                    id
                    name
                  }
                }
                level {
                  id
                  level_name
                }
              }
             */

            return $return;
        }

        return [];
    }
}
