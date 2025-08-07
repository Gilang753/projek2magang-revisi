<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menjalankan migrasi.
     */
    public function up(): void
    {
        Schema::create('fuzzy_ratings', function (Blueprint $table) {
            $table->id();
            
            // Input rating bintang (1-5 bintang)
            $table->tinyInteger('rating_bintang')->nullable()->comment('Rating 1-5 bintang');
            
            // Nilai yang dikonversi (rating_bintang * 20 = skala 0-100)
            $table->float('nilai_rating')->nullable()->comment('Dikonversi ke skala 0-100 (20 per bintang)');
            
            // Parameter fuzzy yang sudah ditetapkan untuk sistem rating
            $table->float('rendah_min')->default(0)->comment('Batas bawah rating rendah (0)');
            $table->float('rendah_max')->default(40)->comment('Batas atas rating rendah (40)');
            $table->float('sedang_min')->default(30)->comment('Batas bawah rating sedang (30)');
            $table->float('sedang_max')->default(70)->comment('Batas atas rating sedang (70)');
            $table->float('tinggi_min')->default(60)->comment('Batas bawah rating tinggi (60)');
            $table->float('tinggi_max')->default(100)->comment('Batas atas rating tinggi (100)');
            
            // Nilai keanggotaan
            $table->float('keanggotaan_rendah')->nullable()->comment('Derajat keanggotaan rating rendah');
            $table->float('keanggotaan_sedang')->nullable()->comment('Derajat keanggotaan rating sedang');
            $table->float('keanggotaan_tinggi')->nullable()->comment('Derajat keanggotaan rating tinggi');
            
            $table->timestamps();
        });
    }

    /**
     * Membalikkan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('fuzzy_ratings');
    }
};