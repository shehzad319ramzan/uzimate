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
        Schema::create('customer_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('merchant_id')->nullable();
            $table->uuid('site_id')->nullable();
            $table->uuid('offer_id')->nullable();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('action_type')->index();
            $table->string('action_category')->default('system')->index();
            $table->text('description');

            $table->integer('points_affected')->nullable();
            $table->integer('points_balance_before')->nullable();
            $table->integer('points_balance_after')->nullable();

            $table->string('related_model_type')->nullable();
            $table->uuid('related_model_id')->nullable();

            $table->json('metadata')->nullable();

            $table->foreignId('performed_by_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->json('location_data')->nullable();

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
            $table->index('offer_id');
            $table->index(['user_id', 'created_at']);
            $table->index(['merchant_id', 'created_at']);
            $table->index(['user_id', 'merchant_id']);
            $table->index(['action_type', 'created_at']);
            $table->index(['action_category', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_logs');
    }
};
