<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ForceJsonResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Only force Accept header to get JSON responses
        if (!$request->expectsJson()) {
            $request->headers->set('Accept', 'application/json');
        }
        
        return $next($request);
    }    
}
