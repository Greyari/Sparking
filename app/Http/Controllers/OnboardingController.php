<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OnboardingController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        $user->refresh(); // Memastikan data user terbaru

        // Jika onboarding sudah selesai, redirect ke dashboard
        if ($user->onboarding_completed) {
            return redirect()->route('dashboard');
        }

        // Menampilkan view berdasarkan step
        if ($user->onboarding_step === 0) {
            return view('user.onboarding.welcome', ['title' => 'Selamat Datang di SPARKING']);
        } elseif ($user->onboarding_step === 1) {
            return view('user.onboarding.step1', ['title' => 'Pilih Jenis User']);
        } elseif ($user->onboarding_step === 2) {
            return view('user.onboarding.step2', ['title' => 'Upload Foto Profil']);
        } elseif ($user->onboarding_step === 3) {
            return view('user.onboarding.step3', ['title' => 'Pilih Kendaraan']);
        } elseif ($user->onboarding_step === 4) {
            return view('user.onboarding.step4', ['title' => 'Upload Foto Kendaraan']);
        } elseif ($user->onboarding_step === 5) {
            return view('user.onboarding.step5', ['title' => 'Plat Kendaraan']);
        } else {
            $user->update(['onboarding_completed' => true]);
            return view('user.onboarding.complete', ['title' => 'Onboarding Selesai']);
        }
    }

    public function nextStep()
    {
        $user = Auth::user();

        if ($user->onboarding_step === 0) {
            $user->update(['onboarding_step' => 1]);
        }

        // Force reload page + disable cache
        return redirect()->route('onboarding.show')->withHeaders([
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
            'Pragma' => 'no-cache',
        ]);
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $step = $request->input('step');

        // Step 1 - Pilih Jenis User
        if ($step == 1) {
            $request->validate([
                'jenis_user' => 'required|string|max:50',
            ]);

            $user->update([
                'jenis_user' => $request->input('jenis_user'),
                'onboarding_step' => 2,
            ]);

            return redirect()->route('onboarding.show');
        }

        // Step 2 - Upload Foto Profil
        elseif ($step == 2) {
            $request->validate([
                'foto_user' => 'required|image|mimes:jpg,jpeg,png|max:5120',
            ]);

            $result = cloudinary()->uploadApi()->upload(
                $request->file('foto_user')->getRealPath(),
                [
                    'folder' => 'foto_user',
                    'timeout' => 120
                ]
            );

            $user->update([
                'foto_user' => $result['secure_url'],
                'onboarding_step' => 3,
            ]);

            return redirect()->route('onboarding.show');
        }

        // Step 3 - Pilih Kendaraan
        elseif ($step == 3) {
            $request->validate([
                'jenis_kendaraan' => 'required|string|max:50',
            ]);

            $user->update([
                'jenis_kendaraan' => $request->input('jenis_kendaraan'),
                'onboarding_step' => 4,
            ]);

            return redirect()->route('onboarding.show');
        }

        // Step 4 - Upload Foto Kendaraan
        elseif ($step == 4) {
            $request->validate([
                'foto_kendaraan' => 'required|image|mimes:jpg,jpeg,png|max:5120',
            ]);

            $result = cloudinary()->uploadApi()->upload(
                $request->file('foto_kendaraan')->getRealPath(),
                [
                    'folder' => 'foto_kendaraan',
                    'timeout' => 120
                ]
            );

            $user->update([
                'foto_kendaraan' => $result['secure_url'],
                'onboarding_step' => 5,
            ]);

            return redirect()->route('onboarding.show');
        }

        // Step 5 - Plat Kendaraan
        elseif ($step == 5) {
            $request->validate([
                'no_plat' => 'required|string|max:20',
            ]);

            $user->update([
                'no_plat' => $request->input('no_plat'),
                'onboarding_step' => 6,
            ]);

            return redirect()->route('onboarding.show');
        }

        // Step 6 - Onboarding Selesai
        elseif ($step == 6) {
            $user->update([
                'onboarding_completed' => true,
                'onboarding_step' => 0,
            ]);

            return redirect()->route('dashboard');
        }

        return redirect()->route('onboarding.show');
    }
}
