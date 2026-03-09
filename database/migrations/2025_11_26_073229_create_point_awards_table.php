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
        Schema::create('point_awards', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('merchant_id')->nullable();
            $table->uuid('site_id');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('awarded_by_id')->nullable()->constrained('users')->nullOnDelete();

            $table->unsignedInteger('points_earned');
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->foreign('merchant_id')
                ->references('id')
                ->on('merchants')
                ->cascadeOnDelete();

            $table->foreign('site_id')
                ->references('id')
                ->on('sites')
                ->cascadeOnDelete();

            $table->index(['user_id', 'merchant_id']);
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('point_awards');
    }
};
