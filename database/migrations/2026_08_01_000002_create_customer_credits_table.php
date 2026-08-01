<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_credits', function (Blueprint $table) {
            $table->id();
            $table->string('customer_id')->unique();
            $table->decimal('balance', 12, 2)->default(1000);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_credits');
    }
};
