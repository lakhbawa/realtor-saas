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
        Schema::table('contact_submissions', function (Blueprint $table) {
            // Drop the old user_id foreign key
            $table->dropForeign(['user_id']);

            // Add tenant_id (this is who the contact submission is FOR)
            $table->foreignId('tenant_id')->after('id')->constrained()->onDelete('cascade');

            // Remove user_id as contact submissions don't need user tracking
            $table->dropColumn('user_id');

            // Add index on tenant_id
            $table->index('tenant_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contact_submissions', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropColumn('tenant_id');

            $table->foreignId('user_id')->after('id')->constrained()->onDelete('cascade');
        });
    }
};
