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

        Log::info('Menampilkan halaman onboarding', ['step' => $user->onboarding_step]);

        // Jika onboarding sudah selesai, redirect ke dashboard
        if ($user->onboarding_completed) {
            return redirect()->route('dashboard');
        }

        // Menampilkan view berdasarkan step
        if ($user->onboarding_step === 0) {
            return view('onboarding.welcome', ['title' => 'Selamat Datang di SPARKING']);
        } elseif ($user->onboarding_step === 1) {
            return view('onboarding.step1', ['title' => 'Pilih Jenis User']);
        } elseif ($user->onboarding_step === 2) {
            return view('onboarding.step2', ['title' => 'Upload Foto Profil']);
        } elseif ($user->onboarding_step === 3) {
            return view('onboarding.step3', ['title' => 'Pilih Kendaraan']);
        } elseif ($user->onboarding_step === 4) {
            return view('onboarding.step4', ['title' => 'Upload Foto Kendaraan']);
        } elseif ($user->onboarding_step === 5) {
            return view('onboarding.step5', ['title' => 'Plat Kendaraan']);
        } else {
            $user->update(['onboarding_completed' => true]);
            return view('onboarding.complete', ['title' => 'Onboarding Selesai']);
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

        Log::info('Memproses update onboarding');

        $step = $request->input('step');

        // Step 1 - Pilih Jenis User
        if ($step == 1) {
            $request->validate([
                'jenis_user' => 'required|string|max:50',
            ]);

            Log::info('Validasi step 1 berhasil');

            $user->update([
                'jenis_user' => $request->input('jenis_user'),
                'onboarding_step' => 2,
            ]);

            Log::info('Data user berhasil diupdate untuk step 1', [
                'jenis_user' => $user->jenis_user,
            ]);

            return redirect()->route('onboarding.show');
        }

        // Step 2 - Upload Foto Profil
        elseif ($step == 2) {
            $request->validate([
                'foto_user' => 'required|image|max:2048',
            ]);

            Log::info('Validasi step 2 berhasil');

            $profilePath = $request->file('foto_user')->store('foto_user','public');

            $user->update([
                'foto_user' => str_replace('public/', '', $profilePath),
                'onboarding_step' => 3,
            ]);

            Log::info('Data user berhasil diupdate untuk step 2', [
                'foto_user' => $user->foto_user,
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

            Log::info('Data kendaraan berhasil diupdate untuk step 3', [
                'jenis_kendaraan' => $user->jenis_kendaraan,
            ]);

            return redirect()->route('onboarding.show');
        }

        // Step 4 - Upload Foto Kendaraan
        elseif ($step == 4) {
            $request->validate([
                'foto_kendaraan' => 'required|image|max:2048',
            ]);

            Log::info('Validasi step 4 berhasil');

            $vehiclePath = $request->file('foto_kendaraan')->store('foto_kendaraan','public');

            $user->update([
                'foto_kendaraan' => str_replace('public/', '', $vehiclePath),
                'onboarding_step' => 5,
            ]);

            Log::info('Data kendaraan berhasil diupdate untuk step 4', [
                'foto_kendaraan' => $user->foto_kendaraan,
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

            Log::info('Data plat kendaraan berhasil diupdate untuk step 5', [
                'no_plat' => $user->no_plat,
            ]);

            return redirect()->route('onboarding.show');
        }

        // Step 6 - Onboarding Selesai
        elseif ($step == 6) {
            $user->update([
                'onboarding_completed' => true,
                'onboarding_step' => 0,
            ]);

            Log::info('Onboarding selesai', ['user_id' => $user->id]);

            return redirect()->route('dashboard');
        }

        return redirect()->route('onboarding.show');
    }
}
