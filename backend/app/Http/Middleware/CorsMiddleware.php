<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CorsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /*$response = $next($request);

        //Allowed origins
        $response->header('Access-Control-Allow-Origin', '*');

        //Allowed http methods
        $response->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE');

        //Allowed headers
        $response->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');

        return $response;*/
        return $next($request)
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods','GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
    }
}
