<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->unsignedInteger('spin_spins_per_day')->nullable()->after('smtp_encryption');
            $table->uuid('spin_default_site_id')->nullable()->after('spin_spins_per_day');
            $table->unsignedTinyInteger('spin_outcome_nothing')->nullable();
            $table->unsignedTinyInteger('spin_outcome_points')->nullable();
            $table->unsignedTinyInteger('spin_outcome_offer')->nullable();
            $table->unsignedTinyInteger('spin_outcome_discount')->nullable();
            $table->unsignedInteger('spin_points_min')->nullable();
            $table->unsignedInteger('spin_points_max')->nullable();
            $table->unsignedTinyInteger('spin_discount_min')->nullable();
            $table->unsignedTinyInteger('spin_discount_max')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('spin_spins_per_day');
            $table->dropColumn('spin_default_site_id');
            $table->dropColumn('spin_outcome_nothing');
            $table->dropColumn('spin_outcome_points');
            $table->dropColumn('spin_outcome_offer');
            $table->dropColumn('spin_outcome_discount');
            $table->dropColumn('spin_points_min');
            $table->dropColumn('spin_points_max');
            $table->dropColumn('spin_discount_min');
            $table->dropColumn('spin_discount_max');
        });
    }
};
