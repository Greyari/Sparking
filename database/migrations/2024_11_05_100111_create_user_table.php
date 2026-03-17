<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pengguna', function (Blueprint $table) {
            $table->id('id_pengguna');
            $table->string('identitas')->unique()->nullable();
            $table->string('email')->unique();
            $table->enum('jenis_pengguna', ['mahasiswa' , 'dosen' , 'karyawan' , 'tamu']);
            $table->string('nama');
            $table->string('password');
            $table->enum('jenis_kendaraan', ['mobil' , 'motor'])->nullable();
            $table->string('no_plat')->unique()->nullable();
            $table->string('foto_kendaraan')->nullable();
            $table->string('foto_pengguna')->nullable();
            $table->string('qr_code')->nullable();
            $table->enum('role', ['admin', 'pengguna'])->default('pengguna');
            $table->enum('status', ['aktif', 'nonAktif', 'ditolak'])->default('nonAktif');
            $table->integer('onboarding_step')->default(0);
            $table->boolean('onboarding_completed')->default(false);
            $table->timestamp('email_verified_at')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengguna');
    }
};
