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
        Schema::create('merchants', function (Blueprint $table) {
            $table->UUID('id')->primary();

            $table->string('name');
            $table->integer('max_sites')->default(1);
            $table->integer('spin_after_days')->default(1);
            $table->integer('scan_after_hours')->default(6);
            $table->boolean('use_other_merchant_points')->default(false);
            $table->string('status')->default(1);

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
        Schema::dropIfExists('merchants');
    }
};
