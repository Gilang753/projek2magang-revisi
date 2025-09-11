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
        Schema::create('inference_results', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rule_id');
            $table->unsignedBigInteger('menu_id')->nullable();
            $table->float('miu_harga');
            $table->float('miu_rating');
            $table->float('miu_rasa');
            $table->float('alpha');
            $table->string('rekomendasi');
            $table->timestamps();

            $table->foreign('rule_id')->references('id')->on('rules')->onDelete('cascade');
            // menu_id nullable, tidak wajib relasi
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inference_results');
    }
};
