<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Validation\Rules\Password;

class LoginController extends Controller
{
    public function redirectToGoogle() {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback() {
        try{
            $googleUser = Socialite::driver('google')->user();

            $user = User::where('google_id', $googleUser->id)->first();

            if(!$user) {
                $user = User::where('email', $googleUser->email)->first();

                if(!$user) {
                    $user = User::create([
                        'name' => $googleUser->name,
                        'email' => $googleUser->email,
                        'google_id' => $googleUser->id,
                        'password' => null, 
                        'role' => 'user'
                    ]);
                } else {
                    $user->google_id = $googleUser->id;
                    $user->save();
                }
            }
            Auth::login($user, true);
            return $this->redirectBasedOnRole();

        } catch (\Exception $e) {
            return redirect('/')->with('error', 'Gagal login menggunakan Google.');
        }
    }

    public function authenticate(Request $request) {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        // Cek jika user terdaftar melalui Google dan tidak punya password lokal
        if ($user && empty($user->password)) {
            return back()->withErrors([
                'email' => 'Akun ini terdaftar melalui Google. Silakan masuk menggunakan tombol "Masuk dengan Google".'
            ])->onlyInput('email');
        }

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            return $this->redirectBasedOnRole();
        }

        return back()->withErrors([
            'email' => 'Alamat email atau kata sandi salah.'
        ])->onlyInput('email');
    }

    protected function redirectBasedOnRole() {
        $user = Auth::user();
        if ($user->role === 'admin') {
            return redirect()->intended('/dashboard');
        }
        return redirect()->intended('/dashboard');
    }

    public function updatePassword(Request $request) {
        $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)],
        ],[
            'password.required' => 'Please enter your password',
            'password.min' => 'Use at least 8 characters for your password.',
            'password.confirmed' => 'Passwords do not match.'
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->password = bcrypt($request->password);
        $user->save();

        return redirect()->back()->with('success', 'Kata sandi berhasil diubah.');
    }

    public function logout(Request $request){
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('status', 'Anda telah berhasil logout.');
    }
}