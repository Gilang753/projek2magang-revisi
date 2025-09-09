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
        Schema::table('tb_menu', function (Blueprint $table) {
            $table->float('miu_harga_murah')->nullable();
            $table->float('miu_harga_sedang')->nullable();
            $table->float('miu_harga_mahal')->nullable();
            $table->float('miu_rating_rendah')->nullable();
            $table->float('miu_rating_sedang')->nullable();
            $table->float('miu_rating_tinggi')->nullable();
            $table->float('miu_rasa_asam')->nullable();
            $table->float('miu_rasa_manis')->nullable();
            $table->float('miu_rasa_pedas')->nullable();
            $table->float('miu_rasa_asin')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_menu', function (Blueprint $table) {
            $table->dropColumn([
                'miu_harga_murah', 'miu_harga_sedang', 'miu_harga_mahal',
                'miu_rating_rendah', 'miu_rating_sedang', 'miu_rating_tinggi',
                'miu_rasa_asam', 'miu_rasa_manis', 'miu_rasa_pedas', 'miu_rasa_asin'
            ]);
        });
    }
};
