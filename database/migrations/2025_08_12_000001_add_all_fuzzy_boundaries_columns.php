<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('fuzzy_boundaries', function (Blueprint $table) {
            $table->float('batas_murah_awal')->nullable();
            $table->float('batas_murah_puncak')->nullable();
            $table->float('batas_murah_akhir')->nullable();
            $table->float('batas_sedang_awal')->nullable();
            $table->float('batas_sedang_puncak')->nullable();
            $table->float('batas_sedang_akhir')->nullable();
            $table->float('batas_mahal_awal')->nullable();
            $table->float('batas_mahal_puncak')->nullable();
            $table->float('batas_mahal_akhir')->nullable();
        });
    }

    public function down()
    {
        Schema::table('fuzzy_boundaries', function (Blueprint $table) {
            $table->dropColumn([
                'batas_murah_awal', 'batas_murah_puncak', 'batas_murah_akhir',
                'batas_sedang_awal', 'batas_sedang_puncak', 'batas_sedang_akhir',
                'batas_mahal_awal', 'batas_mahal_puncak', 'batas_mahal_akhir',
            ]);
        });
    }
};
