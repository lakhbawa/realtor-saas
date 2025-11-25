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
        Schema::table('testimonials', function (Blueprint $table) {
            // Drop the old user_id foreign key
            $table->dropForeign(['user_id']);

            // Add tenant_id
            $table->foreignId('tenant_id')->after('id')->constrained()->onDelete('cascade');

            // Rename user_id to created_by for audit trail
            $table->renameColumn('user_id', 'created_by');

            // Add updated_by for tracking who last modified
            $table->foreignId('updated_by')->nullable()->after('created_by')->constrained('users')->nullOnDelete();

            // Add index on tenant_id
            $table->index('tenant_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('testimonials', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropForeign(['updated_by']);
            $table->dropColumn(['tenant_id', 'updated_by']);

            $table->renameColumn('created_by', 'user_id');
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->change();
        });
    }
};
