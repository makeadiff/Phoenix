<?php
namespace App\Http\Middleware;

use Closure;
use App\Models\User;

class BasicAuth
{
    public function handle($request, Closure $next)
    {
        $username = $request->getUser();
        $password = $request->getPassword();

        $info = false;
        if ($username and $password) {
            $user = new User;
            $user->enable_logging = false;
            $info = $user->login($username, $password);
        }
        // dump($username, $password, $user, $info);

        if (!$info) {
            $headers = array('WWW-Authenticate' => 'Basic');
            return response('{"status": "fail", "data": ["You need a login provided by the Make a Difference Tech Team to acccess this API."]}', 401, $headers);
        }
        $groups = json_decode(json_encode($info->groups), true);
        $group_ids = array_column($groups, 'id');
        define('DEVELOPER_GROUP_ID', 388);

        if (!in_array(DEVELOPER_GROUP_ID, $group_ids)) {
            return response('{"status": "fail", "data": ["Your auth account does not have the Developer user group. Contact the tech team to add this to your account."]}', 401);
        }

        return $next($request);
    }
}
