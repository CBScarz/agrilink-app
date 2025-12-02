<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsBuyer
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || !auth()->user()->isBuyer()) {
            return response()->json([
                'message' => 'Unauthorized. Buyer access required.',
            ], 403);
        }

        return $next($request);
    }
}
