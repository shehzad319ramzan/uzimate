<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reward_rules', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('merchant_id')->nullable();
            $table->string('action_type')->index();
            $table->string('label');
            $table->unsignedInteger('points')->nullable();
            $table->string('trigger_condition')->default('every_time');
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->foreign('merchant_id')
                ->references('id')
                ->on('merchants')
                ->cascadeOnDelete();

            $table->unique(['merchant_id', 'action_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reward_rules');
    }
};
