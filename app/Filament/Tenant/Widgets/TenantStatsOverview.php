<?php

namespace App\Filament\Tenant\Widgets;

use App\Models\Property;
use App\Models\Testimonial;
use App\Models\ContactSubmission;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TenantStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $userId = auth()->id();

        return [
            Stat::make('Active Listings', Property::where('status', 'active')->count())
                ->description('Published properties')
                ->descriptionIcon('heroicon-m-home')
                ->color('success'),
            Stat::make('Total Properties', Property::count())
                ->description('All your listings')
                ->descriptionIcon('heroicon-m-building-office')
                ->color('primary'),
            Stat::make('Testimonials', Testimonial::where('is_published', true)->count())
                ->description('Published reviews')
                ->descriptionIcon('heroicon-m-star')
                ->color('warning'),
            Stat::make('Unread Messages', ContactSubmission::where('is_read', false)->count())
                ->description('New inquiries')
                ->descriptionIcon('heroicon-m-envelope')
                ->color('danger'),
        ];
    }
}
