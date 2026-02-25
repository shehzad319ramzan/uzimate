<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invite_friends', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('referrer_id');
            $table->unsignedBigInteger('referred_user_id');
            $table->unsignedInteger('points_awarded')->default(0);
            $table->timestamps();

            $table->foreign('referrer_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('referred_user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invite_friends');
    }
};
