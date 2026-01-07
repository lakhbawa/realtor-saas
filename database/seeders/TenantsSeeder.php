<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Tenant;
use App\Models\Site;
use App\Models\Template;
use App\Models\Plan;
use Illuminate\Database\Seeder;

class TenantsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating tenants with custom domains for testing...');

        $template = Template::firstOrCreate(
            ['slug' => 'modern'],
            [
                'name' => 'Modern',
                'description' => 'A modern, clean template',
                'is_active' => true,
                'sort_order' => 0,
            ]
        );

        $tenants = [
            [
                'name' => 'Premium Realty Group',
                'user' => [
                    'name' => 'Sarah Johnson',
                    'email' => 'sarah@premiumrealty.com',
                    'password' => 'password',
                ],
                'subscription_status' => 'active',
                'sites' => [
                    [
                        'subdomain' => 'premiumrealty',
                        'site_name' => 'Premium Realty Group',
                        'custom_domain' => 'premiumrealty.com',
                        'custom_domain_verified' => true,
                    ],
                ],
            ],
            [
                'name' => 'Downtown Properties LLC',
                'user' => [
                    'name' => 'Michael Chen',
                    'email' => 'michael@downtownprops.com',
                    'password' => 'password',
                ],
                'subscription_status' => 'active',
                'sites' => [
                    [
                        'subdomain' => 'downtownprops',
                        'site_name' => 'Downtown Properties',
                        'custom_domain' => 'downtownproperties.com',
                        'custom_domain_verified' => true,
                    ],
                ],
            ],
            [
                'name' => 'Coastal Living Realty',
                'user' => [
                    'name' => 'Jennifer Martinez',
                    'email' => 'jen@coastalliving.com',
                    'password' => 'password',
                ],
                'subscription_status' => 'trialing',
                'trial_ends_at' => now()->addDays(7),
                'sites' => [
                    [
                        'subdomain' => 'coastalliving',
                        'site_name' => 'Coastal Living Realty',
                        'custom_domain' => 'coastallivingrealty.com',
                        'custom_domain_verified' => true,
                    ],
                ],
            ],
            [
                'name' => 'Mountain View Estates',
                'user' => [
                    'name' => 'David Thompson',
                    'email' => 'david@mountainview.com',
                    'password' => 'password',
                ],
                'subscription_status' => 'active',
                'sites' => [
                    [
                        'subdomain' => 'mountainview',
                        'site_name' => 'Mountain View Estates',
                        'custom_domain' => 'mountainviewestates.com',
                        'custom_domain_verified' => false, // Pending verification
                    ],
                ],
            ],
            [
                'name' => 'Urban Homes Realty',
                'user' => [
                    'name' => 'Lisa Anderson',
                    'email' => 'lisa@urbanhomes.com',
                    'password' => 'password',
                ],
                'subscription_status' => 'active',
                'sites' => [
                    [
                        'subdomain' => 'urbanhomes',
                        'site_name' => 'Urban Homes Realty',
                        'custom_domain' => null, // No custom domain
                        'custom_domain_verified' => false,
                    ],
                ],
            ],
            [
                'name' => 'Luxury Living Properties',
                'user' => [
                    'name' => 'Robert Wilson',
                    'email' => 'robert@luxuryliving.com',
                    'password' => 'password',
                ],
                'subscription_status' => 'inactive', // Inactive subscription
                'sites' => [
                    [
                        'subdomain' => 'luxuryliving',
                        'site_name' => 'Luxury Living Properties',
                        'custom_domain' => 'luxurylivingproperties.com',
                        'custom_domain_verified' => true, // Verified but subscription inactive
                    ],
                ],
            ],
            [
                'name' => 'Sunset Realty Group',
                'user' => [
                    'name' => 'Amanda Brown',
                    'email' => 'amanda@sunsetrealty.com',
                    'password' => 'password',
                ],
                'subscription_status' => 'trialing',
                'trial_ends_at' => now()->addDays(14),
                'sites' => [
                    [
                        'subdomain' => 'sunsetrealty',
                        'site_name' => 'Sunset Realty Group',
                        'custom_domain' => 'sunsetrealty.com',
                        'custom_domain_verified' => true,
                    ],
                ],
            ],
            [
                'name' => 'Elite Properties Inc',
                'user' => [
                    'name' => 'James Miller',
                    'email' => 'james@eliteproperties.com',
                    'password' => 'password',
                ],
                'subscription_status' => 'canceled',
                'sites' => [
                    [
                        'subdomain' => 'eliteproperties',
                        'site_name' => 'Elite Properties Inc',
                        'custom_domain' => 'eliteproperties.com',
                        'custom_domain_verified' => true,
                    ],
                ],
            ],
            [
                'name' => 'Green Valley Homes',
                'user' => [
                    'name' => 'Patricia Davis',
                    'email' => 'patricia@greenvalley.com',
                    'password' => 'password',
                ],
                'subscription_status' => 'active',
                'sites' => [
                    [
                        'subdomain' => 'greenvalley',
                        'site_name' => 'Green Valley Homes',
                        'custom_domain' => 'greenvalleyhomes.com',
                        'custom_domain_verified' => true,
                    ],
                ],
            ],
            [
                'name' => 'Riverside Realty',
                'user' => [
                    'name' => 'Christopher Lee',
                    'email' => 'chris@riverside.com',
                    'password' => 'password',
                ],
                'subscription_status' => 'active',
                'sites' => [
                    [
                        'subdomain' => 'riverside',
                        'site_name' => 'Riverside Realty',
                        'custom_domain' => 'riversiderealty.com',
                        'custom_domain_verified' => true,
                    ],
                ],
            ],
        ];

        foreach ($tenants as $tenantData) {
            $this->command->info("Creating tenant: {$tenantData['name']}");

            $user = User::updateOrCreate(
                ['email' => $tenantData['user']['email']],
                [
                    'name' => $tenantData['user']['name'],
                    'password' => $tenantData['user']['password'],
                    'is_admin' => false,
                ]
            );

            $tenant = Tenant::updateOrCreate(
                ['name' => $tenantData['name']],
                [
                    'subscription_status' => $tenantData['subscription_status'],
                    'trial_ends_at' => $tenantData['trial_ends_at'] ?? null,
                ]
            );

            $tenant->users()->syncWithoutDetaching([
                $user->id => ['role' => 'owner']
            ]);

            foreach ($tenantData['sites'] as $siteData) {
                $site = Site::updateOrCreate(
                    [
                        'tenant_id' => $tenant->id,
                        'subdomain' => $siteData['subdomain'],
                    ],
                    [
                        'template_id' => $template->id,
                        'updated_by' => $user->id,
                        'site_name' => $siteData['site_name'],
                        'tagline' => 'Your Trusted Real Estate Partner',
                        'email' => $tenantData['user']['email'],
                        'phone' => '(555) ' . rand(100, 999) . '-' . rand(1000, 9999),
                        'address' => rand(100, 9999) . ' Main Street',
                        'city' => 'Los Angeles',
                        'state' => 'CA',
                        'zip' => '90' . rand(100, 999),
                        'bio' => 'Dedicated to helping you find your dream home with personalized service and local market expertise.',
                        'custom_domain' => $siteData['custom_domain'],
                        'custom_domain_verified' => $siteData['custom_domain_verified'],
                        'primary_color' => '#' . substr(md5($siteData['subdomain']), 0, 6),
                        'is_published' => true,
                    ]
                );

                $statusIcon = match(true) {
                    $site->hasCustomDomain() => '✓',
                    $site->customDomainPendingVerification() => '⏳',
                    default => '○',
                };

                $statusText = match(true) {
                    $site->hasCustomDomain() => 'verified',
                    $site->customDomainPendingVerification() => 'pending',
                    default => 'subdomain only',
                };

                $this->command->line("  {$statusIcon} Site: {$siteData['subdomain']} ({$statusText}) - Subscription: {$tenantData['subscription_status']}");
            }
        }

        $this->command->newLine();
        $this->command->info('Summary:');

        $activeCount = Tenant::whereIn('subscription_status', ['active', 'trialing'])->count();
        $verifiedDomainsCount = Site::whereNotNull('custom_domain')
            ->where('custom_domain_verified', true)
            ->whereHas('tenant', function ($query) {
                $query->whereIn('subscription_status', ['active', 'trialing']);
            })
            ->count();
        $pendingDomainsCount = Site::whereNotNull('custom_domain')
            ->where('custom_domain_verified', false)
            ->count();

        $this->command->line("  - Total tenants: " . count($tenants));
        $this->command->line("  - Active/Trialing tenants: {$activeCount}");
        $this->command->line("  - Verified custom domains (active): {$verifiedDomainsCount}");
        $this->command->line("  - Pending custom domains: {$pendingDomainsCount}");

        $this->command->newLine();
        $this->command->info('✓ You can now test with: php artisan traefik:sync --dry-run');
    }
}
