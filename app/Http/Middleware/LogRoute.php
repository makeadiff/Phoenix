<?php

namespace App\Http\Middleware;

use App\Models\Log;
use Closure;

class LogRoute
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
        $response = $next($request);

        $status = $response->getStatusCode();
        $log = [
            'URI' => $request->getUri(),
            'METHOD' => $request->getMethod(),
            // 'REQUEST_BODY' => $request->all(), // :TODO: If this is being saved, make sure there is no password/other sensitive information in it.
            'STATUS' => $status,
            // 'RESPONSE' => $response->getContent()
        ];

        if($status >= 500) {
            $level = 'critical';
        } elseif($status >= 400) {
            $level = 'error';
        }

        $level = 'info';

        $log_model = new Log();
        $log_model->add([
            'name'=> 'api_call',
            'log' => json_encode($log),
            'level'=>$level,
        ]);

        return $response;
    }
}
