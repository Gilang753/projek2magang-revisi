<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('rules', function (Blueprint $table) {
            // Hapus kolom menu_id
            $table->dropForeign(['menu_id']);
            $table->dropColumn('menu_id');
            
            // Tambah kolom rekomendasi
            $table->enum('rekomendasi', ['Rekomendasi', 'Tidak Rekomendasi'])->after('rasa_fuzzy');
        });
    }

    public function down()
    {
        Schema::table('rules', function (Blueprint $table) {
            // Kembalikan kolom menu_id
            $table->foreignId('menu_id')->constrained()->onDelete('cascade');
            
            // Hapus kolom rekomendasi
            $table->dropColumn('rekomendasi');
        });
    }
};