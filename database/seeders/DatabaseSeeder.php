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
        User::updateOrCreate([
            'identitas' => '12345678',
            'email' => 'admin@gmail.com',
            'jenis_pengguna' => 'karyawan',
            'nama' => 'Admin',
            'password' => Hash::make('123'),
            'jenis_kendaraan' => 'motor',
            'no_plat' => 'BP1234AA',
            'role' => 'admin',
            'status' => 'aktif',
        ]);

        User::updateOrCreate([
            'identitas' => '1234567',
            'email' => 'dosen@gmail.com',
            'jenis_pengguna' => 'karyawan',
            'nama' => 'Dosen',
            'password' => Hash::make('123'),
            'jenis_kendaraan' => 'motor',
            'no_plat' => 'BP12341A',
            'role' => 'pengguna',
            'status' => 'aktif',
        ]);
    }
}
