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
        Schema::create('rekomendasi_boundaries', function (Blueprint $table) {
            $table->id();
            $table->integer('batas_tidak_puncak')->default(30);
            $table->integer('batas_tidak_akhir')->default(50);
            $table->integer('batas_rekomendasi_awal')->default(50);
            $table->integer('batas_rekomendasi_puncak')->default(70);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rekomendasi_boundaries');
    }
};