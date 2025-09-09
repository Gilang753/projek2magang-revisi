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
            $table->float('nilai')->nullable()->after('harga_seporsi');
            $table->string('cita_rasa')->nullable()->change(); // ubah enum jadi string
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_menu', function (Blueprint $table) {
            $table->dropColumn('nilai');
            // Tidak bisa rollback enum otomatis, perlu manual jika ingin kembali ke enum
        });
    }
};
