<?php

namespace App\Console\Commands;

use App\Models\Site;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Yaml\Yaml;

class SyncTraefikConfig extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'traefik:sync {--dry-run : Show config without writing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync site domains (subdomains + custom domains) to Traefik dynamic configuration';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Syncing site domains to Traefik...');

        // Get base domain for wildcard subdomain support
        $baseDomain = config('app.base_domain', 'myrealtorsites.com');

        // Get all sites with valid tenant subscriptions
        $sites = Site::with('tenant')
            ->whereHas('tenant', function ($query) {
                $query->whereIn('subscription_status', ['active', 'trialing']);
            })
            ->get();

        if ($sites->isEmpty()) {
            $this->warn('No sites found with active tenant subscriptions.');
            return Command::SUCCESS;
        }

        $this->info('Found ' . $sites->count() . ' site(s) with active subscriptions.');

        // Collect all domains
        $domains = [];
        $customDomainCount = 0;

        foreach ($sites as $site) {
            // Add verified custom domains
            if ($site->hasCustomDomain()) {
                $domains[] = $site->custom_domain;
                $customDomainCount++;
                $this->line("  ✓ Custom domain: {$site->custom_domain}");
            }

            // Log pending verifications
            if ($site->customDomainPendingVerification()) {
                $this->comment("  ⏳ Pending verification: {$site->custom_domain} (skipped)");
            }
        }

        // Add wildcard subdomain rule for base domain
        $wildcardDomain = "*.{$baseDomain}";
        $this->info("\n  ✓ Wildcard subdomain: {$wildcardDomain}");

        $this->newLine();
        $this->info("Summary:");
        $this->line("  - Wildcard subdomain: 1 ({$wildcardDomain})");
        $this->line("  - Custom domains: {$customDomainCount}");
        $this->line("  - Total rules: " . ($customDomainCount + 1));

        // Generate Traefik dynamic config
        $config = $this->generateTraefikConfig($wildcardDomain, $domains);

        if ($this->option('dry-run')) {
            $this->newLine();
            $this->info('Generated config (dry-run):');
            $this->line($config);
            return Command::SUCCESS;
        }

        // Write config to file
        $configPath = $this->getConfigPath();
        $configDir = dirname($configPath);

        if (!File::isDirectory($configDir)) {
            File::makeDirectory($configDir, 0755, true);
            $this->info("Created directory: {$configDir}");
        }

        File::put($configPath, $config);

        $this->newLine();
        $this->info("✓ Config written to: {$configPath}");
        $this->info('✓ Traefik will automatically reload the configuration.');

        return Command::SUCCESS;
    }

    /**
     * Generate Traefik dynamic configuration YAML.
     */
    protected function generateTraefikConfig(string $wildcardDomain, array $customDomains): string
    {
        $serviceName = config('app.traefik_service_name', 'realtor-saas');
        $backendUrl = config('app.traefik_backend_url', 'http://nginx:80');

        // Build the Host rules
        $hostRules = [];

        // Add wildcard subdomain (matches any subdomain)
        $hostRules[] = "HostRegexp(`{subdomain:[a-z0-9-]+}.{$this->extractBaseDomain($wildcardDomain)}`)";

        // Add base domain
        $hostRules[] = "Host(`{$this->extractBaseDomain($wildcardDomain)}`)";

        // Add custom domains (exact match)
        foreach ($customDomains as $domain) {
            $hostRules[] = "Host(`{$domain}`)";
        }

        $rule = implode(' || ', $hostRules);

        $config = [
            'http' => [
                'routers' => [
                    $serviceName => [
                        'rule' => $rule,
                        'service' => $serviceName,
                        'entryPoints' => ['websecure'],
                        'tls' => [
                            'certResolver' => 'lets-encrypt',
                        ],
                    ],
                    "{$serviceName}-http" => [
                        'rule' => $rule,
                        'service' => $serviceName,
                        'entryPoints' => ['web'],
                        'middlewares' => ['redirect-to-https'],
                    ],
                ],
                'services' => [
                    $serviceName => [
                        'loadBalancer' => [
                            'servers' => [
                                ['url' => $backendUrl],
                            ],
                        ],
                    ],
                ],
                'middlewares' => [
                    'redirect-to-https' => [
                        'redirectScheme' => [
                            'scheme' => 'https',
                            'permanent' => true,
                        ],
                    ],
                ],
            ],
        ];

        return Yaml::dump($config, 10, 2);
    }

    /**
     * Extract base domain from wildcard (*.example.com -> example.com).
     */
    protected function extractBaseDomain(string $wildcardDomain): string
    {
        return str_replace('*.', '', $wildcardDomain);
    }


    /**
     * Get the path where Traefik config should be written.
     */
    protected function getConfigPath(): string
    {
        return config('app.traefik_config_path', storage_path('app/traefik/realtor-saas.yml'));
    }
}
