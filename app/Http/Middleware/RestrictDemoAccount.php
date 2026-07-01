<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RestrictDemoAccount
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if ($user && $user->is_demo && in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Akun demo hanya bisa melihat data.'], 403);
            }
            return redirect()->back()->with('error', 'Akun demo hanya bisa melihat data (read-only), tidak diizinkan menambah/mengubah/menghapus.');
        }

        return $next($request);
    }
}