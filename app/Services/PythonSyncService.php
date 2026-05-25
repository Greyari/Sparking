<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PythonSyncService
{
    public static function syncSlots(int $subzonaId): void
    {
        $pythonUrl = config('services.python.url');
        if (!$pythonUrl) return;

        try {
            Http::timeout(5)->get("{$pythonUrl}/update_slots/{$subzonaId}");
            Log::info("[Python Sync] Slot subzona {$subzonaId} berhasil disinkron");
        } catch (\Exception $e) {
            Log::warning("[Python Sync] Gagal sync: " . $e->getMessage());
        }
    }
}
