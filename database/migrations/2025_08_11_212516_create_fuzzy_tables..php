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
        Schema::create('fuzzy_inputs', function (Blueprint $table) {
            $table->id(); // Ini sudah membuat kolom 'id' sebagai primary key
            $table->integer('harga');
            $table->integer('p1');
            $table->integer('p2');
            $table->integer('p3');
            $table->integer('p4');
            $table->integer('p5');
            $table->float('miu_murah', 8, 3);
            $table->float('miu_sedang', 8, 3);
            $table->float('miu_mahal', 8, 3);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fuzzy_inputs');
    }
};
