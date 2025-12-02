<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsFarmer
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || !auth()->user()->isFarmer()) {
            return response()->json([
                'message' => 'Unauthorized. Farmer access required.',
            ], 403);
        }

        return $next($request);
    }
}
