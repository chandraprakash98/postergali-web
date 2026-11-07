<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('credit_settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('signup_credit', 10, 2)->default(1000.00);
            $table->decimal('otp_credit', 10, 2)->default(1000.00);
            $table->decimal('referral_credit', 10, 2)->default(100.00);
            $table->string('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_settings');
    }
};
