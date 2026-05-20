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
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
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

        // Ambil ID zona secara dinamis, jangan hardcode
        $zona1 = Zona::updateOrCreate(
            ['nama_zona' => 'Zona 1'],
            ['keterangan' => 'test', 'fotozona' => 'test']
        );

        $zona2 = Zona::updateOrCreate(
            ['nama_zona' => 'Zona 2'],
            ['keterangan' => 'test', 'fotozona' => 'test']
        );

        $subzona1 = SubZona::updateOrCreate(
            ['zona_id' => $zona2->id, 'nama_subzona' => 'Sub zona 1'],
            ['fotosubzona' => 'test', 'camera_id' => '1']
        );

        $subzona2 = SubZona::updateOrCreate(
            ['zona_id' => $zona1->id, 'nama_subzona' => 'Sub zona 1'],
            ['fotosubzona' => 'test', 'camera_id' => '0']
        );

        Slot::updateOrCreate(
            ['subzona_id' => $subzona2->id, 'nomor_slot' => '1'],
            [
                'keterangan' => 'Terisi',
                'x1' => 89,  'y1' => 71,
                'x2' => 184, 'y2' => 66,
                'x3' => 216, 'y3' => 238,
                'x4' => 67,  'y4' => 239,
            ]
        );

        Slot::updateOrCreate(
            ['subzona_id' => $subzona2->id, 'nomor_slot' => '2'],
            [
                'keterangan' => 'Terisi',
                'x1' => 428, 'y1' => 96,
                'x2' => 519, 'y2' => 94,
                'x3' => 524, 'y3' => 214,
                'x4' => 428, 'y4' => 212,
            ]
        );

        Slot::updateOrCreate(
            ['subzona_id' => $subzona2->id, 'nomor_slot' => '3'],
            [
                'keterangan' => 'Terisi',
                'x1' => 201, 'y1' => 114,
                'x2' => 391, 'y2' => 118,
                'x3' => 388, 'y3' => 246,
                'x4' => 194, 'y4' => 233,
            ]
        );
    }
}
