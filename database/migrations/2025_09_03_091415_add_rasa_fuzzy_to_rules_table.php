<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rules', function (Blueprint $table) {
            if (!Schema::hasColumn('rules', 'rasa_fuzzy')) {
                $table->string('rasa_fuzzy')->after('rating_fuzzy');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rules', function (Blueprint $table) {
            if (Schema::hasColumn('rules', 'rasa_fuzzy')) {
                $table->dropColumn('rasa_fuzzy');
            }
        });
    }
};