<?php

namespace App\Filament\Admin\Widgets;

use App\Models\User;
use App\Models\Property;
use App\Models\ContactSubmission;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalUsers = User::where('is_admin', false)->count();
        $activeSubscriptions = User::where('subscription_status', 'active')->count();
        $trialUsers = User::where('subscription_status', 'trialing')->count();
        $totalProperties = Property::withoutGlobalScopes()->count();

        return [
            Stat::make('Total Tenants', $totalUsers)
                ->description('Registered users')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),
            Stat::make('Active Subscriptions', $activeSubscriptions)
                ->description('Paying customers')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
            Stat::make('Trial Users', $trialUsers)
                ->description('In trial period')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
            Stat::make('Total Properties', $totalProperties)
                ->description('Listings across all tenants')
                ->descriptionIcon('heroicon-m-home')
                ->color('info'),
        ];
    }
}
