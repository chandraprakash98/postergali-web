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
        Schema::create('credit_ledger', function (Blueprint $table) {
            $table->id();
            $table->uuid('ledger_uuid')->unique();
            $table->string('customer_id', 50)->index();
            $table->unsignedBigInteger('payment_transaction_id')->nullable()->index();

            $table->string('entry_type', 20)->index(); // CREDIT, DEBIT, REVERSAL
            $table->string('transaction_category', 50)->index(); // WALLET_TOPUP, POSTER_PURCHASE, REFUND, BONUS, ADMIN_ADJUSTMENT

            $table->decimal('credit_amount', 12, 2)->default(0.00);
            $table->decimal('debit_amount', 12, 2)->default(0.00);
            $table->decimal('balance_before', 12, 2);
            $table->decimal('balance_after', 12, 2);

            $table->string('reference_type', 50)->nullable();
            $table->string('reference_id', 100)->nullable();
            $table->string('description', 255);
            $table->string('idempotency_key', 100)->nullable()->unique();
            $table->string('created_by', 100)->nullable();

            $table->timestamp('created_at')->useCurrent();

            $table->foreign('customer_id')
                ->references('customer_id')
                ->on('customers')
                ->onDelete('cascade');

            $table->foreign('payment_transaction_id')
                ->references('id')
                ->on('payment_transactions')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_ledger');
    }
};
