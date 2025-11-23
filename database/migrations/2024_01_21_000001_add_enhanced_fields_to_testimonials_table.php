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
            // Client details
            $table->string('client_photo', 500)->nullable()->after('client_name');
            $table->string('client_location', 255)->nullable()->after('client_photo');

            // Property relationship
            $table->foreignId('property_id')->nullable()->after('user_id')
                ->constrained()->onDelete('set null');

            // Transaction details
            $table->string('transaction_type', 50)->nullable()->after('content');
            $table->date('transaction_date')->nullable()->after('transaction_type');

            // Media
            $table->string('video_url', 500)->nullable()->after('rating');

            // Featured flag for highlighting
            $table->boolean('is_featured')->default(false)->after('is_published');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('testimonials', function (Blueprint $table) {
            $table->dropForeign(['property_id']);
            $table->dropColumn([
                'client_photo',
                'client_location',
                'property_id',
                'transaction_type',
                'transaction_date',
                'video_url',
                'is_featured',
            ]);
        });
    }
};
