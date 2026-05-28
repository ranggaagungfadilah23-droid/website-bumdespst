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

        $user = Auth::user();
        $role = trim($user->role);

        // Ambil atau buat browser_token (identitas browser ini)
        $browserToken = $request->cookie('browser_token') ?? Str::random(40);

        // Simpan sesi akun ini ke daftar akun aktif
        UserSession::updateOrCreate(
            ['browser_token' => $browserToken, 'user_id' => $user->id],
            ['session_id' => session()->getId()]
        );

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

        return $redirect->withCookie(cookie()->forever('browser_token', $browserToken));
    }

    // Method untuk switch akun
   public function switchAccount(Request $request, $userId): RedirectResponse
{
    $browserToken = $request->cookie('browser_token');

    $userSession = UserSession::where('browser_token', $browserToken)
        ->where('user_id', $userId)
        ->first();

    if (!$userSession) {
        return redirect()->route('login')->with('error', 'Akun tidak ditemukan.');
    }

    Auth::logout();
    $user = \App\Models\User::find($userId);
    Auth::login($user);
    $request->session()->regenerate();

    // Update session_id yang baru setelah regenerate
    $userSession->update(['session_id' => session()->getId()]);

    $role = trim($user->role);

    $redirect = match(true) {
        $role === 'admin'         => redirect()->route('admin.dashboard'),
        $role === 'kepala-bumdes' => redirect()->route('kepala-bumdes.dashboard'),
        $role === 'mitra' && $user->mitra?->status === 'aktif' => redirect()->route('mitra.dashboard'),
        $role === 'mitra'         => redirect()->route('mitra.menunggu'),
        default                   => redirect()->route('customer.dashboard'),
    };

    // ← Kembalikan cookie agar tidak hilang
    return $redirect->withCookie(cookie()->forever('browser_token', $browserToken));
}

    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $browserToken = $request->cookie('browser_token');

        if ($user) {
            ActivityLog::create([
                'user_name' => $user->name,
                'action'    => 'Logout',
                'details'   => $user->name . ' logout dari sistem',
            ]);

            // Hapus hanya sesi akun ini dari daftar
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
