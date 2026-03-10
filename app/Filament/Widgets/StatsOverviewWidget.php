<?php

namespace App\Filament\Widgets;

use App\Models\Link;
use App\Models\QrCode;
use App\Models\StripeSubscription;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalUsers = User::count();
        $newUsersThisMonth = User::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $activeSubscriptions = StripeSubscription::where('stripe_status', 'active')->count();
        $totalLinks = Link::count();
        $totalQrCodes = QrCode::count();

        return [
            Stat::make('Utenti Totali', $totalUsers)
                ->description("+{$newUsersThisMonth} questo mese")
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->icon('heroicon-o-users'),

            Stat::make('Abbonamenti Attivi', $activeSubscriptions)
                ->description('Stripe active')
                ->color('warning')
                ->icon('heroicon-o-credit-card'),

            Stat::make('Link Brevi', $totalLinks)
                ->color('info')
                ->icon('heroicon-o-link'),

            Stat::make('QR Code', $totalQrCodes)
                ->color('primary')
                ->icon('heroicon-o-qr-code'),
        ];
    }
}
