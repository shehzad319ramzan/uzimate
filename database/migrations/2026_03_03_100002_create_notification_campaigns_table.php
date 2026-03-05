<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('type', 50); // miss_you, special_day
            $table->string('name')->nullable();
            $table->text('message');
            $table->json('channels')->nullable(); // ["email", "push"]
            $table->string('target_type', 50)->default('all'); // all_inactive, selected, all
            $table->json('target_config')->nullable(); // inactive_days, user_ids, etc.
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->string('status', 20)->default('draft'); // draft, scheduled, sent, failed
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_campaigns');
    }
};
