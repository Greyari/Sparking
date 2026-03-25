<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\BrevoMailService;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    protected BrevoMailService $brevo;

    public function __construct(BrevoMailService $brevo)
    {
        $this->brevo = $brevo;
    }

    public function login_proses(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
        ];


        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $status = Auth::user()->status;

            switch ($status) {
                case 'nonAktif':
                    return redirect()
                        ->back()
                        ->with('error', 'Akun Anda belum terverifikasi. Silahkan cek email dan klik tombol/link untuk melakukan verifikasi.');

                case 'aktif':
                    if (Auth::user()->role === 'admin') {
                        return redirect()->route('admin-dashboard')->with('success', 'Login berhasil');
                    } else {
                        return redirect()->route('user-dashboard')->with('success', 'Login berhasil');
                    }

                default:
                    return redirect()
                        ->back()
                        ->with('error', 'Status akun tidak valid.');
            }

        } else {
            return redirect()
                ->back()
                ->with('error', 'Email atau password salah.');
        }
    }

    public function registrasi_proses(Request $request)
    {
        try {
            $validated = $request->validate([
                'email'    => 'required|unique:users,email|email',
                'nama'     => 'required',
                'password' => 'required|confirmed',
            ], [
                'nama.required'      => 'Nama Lengkap harus diisi',
                'email.required'     => 'Email harus diisi',
                'email.unique'       => 'Email sudah terdaftar',
                'email.email'        => 'Format email tidak valid',
                'password.required'  => 'Password wajib diisi',
                'password.confirmed' => 'Konfirmasi password tidak cocok',
            ]);
        } catch (ValidationException $e) {
            return back()
                ->withInput()
                ->with('error', collect($e->validator->errors()->all())->first())
                ->with('showModalError', true);
        }

        $user = User::create([
            'email'                => $validated['email'],
            'nama'                 => $validated['nama'],
            'password'             => Hash::make($validated['password']),
            'onboarding_step'      => 0,
            'onboarding_completed' => false,
        ]);

        if ($user) {
            $verificationUrl = URL::temporarySignedRoute(
                'verification.verify',
                now()->addMinutes(60),
                ['id' => $user->id, 'hash' => sha1($user->email)]
            );

            // Kirim via Brevo
            $this->brevo->sendVerificationEmail(
                $user->email,
                $user->nama,
                $verificationUrl
            );

            return redirect()
                ->route('login')
                ->with('success', 'Pendaftaran berhasil. Silakan periksa email Anda untuk verifikasi.')
                ->with('showVerificationModal', true);
        }

        return back()
            ->withInput()
            ->with('error', 'Pendaftaran gagal')
            ->with('showModalError', true);
    }

    public function verifyEmail($id, $hash)
    {
        $user = User::findOrFail($id);

        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            abort(403, 'Invalid verification link');
        }

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            $user->status = 'aktif';
            $user->save();

            event(new Verified($user));
        }

        return redirect()->route('login')->with('success', 'Akun anda sudah diverifikasi dan siap digunakan!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('succes', 'Logout Berhasil');
    }
}
