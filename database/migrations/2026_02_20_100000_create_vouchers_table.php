<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('merchant_id');

            $table->string('title');
            $table->longText('description')->nullable();
            $table->longText('terms_and_conditions')->nullable();
            $table->unsignedInteger('points_required')->default(0);
            $table->date('valid_until')->nullable();
            $table->string('status', 1)->default('1');
            $table->timestamps();

            $table->foreign('merchant_id')
                ->references('id')
                ->on('merchants')
                ->onDelete('cascade');

            $table->index('merchant_id');
            $table->index('status');
            $table->index('valid_until');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
