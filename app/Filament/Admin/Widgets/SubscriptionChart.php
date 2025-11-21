<?php

namespace App\Filament\Admin\Widgets;

use App\Models\User;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class SubscriptionChart extends ChartWidget
{
    protected static ?string $heading = 'Subscriptions Over Time';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $data = [];
        $labels = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $labels[] = $date->format('M Y');

            $data[] = User::where('is_admin', false)
                ->whereIn('subscription_status', ['active', 'trialing'])
                ->whereDate('created_at', '<=', $date->endOfMonth())
                ->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Active Subscriptions',
                    'data' => $data,
                    'fill' => true,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.2)',
                    'borderColor' => 'rgb(59, 130, 246)',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
