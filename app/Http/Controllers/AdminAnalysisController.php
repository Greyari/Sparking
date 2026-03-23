<?php

namespace App\Http\Controllers;

use App\Models\Zona;
use App\Models\LogParkir;
use Carbon\Carbon;

class AdminAnalysisController extends Controller
{
    public function index()
    {
        Carbon::setLocale('id');

        $zonas = Zona::all();

        $awalMinggu = Carbon::now()->startOfWeek();
        $akhirMinggu = Carbon::now()->endOfWeek();

        $dataParkir = LogParkir::whereBetween('waktu_mulai', [$awalMinggu, $akhirMinggu])
            ->whereNotNull('waktu_mulai')
            ->whereNotNull('durasi')
            ->get();

        // Slot jam 4-jam dari 05:00 - 24:00
        $slotJam = [];
        for ($jam = 5; $jam < 24; $jam += 4) {
            $jamMulai = sprintf('%02d:00', $jam);
            $jamAkhir = sprintf('%02d:00', min($jam + 4, 24));
            $slotJam["$jamMulai - $jamAkhir"] = 0;
        }

        $daftarHari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        $jumlahPerHari = array_fill_keys($daftarHari, 0);
        $jumlahPerSlotPerHari = [];

        foreach ($daftarHari as $hari) {
            foreach ($slotJam as $slot => $_) {
                $jumlahPerSlotPerHari[$hari][$slot] = 0;
            }
        }

        foreach ($dataParkir as $log) {
            $waktuMulai = Carbon::parse($log->waktu_mulai);
            $hari = $waktuMulai->isoFormat('dddd');
            $jam  = (int) $waktuMulai->format('H');

            if (isset($jumlahPerHari[$hari])) {
                $jumlahPerHari[$hari]++;
            }

            foreach ($slotJam as $range => $_) {
                [$mulai, $akhir] = explode(' - ', $range);
                $jamMulai = (int) explode(':', $mulai)[0];
                $jamAkhir = (int) explode(':', $akhir)[0];

                if ($jam >= $jamMulai && $jam < $jamAkhir) {
                    $slotJam[$range]++;
                    $jumlahPerSlotPerHari[$hari][$range]++;
                    break;
                }
            }
        }

        $jamTersibuk = collect($slotJam)->sortDesc()->keys()->first();

        $maksJumlahHari = max($jumlahPerHari);
        $hariTersibuk = collect($jumlahPerHari)
            ->filter(fn($jumlah) => $jumlah == $maksJumlahHari)
            ->keys()
            ->all();

        $totalParkirHariTersibuk = $dataParkir->filter(function ($log) use ($hariTersibuk) {
            $hari = Carbon::parse($log->waktu_mulai)->isoFormat('dddd');
            return in_array($hari, $hariTersibuk);
        })->count();

        $rataRataDurasiDetik = round($dataParkir->avg('durasi'));
        $jam     = floor($rataRataDurasiDetik / 3600);
        $menit   = floor(($rataRataDurasiDetik % 3600) / 60);
        $durasiFormat = sprintf('%02d jam %02d menit', $jam, $menit);

        return view('admin.manageAnalysis', [
            'title'                    => 'ManageAnalysis',
            'zonas'                    => $zonas,
            'jamSibuk'                 => $jumlahPerSlotPerHari,
            'jamTersibuk'              => $jamTersibuk,
            'hariTersibuk'             => $hariTersibuk,
            'totalParkirHariTersibuk'  => $totalParkirHariTersibuk,
            'durasiFormat'             => $durasiFormat,
        ]);
    }
}
