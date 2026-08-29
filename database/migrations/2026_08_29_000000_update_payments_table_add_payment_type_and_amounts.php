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
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'payment_type')) {
                $table->string('payment_type', 50)->default('FULL_UPI')->after('item_type');
            }
            if (!Schema::hasColumn('payments', 'total_amount')) {
                $table->decimal('total_amount', 12, 2)->default(0.00)->after('payment_type');
            }
            if (!Schema::hasColumn('payments', 'razorpay_amount')) {
                $table->decimal('razorpay_amount', 12, 2)->default(0.00)->after('total_amount');
            }
            if (!Schema::hasColumn('payments', 'credit_amount')) {
                $table->decimal('credit_amount', 12, 2)->default(0.00)->after('razorpay_amount');
            }
            if (!Schema::hasColumn('payments', 'razorpay_order_id')) {
                $table->string('razorpay_order_id')->nullable()->after('credit_amount');
            }
            if (!Schema::hasColumn('payments', 'razorpay_payment_id')) {
                $table->string('razorpay_payment_id')->nullable()->after('razorpay_order_id');
            }
            if (!Schema::hasColumn('payments', 'payment_status')) {
                $table->string('payment_status', 50)->default('COMPLETED')->after('razorpay_payment_id');
            }
        });

        // Backfill existing payment records to ensure no data loss/corruption
        $existingPayments = DB::table('payments')->get();
        foreach ($existingPayments as $payment) {
            $amount = isset($payment->amount) ? (float) $payment->amount : 0.00;
            $creditMode = isset($payment->credit_mode) ? strtolower((string) $payment->credit_mode) : 'full_upi';

            $paymentType = match ($creditMode) {
                'full_credit' => 'FULL_CREDIT',
                'semi' => 'SEMI',
                default => 'FULL_UPI',
            };

            $razorpayAmount = match ($paymentType) {
                'FULL_CREDIT' => 0.00,
                'SEMI' => $amount, // If legacy semi didn't split, retain amount as razorpay or split appropriately
                default => $amount,
            };

            $creditAmount = match ($paymentType) {
                'FULL_CREDIT' => $amount,
                default => 0.00,
            };

            DB::table('payments')
                ->where('id', $payment->id)
                ->update([
                    'payment_type' => $paymentType,
                    'total_amount' => $amount,
                    'razorpay_amount' => $razorpayAmount,
                    'credit_amount' => $creditAmount,
                    'payment_status' => 'COMPLETED',
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $columnsToDrop = [];
            foreach (['payment_type', 'total_amount', 'razorpay_amount', 'credit_amount', 'razorpay_order_id', 'razorpay_payment_id', 'payment_status'] as $col) {
                if (Schema::hasColumn('payments', $col)) {
                    $columnsToDrop[] = $col;
                }
            }

            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
