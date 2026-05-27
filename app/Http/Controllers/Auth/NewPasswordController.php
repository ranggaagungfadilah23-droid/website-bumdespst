<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    /**
     * Tampilkan form reset password.
     * Jika dari profil (user login) → redirect ke profil dengan token.
     * Jika dari forgot password (guest) → tampilkan halaman reset.
     */
    public function create(Request $request)
    {
        // Jika user sudah login → arahkan ke profil
        if (auth()->check()) {
            return redirect()->route('profile.edit', [
                'token'    => $request->route('token'),
                'email'    => $request->email,
                'is_reset' => 1,
            ]);
        }

        // Jika guest (dari forgot password) → tampilkan form reset
        return view('auth.reset-password', ['request' => $request]);
    }

    /**
     * Proses reset password.
     */
   public function store(Request $request): RedirectResponse
{
    $request->validate([
        'token'    => ['required'],
        'email'    => ['required', 'email'],
        'password' => ['required', 'confirmed', Rules\Password::defaults()],
    ]);

    // ✅ Cek apakah password baru sama dengan password lama
    $user = \App\Models\User::where('email', $request->email)->first();
    
    if ($user && Hash::check($request->password, $user->password)) {
        return back()
            ->withInput($request->only('email'))
            ->withErrors(['password' => 'Kata sandi baru tidak boleh sama dengan kata sandi sebelumnya.']);
    }

    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function (User $user) use ($request) {
            $user->forceFill([
                'password'       => Hash::make($request->password),
                'remember_token' => Str::random(60),
            ])->save();

            event(new PasswordReset($user));
        }
    );

    return $status == Password::PASSWORD_RESET
        ? redirect()->route('login')->with('status', __($status))
        : back()->withInput($request->only('email'))
            ->withErrors(['email' => __($status)]);
}
}