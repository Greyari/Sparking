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
        Schema::create('notifikasi_slot', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->constrained('users')->onDelete('cascade');
            $table->unsignedBigInteger('zona_id')->constrained('zona')->onDelete('cascade');
            $table->enum('status', ['menunggu', 'terkirim'])->default('menunggu');
            $table->timestamp('terkirim_at')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'zona_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifikasi_slot');
    }
};
