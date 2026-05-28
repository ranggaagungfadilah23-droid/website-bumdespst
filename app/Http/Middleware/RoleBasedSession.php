<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleBasedSession
{
    public function handle(Request $request, Closure $next)
    {
        $path = $request->path();

        if (str_starts_with($path, 'admin')) {
            config(['session.cookie' => 'session_admin']);
        } elseif (str_starts_with($path, 'kepala-bumdes')) {
            config(['session.cookie' => 'session_kb']);
        } elseif (str_starts_with($path, 'mitra')) {
            config(['session.cookie' => 'session_mitra']);
        } elseif (str_starts_with($path, 'customer')) {
            config(['session.cookie' => 'session_customer']);
        }

        return $next($request);
    }
}
