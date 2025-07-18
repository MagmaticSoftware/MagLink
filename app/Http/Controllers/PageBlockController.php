<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePageBlockRequest;
use App\Http\Requests\UpdatePageBlockRequest;
use App\Models\PageBlock;

class PageBlockController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePageBlockRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(PageBlock $pageBlock)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PageBlock $pageBlock)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePageBlockRequest $request, PageBlock $pageBlock)
    {
        $validated = $request->validated();
        $pageBlock->update($validated);
        return response()->json(['success' => true, 'block' => $pageBlock]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PageBlock $pageBlock)
    {
        //
    }

        /**
     * Update the position of a block.
     */
    public function updatePosition($id)
    {
        $block = PageBlock::findOrFail($id);
        $data = request()->validate([
            'x' => 'required|integer',
            'y' => 'required|integer',
        ]);
        $block->position = array_merge($block->position ?? [], [
            'x' => $data['x'],
            'y' => $data['y'],
        ]);
        $block->save();
        return response()->json(['success' => true]);
    }

    /**
     * Update the size of a block.
     */
    public function updateSize($id)
    {
        $block = PageBlock::findOrFail($id);
        $data = request()->validate([
            'width' => 'required|integer',
            'height' => 'required|integer',
        ]);
        $block->size = array_merge($block->size ?? [], [
            'width' => $data['width'],
            'height' => $data['height'],
        ]);
        $block->save();
        return response()->json(['success' => true]);
    }
}
