<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\ActivityLog;
use App\Models\UserSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
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

        $user  = Auth::user();
        $role  = trim($user->role);

        // Ambil browser_token dari cookie, atau buat baru
        $browserToken = $request->cookie('browser_token') ?? Str::random(40);

        try {
            UserSession::updateOrCreate(
                ['browser_token' => $browserToken, 'user_id' => $user->id],
                ['session_id'    => session()->getId()]
            );
        } catch (\Exception $e) {
            \Log::error('UserSession store failed: ' . $e->getMessage());
        }

        ActivityLog::create([
            'user_name' => $user->name,
            'action'    => 'Login',
            'details'   => $user->name . ' (' . $role . ') login ke sistem',
        ]);

        $redirect = match(true) {
            $role === 'admin'         => redirect()->route('admin.dashboard'),
            $role === 'kepala-bumdes' => redirect()->route('kepala-bumdes.dashboard'),
            $role === 'mitra' && $user->mitra?->status === 'aktif' => redirect()->route('mitra.dashboard'),
            $role === 'mitra'         => redirect()->route('mitra.menunggu'),
            $role === 'customer'      => redirect()->route('customer.dashboard'),
            default                   => redirect()->intended('/'),
        };

        return $redirect->withCookie(
            cookie('browser_token', $browserToken, 60 * 24 * 365, '/', null, true, false, false, 'lax')
        );
    }

    public function switchAccount(Request $request, $userId): RedirectResponse
    {
        $browserToken = $request->cookie('browser_token');

        $userSession = UserSession::where('browser_token', $browserToken)
            ->where('user_id', $userId)
            ->first();

        if (!$userSession) {
            return redirect()->route('login')->with('error', 'Akun tidak ditemukan.');
        }

        $user = \App\Models\User::find($userId);
        if (!$user) {
            return redirect()->route('login')->with('error', 'User tidak ditemukan.');
        }

        Auth::guard('web')->logout();
        $request->session()->flush();

        Auth::login($user);
        $request->session()->regenerate(true);

        $userSession->update(['session_id' => session()->getId()]);

        $role = trim($user->role);

        ActivityLog::create([
            'user_name' => $user->name,
            'action'    => 'Switch Akun',
            'details'   => 'Beralih ke akun: ' . $user->name . ' (' . $role . ')',
        ]);

        $redirect = match(true) {
            $role === 'admin'         => redirect()->route('admin.dashboard'),
            $role === 'kepala-bumdes' => redirect()->route('kepala-bumdes.dashboard'),
            $role === 'mitra' && $user->mitra?->status === 'aktif' => redirect()->route('mitra.dashboard'),
            $role === 'mitra'         => redirect()->route('mitra.menunggu'),
            default                   => redirect()->route('customer.dashboard'),
        };

        return $redirect->withCookie(
            cookie('browser_token', $browserToken, 60 * 24 * 365, '/', null, true, false, false, 'lax')
        );
    }

    public function destroy(Request $request): RedirectResponse
    {
        $user         = Auth::user();
        $browserToken = $request->cookie('browser_token');

        if ($user) {
            ActivityLog::create([
                'user_name' => $user->name,
                'action'    => 'Logout',
                'details'   => $user->name . ' logout dari sistem',
            ]);

            UserSession::where('browser_token', $browserToken)
                ->where('user_id', $user->id)
                ->delete();
        }

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
