<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add composite indexes on latitude, longitude for faster geo queries
        // These work well with bounding box queries and Haversine calculations
        Schema::table('jobs', function (Blueprint $table) {
            $table->index(['latitude', 'longitude']);
            $table->index('status');
            $table->index('expires_at');
            $table->index('city');
        });
        
        Schema::table('offers', function (Blueprint $table) {
            $table->index(['latitude', 'longitude']);
            $table->index('status');
            $table->index('expires_at');
            $table->index('city');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropIndex('jobs_latitude_longitude_index');
            $table->dropIndex('jobs_status_index');
            $table->dropIndex('jobs_expires_at_index');
            $table->dropIndex('jobs_city_index');
        });
        
        Schema::table('offers', function (Blueprint $table) {
            $table->dropIndex('offers_latitude_longitude_index');
            $table->dropIndex('offers_status_index');
            $table->dropIndex('offers_expires_at_index');
            $table->dropIndex('offers_city_index');
        });
    }
};
