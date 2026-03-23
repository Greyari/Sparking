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
        Schema::create('subzona', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('zona_id');
            $table->string('nama_subzona');
            $table->string('fotosubzona');
            $table->timestamps();
            $table->integer('camera_id')->nullable();
            $table->foreign('zona_id')->references('id')->on('zona')->onDelete('cascade');
            $table->unique(['zona_id', 'nama_subzona']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subzona');
    }
};
