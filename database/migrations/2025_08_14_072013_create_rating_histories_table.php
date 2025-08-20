<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('rating_histories', function (Blueprint $table) {
            $table->id();
            $table->float('rating');
            $table->float('p1');
            $table->float('p2');
            $table->float('p3');
            $table->float('p4');
            $table->float('p5');
            $table->float('miu_rendah', 8, 3);
            $table->float('miu_sedang', 8, 3);
            $table->float('miu_tinggi', 8, 3);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('rating_histories');
    }
};