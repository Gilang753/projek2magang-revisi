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
        Schema::table('fuzzy_inputs', function (Blueprint $table) {
            $table->float('p1')->nullable();
            $table->float('p2')->nullable();
            $table->float('p3')->nullable();
            $table->float('p4')->nullable();
            $table->float('miu_murah')->nullable();
            $table->float('miu_sedang')->nullable();
            $table->float('miu_mahal')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fuzzy_inputs', function (Blueprint $table) {
            $table->dropColumn('p1');
            $table->dropColumn('p2');
            $table->dropColumn('p3');
            $table->dropColumn('p4');
            $table->dropColumn('miu_murah');
            $table->dropColumn('miu_sedang');
            $table->dropColumn('miu_mahal');
        });
    }
};