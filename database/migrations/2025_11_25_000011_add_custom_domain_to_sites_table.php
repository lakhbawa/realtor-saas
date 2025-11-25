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
        Schema::table('sites', function (Blueprint $table) {
            // Add custom domain support (one custom domain per site for now)
            $table->string('custom_domain')->nullable()->unique()->after('subdomain');
            $table->boolean('custom_domain_verified')->default(false)->after('custom_domain');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->dropUnique(['custom_domain']);
            $table->dropColumn(['custom_domain', 'custom_domain_verified']);
        });
    }
};
