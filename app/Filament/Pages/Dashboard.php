<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\LatestLinksWidget;
use App\Filament\Widgets\LatestUsersWidget;
use App\Filament\Widgets\StatsOverviewWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-home';

    protected static string $routePath = '/';

    protected static ?string $title = 'Dashboard';

    public function getWidgets(): array
    {
        return [
            StatsOverviewWidget::class,
            LatestUsersWidget::class,
            LatestLinksWidget::class,
        ];
    }
}
