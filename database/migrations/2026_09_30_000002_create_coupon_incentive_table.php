<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupon_incentive_table', function (Blueprint $table) {
            $table->id();
            $table->string('influencer_bonus_coupon_id');
            $table->unsignedBigInteger('ad_id');
            $table->string('customer_id');
            $table->string('mobile_number');
            $table->date('usage_date');
            $table->enum('payment_method', ['free', 'paid']);
            $table->unsignedInteger('poster_count')->default(1);
            $table->date('incentive_processed_date')->nullable();
            $table->timestamps();

            $table->index('influencer_bonus_coupon_id');
            $table->index('customer_id');
            $table->index('usage_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupon_incentive_table');
    }
};