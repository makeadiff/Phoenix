<?php
namespace App\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Models\Event;
use Illuminate\Http\Request;

class createRecurringEvent
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
        $event_id = $args['event_id'];
        $event_model = new Event;
        $request_dummy = new Request;

        $event = $event_model->find($event_id);
        if (!$event) {
            return 0;
        } // No event with given id
        $frequency = $event->frequency = $args['frequency'];
        $repeat_until= $event->repeat_until=$args['repeat_until'];

        
        $recurring = $event->createRecurringInstances($event,$frequency,$repeat_until);
        $event->createRecurringEvent($event_id, $args['event_data']);
        $count =count($recurring);
        return $count;
    }
}

