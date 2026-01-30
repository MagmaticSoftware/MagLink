<?php

namespace App\Http\Controllers;

use App\Models\Link;
use App\Models\QrCode;
use App\Models\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    /**
     * Display analytics for a specific link.
     */
    public function link(Link $link)
    {
        $stats = $this->getDetailedStats('link', $link->id);
        
        return Inertia::render('tenant/analytics/LinkAnalytics', [
            'item' => $link,
            'stats' => $stats,
            'type' => 'link',
        ]);
    }

    /**
     * Display analytics for a specific QR code.
     */
    public function qrcode(QrCode $qrcode)
    {
        $stats = $this->getDetailedStats('qrcode', $qrcode->id);
        
        return Inertia::render('tenant/analytics/QrCodeAnalytics', [
            'item' => $qrcode,
            'stats' => $stats,
            'type' => 'qrcode',
        ]);
    }

    /**
     * Get detailed statistics for a viewable item.
     */
    private function getDetailedStats(string $type, int $id): array
    {
        $viewableType = $type === 'link' ? Link::class : QrCode::class;
        
        // Total views breakdown
        $totalViews = View::where('viewable_type', $viewableType)
            ->where('viewable_id', $id)
            ->count();
            
        $consentViews = View::where('viewable_type', $viewableType)
            ->where('viewable_id', $id)
            ->where('consent_given', true)
            ->count();
            
        $anonymousViews = $totalViews - $consentViews;
        
        // Daily views for last 30 days
        $dailyViews = View::where('viewable_type', $viewableType)
            ->where('viewable_id', $id)
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN consent_given = 1 THEN 1 ELSE 0 END) as with_consent'),
                DB::raw('SUM(CASE WHEN consent_given = 0 THEN 1 ELSE 0 END) as anonymous')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // Country distribution (only for consent views)
        $countryStats = View::where('viewable_type', $viewableType)
            ->where('viewable_id', $id)
            ->where('consent_given', true)
            ->whereNotNull('country')
            ->select('country', DB::raw('COUNT(*) as count'))
            ->groupBy('country')
            ->orderByDesc('count')
            ->limit(10)
            ->get();
        
        // Browser distribution (only for consent views)
        $browserStats = View::where('viewable_type', $viewableType)
            ->where('viewable_id', $id)
            ->where('consent_given', true)
            ->whereNotNull('browser')
            ->select('browser', DB::raw('COUNT(*) as count'))
            ->groupBy('browser')
            ->orderByDesc('count')
            ->limit(10)
            ->get();
        
        // Device type distribution (only for consent views)
        $deviceStats = View::where('viewable_type', $viewableType)
            ->where('viewable_id', $id)
            ->where('consent_given', true)
            ->whereNotNull('device_type')
            ->select('device_type', DB::raw('COUNT(*) as count'))
            ->groupBy('device_type')
            ->orderByDesc('count')
            ->get();
        
        // Platform/OS distribution (only for consent views)
        $platformStats = View::where('viewable_type', $viewableType)
            ->where('viewable_id', $id)
            ->where('consent_given', true)
            ->whereNotNull('platform')
            ->select('platform', DB::raw('COUNT(*) as count'))
            ->groupBy('platform')
            ->orderByDesc('count')
            ->limit(10)
            ->get();
        
        // Recent views (last 50 with consent)
        $recentViews = View::where('viewable_type', $viewableType)
            ->where('viewable_id', $id)
            ->where('consent_given', true)
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get(['id', 'country', 'city', 'browser', 'platform', 'device_type', 'created_at']);
        
        return [
            'overview' => [
                'total' => $totalViews,
                'with_consent' => $consentViews,
                'anonymous' => $anonymousViews,
                'consent_percentage' => $totalViews > 0 ? round(($consentViews / $totalViews) * 100, 1) : 0,
            ],
            'daily_views' => $dailyViews,
            'countries' => $countryStats,
            'browsers' => $browserStats,
            'devices' => $deviceStats,
            'platforms' => $platformStats,
            'recent_views' => $recentViews,
        ];
    }
}
