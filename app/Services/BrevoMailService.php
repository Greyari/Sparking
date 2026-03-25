<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Service untuk mengirim email notifikasi menggunakan Brevo API.
 * Dokumentasi Brevo API: https://developers.brevo.com/reference/sendtransacemail
 */
class BrevoMailService
{
    protected string $apiKey;
    protected string $fromEmail;
    protected string $fromName;

    public function __construct()
    {
        // Ambil konfigurasi dari config/services.php dan config/mail.php
        $this->apiKey    = config('services.brevo.api_key');
        $this->fromEmail = config('mail.from.address');
        $this->fromName  = config('mail.from.name');
    }

    /**
     * Kirim email notifikasi ketika slot parkir tersedia.
     *
     * @param string $toEmail  Email penerima
     * @param string $toName   Nama penerima
     * @param string $namaZona Nama zona parkir yang slot-nya tersedia
     * @return bool True jika berhasil, false jika gagal
     */
    public function sendSlotAvailableNotification(string $toEmail, string $toName, string $namaZona): bool
    {
        // Render blade template jadi HTML string
        $htmlContent = view('email.slot_tersedia', [
            'nama'     => $toName,
            'namaZona' => $namaZona,
        ])->render();

        $response = Http::withHeaders([
            'api-key'      => $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post('https://api.brevo.com/v3/smtp/email', [
            'sender' => [
                'email' => $this->fromEmail,
                'name'  => $this->fromName,
            ],
            'to' => [
                ['email' => $toEmail, 'name' => $toName]
            ],
            'subject'     => '🚗 Slot Parkir Tersedia - ' . $namaZona,
            'htmlContent' => $htmlContent,
        ]);

        if ($response->failed()) {
            Log::error('Brevo gagal kirim email', [
                'email'  => $toEmail,
                'zona'   => $namaZona,
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            return false;
        }

        return true;
    }

    /**
     * Kirim email link reset password.
     * Menggunakan template blade: resources/views/email/reset_password.blade.php
     */
    public function sendPasswordResetEmail(string $toEmail, string $toName, string $resetUrl): bool
    {
        // Render blade template jadi HTML string
        $htmlContent = view('email.reset_password', [
            'nama'     => $toName,
            'resetUrl' => $resetUrl,
        ])->render();

        $response = Http::withHeaders([
            'api-key'      => $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post('https://api.brevo.com/v3/smtp/email', [
            'sender' => [
                'email' => $this->fromEmail,
                'name'  => $this->fromName,
            ],
            'to' => [
                ['email' => $toEmail, 'name' => $toName]
            ],
            'subject'     => '🔐 Reset Kata Sandi - Sparking',
            'htmlContent' => $htmlContent,  // ← pakai blade template
        ]);

        if ($response->failed()) {
            Log::error('Brevo gagal kirim email reset password', [
                'email'  => $toEmail,
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            return false;
        }

        return true;
    }

    /**
     * Kirim email verifikasi akun baru.
     */
    public function sendVerificationEmail(string $toEmail, string $toName, string $verificationUrl): bool
    {
        // Render blade template yang sudah ada
        $htmlContent = view('email.verifikasi', [
            'nama' => $toName,
            'url'  => $verificationUrl,
        ])->render();

        $response = Http::withHeaders([
            'api-key'      => $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post('https://api.brevo.com/v3/smtp/email', [
            'sender' => [
                'email' => $this->fromEmail,
                'name'  => $this->fromName,
            ],
            'to' => [
                ['email' => $toEmail, 'name' => $toName]
            ],
            'subject'     => '✅ Verifikasi Email Anda di SPARKING',
            'htmlContent' => $htmlContent,
        ]);

        if ($response->failed()) {
            Log::error('Brevo gagal kirim email verifikasi', [
                'email'  => $toEmail,
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            return false;
        }

        return true;
    }
}
