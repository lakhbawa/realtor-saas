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
        Schema::create('sites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade')->index();
            $table->string('subdomain', 100)->unique();
            $table->string('custom_domain')->nullable()->unique();
            $table->boolean('custom_domain_verified')->default(false);
            $table->foreignId('template_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('site_name');
            $table->string('tagline', 500)->nullable();
            $table->string('logo_path', 500)->nullable();
            $table->string('headshot', 500)->nullable();
            $table->string('hero_image', 500)->nullable();
            $table->string('primary_color', 7)->default('#3B82F6');
            $table->string('phone', 50)->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('zip', 20)->nullable();
            $table->text('bio')->nullable();
            $table->string('license_number', 100)->nullable();
            $table->string('brokerage', 255)->nullable();
            $table->integer('years_experience')->nullable();
            $table->string('specialties', 500)->nullable();
            $table->integer('stat_properties_sold')->nullable();
            $table->integer('stat_sales_volume')->nullable();
            $table->integer('stat_happy_clients')->nullable();
            $table->decimal('stat_average_rating', 2, 1)->nullable();
            $table->string('stat_properties_sold_label', 100)->nullable();
            $table->string('stat_sales_volume_label', 100)->nullable();
            $table->string('stat_happy_clients_label', 100)->nullable();
            $table->string('stat_average_rating_label', 100)->nullable();
            $table->string('facebook', 500)->nullable();
            $table->string('instagram', 500)->nullable();
            $table->string('linkedin', 500)->nullable();
            $table->string('twitter', 500)->nullable();
            $table->string('youtube', 500)->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sites');
    }
};
