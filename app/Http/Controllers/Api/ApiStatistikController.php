<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LogParkir;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ApiStatistikController extends Controller
{
    public function getStatistik(Request $request)
    {
        $zonaId = $request->input('zona_id');

        // Filter minggu ini saja
        $awalMinggu  = Carbon::now()->startOfWeek();
        $akhirMinggu = Carbon::now()->endOfWeek();

        $data = LogParkir::where('zona_id', $zonaId)
            ->whereBetween('waktu_mulai', [$awalMinggu, $akhirMinggu])
            ->whereNotNull('waktu_selesai')
            ->whereNotNull('durasi')
            ->get();

        $hariMap = [
            'Monday'    => 'Senin',
            'Tuesday'   => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday'  => 'Kamis',
            'Friday'    => 'Jumat',
            'Saturday'  => 'Sabtu',
            'Sunday'    => 'Minggu',
        ];

        $orderedLabels = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

        if ($data->isEmpty()) {
            return response()->json([
                'total'         => 0,
                'avg_per_day'   => 0,
                'hari_terpadat' => '-',
                'chart'         => ['labels' => $orderedLabels, 'data' => [0,0,0,0,0,0,0]],
                'jam_sibuk'     => (object)[],
            ]);
        }

        // --- Chart per hari ---
        $perHari = $data->groupBy(fn($item) => Carbon::parse($item->waktu_mulai)->format('l'))
                        ->map->count();

        $hariPuncak  = $perHari->sortDesc()->keys()->first();
        $orderedData = [];
        foreach ($orderedLabels as $hari) {
            $englishDay    = array_search($hari, $hariMap);
            $orderedData[] = $perHari[$englishDay] ?? 0;
        }

        $total = $data->count();
        $avg   = $data->groupBy(fn($item) => Carbon::parse($item->waktu_mulai)->format('Y-m-d'))
                    ->map->count()->avg();

        // --- Jam sibuk per hari ---
        $slotJam = [];
        for ($jam = 5; $jam < 24; $jam += 4) {
            $label           = sprintf('%02d:00', $jam) . ' - ' . sprintf('%02d:00', min($jam + 4, 24));
            $slotJam[$label] = 0;
        }

        $jumlahPerSlotPerHari = [];
        foreach ($orderedLabels as $h) {
            foreach ($slotJam as $slot => $_) {
                $jumlahPerSlotPerHari[$h][$slot] = 0;
            }
        }

        foreach ($data as $log) {
            $waktuMulai = Carbon::parse($log->waktu_mulai);
            $hari       = $hariMap[$waktuMulai->format('l')] ?? null;
            if (!$hari) continue;

            $jam = (int) $waktuMulai->format('H');

            foreach ($slotJam as $range => $_) {
                [$mulai, $akhir] = explode(' - ', $range);
                $jamMulai = (int) explode(':', $mulai)[0];
                $jamAkhir = (int) explode(':', $akhir)[0];
                if ($jam >= $jamMulai && $jam < $jamAkhir) {
                    $jumlahPerSlotPerHari[$hari][$range]++;
                    break;
                }
            }
        }

        // Hapus slot & hari yang kosong
        foreach ($jumlahPerSlotPerHari as $hari => $slots) {
            $jumlahPerSlotPerHari[$hari] = array_filter($slots, fn($v) => $v > 0);
        }
        $jumlahPerSlotPerHari = array_filter($jumlahPerSlotPerHari, fn($slots) => count($slots) > 0);

        return response()->json([
            'total'         => $total,
            'avg_per_day'   => round($avg, 1),
            'hari_terpadat' => $hariMap[$hariPuncak] ?? '-',
            'chart'         => ['labels' => $orderedLabels, 'data' => $orderedData],
            'jam_sibuk'     => $jumlahPerSlotPerHari,
        ]);
    }

}
