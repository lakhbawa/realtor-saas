<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->string('headshot', 500)->nullable()->after('logo_path');
            $table->string('hero_image', 500)->nullable()->after('headshot');
            $table->string('youtube', 500)->nullable()->after('twitter');
            $table->string('license_number', 100)->nullable()->after('bio');
            $table->string('brokerage', 255)->nullable()->after('license_number');
            $table->integer('years_experience')->nullable()->after('brokerage');
            $table->string('specialties', 500)->nullable()->after('years_experience');
        });
    }

    public function down(): void
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->dropColumn(['headshot', 'hero_image', 'youtube', 'license_number', 'brokerage', 'years_experience', 'specialties']);
        });
    }
};
