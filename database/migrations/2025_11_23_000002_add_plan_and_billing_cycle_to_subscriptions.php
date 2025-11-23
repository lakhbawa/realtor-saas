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
            // Add plan relationship
            $table->foreignId('plan_id')->nullable()->after('user_id')->constrained()->nullOnDelete();

            // Add billing cycle
            $table->string('billing_cycle', 20)->default('monthly')->after('stripe_price_id');

            // Add missing fields that webhooks expect
            $table->timestamp('current_period_start')->nullable()->after('status');
            $table->timestamp('current_period_end')->nullable()->after('current_period_start');
            $table->timestamp('canceled_at')->nullable()->after('ends_at');

            // Add quantity for seat-based billing (future use)
            $table->integer('quantity')->default(1)->after('billing_cycle');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropForeign(['plan_id']);
            $table->dropColumn([
                'plan_id',
                'billing_cycle',
                'current_period_start',
                'current_period_end',
                'canceled_at',
                'quantity',
            ]);
        });
    }
};
