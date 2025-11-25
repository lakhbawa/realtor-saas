<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Starter',
                'slug' => 'starter',
                'description' => 'Perfect for new agents getting started',
                'features' => [
                    'Up to 5 property listings',
                    'Basic website template',
                    'Contact form',
                    '3 blog posts',
                    '5 testimonials',
                    'Email support',
                ],
                'limits' => [
                    'max_properties' => 5,
                    'max_blog_posts' => 3,
                    'max_pages' => 2,
                    'max_testimonials' => 5,
                    'max_images_per_property' => 5,
                    'can_use_custom_domain' => false,
                    'can_access_analytics' => false,
                    'can_remove_branding' => false,
                    'priority_support' => false,
                ],
                'monthly_price' => 1900, // $19.00
                'quarterly_price' => 4900, // $49.00 (save ~14%)
                'annual_price' => 15900, // $159.00 (save ~30%)
                'trial_days' => 14,
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 1,
            ],
            [
                'name' => 'Professional',
                'slug' => 'professional',
                'description' => 'For growing agents who need more features',
                'features' => [
                    'Up to 25 property listings',
                    'All website templates',
                    'Contact form with notifications',
                    'Unlimited blog posts',
                    'Unlimited testimonials',
                    'Basic analytics',
                    'Priority email support',
                ],
                'limits' => [
                    'max_properties' => 25,
                    'max_blog_posts' => -1, // unlimited
                    'max_pages' => 5,
                    'max_testimonials' => -1, // unlimited
                    'max_images_per_property' => 15,
                    'can_use_custom_domain' => false,
                    'can_access_analytics' => true,
                    'can_remove_branding' => false,
                    'priority_support' => true,
                ],
                'monthly_price' => 3900, // $39.00
                'quarterly_price' => 9900, // $99.00 (save ~15%)
                'annual_price' => 34900, // $349.00 (save ~25%)
                'trial_days' => 14,
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'description' => 'For top-producing agents and teams',
                'features' => [
                    'Unlimited property listings',
                    'All premium templates',
                    'Custom domain support',
                    'Advanced analytics dashboard',
                    'Remove platform branding',
                    'Unlimited everything',
                    'Priority phone & email support',
                    'Dedicated account manager',
                ],
                'limits' => [
                    'max_properties' => -1, // unlimited
                    'max_blog_posts' => -1, // unlimited
                    'max_pages' => -1, // unlimited
                    'max_testimonials' => -1, // unlimited
                    'max_images_per_property' => -1, // unlimited
                    'can_use_custom_domain' => true,
                    'can_access_analytics' => true,
                    'can_remove_branding' => true,
                    'priority_support' => true,
                ],
                'monthly_price' => 7900, // $79.00
                'quarterly_price' => 19900, // $199.00 (save ~16%)
                'annual_price' => 69900, // $699.00 (save ~26%)
                'trial_days' => 14,
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 3,
            ],
        ];

        foreach ($plans as $planData) {
            Plan::updateOrCreate(
                ['slug' => $planData['slug']],
                $planData
            );
        }

        $this->command->info('Plans seeded successfully!');
    }
}
