<?php

namespace App\Filament\Widgets;

use App\Models\Post;
use App\Models\Subscription;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Users', User::count())
                ->description('Registered users')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success'),
            Stat::make('Total Posts', Post::count())
                ->description('Published posts')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary'),
            Stat::make('Active Subscriptions', Subscription::where('status', 'active')->count())
                ->description('Active subscribers')
                ->descriptionIcon('heroicon-m-credit-card')
                ->color('warning'),
        ];
    }
}
