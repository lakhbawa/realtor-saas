<?php

namespace App\Traits;

use App\Models\User;
use Filament\Notifications\Notification;

trait ChecksPlanLimits
{
    /**
     * Check if user can create more of a resource based on plan limits.
     */
    public static function canCreateMore(string $limitKey, string $relation): bool
    {
        $user = auth()->user();

        if (!$user || $user->isSuperAdmin()) {
            return true;
        }

        $subscription = $user->subscription;

        if (!$subscription || !$subscription->plan) {
            return false;
        }

        $limit = $subscription->getLimit($limitKey);

        // null or -1 means unlimited
        if ($limit === null || $limit === -1) {
            return true;
        }

        $currentCount = $user->$relation()->count();

        return $currentCount < $limit;
    }

    /**
     * Get the remaining quota for a resource.
     */
    public static function getRemainingQuota(string $limitKey, string $relation): ?int
    {
        $user = auth()->user();

        if (!$user || $user->isSuperAdmin()) {
            return null; // unlimited
        }

        $subscription = $user->subscription;

        if (!$subscription || !$subscription->plan) {
            return 0;
        }

        $limit = $subscription->getLimit($limitKey);

        // null or -1 means unlimited
        if ($limit === null || $limit === -1) {
            return null;
        }

        $currentCount = $user->$relation()->count();

        return max(0, $limit - $currentCount);
    }

    /**
     * Show limit reached notification.
     */
    public static function showLimitReachedNotification(string $resourceName, int $limit): void
    {
        Notification::make()
            ->title('Plan Limit Reached')
            ->body("You've reached your plan's limit of {$limit} {$resourceName}. Please upgrade your plan to add more.")
            ->warning()
            ->persistent()
            ->actions([
                \Filament\Notifications\Actions\Action::make('upgrade')
                    ->label('Upgrade Plan')
                    ->url(route('filament.admin.pages.my-subscription'))
                    ->button(),
            ])
            ->send();
    }

    /**
     * Check if user has a specific feature enabled.
     */
    public static function hasFeature(string $featureKey): bool
    {
        $user = auth()->user();

        if (!$user || $user->isSuperAdmin()) {
            return true;
        }

        $subscription = $user->subscription;

        if (!$subscription || !$subscription->plan) {
            return false;
        }

        return $subscription->hasFeature($featureKey);
    }
}
