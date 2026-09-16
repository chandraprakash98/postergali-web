<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('otp_verifications', function (Blueprint $table): void {
            $table->id();
            $table->string('mobile', 15)->index();
            $table->string('code_hash');
            $table->timestamp('expires_at')->index();
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->timestamp('verified_at')->nullable();
            $table->string('provider_token')->nullable();
            $table->timestamps();

            $table->index(['mobile', 'verified_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('otp_verifications');
    }
};