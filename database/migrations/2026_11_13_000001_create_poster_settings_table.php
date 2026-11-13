<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('poster_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('daily_limit_per_phone')->default(2);
            $table->string('updated_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('poster_settings');
    }
};
