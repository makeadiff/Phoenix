<?php
namespace App\GraphQL\Queries;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Models\User;

class verifyOtp
{
    public function resolve($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $user_model = new User;
        $matched_users = $user_model->search($args);
        $user = $matched_users->first();

        // We are making the OTP from the users ID, Email, Phone and today's date(will automatically 'expire' at end of day).
        //  This will avoid a DB write/adding DB columns. BUT. It will make it PREDICTABLE.
        //  Right now, I'm condisdering it decent trade off. Later might have to update it.
        $md5 = md5($user->id . $user->email . $user->phone . date("Y-m-d"));
        $md5_otp = substr($md5, -4); // Last 4 chars of the MD5 will be the OTP.

        if (!empty($args['otp']) and $args['otp'] === $md5_otp) {
            return 1;
        }
        return 0;
    }
}
