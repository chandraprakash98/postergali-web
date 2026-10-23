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
        Schema::table('plans', function (Blueprint $table) {
            if (Schema::hasColumn('plans', 'duration')) {
                $table->string('duration', 30)->nullable()->change();
            }

            if (!Schema::hasColumn('plans', 'plan_code')) {
                $table->string('plan_code', 50)->nullable()->unique()->after('plan_title');
            }

            if (!Schema::hasColumn('plans', 'product_id')) {
                $table->string('product_id', 150)->nullable()->unique()->after('plan_code');
            }

            if (!Schema::hasColumn('plans', 'credits')) {
                $table->unsignedInteger('credits')->nullable()->after('product_id');
            }

            if (!Schema::hasColumn('plans', 'currency')) {
                $table->string('currency', 3)->default('INR')->after('price');
            }

            if (!Schema::hasColumn('plans', 'plan_type')) {
                $table->string('plan_type', 30)->default('CONSUMABLE')->after('currency');
            }

            if (!Schema::hasColumn('plans', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('plan_type');
            }

            if (!Schema::hasColumn('plans', 'sort_order')) {
                $table->unsignedInteger('sort_order')->default(0)->after('is_active');
            }
        });

        // Set existing plans as POSTER_DURATION tiers for backward compatibility
        DB::table('plans')
            ->whereNotNull('duration')
            ->whereNull('credits')
            ->update([
                'plan_type' => 'POSTER_DURATION',
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $columnsToDrop = [];
            foreach (['plan_code', 'product_id', 'credits', 'currency', 'plan_type', 'is_active', 'sort_order'] as $col) {
                if (Schema::hasColumn('plans', $col)) {
                    $columnsToDrop[] = $col;
                }
            }

            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
