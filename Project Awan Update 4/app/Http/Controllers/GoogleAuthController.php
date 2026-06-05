<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Alihkan request user ke halaman login Google.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Tangani callback data dari Google OAuth.
     */
    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors([
                'email' => 'Gagal melakukan autentikasi menggunakan Google. Silakan coba lagi.',
            ]);
        }

        // Cari user berdasarkan google_id atau email
        $user = User::where('google_id', $googleUser->getId())->first();

        if ($user) {
            // Jika user dengan google_id sudah terdaftar, langsung login-kan
            Auth::login($user);
        } else {
            // Jika google_id belum terdaftar, cari berdasarkan email
            $existingUser = User::where('email', $googleUser->getEmail())->first();

            if ($existingUser) {
                // Hubungkan google_id ke akun email yang sudah terdaftar sebelumnya
                $existingUser->update([
                    'google_id' => $googleUser->getId()
                ]);
                Auth::login($existingUser);
            } else {
                // Buat user baru jika email & google_id belum terdaftar
                $newUser = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'password' => Hash::make(Str::random(16)) // password acak yang aman
                ]);
                Auth::login($newUser);
            }
        }

        // Regenerasi session demi keamanan multi-server/load balancer
        $request->session()->regenerate();

        // Redirect mutlak ke halaman utama (Home) setelah berhasil login
        return redirect()->route('home');
    }
}
