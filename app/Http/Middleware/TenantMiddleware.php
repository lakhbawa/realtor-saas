<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TenantMiddleware
{
    /**
     * Handle an incoming request.
     * Resolves the tenant from the subdomain and makes it available.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();
        $baseDomain = config('app.base_domain', 'myrealtorsites.com');

        // Extract subdomain from host
        $subdomain = $this->extractSubdomain($host, $baseDomain);

        // Find the tenant
        $tenant = null;

        if ($subdomain) {
            // Skip reserved subdomains
            $reserved = ['www', 'admin', 'api', 'app', 'mail', 'ftp', 'dashboard'];
            if (in_array($subdomain, $reserved)) {
                return $next($request);
            }

            // Find the tenant by subdomain
            $tenant = Tenant::where('subdomain', $subdomain)->first();

            if (!$tenant) {
                abort(404, 'Site not found');
            }
        } else {
            // For development/testing: use first tenant with valid subscription
            if ($this->isLocalDevelopment($host)) {
                $tenant = Tenant::whereIn('subscription_status', ['active', 'trialing'])
                    ->first();
            }
        }

        if (!$tenant) {
            return $next($request);
        }

        // Check if tenant has valid subscription
        if (!$tenant->hasValidSubscription()) {
            abort(403, 'This site is currently unavailable. Please check your subscription.');
        }

        // Share tenant with all views and bind to container
        app()->instance('tenant', $tenant);
        app()->instance('currentTenant', $tenant);
        view()->share('tenant', $tenant);

        // Set tenant in request for easy access
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
