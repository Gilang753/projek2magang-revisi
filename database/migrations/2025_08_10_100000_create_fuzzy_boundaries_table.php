<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fuzzy_boundaries', function (Blueprint $table) {
            $table->id();
            $table->integer('batas1');
            $table->integer('batas2');
            $table->integer('batas3');
            $table->integer('batas4');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fuzzy_boundaries');
    }
};
