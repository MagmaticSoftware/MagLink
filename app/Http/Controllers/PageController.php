<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePageRequest;
use App\Http\Requests\UpdatePageRequest;
use App\Models\Page;
use Inertia\Inertia;

class PageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pages = Page::orderBy('created_at', 'desc')->get();
        return Inertia::render('tenant/pages/Index', [
            'pages' => $pages,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('tenant/pages/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePageRequest $request)
    {
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
        return Inertia::render('tenant/pages/Public', [
            'page' => $page->load('blocks'),
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
}
