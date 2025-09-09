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
            $table->float('nilai_rating')->nullable()->after('nilai_rasa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_menu', function (Blueprint $table) {
            $table->dropColumn('nilai_rating');
        });
    }
};
