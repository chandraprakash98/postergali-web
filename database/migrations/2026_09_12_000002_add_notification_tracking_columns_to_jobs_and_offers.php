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
        Schema::table('jobs', function (Blueprint $table) {
            $table->timestamp('day_before_expiry_notified_at')->nullable()->after('expiry_notified_at')->index();
            $table->timestamp('expired_notified_at')->nullable()->after('day_before_expiry_notified_at')->index();
            $table->unsignedInteger('last_view_milestone_notified')->default(0)->after('view_count');
        });

        Schema::table('offers', function (Blueprint $table) {
            $table->timestamp('day_before_expiry_notified_at')->nullable()->after('expiry_notified_at')->index();
            $table->timestamp('expired_notified_at')->nullable()->after('day_before_expiry_notified_at')->index();
            $table->unsignedInteger('last_view_milestone_notified')->default(0)->after('view_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropIndex(['day_before_expiry_notified_at']);
            $table->dropIndex(['expired_notified_at']);
            $table->dropColumn([
                'day_before_expiry_notified_at',
                'expired_notified_at',
                'last_view_milestone_notified',
            ]);
        });

        Schema::table('offers', function (Blueprint $table) {
            $table->dropIndex(['day_before_expiry_notified_at']);
            $table->dropIndex(['expired_notified_at']);
            $table->dropColumn([
                'day_before_expiry_notified_at',
                'expired_notified_at',
                'last_view_milestone_notified',
            ]);
        });
    }
};
