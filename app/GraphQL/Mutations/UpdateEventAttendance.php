<?php
namespace App\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Models\Event;

// :TODO:
// Disabled for now. Add this to the mutation part of schema for this to be enabled...
// updateEventAttendance(event_id: ID!, user_ids:[ID]!): Int

class updateEventAttendance
{
    public function resolve($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $event_id = $args['event_id'];
        $user_ids = $args['user_ids'];
        $event_model = new Event;

        if (!is_array($user_ids)) {
            $user_ids = [$user_ids];
        }

        $event = $event_model->find($event_id);
        if (!$event) {
            return 0;
        } // No event with given id
        
        $event->updateAttendance([$user_ids, $event_id]);
        $count = count($user_ids);

        return $count;
    }
}