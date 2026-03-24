<?php

namespace Database\Seeders;

use App\Models\Slot;
use App\Models\SubZona;
use App\Models\User;
use App\Models\Zona;
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
                'jenis_user' => 'karyawan',
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
                'jenis_user' => 'karyawan',
                'nama' => 'Dosen',
                'password' => Hash::make('123'),
                'jenis_kendaraan' => 'motor',
                'no_plat' => 'BP12341A',
                'role' => 'user',
                'status' => 'aktif',
                'onboarding_completed' => true,
            ]
        );

        Zona::updateOrCreate(
            [
                'nama_zona' => 'test 1',
                'keterangan' => 'test',
                'fotozona' => 'test',
            ]
        );

        Zona::updateOrCreate(
            [
                'nama_zona' => 'test 2',
                'keterangan' => 'test',
                'fotozona' => 'test',
            ]
        );

        SubZona::updateOrCreate(
            [
                'zona_id' => 1,
                'nama_subzona' => 'test 1',
                'fotosubzona' => 'test',
                'camera_id' => '1',
            ]
        );

        Slot::updateOrCreate(
            [
                'subzona_id' => 1,
                'nomor_slot' => '1',
                'keterangan' => 'Terisi',
                'x1' => 0,
                'y1' => 0,
                'x2' => 0,
                'y2' => 0,
                'x3' => 0,
                'y3' => 0,
                'x4' => 0,
                'y4' => 0,
            ]
        );

        Slot::updateOrCreate(
            [
                'subzona_id' => 1,
                'nomor_slot' => '2',
                'keterangan' => 'Terisi',
                'x1' => 0,
                'y1' => 0,
                'x2' => 0,
                'y2' => 0,
                'x3' => 0,
                'y3' => 0,
                'x4' => 0,
                'y4' => 0,
            ]
        );

        Slot::updateOrCreate(
            [
                'subzona_id' => 1,
                'nomor_slot' => '3',
                'keterangan' => 'Terisi',
                'x1' => 0,
                'y1' => 0,
                'x2' => 0,
                'y2' => 0,
                'x3' => 0,
                'y3' => 0,
                'x4' => 0,
                'y4' => 0,
            ]
        );
    }
}
