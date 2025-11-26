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
        Schema::create('offers', function (Blueprint $table) {
            $table->UUID('id')->primary();

            $table->UUID('merchant_id')->nullable();
            $table->UUID('site_id');
            $table->string('title');
            $table->integer('points_required');
            $table->date('expires_on')->nullable();
            $table->json('weekdays')->nullable();
            $table->string('description', 255)->nullable();
            $table->string('status')->default(1);
            
            $table->timestamps();

            $table->foreign('merchant_id')->references('id')->on('merchants')->onDelete('cascade');
            $table->foreign('site_id')->references('id')->on('sites')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('offers');
    }
};
