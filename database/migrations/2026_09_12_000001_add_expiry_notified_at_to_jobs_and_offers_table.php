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
            $table->timestamp('expiry_notified_at')->nullable()->after('expires_at')->index();
        });

        Schema::table('offers', function (Blueprint $table) {
            $table->timestamp('expiry_notified_at')->nullable()->after('expires_at')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropIndex(['expiry_notified_at']);
            $table->dropColumn('expiry_notified_at');
        });

        Schema::table('offers', function (Blueprint $table) {
            $table->dropIndex(['expiry_notified_at']);
            $table->dropColumn('expiry_notified_at');
        });
    }
};
