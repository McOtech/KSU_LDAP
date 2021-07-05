<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAuthSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$request->session()->has('user')) {
            auth()->user()->tokens()->delete();
            $request->session()->flush();
            return response()->json(['message' => 'You are logged out. Please log in to proceed'], 401);
        }
        return $next($request);
    }
}
