<?php
namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use JWTAuth;
use Exception;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use Illuminate\Support\Facades\Auth;

// Combining both BasicAuth.php and JwtMiddleware.php Middlewares together. Either auth format is supported.

class BasicOrJwtAuth
{
    public function handle($request, Closure $next)
    {
        $username = $request->getUser();
        $password = $request->getPassword();

        $auth_details = false;
        if ($username and $password) {
            $user = new User;
            $user->enable_logging = false;
            $auth_details = $user->login($username, $password);

            if (!$auth_details) {
                $headers = array('WWW-Authenticate' => 'Basic');
                return response(['status' => "fail", "data" => ["You need a login provided by the Make a Difference Tech Team to acccess this API."]], 401, $headers);
            }
            $groups = json_decode(json_encode($auth_details->groups), true);
            $group_ids = array_column($groups, 'id');
            define('DEVELOPER_GROUP_ID', 388);

            if (!in_array(DEVELOPER_GROUP_ID, $group_ids)) {
                return response(
                    [
                    'status'=> "fail", 
                    'data'  => ["Your auth account does not have the Developer user group. Contact the tech team to add this to your account."]
                ], 401);
            }

        } else { // Basic authentication not present. Try JWT 
            try {
                // dump(JWTAuth::parseToken()); exit;
                $auth_details = JWTAuth::parseToken()->authenticate();
            } catch (Exception $e) {
                if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                    return response(['status' => 'fail', 'data' => ['Token is Invalid']], 401);
                } else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                    return response(['status' => 'fail', 'data' => ['Token is Expired']], 401);
                // } else {
                //     return response(['status' => 'fail', 'data' => ['Authorization Token not found in request']], 401);
                }
            }
        }

        if (!$auth_details) {
            $headers = array('WWW-Authenticate' => 'Basic');
            return response(['status' => "fail", "data" => ["You need a login provided by the Make a Difference Tech Team to acccess this API."]], 401, $headers);
        }

        Auth::login($auth_details);

        return $next($request);
    }
}
