<?php

namespace App\Http\Middleware;

use App\Models\Site;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class TenantMiddleware
{
    /**
     * Handle an incoming request.
     * Resolves the site and tenant from the subdomain and makes them available.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();
        $baseDomain = config('app.base_domain', 'myrealtorsites.com');

        Log::info('TenantMiddleware', [
            'host' => $host,
            'baseDomain' => $baseDomain,
        ]);
        // Find the site and tenant
        $site = null;
        $tenant = null;

        // First, try to find by custom domain (exact match)
        $site = Site::where('custom_domain', $host)
            ->where('custom_domain_verified', true)
            ->with('tenant')
            ->first();

        // If not found by custom domain, try subdomain
        Log::info('Custom domain check', ['found' => $site ? true : false]);

        // If not found by custom domain, try subdomain
        if (!$site) {
            $subdomain = $this->extractSubdomain($host, $baseDomain);

            Log::info('Subdomain extraction', ['subdomain' => $subdomain]);

            if ($subdomain) {
                // Skip reserved subdomains
                $reserved = ['www', 'admin', 'api', 'app', 'mail', 'ftp', 'dashboard'];
                if (in_array($subdomain, $reserved)) {
                    Log::info('Reserved subdomain, skipping');
                    return $next($request);
                }

                // Find the site by subdomain
                $site = Site::where('subdomain', $subdomain)
                    ->with('tenant')
                    ->first();

                Log::info('Site lookup by subdomain', [
                    'subdomain' => $subdomain,
                    'found' => $site ? true : false,
                    'site_id' => $site?->id,
                ]);

                if (!$site) {
                    abort(404, 'Site not found');
                }

            } elseif ($this->isLocalDevelopment($host)) {
                // For development/testing: use first site with tenant that has valid subscription
                $site = Site::whereHas('tenant', function ($query) {
                    $query->whereIn('subscription_status', ['active', 'trialing']);
                })->with('tenant')->first();
            }
        }

        if ($site) {
            $tenant = $site->tenant;
        }

        if (!$site || !$tenant) {
            return $next($request);
        }

        // Check if tenant has valid subscription
        if (!$tenant->hasValidSubscription()) {
            abort(403, 'This site is currently unavailable. Please check your subscription.');
        }

        // Share site and tenant with all views and bind to container
        app()->instance('site', $site);
        app()->instance('tenant', $tenant);
        app()->instance('currentTenant', $tenant);
        app()->instance('currentSite', $site);
        view()->share('site', $site);
        view()->share('tenant', $tenant);

        // Set tenant and site in request for easy access
        $request->attributes->set('site', $site);
        $request->attributes->set('tenant', $tenant);

        // If user is authenticated, set the current tenant context
        if (auth()->check()) {
            auth()->user()->setCurrentTenant($tenant);
        }

        return $next($request);
    }

    /**
     * Extract subdomain from the host.
     */
    protected function extractSubdomain(string $host, string $baseDomain): ?string
    {
        // Handle localhost development - check query param first
        if ($this->isLocalDevelopment($host)) {
            return request()->query('subdomain');
        }

        // Remove the base domain from the host
        $subdomain = str_replace('.' . $baseDomain, '', $host);

        // If the subdomain equals the host, no subdomain was found
        if ($subdomain === $host || $subdomain === 'www') {
            return null;
        }

        return strtolower($subdomain);
    }

    /**
     * Check if running in local development.
     */
    protected function isLocalDevelopment(string $host): bool
    {
        return str_contains($host, 'localhost')
            || str_contains($host, '127.0.0.1')
            || app()->environment('local', 'testing');
    }
}
