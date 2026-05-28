<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\ActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = Auth::user();
        $role = trim($user->role);

        // ✅ Log login
        ActivityLog::create([
            'user_name' => $user->name,
            'action'    => 'Login',
            'details'   => $user->name . ' (' . $role . ') login ke sistem',
        ]);

        if ($role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($role === 'kepala-bumdes') {
            return redirect()->route('kepala-bumdes.dashboard');
        } elseif ($role === 'mitra') {
            if ($user->mitra && $user->mitra->status === 'aktif') {
                return redirect()->route('mitra.dashboard');
            } else {
                return redirect()->route('mitra.menunggu');
            }
        } elseif ($role === 'customer') {
            return redirect()->route('customer.dashboard');
        } else {
            return redirect()->intended('/');
        }
    }

    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::user();

        // ✅ Log logout
        if ($user) {
            ActivityLog::create([
                'user_name' => $user->name,
                'action'    => 'Logout',
                'details'   => $user->name . ' logout dari sistem',
            ]);
        }

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
