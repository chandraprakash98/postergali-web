<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->decimal('amount', 12, 2)->nullable()->after('salary');
        });

        Schema::table('offers', function (Blueprint $table) {
            $table->decimal('amount', 12, 2)->nullable()->after('offer_type');
        });
    }

    public function down(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropColumn('amount');
        });

        Schema::table('offers', function (Blueprint $table) {
            $table->dropColumn('amount');
        });
    }
};
