<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSubscriptionActive
{
    /**
     * Handle an incoming request.
     * Ensures the authenticated user has an active subscription.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Allow admins to bypass subscription check
        if ($user->is_admin) {
            return $next($request);
        }

        // Check for active subscription
        if (!$user->hasActiveSubscription()) {
            // Redirect to billing page to complete subscription
            return redirect()
                ->route('filament.tenant.pages.billing')
                ->with('warning', 'Please activate your subscription to access this feature.');
        }

        return $next($request);
    }
}
