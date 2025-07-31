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
        Schema::create('tb_menu', function (Blueprint $table) {
            $table->id(); // Ini akan menjadi kode makanan sekaligus primary key
            $table->string('nama');
            $table->text('deskripsi')->nullable(); // Kolom deskripsi baru
            $table->decimal('harga_seporsi', 10, 2);
            $table->enum('cita_rasa', ['asin', 'manis', 'pedas', 'asam', 'gurih', 'pahit']);
            $table->tinyInteger('rating')->unsigned()->between(1, 5); // Rating 1-5 bintang
            $table->string('gambar')->nullable(); // Untuk menyimpan path/nama file gambar
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_menu');
    }
};