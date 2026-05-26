<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Mitra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Hash, Auth, Log, DB};
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Auth\Events\Registered;
use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;

class GoogleController extends Controller
{
    /**
     * Mengarahkan ke Google Login dengan Pengaman Ganda
     */
    public function redirectToGoogle(Request $request)
    {
        $role = $request->role ?? 'customer';

        // 1. Simpan ke Session & Paksa Tulis ke Memori saat ini juga
        session()->put('register_role', $role);
        session()->save();

        // 2. Backup menggunakan Cookie (Bertahan 10 menit) agar tidak hilang saat redirect Ngrok
        $cookie = cookie('register_role', $role, 10);

        return Socialite::driver('google')->redirect()->withCookie($cookie);
    }

    /**
     * Handle respon kembalian dari Google
     */
    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            $existingUser = User::where('email', $googleUser->getEmail())->first();

            if ($existingUser) {
                // --- JIKA USER SUDAH PERNAH DAFTAR ---
                Auth::login($existingUser);

                session()->forget('register_role');
                $hapusCookie = cookie()->forget('register_role');

                $role = trim($existingUser->role);

                if ($role === 'admin') {
                    return redirect()->route('admin.dashboard')->withCookie($hapusCookie);
                } elseif ($role === 'kepala-bumdes') {
                    return redirect()->route('kepala-bumdes.dashboard')->withCookie($hapusCookie);
                } elseif ($role === 'mitra') {
                    if ($existingUser->status === 'aktif') {
                        return redirect()->route('mitra.dashboard')->withCookie($hapusCookie);
                    } else {
                        return redirect()->route('mitra.menunggu')->withCookie($hapusCookie);
                    }
                } elseif ($role === 'customer') {
                    return redirect()->route('customer.dashboard')->withCookie($hapusCookie);
                }
                return redirect()->route('index')->withCookie($hapusCookie);

            } else {
                // --- JIKA USER BARU (PROSES REGISTRASI) ---

                // Cek dari Session dulu, jika kosong cek dari Cookie, jika masih kosong default 'customer'
                $role = session('register_role') ?? $request->cookie('register_role') ?? 'customer';

                // Bersihkan penanda agar tidak nyangkut untuk registrasi berikutnya
                session()->forget('register_role');
                $hapusCookie = cookie()->forget('register_role');

                // Arahkan ke form yang tepat berdasarkan peran (role) yang diingat
                if ($role === 'mitra') {
                    return redirect()->route('register.mitra')->with([
                        'google_email' => $googleUser->getEmail(),
                        'google_name'  => $googleUser->getName(),
                        'google_id'    => $googleUser->getId()
                    ])->withCookie($hapusCookie);
                } else {
                    return redirect()->route('register.pelanggan')->with([
                        'google_email' => $googleUser->getEmail(),
                        'google_name'  => $googleUser->getName(),
                        'google_id'    => $googleUser->getId()
                    ])->withCookie($hapusCookie);
                }
            }
        } catch (\Exception $e) {
            Log::error('Google Login Error: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Gagal login Google.');
        }
    }

    /**
     * Store Data Registrasi Mitra
     */
    public function storeMitra(Request $request)
{
    $request->validate([
        'email'       => 'required|email|unique:users',
        'password'    => 'required|min:8|confirmed',
        'sku'         => 'required|file|mimes:jpg,png,pdf|max:5120',
        'syarat'      => 'accepted',
        'jenis_usaha' => 'required|in:Jasa,Produk',
        'no_hp'       => 'required'
    ]);

    return DB::transaction(function () use ($request) {

        // Upload SKU ke Cloudinary
        $skuUrl = null;
        if ($request->hasFile('sku')) {
            $cloudinary = new Cloudinary(
                Configuration::instance([
                    'cloud' => [
                        'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                        'api_key'    => env('CLOUDINARY_API_KEY'),
                        'api_secret' => env('CLOUDINARY_API_SECRET'),
                    ],
                    'url' => ['secure' => true]
                ])
            );
          $result = $cloudinary->uploadApi()->upload(
    $request->file('sku')->getRealPath(),
    [
        'resource_type' => 'raw', // ← ganti ini
        'folder'        => 'dokumen_mitra'
    ]
);
            $skuUrl = $result['secure_url'];
        }

        $user = User::create([
            'name'     => $request->nama_pemilik,
            'email'    => $request->email,
            'no_hp'    => $request->no_hp,
            'password' => Hash::make($request->password),
            'role'     => 'mitra',
            'status'   => 'pending',
        ]);

        Mitra::create([
            'user_id'      => $user->id,
            'nama_usaha'   => $request->nama_usaha,
            'nama_pemilik' => $request->nama_pemilik,
            'jenis_usaha'  => $request->jenis_usaha,
            'nik'          => $request->nik,
            'alamat_usaha' => $request->alamat_usaha,
            'no_hp'        => $request->no_hp,
            'dusun'        => $request->dusun,
            'sku'          => $skuUrl, // ← sekarang URL Cloudinary
            'status'       => 'pending',
        ]);

        event(new Registered($user));
        Auth::login($user);

        return redirect()->route('mitra.menunggu')->with('success', 'Pendaftaran berhasil! Silakan verifikasi email Anda.');
    });
}
}
