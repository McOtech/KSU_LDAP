<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class AdminAuthorization
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
        $userSession = $request->session()->get('user');
        $user = User::where('id', $userSession['id'])->first();
        if ($user->profile->type != 'admin') {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        return $next($request);
    }
}
