<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ugc_reports', function (Blueprint $table) {
            $table->id();
            $table->string('content_type', 20);
            $table->unsignedBigInteger('content_id');
            $table->string('reason');
            $table->text('details')->nullable();
            $table->string('author_id')->nullable();
            $table->timestamps();

            $table->index(['content_type', 'content_id']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ugc_reports');
    }
};
