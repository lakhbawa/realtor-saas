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
        Schema::table('properties', function (Blueprint $table) {
            $table->string('listing_status', 50)->default('for_sale')->after('status');
            $table->integer('year_built')->nullable()->after('square_feet');
            $table->json('features')->nullable()->after('year_built');
            $table->string('video_url', 500)->nullable()->after('featured_image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn(['listing_status', 'year_built', 'features', 'video_url']);
        });
    }
};
