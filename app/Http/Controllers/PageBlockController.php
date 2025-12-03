<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePageBlockRequest;
use App\Http\Requests\UpdatePageBlockRequest;
use App\Models\Page;
use App\Models\PageBlock;
use Illuminate\Http\Request;

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
        $validated = $request->validated();
        $pageBlock = PageBlock::create($validated);
        return response()->json(['success' => true, 'block' => $pageBlock]);
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
        $pageBlock->delete();
        return response()->json(['success' => true]);
    }

    /**
     * Remove all blocks for a specific page.
     */
    public function deleteAllForPage(Page $page)
    {
        $deleted = PageBlock::where('page_id', $page->id)->delete();
        
        return response()->json([
            'success' => true, 
            'deleted_count' => $deleted
        ]);
    }

    /**
     * Update positions of multiple blocks.
     */
    public function updatePositions(Request $request)
    {
        $positions = $request->input('positions', []);
        foreach ($positions as $pos) {
            if (!isset($pos['id'], $pos['x'], $pos['y'])) continue;
            $block = PageBlock::find($pos['id']);
            if ($block) {
                $block->position = array_merge($block->position ?? [], [
                    'x' => $pos['x'],
                    'y' => $pos['y'],
                ]);
                $block->save();
            }
        }
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
