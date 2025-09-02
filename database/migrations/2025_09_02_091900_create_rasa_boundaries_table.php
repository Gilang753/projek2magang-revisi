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
        Schema::create('rasa_boundaries', function (Blueprint $table) {
            $table->id();
            $table->float('batas_asam_puncak')->default(10);
            $table->float('batas_asam_akhir')->default(30);
            $table->float('batas_manis_awal')->default(20);
            $table->float('batas_manis_puncak')->default(40);
            $table->float('batas_manis_akhir')->default(60);
            $table->float('batas_pedas_awal')->default(50);
            $table->float('batas_pedas_puncak')->default(70);
            $table->float('batas_pedas_akhir')->default(90);
            $table->float('batas_asin_awal')->default(80);
            $table->float('batas_asin_puncak')->default(100);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rasa_boundaries');
    }
};