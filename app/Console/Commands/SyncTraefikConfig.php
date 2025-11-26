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

        // Collect all custom domains
        $customDomains = [];

        foreach ($sites as $site) {
            // Add verified custom domains
            if ($site->hasCustomDomain()) {
                $customDomains[] = $site->custom_domain;
                $this->line("  ✓ Custom domain: {$site->custom_domain}");
            }

            // Log pending verifications
            if ($site->customDomainPendingVerification()) {
                $this->comment("  ⏳ Pending verification: {$site->custom_domain} (skipped)");
            }
        }

        $this->info("\n  ✓ Wildcard subdomain: *.{$baseDomain}");

        $this->newLine();
        $this->info("Summary:");
        $this->line("  - Wildcard subdomain: 1 (*.{$baseDomain})");
        $this->line("  - Custom domains: " . count($customDomains));

        // Generate Traefik dynamic config
        $config = $this->generateTraefikConfig($baseDomain, $customDomains);

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
    protected function generateTraefikConfig(string $baseDomain, array $customDomains): string
    {
        $serviceName = config('app.traefik_service_name', 'realtor-saas');
        $backendUrl = config('app.traefik_backend_url', 'http://nginx:80');

        // Build host rules for base domain (wildcard + root)
        $baseRule = 'HostRegexp(`{subdomain:[a-z0-9-]+}.' . $baseDomain . '`, `' . $baseDomain . '`)';

        // Build config array
        $config = [
            'http' => [
                'routers' => [
                    // Base domain router - DNS challenge (supports wildcard)
                    $serviceName => [
                        'rule' => $baseRule,
                        'service' => $serviceName,
                        'entryPoints' => ['websecure'],
                        'tls' => [
                            'certResolver' => 'lets-encrypt',
                            'domains' => [
                                [
                                    'main' => $baseDomain,
                                    'sans' => ["*.{$baseDomain}"],
                                ],
                            ],
                        ],
                    ],
                    // Base domain HTTP redirect
                    "{$serviceName}-http" => [
                        'rule' => $baseRule,
                        'service' => $serviceName,
                        'entryPoints' => ['web'],
                        'middlewares' => ['redirect-to-https'],
                    ],
                ],
                'services' => [
                    $serviceName => [
                        'loadBalancer' => [
                            'passHostHeader' => true,
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

        // Add SEPARATE router for EACH custom domain
        // This ensures one failing domain doesn't block others from getting certificates
        foreach ($customDomains as $domain) {
            $safeName = $this->getSafeRouterName($domain);

            // HTTPS router for this domain
            $config['http']['routers']["{$serviceName}-{$safeName}"] = [
                'rule' => "Host(`{$domain}`)",
                'service' => $serviceName,
                'entryPoints' => ['websecure'],
                'tls' => [
                    'certResolver' => 'lets-encrypt-http',
                ],
            ];

            // HTTP redirect router for this domain
            $config['http']['routers']["{$serviceName}-{$safeName}-http"] = [
                'rule' => "Host(`{$domain}`)",
                'service' => $serviceName,
                'entryPoints' => ['web'],
                'middlewares' => ['redirect-to-https'],
            ];
        }

        return Yaml::dump($config, 10, 2);
    }

    /**
     * Convert domain to safe router name.
     * e.g., "john-smith.realty.com" -> "john-smith-realty-com"
     */
    protected function getSafeRouterName(string $domain): string
    {
        return preg_replace('/[^a-z0-9-]/', '-', strtolower($domain));
    }

    /**
     * Get the path where Traefik config should be written.
     */
    protected function getConfigPath(): string
    {
        return config('app.traefik_config_path', storage_path('app/traefik/realtor-saas.yml'));
    }
}