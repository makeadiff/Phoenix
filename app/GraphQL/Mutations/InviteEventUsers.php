<?php
namespace App\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Models\Event;

class inviteEventUsers
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
        $user_ids = $args['user_ids'];
        $send_invites = $args['send_invites']; //True or False
        $event_model = new Event;

        $event = $event_model->find($event_id);
        if (!$event) {
            return 0;
        } // No event with given id

        $event->invite($user_ids, $send_invites, $event_id);
        $count = count($user_ids);

        return $count;
    }
}