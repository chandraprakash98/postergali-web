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
        Schema::create('offers', function (Blueprint $table) {
            $table->id();

            $table->string('device_id');
            $table->string('temp_id')->nullable();
            $table->string('device_os');

            $table->string('master_category');
            $table->string('subcategory')->nullable();

            $table->string('business_name');
            $table->string('offer_details');
            $table->string('offer_type')->nullable();

            $table->json('media')->nullable();

            $table->string('mobile_number');

            $table->decimal('latitude', 10, 6);
            $table->decimal('longitude', 10, 6);
            $table->string('city');

            $table->timestamp('approved_at')->nullable();
            $table->timestamp('expires_at')->nullable();

            $table->string('status')->default('pending');
            $table->string('status_note')->nullable();

            $table->unsignedInteger('view_count')->default(0);

            $table->string('reviewed_by')->nullable();

            $table->boolean('is_steal_deal')->default(false);

            $table->tinyInteger('ad_boost_poster')->nullable();

            $table->string('plan_id');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};
