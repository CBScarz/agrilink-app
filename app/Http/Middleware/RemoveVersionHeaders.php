<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RemoveVersionHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Remove version headers to hide framework information
        if ($response->headers->has('X-Powered-By')) {
            $response->headers->remove('X-Powered-By');
        }
        if ($response->headers->has('Server')) {
            $response->headers->remove('Server');
        }
        if ($response->headers->has('X-Framework')) {
            $response->headers->remove('X-Framework');
        }

        return $response;
    }
}
