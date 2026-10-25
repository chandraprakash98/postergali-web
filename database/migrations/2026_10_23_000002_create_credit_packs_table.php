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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_packs');
    }
};
