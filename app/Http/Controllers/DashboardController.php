<?php

namespace App\Http\Controllers;

use App\Models\Link;
use App\Models\QrCode;
use App\Models\Page;
use App\Models\PageBlock;
use App\Models\View;
use App\Support\Trend;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Periodi selezionabili per grafico e trend (in giorni).
     */
    private const PERIODS = [7, 30];

    public function index(Request $request)
    {
        $user = Auth::user();
        $days = in_array((int) $request->query('days'), self::PERIODS, true) ? (int) $request->query('days') : 7;
        
        $linkIds = Link::where('user_id', $user->id)->pluck('id');
        $qrcodeIds = QrCode::where('user_id', $user->id)->pluck('id');

        // Statistiche generali
        $stats = [
            'links_count' => $linkIds->count(),
            'qrcodes_count' => $qrcodeIds->count(),
            'pages_count' => Page::where('user_id', $user->id)->count(),
            'blocks_count' => PageBlock::whereHas('page', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->count(),
            'total_views' => $this->viewsQuery($linkIds, $qrcodeIds)->count() +
                           Page::where('user_id', $user->id)->sum('views'),
        ];

        // Ultimi link creati
        $latest_links = Link::where('user_id', $user->id)
            ->withCount('views')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get(['id', 'title', 'url', 'created_at']);
        
        // Ultimi QR Code
        $latest_qrcodes = QrCode::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get(['id', 'name', 'slug', 'scans', 'created_at']);
        
        // Ultime pagine
        $latest_pages = Page::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get(['id', 'title', 'slug', 'views', 'is_active', 'created_at']);
        
        // Ultimi blocchi
        $latest_blocks = PageBlock::whereHas('page', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with('page:id,title,slug')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get(['id', 'page_id', 'type', 'title', 'position', 'created_at']);
        
        // Attività del periodo: click sui link e scansioni QR registrati nella tabella views
        $from = now()->subDays($days - 1)->startOfDay();
        $performanceData = $this->viewsQuery($linkIds, $qrcodeIds)
            ->where('created_at', '>=', $from)
            ->selectRaw('DATE(created_at) as date')
            ->selectRaw('SUM(CASE WHEN viewable_type = ? THEN 1 ELSE 0 END) as link_views', [Link::class])
            ->selectRaw('SUM(CASE WHEN viewable_type = ? THEN 1 ELSE 0 END) as qrcode_views', [QrCode::class])
            ->groupBy('date')
            ->get()
            ->keyBy('date');

        // Assicurati che tutti i giorni del periodo siano inclusi, anche senza visite
        $performance = collect();
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $dayData = $performanceData->get($date);
            $linkViews = $dayData ? (int) $dayData->link_views : 0;
            $qrcodeViews = $dayData ? (int) $dayData->qrcode_views : 0;
            $performance->push([
                'date' => $date,
                'link_views' => $linkViews,
                'qrcode_views' => $qrcodeViews,
                'total_items' => $linkViews + $qrcodeViews,
            ]);
        }

        // Trend delle card: serie giornaliera del periodo e variazione rispetto al periodo precedente
        $blocksQuery = fn () => PageBlock::whereHas('page', fn ($query) => $query->where('user_id', $user->id));
        $trends = [
            'links' => Trend::daily(fn () => Link::where('user_id', $user->id), $days),
            'qrcodes' => Trend::daily(fn () => QrCode::where('user_id', $user->id), $days),
            'pages' => Trend::daily(fn () => Page::where('user_id', $user->id), $days),
            'blocks' => Trend::daily($blocksQuery, $days),
            'views' => Trend::daily(fn () => $this->viewsQuery($linkIds, $qrcodeIds), $days),
        ];

        // Link più performanti del periodo, con variazione rispetto al periodo precedente
        $previousFrom = $from->copy()->subDays($days);
        $topLinks = Link::where('user_id', $user->id)
            ->withCount([
                'views as period_views' => fn ($query) => $query->where('created_at', '>=', $from),
                'views as previous_views' => fn ($query) => $query->where('created_at', '>=', $previousFrom)->where('created_at', '<', $from),
            ])
            ->orderByDesc('period_views')
            ->take(5)
            ->get(['id', 'title', 'slug', 'url'])
            ->filter(fn (Link $link) => $link->period_views > 0)
            ->map(fn (Link $link) => [
                'id' => $link->id,
                'title' => $link->title,
                'slug' => $link->slug,
                'url' => $link->url,
                'views' => $link->period_views,
                'change' => Trend::change($link->period_views, $link->previous_views),
            ])
            ->values();

        return Inertia::render('tenant/Dashboard', [
            'stats' => $stats,
            'latest_links' => $latest_links,
            'latest_qrcodes' => $latest_qrcodes,
            'latest_pages' => $latest_pages,
            'latest_blocks' => $latest_blocks,
            // Sparkline 7 giorni degli ultimi link e QR code
            'latest_sparklines' => [
                'links' => Trend::viewsPerItem(Link::class, $latest_links->pluck('id'), 7),
                'qrcodes' => Trend::viewsPerItem(QrCode::class, $latest_qrcodes->pluck('id'), 7),
            ],
            'performance' => $performance,
            'trends' => $trends,
            'top_links' => $topLinks,
            'shortUrl' => config('app.short_url'),
            'period' => $days,
            'periods' => self::PERIODS,
        ]);
    }

    /**
     * Query sulle views (click link + scansioni QR) dei contenuti dell'utente.
     */
    private function viewsQuery(Collection $linkIds, Collection $qrcodeIds): Builder
    {
        return View::query()->where(function (Builder $query) use ($linkIds, $qrcodeIds) {
            $query->where(function (Builder $q) use ($linkIds) {
                $q->where('viewable_type', Link::class)->whereIn('viewable_id', $linkIds);
            })->orWhere(function (Builder $q) use ($qrcodeIds) {
                $q->where('viewable_type', QrCode::class)->whereIn('viewable_id', $qrcodeIds);
            });
        });
    }
}
