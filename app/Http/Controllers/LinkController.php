<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLinkRequest;
use App\Http\Requests\UpdateLinkRequest;
use App\Models\Link;
use Inertia\Inertia;
use Illuminate\Support\Str;

class LinkController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $links = Link::orderBy('created_at', 'desc')->get();
        return Inertia::render('tenant/links/Index', [
            'links' => $links,
            'shortUrl' => config('app.short_url'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $slug = $this->generateRandomSlug();
        return Inertia::render('tenant/links/Create', [
            'slug' => $slug,
            'shortUrl' => config('app.short_url'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLinkRequest $request)
    {
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
