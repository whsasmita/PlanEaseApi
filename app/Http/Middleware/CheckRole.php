<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (! Auth::guard('api')->check()) {
            return response()->json([
                'message' => 'Unauthenticated: Please log in.',
            ], 401); 
        }

        if (Auth::guard('api')->user()->role !== $role) {
            return response()->json([
                'message' => 'Forbidden: You do not have the required role.',
            ], 403); 
        }

        return $next($request);
    }
}
