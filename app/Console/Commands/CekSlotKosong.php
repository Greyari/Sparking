<?php

namespace App\Console\Commands;

use App\Models\Zona;
use App\Http\Controllers\NotifikasiSlotController;
use Illuminate\Console\Command;

/**
 * Command untuk mengecek slot parkir kosong dan mengirim notifikasi.
 *
 * Dijalankan otomatis oleh scheduler setiap menit.
 * Untuk test manual: php artisan parkir:cek-slot-kosong
 */
class CekSlotKosong extends Command
{
    protected $signature   = 'parkir:cek-slot-kosong';
    protected $description = 'Cek slot parkir kosong dan kirim notifikasi email ke user yang mendaftar';

    /**
     * Jalankan pengecekan slot untuk semua zona.
     * Jika ada slot tersedia di zona tertentu, kirim notifikasi ke pendaftar.
     */
    public function handle(NotifikasiSlotController $controller): void
    {
        // Load semua zona beserta subzona dan slot-nya sekaligus (eager loading)
        $zonas = Zona::with('subzonas.slots')->get();

        foreach ($zonas as $zona) {
            // Hitung total slot 'Tersedia' dari semua subzona milik zona ini
            $slotTersedia = $zona->subzonas
                ->flatMap(fn($sub) => $sub->slots)
                ->where('keterangan', 'Tersedia')
                ->count();

            $this->info("{$zona->nama_zona}: {$slotTersedia} slot tersedia");

            if ($slotTersedia > 0) {
                // Ada slot kosong — kirim notifikasi ke user yang menunggu
                $controller->kirimNotifikasi($zona->id);
                $this->info("✅ Notifikasi dikirim untuk {$zona->nama_zona}");
            } else {
                // Zona masih penuh — skip
                $this->info("⏳ {$zona->nama_zona} masih penuh, skip.");
            }
        }

        $this->info('Selesai: ' . now());
    }
}
