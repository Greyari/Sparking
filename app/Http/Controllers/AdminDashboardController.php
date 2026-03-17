<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Zona;
use App\Models\Slot;

class AdminDashboardController extends Controller
{
    public function index()
    {
            $data = [
                'total_user' => User::where('status', 'aktif')->count(),
                'total_persetujuan' => User::where('status', '!=', 'aktif')->count(),
                'total_mobil' => User::where('jenis_kendaraan', 'mobil')->count(),
                'total_motor' => User::where('jenis_kendaraan', 'motor')->count(),
                'total_zona' => Zona::count(),
                'total_slot' => Slot::count(),
            ];

            return view('admin.dashboard', [
                'title' => 'Admin Dashboard',
                'data' => $data,
            ]);
    }
}
