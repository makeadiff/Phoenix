<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use App\Models\User;

class BasicAuth
{
    public function handle($request, Closure $next)
    {
    	$username = $request->getUser();
    	$password = $request->getPassword();

        $user = new User;
    	$info = $user->login($username, $password);

    	if(!$info) {
            $headers = array('WWW-Authenticate' => 'Basic');
            return response('You need a login provided by the Make a Difference Tech Team to acccess this API.', 401, $headers);
        }

        return $next($request);
    }
}
