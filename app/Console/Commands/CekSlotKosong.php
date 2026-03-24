<?php

namespace App\Console\Commands;

use App\Models\Zona;
use App\Http\Controllers\NotifikasiSlotController;
use Illuminate\Console\Command;

class CekSlotKosong extends Command
{
    protected $signature   = 'parkir:cek-slot-kosong';
    protected $description = 'Cek slot parkir kosong dan kirim notifikasi';

    public function handle(NotifikasiSlotController $controller): void
    {
        $zonas = Zona::with('subzonas.slots')->get();

        foreach ($zonas as $zona) {
            // Hitung slot Tersedia dari semua subzona milik zona ini
            $slotTersedia = $zona->subzonas
                ->flatMap(fn($sub) => $sub->slots)
                ->where('keterangan', 'Tersedia')
                ->count();

            $this->info("{$zona->nama_zona}: {$slotTersedia} slot tersedia");

            if ($slotTersedia > 0) {
                $controller->kirimNotifikasi($zona->id);
                $this->info("✅ Notifikasi dikirim untuk {$zona->nama_zona}");
            } else {
                $this->info("⏳ {$zona->nama_zona} masih penuh, skip.");
            }
        }

        $this->info('Selesai: ' . now());
    }
}
