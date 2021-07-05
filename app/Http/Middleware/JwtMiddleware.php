<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;
use Exception;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class JwtMiddleware extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            // dump(JWTAuth::parseToken()); exit;
            $user = JWTAuth::parseToken()->authenticate();
        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                return response(['status' => 'fail', 'data' => ['Token is Invalid']], 401);
            } else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                return response(['status' => 'fail', 'data' => ['Token is Expired']], 401);
            } else {
                return response(['status' => 'fail', 'data' => ['Authorization Token not found in request']], 401);
            }
        }
        return $next($request);
    }
}
