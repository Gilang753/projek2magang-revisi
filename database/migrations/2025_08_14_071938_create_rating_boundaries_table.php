<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('rating_boundaries', function (Blueprint $table) {
            $table->id();
            $table->float('batas_rendah_awal')->default(0);
            $table->float('batas_rendah_puncak')->default(20);
            $table->float('batas_rendah_akhir')->default(40);
            $table->float('batas_sedang_awal')->default(30);
            $table->float('batas_sedang_puncak')->default(50);
            $table->float('batas_sedang_akhir')->default(70);
            $table->float('batas_tinggi_awal')->default(60);
            $table->float('batas_tinggi_puncak')->default(80);
            $table->float('batas_tinggi_akhir')->default(100);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('rating_boundaries');
    }
};