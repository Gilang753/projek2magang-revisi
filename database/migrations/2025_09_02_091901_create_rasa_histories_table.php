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
        Schema::create('rasa_histories', function (Blueprint $table) {
            $table->id();
            $table->float('rasa');
            $table->float('miu_asam');
            $table->float('miu_manis');
            $table->float('miu_pedas');
            $table->float('miu_asin');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rasa_histories');
    }
};