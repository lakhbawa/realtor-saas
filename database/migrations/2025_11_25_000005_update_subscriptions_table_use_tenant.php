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
        Schema::table('subscriptions', function (Blueprint $table) {
            // Drop the user_id foreign key
            $table->dropForeign(['user_id']);

            // Add tenant_id
            $table->foreignId('tenant_id')->after('id')->constrained()->onDelete('cascade');

            // Remove user_id
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
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropColumn('tenant_id');

            $table->foreignId('user_id')->after('id')->constrained()->onDelete('cascade');
        });
    }
};
