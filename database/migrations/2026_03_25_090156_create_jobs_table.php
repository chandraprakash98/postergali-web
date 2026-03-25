<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->id('ad_id'); // Primary key
            $table->string('device_id')->nullable();
            $table->string('device_os')->nullable();
            $table->timestamp('ad_creation_timestamp')->useCurrent();
            $table->string('master_category', 20)->nullable();
            $table->string('ad_subcategory', 20)->nullable();
            $table->string('business_name', 100)->nullable();
            $table->string('ad_description', 200)->nullable();
            $table->string('ad_job_type', 20)->nullable();
            $table->integer('ad_job_salary')->nullable();
            $table->string('phone_number', 15)->nullable();
            $table->string('location_latlong')->nullable();
            $table->string('city', 50)->nullable();
            $table->string('ad_area_coverage', 50)->nullable();
            $table->integer('ad_days')->nullable();
            $table->timestamp('ad_approval_timestamp')->nullable();
            $table->timestamp('ad_expiry_timestamp')->nullable();
            $table->string('ad_status', 20)->default('Pending');
            $table->string('ad_status_comments', 200)->nullable();
            $table->string('ad_reviewed_by', 50)->nullable();
            $table->integer('ad_boost_poster')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};