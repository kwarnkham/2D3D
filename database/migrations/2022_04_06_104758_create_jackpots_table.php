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
        Schema::create('jackpots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('two_digit_id')->constrained();
            $table->double('amount');
            $table->foreignId('jackpot_reward_id')->nullable()->constrained();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
            $table->unique('two_digit_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jackpots');
    }
};
