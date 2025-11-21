<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TenantMiddleware
{
    /**
     * Handle an incoming request.
     * Resolves the tenant (user) from the subdomain and makes it available.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();
        $baseDomain = config('app.base_domain', 'myrealtorsites.com');

        // Extract subdomain from host
        $subdomain = $this->extractSubdomain($host, $baseDomain);

        if (!$subdomain) {
            // No subdomain - either main site or invalid
            return $next($request);
        }

        // Skip reserved subdomains
        $reserved = ['www', 'admin', 'api', 'app', 'mail', 'ftp', 'localhost', 'dashboard'];
        if (in_array($subdomain, $reserved)) {
            return $next($request);
        }

        // Find the tenant by subdomain
        $tenant = User::where('subdomain', $subdomain)->first();

        if (!$tenant) {
            abort(404, 'Site not found');
        }

        // Check if tenant has active subscription
        if (!$tenant->hasActiveSubscription()) {
            abort(403, 'This site is currently unavailable');
        }

        // Share tenant with all views and bind to container
        app()->instance('tenant', $tenant);
        view()->share('tenant', $tenant);

        // Set tenant in request for easy access
        $request->attributes->set('tenant', $tenant);

        return $next($request);
    }

    /**
     * Extract subdomain from the host.
     */
    protected function extractSubdomain(string $host, string $baseDomain): ?string
    {
        // Handle localhost development
        if (str_contains($host, 'localhost') || str_contains($host, '127.0.0.1')) {
            // For local development, check for subdomain in query string
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
}
