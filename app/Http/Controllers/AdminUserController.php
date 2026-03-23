<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index()
    {
        $users = User::paginate(10);

        return view('admin.manageUsers', [
            "title" => "ManageUsers",
            "users" => $users
        ]);
    }

    public function search(Request $request)
    {
        $search = $request->input('search');

        $users = User::where('status', 'active')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama', 'LIKE', "%{$search}%")
                    ->orWhere('no_plat', 'LIKE', "%{$search}%")
                    ->orWhere('identitas', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('jenis_user', 'LIKE', "%{$search}%")
                    ->orWhere('jenis_kendaraan', 'LIKE', "%{$search}%");
                });
            })
            ->get();

        return response()->json([
            'data' => $users,
        ]);
    }

    public function delete($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->back()->with('success', 'User berhasil dihapus');
    }

}
