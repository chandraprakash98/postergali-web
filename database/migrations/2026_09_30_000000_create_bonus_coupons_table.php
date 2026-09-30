<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bonus_coupons', function (Blueprint $table) {
            $table->id();
            $table->string('influencer_bonus_coupon_id')->unique();
            $table->decimal('bonus_coupon_poster_credit', 12, 2);
            $table->string('bonus_coupon_status')->default('active');
            $table->string('influencer_name');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bonus_coupons');
    }
};