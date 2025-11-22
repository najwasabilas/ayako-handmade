<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\User;
use Carbon\Carbon;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function showRegister() {
        if (Auth::check()) {
            return redirect()->route('home');
        }
        return view('auth.register');
    }

    public function register(Request $request) {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);

        $otp = rand(100000, 999999);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'customer',
            'verification_code' => $otp,
            'verification_expires_at' => now()->addMinutes(5),
            'is_verified' => false,
        ]);

        // KIRIM EMAIL OTP
        \Mail::raw("Kode verifikasi Anda adalah: $otp", function ($msg) use ($user) {
            $msg->to($user->email)->subject('Kode Verifikasi Akun Ayako');
        });

        return view('auth.verify-otp', ['email' => $user->email]);
    }


    public function showLogin() {
        if (Auth::check()) {
            return redirect()->route('home');
        }
        return view('auth.login');
    }

    public function login(Request $request) {
        $credentials = $request->validate([
            'email'=>'required|email',
            'password'=>'required'
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard'); 
            }

            return redirect()->route('home');
        }


        return back()->withErrors([
            'email'=>'Email atau password salah',
        ]);
    }

    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
    public function showForgotForm() {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request) {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Email tidak ditemukan.']);
        }

        // Generate token dan simpan di tabel password_resets
        $token = Str::random(64);
        DB::table('password_resets')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => $token,
                'created_at' => Carbon::now()
            ]
        );

        // Kirim email (gunakan mailtrap / smtp aktif di .env)
        \Mail::send('emails.reset-password', ['token' => $token], function($message) use ($request) {
            $message->to($request->email);
            $message->subject('Reset Password Anda');
        });

        return back()->with('status', 'Link reset password telah dikirim ke email Anda.');
    }

    public function showResetForm($token) {
        return view('auth.reset-password', ['token' => $token]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|min:6|confirmed',
            'token' => 'required'
        ]);

        // Ambil data reset berdasarkan token
        $resetData = DB::table('password_resets')
                        ->where('token', $request->token)
                        ->first();

        if (!$resetData) {
            return back()->withErrors(['token' => 'Token reset tidak valid atau sudah digunakan.']);
        }

        // Update password user berdasarkan email di tabel password_resets
        $user = User::where('email', $resetData->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Akun tidak ditemukan.']);
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        // Hapus token setelah digunakan
        DB::table('password_resets')->where('email', $resetData->email)->delete();

        return redirect()->route('login')->with('status', 'Password berhasil diperbarui. Silakan login kembali.');
    }
     public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /** Handle callback dari Google */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors(['google' => 'Gagal login menggunakan Google.']);
        }

        // Cari user berdasarkan email
        $user = User::where('email', $googleUser->getEmail())->first();

        // Jika belum ada, buat akun baru
        if (!$user) {
            $user = User::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'password' => Hash::make(uniqid()), // password acak
                'role' => 'customer',
                'profile_picture' => $googleUser->getAvatar(),
            ]);
        }

        // Login user
        Auth::login($user);

        return redirect()->route('home');
    }
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $code = $request->digit1 . $request->digit2 . $request->digit3 .
                $request->digit4 . $request->digit5 . $request->digit6;

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Akun tidak ditemukan.']);
        }

        if ($user->verification_code !== $code) {
            return back()->withErrors(['otp' => 'Kode salah.']);
        }

        if (now()->greaterThan($user->verification_expires_at)) {
            return back()->withErrors(['otp' => 'Kode sudah kadaluarsa.']);
        }

        $user->update([
            'is_verified' => true,
            'verification_code' => null,
        ]);

        Auth::login($user);

        $user->email_verified_at = now();
        $user->save();

        return redirect()->route('email.verified.success');
    }
    public function resendOtp($email)
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Email tidak ditemukan.']);
        }

        $newOtp = rand(100000, 999999);

        $user->update([
            'verification_code' => $newOtp,
            'verification_expires_at' => now()->addMinutes(5),
        ]);

        \Mail::raw("Kode verifikasi baru Anda adalah: $newOtp", function ($msg) use ($user) {
            $msg->to($user->email)->subject('Kode OTP Baru Ayako');
        });

        return back()->with('status', 'Kode baru telah dikirim.');
    }

}
