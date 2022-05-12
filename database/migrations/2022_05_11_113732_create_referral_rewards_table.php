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
        Schema::create('referral_rewards', function (Blueprint $table) {
            $table->id();
            $table->double('amount');
            $table->double('rate');
            $table->unsignedBigInteger('referrer_id');
            $table->unsignedBigInteger('referee_id');
            $table->foreignId('point_id')->constrained();
            $table->foreign('referrer_id')->references('id')->on('users');
            $table->foreign('referee_id')->references('id')->on('users');
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
        Schema::dropIfExists('referral_rewards');
    }
};
