<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Zona;
use App\Models\LogParkir;
use Carbon\Carbon;

class StatistikController extends Controller
{
    public function index()
    {
        $zonas = Zona::all();

        return view('user.statistik', [
            'title' => 'Statistik',
            'zonas' => $zonas,
        ]);
    }
}
