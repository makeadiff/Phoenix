<?php
namespace App\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Models\Event;
use Illuminate\Http\Request;

// :TODO:
// Disabled for now. Add this to the mutation part of schema for this to be enabled...
// createRecurringEvent(event_id: ID!, event_data:[InputEventData]!): Int

class createRecurringEvent
{
    public function resolve($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $event_id = $args['event_id'];
        $event_model = new Event;

        $event = $event_model->find($event_id);
        if (!$event) {
            return 0;
        } // No event with given id
        $frequency = $event->frequency = $args['frequency'];
        $repeat_until= $event->repeat_until=$args['repeat_until'];

        $recurring = $event->createRecurringInstances($event,$frequency,$repeat_until);
        $event->createRecurringEvent($event_id, $args['event_data']);
        $count = count($recurring);
        return $count;
    }
}

