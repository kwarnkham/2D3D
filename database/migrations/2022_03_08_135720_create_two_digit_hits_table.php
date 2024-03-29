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
        Schema::create('two_digit_hits', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('number');
            $table->tinyInteger('rate');
            $table->timestamp('day');
            $table->boolean('morning');
            $table->double('set');
            $table->double('value');
            $table->timestamps();
            $table->unique(['day', 'morning']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('two_digit_hits');
    }
};
