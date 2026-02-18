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
        Schema::create('spin_histories', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('merchant_id')->nullable();
            $table->uuid('site_id')->nullable();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('spin_result_type')->default('nothing'); // points, offer, nothing, discount
            $table->unsignedInteger('points_earned')->default(0);
            $table->uuid('offer_id')->nullable();
            $table->decimal('reward_value', 10, 2)->nullable();
            $table->unsignedInteger('spin_number')->default(1);
            $table->boolean('is_eligible')->default(true);
            $table->date('last_spin_date')->nullable();

            $table->text('notes')->nullable();
            $table->string('ip_address')->nullable();
            $table->json('device_info')->nullable();

            $table->timestamps();

            $table->foreign('merchant_id')
                ->references('id')
                ->on('merchants')
                ->cascadeOnDelete();

            $table->foreign('site_id')
                ->references('id')
                ->on('sites')
                ->cascadeOnDelete();

            $table->foreign('offer_id')
                ->references('id')
                ->on('offers')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('spin_histories');
    }
};
