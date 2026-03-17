<?php
namespace App\Http\Controllers;
use App\Models\Datakendaraan;
use App\Models\User;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $search = $request->input('search');
        $users = User::where('status', 'active')->when($search, function ($query, $search) {
            $query->where('nama', 'LIKE', "%{$search}%")
                ->orWhere('no_plat', 'LIKE', "%{$search}%");
        })->get();

        $vehicles = Datakendaraan::with('user')
        ->whereHas('user', function ($query) use ($search) {
            $query->where('nama', 'like', '%' . $search . '%');
        })
        ->orWhere('no_plat1', 'like', '%' . $search . '%')
        ->get();

        return response()->json([
            'data' => $users,
            'data_kendaraan' => $vehicles,
        ]);
    }
}
