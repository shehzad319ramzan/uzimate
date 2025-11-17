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
        Schema::create('site_users', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('merchant_id');
            $table->uuid('site_id');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->boolean('status')->default(true);

            $table->timestamps();

            $table->foreign('merchant_id')->references('id')->on('merchants')->cascadeOnDelete();
            $table->foreign('site_id')->references('id')->on('sites')->cascadeOnDelete();

            $table->unique(['site_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('site_users');
    }
};
