<?php

namespace App\Http\Controllers;

use App\Models\Link;
use App\Models\QrCode;
use App\Models\Page;
use App\Models\PageBlock;
use App\Models\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
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
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get(['id', 'title', 'url', 'created_at']);
        
        // Ultimi QR Code
        $latest_qrcodes = QrCode::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get(['id', 'name', 'scans', 'created_at']);
        
        // Ultime pagine
        $latest_pages = Page::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get(['id', 'title', 'slug', 'views', 'is_active', 'created_at']);
        
        // Ultimi blocchi
        $latest_blocks = PageBlock::whereHas('page', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with('page:id,title')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get(['id', 'page_id', 'type', 'title', 'position', 'created_at']);
        
        // Attività ultimi 7 giorni: click sui link e scansioni QR registrati nella tabella views
        $performanceData = $this->viewsQuery($linkIds, $qrcodeIds)
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->selectRaw('DATE(created_at) as date')
            ->selectRaw('SUM(CASE WHEN viewable_type = ? THEN 1 ELSE 0 END) as link_views', [Link::class])
            ->selectRaw('SUM(CASE WHEN viewable_type = ? THEN 1 ELSE 0 END) as qrcode_views', [QrCode::class])
            ->groupBy('date')
            ->get()
            ->keyBy('date');

        // Assicurati che tutti i 7 giorni siano inclusi, anche senza visite
        $performance = collect();
        for ($i = 6; $i >= 0; $i--) {
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

        return Inertia::render('tenant/Dashboard', [
            'stats' => $stats,
            'latest_links' => $latest_links,
            'latest_qrcodes' => $latest_qrcodes,
            'latest_pages' => $latest_pages,
            'latest_blocks' => $latest_blocks,
            'performance' => $performance,
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
