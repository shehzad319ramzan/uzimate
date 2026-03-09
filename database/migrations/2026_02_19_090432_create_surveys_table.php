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
        Schema::create('surveys', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedInteger('points')->default(0);
            $table->unsignedTinyInteger('estimated_minutes')->default(1);
            $table->uuid('merchant_id')->nullable();
            $table->string('status', 1)->default('1');
            $table->timestamps();

            $table->foreign('merchant_id')->references('id')->on('merchants')->nullOnDelete();

            $table->index('merchant_id');
            $table->index('status');
        });

        Schema::create('survey_questions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('survey_id');
            $table->text('question_text');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('survey_id')->references('id')->on('surveys')->cascadeOnDelete();
        });

        Schema::create('survey_question_options', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('survey_question_id');
            $table->string('option_text');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('survey_question_id')->references('id')->on('survey_questions')->cascadeOnDelete();
        });

        Schema::create('survey_responses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('survey_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->foreign('survey_id')->references('id')->on('surveys')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->unique(['survey_id', 'user_id']);
        });

        Schema::create('survey_answers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('survey_response_id');
            $table->uuid('survey_question_id');
            $table->uuid('survey_question_option_id')->nullable();
            $table->timestamps();

            $table->foreign('survey_response_id')->references('id')->on('survey_responses')->cascadeOnDelete();
            $table->foreign('survey_question_id')->references('id')->on('survey_questions')->cascadeOnDelete();
            $table->foreign('survey_question_option_id')->references('id')->on('survey_question_options')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('survey_answers');
        Schema::dropIfExists('survey_responses');
        Schema::dropIfExists('survey_question_options');
        Schema::dropIfExists('survey_questions');
        Schema::dropIfExists('surveys');
    }
};
