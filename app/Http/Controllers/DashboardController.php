<?php

namespace App\Http\Controllers;

use App\Models\Link;
use App\Models\QrCode;
use App\Models\Page;
use App\Models\PageBlock;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Statistiche generali
        $stats = [
            'links_count' => Link::where('user_id', $user->id)->count(),
            'qrcodes_count' => QrCode::where('user_id', $user->id)->count(),
            'pages_count' => Page::where('user_id', $user->id)->count(),
            'blocks_count' => PageBlock::whereHas('page', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->count(),
            'total_views' => QrCode::where('user_id', $user->id)->sum('scans') + 
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
        
        // Performance ultimi 7 giorni (conteggio creazioni per giorno)
        $performanceData = Link::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total_items')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->keyBy('date');
        
        // Assicurati che tutti i 7 giorni siano inclusi, anche con 0 link
        $performance = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $dayData = $performanceData->get($date);
            $performance->push([
                'date' => $date,
                'total_items' => $dayData ? (int)$dayData->total_items : 0
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
}
