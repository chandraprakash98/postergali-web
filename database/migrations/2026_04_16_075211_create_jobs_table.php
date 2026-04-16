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
    Schema::create('jobs', function (Blueprint $table) {
        $table->id('job_id');

        $table->string('temp_id')->nullable();

        $table->string('device_id')->nullable();
        $table->string('device_os')->nullable();

        $table->string('biz_name');
        $table->string('title'); // job title (NEW important)

        $table->text('description')->nullable();

        $table->string('job_type')->nullable(); // full-time, part-time
        $table->integer('salary')->nullable();

        $table->string('phone');
        $table->string('city')->nullable();
        $table->string('lat_long')->nullable();

        $table->string('category')->nullable();
        $table->string('sub_category')->nullable();

        $table->integer('duration_days')->default(7);

        $table->timestamp('approved_at')->nullable();
        $table->timestamp('expires_at')->nullable();

        $table->string('status')->default('pending');
        $table->string('status_note')->nullable();

        $table->string('reviewed_by')->nullable();

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};
