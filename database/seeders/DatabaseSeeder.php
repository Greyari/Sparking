<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'identitas' => '12345678',
            'email' => 'admin@gmail.com',
            'jenis_pengguna' => 'karyawan',
            'nama' => 'Admin',
            'password' => Hash::make('12345678'),
            'jenis_kendaraan' => 'motor',
            'no_plat' => 'BP1234AA',
            'role' => 'admin',
            'status' => 'aktif',
        ]);
    }
}
