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
        Schema::create('jack_pots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jack_potable_id');
            $table->string('jack_potable_type');
            $table->double('amount');
            $table->timestamps();
            $table->unique(['jack_potable_id', 'jack_potable_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jack_pots');
    }
};
