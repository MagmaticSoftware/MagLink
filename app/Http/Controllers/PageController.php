<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePageRequest;
use App\Http\Requests\UpdatePageRequest;
use App\Models\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pages = Page::withCount('blocks')->orderBy('created_at', 'desc')->get();
        
        $totalBlocks = $pages->sum('blocks_count');
        $stats = [
            'total' => $pages->count(),
            'published' => $pages->where('is_active', true)->count(),
            'totalBlocks' => $totalBlocks,
            'avgBlocksPerPage' => $pages->count() > 0 ? round($totalBlocks / $pages->count()) : 0,
        ];
        
        return Inertia::render('tenant/pages/Index', [
            'pages' => $pages,
            'stats' => $stats,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        
        // Verifica se l'utente può creare nuove pagine
        if (!$user->canCreatePage()) {
            $limits = $user->getPlanLimits();
            $pageLimit = $limits['pages'] ?? 0;
            
            return redirect()->route('pages.index')
                ->with('error', "Hai raggiunto il limite di {$pageLimit} pagina del piano gratuito. Effettua l'upgrade per continuare.")
                ->with('showUpgradeBanner', true);
        }
        
        $pageCount = Page::where('user_id', $user->id)->count();
        
        return Inertia::render('tenant/pages/Create', [
            'pageCount' => $pageCount,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePageRequest $request)
    {
        $user = $request->user();
        
        // Verifica se l'utente può creare nuove pagine
        if (!$user->canCreatePage()) {
            $limits = $user->getPlanLimits();
            $pageLimit = $limits['pages'] ?? 0;
            
            return redirect()->back()
                ->with('error', "Hai raggiunto il limite di {$pageLimit} pagina del piano gratuito. Effettua l'upgrade per continuare.")
                ->with('showUpgradeBanner', true);
        }
        
        $validated = $request->validated();
        $page = Page::create($validated);
        return redirect()->route('pages.index')->with('success', 'Pagina creata con successo.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Page $page)
    {
        return Inertia::render('tenant/pages/Show', [
            'page' => $page,
            'blocks' => $page->blocks()->orderBy('position')->get(),
        ]);
    }

    /**
     * Show the public page view.
     * 
     * @param Page $page
     */
    public function showPublic(Page $page)
    {
        // Load page with blocks and ensure settings is included
        $page->load('blocks');
        
        return Inertia::render('tenant/pages/Public', [
            'page' => $page,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Page $page)
    {
        return Inertia::render('tenant/pages/Edit', [
            'page' => $page,
            'blocks' => $page->blocks()->orderBy('position')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePageRequest $request, Page $page)
    {
        $validated = $request->validated();
        $page->update($validated);
        return redirect()->route('pages.index')->with('success', 'Pagina aggiornata con successo.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Page $page)
    {
        $page->delete();
        return redirect()->route('pages.index');
    }

    /**
     * Upload profile image for a page.
     */
    public function uploadProfileImage(Request $request, Page $page)
    {
        $request->validate([
            'profile_image' => 'required|image|max:2048', // 2MB max
        ]);

        // Delete old profile image if exists
        if ($page->settings && isset($page->settings['profile_image'])) {
            $oldPath = str_replace('/storage/', '', $page->settings['profile_image']);
            Storage::disk('public')->delete($oldPath);
        }

        // Store the new image in a folder based on page ID
        $path = $request->file('profile_image')->store("pages/{$page->id}", 'public');
        $url = Storage::url($path);

        // Update page settings with new image URL
        $settings = $page->settings ?? [];
        $settings['profile_image'] = $url;
        $page->update(['settings' => $settings]);

        return response()->json(['url' => $url]);
    }

    /**
     * Delete profile image for a page.
     */
    public function deleteProfileImage(Page $page)
    {
        // Delete profile image file if exists
        if ($page->settings && isset($page->settings['profile_image'])) {
            $oldPath = str_replace('/storage/', '', $page->settings['profile_image']);
            Storage::disk('public')->delete($oldPath);
            
            // Update page settings to remove image URL
            $settings = $page->settings;
            unset($settings['profile_image']);
            $page->update(['settings' => $settings]);
        }

        return response()->json(['success' => true]);
    }
}
