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
        Schema::create('two_digits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->tinyInteger('number');
            $table->double('amount');
            $table->foreignId('point_id')->constrained();
            $table->foreignId('two_digit_hit_id')->nullable()->constrained();
            $table->foreignId('jack_pot_reward_id')->nullable()->constrained();
            $table->timestamp('settled_at')->nullable();
            $table->timestamp('jack_potted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('two_digits');
    }
};
