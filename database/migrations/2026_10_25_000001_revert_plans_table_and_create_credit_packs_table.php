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
        // 1. Create credit_packs table for wallet top-up packages
        if (!Schema::hasTable('credit_packs')) {
            Schema::create('credit_packs', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100);
                $table->string('pack_code', 50)->unique();
                $table->string('product_id', 150)->unique();
                $table->unsignedInteger('credits');
                $table->decimal('price', 10, 2);
                $table->string('currency', 3)->default('INR');
                $table->boolean('is_active')->default(true)->index();
                $table->unsignedInteger('sort_order')->default(0)->index();
                $table->timestamps();
            });
        }

        // 2. Remove consumable wallet records from plans table so plans table only contains duration tiers
        if (Schema::hasTable('plans') && Schema::hasColumn('plans', 'credits')) {
            DB::table('plans')->whereNotNull('credits')->delete();
        }

        // 3. Drop foreign key constraint on payment_transactions and transition to credit_pack_id
        if (Schema::hasTable('payment_transactions')) {
            if (DB::getDriverName() === 'mysql') {
                try {
                    // Check if foreign key exists before dropping
                    $fkExists = DB::select("
                        SELECT CONSTRAINT_NAME 
                        FROM information_schema.TABLE_CONSTRAINTS 
                        WHERE TABLE_SCHEMA = DATABASE() 
                          AND TABLE_NAME = 'payment_transactions' 
                          AND CONSTRAINT_NAME = 'payment_transactions_plan_id_foreign'
                    ");

                    if (!empty($fkExists)) {
                        DB::statement("ALTER TABLE payment_transactions DROP FOREIGN KEY payment_transactions_plan_id_foreign");
                    }
                } catch (\Throwable $e) {
                    // Ignore if foreign key does not exist
                }
            }

            Schema::table('payment_transactions', function (Blueprint $table) {
                if (!Schema::hasColumn('payment_transactions', 'credit_pack_id')) {
                    $table->unsignedBigInteger('credit_pack_id')->nullable()->after('customer_id')->index();
                }
            });

            // If plan_id exists, migrate value to credit_pack_id
            if (Schema::hasColumn('payment_transactions', 'plan_id') && Schema::hasColumn('payment_transactions', 'credit_pack_id')) {
                DB::table('payment_transactions')
                    ->whereNull('credit_pack_id')
                    ->update(['credit_pack_id' => DB::raw('plan_id')]);
            }

            // Add foreign key constraint to credit_packs
            if (DB::getDriverName() === 'mysql') {
                try {
                    Schema::table('payment_transactions', function (Blueprint $table) {
                        $table->foreign('credit_pack_id')
                            ->references('id')
                            ->on('credit_packs')
                            ->onDelete('restrict');
                    });
                } catch (\Throwable $e) {
                    // Ignore if already exists
                }
            }
        }

        // 4. Revert plans table: drop extra columns added for credit packs
        if (Schema::hasTable('plans')) {
            Schema::table('plans', function (Blueprint $table) {
                $columnsToDrop = [
                    'plan_code',
                    'product_id',
                    'credits',
                    'currency',
                    'plan_type',
                    'is_active',
                    'sort_order',
                ];

                $existingColumns = [];
                foreach ($columnsToDrop as $col) {
                    if (Schema::hasColumn('plans', $col)) {
                        $existingColumns[] = $col;
                    }
                }

                if (!empty($existingColumns)) {
                    $table->dropColumn($existingColumns);
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_packs');
    }
};
