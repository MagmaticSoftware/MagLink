<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLinkRequest;
use App\Http\Requests\UpdateLinkRequest;
use App\Models\Link;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Str;

class LinkController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $links = Link::withCount([
            'views',
            'views as views_with_consent_count' => function ($query) {
                $query->where('consent_given', true);
            },
            'views as views_anonymous_count' => function ($query) {
                $query->where('consent_given', false);
            },
        ])->orderBy('created_at', 'desc')->get();
        
        // Calculate stats
        $totalViews = $links->sum('views_count');
        $stats = [
            'total' => $links->count(),
            'active' => $links->where('is_active', true)->count(),
            'totalClicks' => $totalViews,
            'avgClicksPerLink' => $links->count() > 0 ? round($totalViews / $links->count(), 1) : 0,
        ];
        
        return Inertia::render('tenant/links/Index', [
            'links' => $links,
            'shortUrl' => config('app.short_url'),
            'stats' => $stats,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        
        // Verifica se l'utente può creare nuovi link
        if (!$user->canCreateLink()) {
            $limits = $user->getPlanLimits();
            $linkLimit = $limits['links'] ?? 0;
            
            return redirect()->route('links.index')
                ->with('error', "Hai raggiunto il limite di {$linkLimit} link del tuo piano. Effettua l'upgrade per continuare.")
                ->with('showUpgradeBanner', true);
        }
        
        $linkCount = Link::where('user_id', $user->id)->count();
        
        $slug = $this->generateRandomSlug();
        return Inertia::render('tenant/links/Create', [
            'slug' => $slug,
            'shortUrl' => config('app.short_url'),
            'linkCount' => $linkCount,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLinkRequest $request)
    {
        $user = $request->user();
        
        // Verifica se l'utente può creare nuovi link
        if (!$user->canCreateLink()) {
            $limits = $user->getPlanLimits();
            $linkLimit = $limits['links'] ?? 0;
            
            return redirect()->back()
                ->with('error', "Hai raggiunto il limite di {$linkLimit} link del tuo piano. Effettua l'upgrade per continuare.")
                ->with('showUpgradeBanner', true);
        }
        
        $validate = $request->validated();
        $link = Link::create($validate);
        return redirect()->route('links.index')->with('success', 'Link created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Link $link)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Link $link)
    {
        return Inertia::render('tenant/links/Edit', [
            'link' => $link,
            'shortUrl' => config('app.short_url'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLinkRequest $request, Link $link)
    {
        $validate = $request->validated();
        $link->update($validate);
        return redirect()->route('links.index')->with('success', 'Link updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Link $link)
    {
        $link->delete();
        return redirect()->route('links.index');
    }

    /**
     * Generate a random slug unique in database.
     */
    private function generateRandomSlug(): string
    {
        do {
            $slug = strtolower(Str::random(6)); // 6 caratteri alfanumerici
        } while (Link::where('slug', $slug)->exists());

        return $slug;
    }

    /**
     * Handle public short link redirection.
     */
    public function showPublicShort(Link $link)
    {
        //TODO REDIRECT O PAGINA DI ACCETTAZIONE
        //RECUPERA INFORMAZIONI UTENTE AI FINI STATISTICI
        return redirect($link->url);
    }
}
