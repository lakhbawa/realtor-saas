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
            // Remove the old user_id unique constraint and foreign key
            $table->dropForeign(['user_id']);
            $table->dropUnique(['user_id']);

            // Add tenant_id
            $table->foreignId('tenant_id')->after('id')->constrained()->onDelete('cascade');

            // Keep user_id but make it nullable and non-unique (for tracking who last edited)
            $table->foreignId('user_id')->nullable()->change();
            $table->renameColumn('user_id', 'updated_by');

            // Add unique constraint on tenant_id (one site per tenant)
            $table->unique('tenant_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropUnique(['tenant_id']);
            $table->dropColumn('tenant_id');

            $table->renameColumn('updated_by', 'user_id');
            $table->foreignId('user_id')->change();
            $table->unique('user_id');
        });
    }
};
