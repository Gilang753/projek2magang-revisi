<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('fuzzy_boundaries', function (Blueprint $table) {
            $table->double('batas_murah_awal')->nullable();
            $table->double('batas_murah_puncak')->nullable();
            $table->double('batas_murah_akhir')->nullable();
            $table->double('batas_sedang_awal')->nullable();
            $table->double('batas_sedang_puncak')->nullable();
            $table->double('batas_sedang_akhir')->nullable();
            $table->double('batas_mahal_awal')->nullable();
            $table->double('batas_mahal_puncak')->nullable();
            $table->double('batas_mahal_akhir')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fuzzy_boundaries', function (Blueprint $table) {
            $table->dropColumn([
                'batas_murah_awal',
                'batas_murah_puncak',
                'batas_murah_akhir',
                'batas_sedang_awal',
                'batas_sedang_puncak',
                'batas_sedang_akhir',
                'batas_mahal_awal',
                'batas_mahal_puncak',
                'batas_mahal_akhir',
            ]);
        });
    }
};
