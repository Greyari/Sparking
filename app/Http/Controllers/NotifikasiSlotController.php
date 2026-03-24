<?php

namespace App\Http\Controllers;

use App\Models\Zona;
use App\Models\NotifikasiSlot;
use App\Services\BrevoMailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Controller untuk mengelola fitur notifikasi slot parkir kosong.
 *
 * Alur kerja:
 * 1. User mendaftar notifikasi saat parkiran penuh
 * 2. Scheduler cek slot tersedia setiap menit
 * 3. Jika ada slot kosong, email dikirim ke semua user yang mendaftar
 */
class NotifikasiSlotController extends Controller
{
    protected BrevoMailService $brevo;

    /**
     * Inject BrevoMailService via constructor (dependency injection).
     */
    public function __construct(BrevoMailService $brevo)
    {
        $this->brevo = $brevo;
    }

    /**
     * Daftarkan user untuk menerima notifikasi slot kosong.
     * Hanya bisa didaftarkan jika zona memang sedang penuh (available = 0).
     */
    public function daftar(Request $request)
    {
        $request->validate(['zona_id' => 'required|exists:zona,id']);

        $zona = Zona::findOrFail($request->zona_id);

        // Cek apakah parkiran memang penuh — kalau masih ada slot, tidak perlu notifikasi
        if ($zona->available > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Slot masih tersedia, tidak perlu daftar notifikasi.'
            ], 422);
        }

        // Simpan atau update pendaftaran (hindari duplikasi data)
        NotifikasiSlot::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'zona_id' => $request->zona_id
            ],
            [
                'status'      => 'menunggu',
                'terkirim_at' => null
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Berhasil! Kami akan mengirim notifikasi saat slot tersedia.'
        ]);
    }

    /**
     * Batalkan pendaftaran notifikasi user untuk zona tertentu.
     */
    public function batal(Request $request)
    {
        $request->validate(['zona_id' => 'required|exists:zona,id']);

        NotifikasiSlot::where('user_id', Auth::id())
            ->where('zona_id', $request->zona_id)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pendaftaran notifikasi dibatalkan.'
        ]);
    }

    /**
     * Cek apakah user sudah terdaftar notifikasi untuk zona tertentu.
     * Dipakai oleh frontend untuk menampilkan status tombol notifikasi.
     */
    public function status(Request $request)
    {
        $request->validate(['zona_id' => 'required|exists:zona,id']);

        $terdaftar = NotifikasiSlot::where('user_id', Auth::id())
            ->where('zona_id', $request->zona_id)
            ->where('status', 'menunggu')
            ->exists();

        return response()->json(['terdaftar' => $terdaftar]);
    }

    /**
     * Kirim email notifikasi ke semua user yang mendaftar di zona tertentu.
     * Dipanggil oleh CekSlotKosong command (scheduler).
     *
     * @param int $zonaId ID zona yang slot-nya sudah tersedia
     */
    public function kirimNotifikasi(int $zonaId): void
    {
        // Ambil semua pendaftar yang masih menunggu di zona ini
        $pendaftar = NotifikasiSlot::with('user', 'zona')
            ->where('zona_id', $zonaId)
            ->where('status', 'menunggu')
            ->get();

        foreach ($pendaftar as $notif) {
            // Kirim email via Brevo API
            $berhasil = $this->brevo->sendSlotAvailableNotification(
                $notif->user->email,
                $notif->user->nama,
                $notif->zona->nama_zona
            );

            if ($berhasil) {
                // Update status jadi 'terkirim' supaya tidak kirim email duplikat
                $notif->update([
                    'status'      => 'terkirim',
                    'terkirim_at' => now(),
                ]);
            } else {
                Log::warning('Gagal kirim notifikasi ke ' . $notif->user->email);
            }
        }
    }
}
