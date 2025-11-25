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
        Schema::table('users', function (Blueprint $table) {
            // Drop subdomain and subscription-related fields
            // These now belong to the Tenant model
            $table->dropUnique(['subdomain']);
            $table->dropColumn([
                'subdomain',
                'stripe_customer_id',
                'stripe_subscription_id',
                'subscription_status',
                'trial_ends_at'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('subdomain', 100)->unique();
            $table->string('stripe_customer_id')->nullable()->index();
            $table->string('stripe_subscription_id')->nullable()->index();
            $table->string('subscription_status', 50)->default('incomplete')->index();
            $table->timestamp('trial_ends_at')->nullable();
        });
    }
};
