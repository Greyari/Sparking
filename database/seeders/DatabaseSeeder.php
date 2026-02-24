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
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'], // kondisi unik
            [
                'identitas' => '12345678',
                'jenis_pengguna' => 'karyawan',
                'nama' => 'Admin',
                'password' => Hash::make('123'),
                'jenis_kendaraan' => 'motor',
                'no_plat' => 'BP1234AA',
                'role' => 'admin',
                'status' => 'aktif',
            ]
        );

        User::updateOrCreate(
            ['email' => 'dosen@gmail.com'],
            [
                'identitas' => '1234567',
                'jenis_pengguna' => 'karyawan',
                'nama' => 'Dosen',
                'password' => Hash::make('123'),
                'jenis_kendaraan' => 'motor',
                'no_plat' => 'BP12341A',
                'role' => 'pengguna',
                'status' => 'aktif',
            ]
        );
    }
}
