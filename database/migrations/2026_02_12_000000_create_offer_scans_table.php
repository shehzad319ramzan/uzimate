<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('offer_scans', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('merchant_id')->nullable();
            $table->uuid('site_id');
            $table->uuid('offer_id');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->unsignedInteger('points_earned')->default(0);

            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->json('metadata')->nullable();

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
                ->cascadeOnDelete();

            $table->index(['user_id', 'offer_id']);
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offer_scans');
    }
};
