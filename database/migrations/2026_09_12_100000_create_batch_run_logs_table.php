<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('batch_run_logs', function (Blueprint $table) {
            $table->id();
            $table->timestamp('ran_at')->useCurrent();
            $table->string('status', 20)->default('success'); // success | disabled | error
            $table->boolean('dry_run')->default(false);
            $table->unsignedInteger('day_before_jobs')->default(0);
            $table->unsignedInteger('day_before_offers')->default(0);
            $table->unsignedInteger('on_expiry_jobs')->default(0);
            $table->unsignedInteger('on_expiry_offers')->default(0);
            $table->unsignedInteger('notifications_sent')->default(0);
            $table->unsignedInteger('skipped_no_token')->default(0);
            $table->unsignedInteger('duration_ms')->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('batch_run_logs');
    }
};
