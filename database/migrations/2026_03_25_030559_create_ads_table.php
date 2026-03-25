<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('ads', function (Blueprint $table) {
        $table->id('ad_id');

        $table->string('device_id', 50);
        $table->string('device_os', 20)->nullable();

        $table->timestamp('created_t')->nullable();

        $table->string('master_category', 40);
        $table->string('ad_subcategory', 40)->nullable();

        $table->string('business_name', 100);
        $table->string('ad_description', 200)->nullable();

        $table->string('ad_media')->nullable();

        $table->bigInteger('mobile')->nullable();

        $table->string('location')->nullable(); // lat,long

        $table->string('city', 50)->nullable();
        $table->string('ad_range', 50)->nullable();

        $table->integer('add_duration')->nullable();

        $table->timestamp('approved_at')->nullable();
        $table->timestamp('expired_at')->nullable();

        $table->string('status', 20)->default('pending');

        $table->string('ad_status_comment', 200)->nullable();
        $table->string('reviewed_by', 50)->nullable();

        $table->boolean('ad_steal_deal')->default(false);
        $table->integer('ad_boost_poster')->nullable();

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ads');
    }
};
