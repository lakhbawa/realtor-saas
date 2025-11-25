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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Starter", "Pro", "Enterprise"
            $table->string('slug')->unique();
            $table->text('description')->nullable();

            // Display features (shown to users)
            $table->json('features')->nullable();

            // Enforcement limits (used for access control)
            $table->json('limits')->nullable();

            // Stripe Price IDs for different billing cycles
            $table->string('stripe_monthly_price_id')->nullable();
            $table->string('stripe_quarterly_price_id')->nullable();
            $table->string('stripe_annual_price_id')->nullable();

            // Display prices (in cents)
            $table->integer('monthly_price')->default(0);
            $table->integer('quarterly_price')->default(0);
            $table->integer('annual_price')->default(0);

            // Trial period in days
            $table->integer('trial_days')->default(14);

            // Plan status and ordering
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('sort_order')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
