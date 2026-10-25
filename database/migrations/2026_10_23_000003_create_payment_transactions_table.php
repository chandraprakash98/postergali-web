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
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->uuid('transaction_uuid')->unique();
            $table->string('customer_id', 50)->index();
            $table->unsignedBigInteger('credit_pack_id')->nullable()->index();
            $table->unsignedBigInteger('plan_id')->nullable()->index();

            $table->string('payment_provider', 30)->index(); // RAZORPAY or APPLE_IAP
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('INR');
            $table->unsignedInteger('credits_to_add');

            // Razorpay identifiers
            $table->string('provider_order_id')->nullable()->index();
            $table->string('provider_payment_id')->nullable()->unique();

            // Apple IAP identifiers
            $table->string('provider_transaction_id')->nullable()->unique();
            $table->string('provider_product_id')->nullable();
            $table->string('provider_original_transaction_id')->nullable()->index();

            // Status and Verification
            $table->string('status', 30)->default('PENDING')->index(); // PENDING, SUCCESS, FAILED, REFUNDED
            $table->string('verification_status', 50)->default('UNVERIFIED'); // UNVERIFIED, VERIFIED, FAILED

            // Failure handling
            $table->string('failure_code', 50)->nullable();
            $table->text('failure_reason')->nullable();

            // Audit & Idempotency
            $table->string('idempotency_key', 100)->nullable()->index();
            $table->json('raw_payload')->nullable();

            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->foreign('customer_id')
                ->references('customer_id')
                ->on('customers')
                ->onDelete('cascade');

            $table->foreign('credit_pack_id')
                ->references('id')
                ->on('credit_packs')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
