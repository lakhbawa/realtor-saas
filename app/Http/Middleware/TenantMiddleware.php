<?php

namespace App\Http\Middleware;

use App\Models\Site;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class TenantMiddleware
{
    private const RESERVED_SUBDOMAINS = ['www', 'admin', 'api', 'app', 'mail', 'ftp', 'dashboard'];

    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();
        $baseDomains = $this->baseDomains();

        Log::info('TenantMiddleware', compact('host', 'baseDomains'));

        $site = $this->resolveSite($host, $baseDomains);

        if (!$site || !$site->tenant) {
            return $next($request);
        }

        if (!$site->tenant->hasValidSubscription()) {
            abort(403, 'This site is currently unavailable');
        }

        $this->bindToContainer($site);
        $this->setRequestAttributes($request, $site);

        if (auth()->check()) {
            auth()->user()->setCurrentTenant($site->tenant);
        }

        return $next($request);
    }

    private function resolveSite(string $host, array $baseDomains): ?Site
    {
        if ($site = $this->findByCustomDomain($host)) {
            return $site;
        }

        if ($subdomain = $this->extractSubdomain($host, $baseDomains)) {
            return $this->isReserved($subdomain)
                ? null
                : $this->findBySubdomain($subdomain);
        }

        return $this->isLocalEnvironment($host)
            ? $this->findActiveSite()
            : null;
    }

    private function findByCustomDomain(string $host): ?Site
    {
        return Site::where('custom_domain', $host)
            ->where('custom_domain_verified', true)
            ->with('tenant')
            ->first();
    }

    private function findBySubdomain(string $subdomain): ?Site
    {
        $site = Site::where('subdomain', $subdomain)
            ->with('tenant')
            ->first();

        Log::info('Site lookup by subdomain', [
            'subdomain' => $subdomain,
            'found' => (bool) $site,
            'site_id' => $site?->id,
        ]);

        return $site ?? abort(404, 'Site not found');
    }

    private function findActiveSite(): ?Site
    {
        return Site::whereHas('tenant', fn($q) =>
            $q->whereIn('subscription_status', ['active', 'trialing'])
        )->with('tenant')->first();
    }

    private function extractSubdomain(string $host, array $baseDomains): ?string
    {
        foreach ($baseDomains as $baseDomain) {
            if (str_ends_with($host, '.'.$baseDomain)) {
                $subdomain = str_replace('.'.$baseDomain, '', $host);

                if ($subdomain && $subdomain !== 'www') {
                    return strtolower($subdomain);
                }
            }
        }

        return $this->isLocalEnvironment($host)
            ? request()->query('subdomain')
            : null;
    }

    private function bindToContainer(Site $site): void
    {
        app()->instance('site', $site);
        app()->instance('tenant', $site->tenant);
        app()->instance('currentTenant', $site->tenant);
        app()->instance('currentSite', $site);

        view()->share('site', $site);
        view()->share('tenant', $site->tenant);
    }

    private function setRequestAttributes(Request $request, Site $site): void
    {
        $request->attributes->set('site', $site);
        $request->attributes->set('tenant', $site->tenant);
    }

    private function baseDomains(): array
    {
        return array_merge(
            [config('app.base_domain', 'myrealtorsites.com')],
            config('settings.additional_base_domains', [])
        );
    }

    private function isReserved(string $subdomain): bool
    {
        return in_array($subdomain, self::RESERVED_SUBDOMAINS);
    }

    private function isLocalEnvironment(string $host): bool
    {
        return str_contains($host, 'localhost')
            || str_contains($host, '127.0.0.1')
            || app()->environment('local', 'testing');
    }
}
