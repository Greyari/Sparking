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
        $response = Http::withHeaders([
            'api-key'      => $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post('https://api.brevo.com/v3/smtp/email', [
            // Pengirim email
            'sender' => [
                'email' => $this->fromEmail,
                'name'  => $this->fromName,
            ],
            // Penerima email
            'to' => [
                ['email' => $toEmail, 'name' => $toName]
            ],
            'subject'     => '🚗 Slot Parkir Tersedia - ' . $namaZona,
            'htmlContent' => $this->buildEmailTemplate($toName, $namaZona),
        ]);

        // Jika request ke Brevo API gagal, log error dan return false
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
     * Buat template HTML untuk email notifikasi.
     *
     * @param string $nama     Nama penerima untuk sapaan
     * @param string $namaZona Nama zona parkir
     * @return string HTML email
     */
    private function buildEmailTemplate(string $nama, string $namaZona): string
    {
        return "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <div style='background: linear-gradient(135deg, #3b82f6, #6366f1); padding: 30px; border-radius: 12px 12px 0 0;'>
                <h1 style='color: white; margin: 0; font-size: 24px;'>🚗 Slot Parkir Tersedia!</h1>
            </div>
            <div style='background: #f8fafc; padding: 30px; border-radius: 0 0 12px 12px; border: 1px solid #e2e8f0;'>
                <p style='font-size: 16px; color: #374151;'>Halo, <strong>{$nama}</strong>!</p>
                <p style='color: #6b7280;'>
                    Kabar baik! Slot parkir di <strong style='color: #3b82f6;'>{$namaZona}</strong>
                    kini sudah tersedia kembali.
                </p>
                <div style='background: #dbeafe; border-left: 4px solid #3b82f6; padding: 16px; border-radius: 8px; margin: 20px 0;'>
                    <p style='margin: 0; color: #1e40af; font-weight: bold;'>
                        Segera menuju lokasi sebelum slot terisi kembali!
                    </p>
                </div>
                <p style='color: #9ca3af; font-size: 12px; margin-top: 30px;'>
                    Notifikasi ini dikirim otomatis oleh Sistem Parkir.<br>
                    Anda menerima email ini karena telah mendaftar notifikasi slot kosong.
                </p>
            </div>
        </div>";
    }
}
