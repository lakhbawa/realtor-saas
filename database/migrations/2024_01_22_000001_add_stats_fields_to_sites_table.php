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
            // Stats fields for homepage display
            $table->integer('stat_properties_sold')->nullable()->after('specialties');
            $table->integer('stat_sales_volume')->nullable()->after('stat_properties_sold');
            $table->integer('stat_happy_clients')->nullable()->after('stat_sales_volume');
            $table->decimal('stat_average_rating', 2, 1)->nullable()->after('stat_happy_clients');

            // Custom stat labels (optional)
            $table->string('stat_properties_sold_label', 100)->nullable()->after('stat_average_rating');
            $table->string('stat_sales_volume_label', 100)->nullable()->after('stat_properties_sold_label');
            $table->string('stat_happy_clients_label', 100)->nullable()->after('stat_sales_volume_label');
            $table->string('stat_average_rating_label', 100)->nullable()->after('stat_happy_clients_label');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->dropColumn([
                'stat_properties_sold',
                'stat_sales_volume',
                'stat_happy_clients',
                'stat_average_rating',
                'stat_properties_sold_label',
                'stat_sales_volume_label',
                'stat_happy_clients_label',
                'stat_average_rating_label',
            ]);
        });
    }
};
