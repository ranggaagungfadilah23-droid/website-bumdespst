<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RestrictDemoAccount
{
    // Route yang tetap boleh diakses akun demo meski method-nya bukan GET
    protected $excludedRoutes = [
        'logout',
    ];

    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        $routeName = $request->route()?->getName();

        if ($user && $user->is_demo
            && in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])
            && !in_array($routeName, $this->excludedRoutes)
        ) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Akun demo hanya bisa melihat data.'], 403);
            }
            return redirect()->back()->with('error', 'Akun demo hanya bisa melihat data (read-only), tidak diizinkan menambah/mengubah/menghapus.');
        }

        return $next($request);
    }
}