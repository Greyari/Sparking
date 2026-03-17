<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index()
    {
        $users = User::where('status', 'aktif')->paginate(10);

        return view('admin.manageUsers', [
            "title" => "ManageUsers",
            "users" => $users
        ]);
    }

    public function delete($id_user)
    {
        $user = User::findOrFail($id_user);
        $user->delete();
        return redirect()->back()->with('success', 'User berhasil dihapus');
    }

}
