<?php

namespace App\Http\Controllers;

use App\Models\Zona;
use App\Models\NotifikasiSlot;
use App\Services\BrevoMailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotifikasiSlotController extends Controller
{
    protected BrevoMailService $brevo;

    public function __construct(BrevoMailService $brevo)
    {
        $this->brevo = $brevo;
    }

    // User daftar notifikasi
    public function daftar(Request $request)
    {
        $request->validate(['zona_id' => 'required|exists:zona,id']);

        $zona = Zona::findOrFail($request->zona_id);

        // Cek apakah parkiran memang penuh dulu
        if ($zona->available > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Slot masih tersedia, tidak perlu daftar notifikasi.'
            ], 422);
        }

        // Simpan atau update pendaftaran
        NotifikasiSlot::updateOrCreate(
            ['user_id' => Auth::id(), 'zona_id' => $request->zona_id],
            ['status' => 'menunggu', 'terkirim_at' => null]
        );

        return response()->json([
            'success' => true,
            'message' => 'Berhasil! Kami akan mengirim notifikasi saat slot tersedia.'
        ]);
    }

    // User batal notifikasi
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

    // Cek status pendaftaran user
    public function status(Request $request)
    {
        $request->validate(['zona_id' => 'required|exists:zona,id']);

        $terdaftar = NotifikasiSlot::where('user_id', Auth::id())
            ->where('zona_id', $request->zona_id)
            ->where('status', 'menunggu')
            ->exists();

        return response()->json(['terdaftar' => $terdaftar]);
    }

    // Dipanggil oleh sistem/scheduler saat slot tersedia
    public function kirimNotifikasi(int $zonaId): void
    {
        $pendaftar = NotifikasiSlot::with('user', 'zona')
            ->where('zona_id', $zonaId)
            ->where('status', 'menunggu')
            ->get();

        foreach ($pendaftar as $notif) {
            $berhasil = $this->brevo->sendSlotAvailableNotification(
                $notif->user->email,
                $notif->user->nama,
                $notif->zona->nama_zona
            );

            if ($berhasil) {
                $notif->update([
                    'status'      => 'terkirim',
                    'terkirim_at' => now(),
                ]);
            }
        }
    }
}
