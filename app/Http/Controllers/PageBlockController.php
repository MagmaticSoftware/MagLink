<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePageBlockRequest;
use App\Http\Requests\UpdatePageBlockRequest;
use App\Models\Page;
use App\Models\PageBlock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

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
        $user = Auth::user();
        $pageId = $request->input('page_id');
        
        // Verifica se l'utente può aggiungere blocchi a questa pagina
        if (!$user->canAddBlockToPage($pageId)) {
            $limits = $user->getPlanLimits();
            $blockLimit = $limits['blocks_per_page'] ?? 0;
            
            return response()->json([
                'success' => false,
                'error' => "Hai raggiunto il limite di {$blockLimit} blocchi per pagina del piano gratuito. Effettua l'upgrade per continuare.",
                'showUpgradeBanner' => true
            ], 403);
        }
        
        $validated = $request->validated();
        
        // Imposta dimensioni predefinite per separator (4x1: larghezza intera, altezza minima)
        if ($validated['type'] === 'separator') {
            $validated['size'] = ['width' => 4, 'height' => 1];
        }
        
        // Imposta dimensioni predefinite per title (altezza ridotta)
        if ($validated['type'] === 'title') {
            $validated['size'] = ['width' => $validated['size']['width'] ?? 2, 'height' => 1];
        }
        
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
        
        // Raccogli tutti i blocchi interessati e la loro pagina
        $pageId = null;
        $updatedBlockIds = [];
        
        foreach ($positions as $pos) {
            if (!isset($pos['id'], $pos['x'], $pos['y'])) continue;
            $block = PageBlock::find($pos['id']);
            if ($block) {
                $block->position = array_merge($block->position ?? [], [
                    'x' => $pos['x'],
                    'y' => $pos['y'],
                ]);
                $block->save();
                $updatedBlockIds[] = $block->id;
                if (!$pageId) {
                    $pageId = $block->page_id;
                }
            }
        }
        
        // Se abbiamo aggiornato dei blocchi, verifica sovrapposizioni su TUTTA la pagina
        if ($pageId && count($updatedBlockIds) > 0) {
            $allBlocks = PageBlock::where('page_id', $pageId)->get()->toArray();
            
            // Controlla se ci sono davvero sovrapposizioni
            if ($this->hasOverlaps($allBlocks)) {
                // Solo se ci sono sovrapposizioni, riorganizza
                $resolvedBlocks = $this->resolveOverlaps($allBlocks);
                
                // Salva i blocchi riorganizzati
                foreach ($resolvedBlocks as $b) {
                    if (is_array($b)) {
                        PageBlock::where('id', $b['id'])->update([
                            'position' => $b['position'] ?? ['x' => 0, 'y' => 0]
                        ]);
                    } else {
                        $b->save();
                    }
                }
            }
        }
        
        return response()->json(['success' => true]);
    }
    
    /**
     * Verifica se ci sono sovrapposizioni tra i blocchi.
     */
    private function hasOverlaps(array $blocks): bool
    {
        $occupiedCells = [];
        
        foreach ($blocks as $block) {
            $position = is_array($block) ? ($block['position'] ?? ['x' => 0, 'y' => 0]) : ($block->position ?? ['x' => 0, 'y' => 0]);
            $size = is_array($block) ? ($block['size'] ?? ['width' => 1, 'height' => 2]) : ($block->size ?? ['width' => 1, 'height' => 2]);
            
            $x = $position['x'];
            $y = $position['y'];
            $w = $size['width'];
            $h = $size['height'];
            
            // Verifica ogni cella occupata da questo blocco
            for ($dy = 0; $dy < $h; $dy++) {
                for ($dx = 0; $dx < $w; $dx++) {
                    $cellKey = ($y + $dy) . ',' . ($x + $dx);
                    if (isset($occupiedCells[$cellKey])) {
                        return true; // Trovata sovrapposizione
                    }
                    $occupiedCells[$cellKey] = true;
                }
            }
        }
        
        return false; // Nessuna sovrapposizione
    }
    
    /**
     * Risolve le sovrapposizioni tra blocchi riorganizzandoli automaticamente.
     */
    private function resolveOverlaps(array $blocks): array
    {
        $gridCols = 4; // La griglia ha 4 colonne
        $occupiedCells = [];
        $resolvedBlocks = [];
        
        // Ordina i blocchi per posizione (y prima, poi x)
        usort($blocks, function($a, $b) {
            // Gestisci sia array che oggetti Eloquent
            $posA = is_array($a) ? ($a['position'] ?? ['x' => 0, 'y' => 0]) : ($a->position ?? ['x' => 0, 'y' => 0]);
            $posB = is_array($b) ? ($b['position'] ?? ['x' => 0, 'y' => 0]) : ($b->position ?? ['x' => 0, 'y' => 0]);
            if ($posA['y'] != $posB['y']) {
                return $posA['y'] - $posB['y'];
            }
            return $posA['x'] - $posB['x'];
        });
        
        foreach ($blocks as $block) {
            // Gestisci sia array che oggetti Eloquent
            $position = is_array($block) ? ($block['position'] ?? ['x' => 0, 'y' => 0]) : ($block->position ?? ['x' => 0, 'y' => 0]);
            $size = is_array($block) ? ($block['size'] ?? ['width' => 1, 'height' => 2]) : ($block->size ?? ['width' => 1, 'height' => 2]);
            $blockId = is_array($block) ? $block['id'] : $block->id;
            
            $x = $position['x'];
            $y = $position['y'];
            $w = $size['width'];
            $h = $size['height'];
            
            // Verifica se la posizione attuale è occupata
            $hasOverlap = false;
            for ($dy = 0; $dy < $h; $dy++) {
                for ($dx = 0; $dx < $w; $dx++) {
                    $cellKey = ($y + $dy) . ',' . ($x + $dx);
                    if (isset($occupiedCells[$cellKey])) {
                        $hasOverlap = true;
                        break 2;
                    }
                }
            }
            
            // Se c'è sovrapposizione, trova una nuova posizione
            if ($hasOverlap) {
                $newPosition = $this->findEmptySpace($occupiedCells, $w, $h, $gridCols);
                if (is_array($block)) {
                    $block['position'] = array_merge($block['position'] ?? [], $newPosition);
                } else {
                    $block->position = array_merge($block->position ?? [], $newPosition);
                }
                $x = $newPosition['x'];
                $y = $newPosition['y'];
            }
            
            // Marca le celle come occupate
            for ($dy = 0; $dy < $h; $dy++) {
                for ($dx = 0; $dx < $w; $dx++) {
                    $cellKey = ($y + $dy) . ',' . ($x + $dx);
                    $occupiedCells[$cellKey] = $blockId;
                }
            }
            
            $resolvedBlocks[] = $block;
        }
        
        return $resolvedBlocks;
    }
    
    /**
     * Trova il primo spazio vuoto disponibile nella griglia.
     */
    private function findEmptySpace(array $occupiedCells, int $width, int $height, int $gridCols): array
    {
        $maxRow = 0;
        foreach (array_keys($occupiedCells) as $cellKey) {
            list($y, $x) = explode(',', $cellKey);
            $maxRow = max($maxRow, (int)$y + 1);
        }
        
        // Cerca nelle righe esistenti e in quelle nuove
        for ($y = 0; $y < $maxRow + 10; $y++) {
            for ($x = 0; $x <= $gridCols - $width; $x++) {
                $isFree = true;
                
                // Verifica se questo spazio è libero per l'intero blocco
                for ($dy = 0; $dy < $height; $dy++) {
                    for ($dx = 0; $dx < $width; $dx++) {
                        $cellKey = ($y + $dy) . ',' . ($x + $dx);
                        if (isset($occupiedCells[$cellKey])) {
                            $isFree = false;
                            break 2;
                        }
                    }
                }
                
                if ($isFree) {
                    return ['x' => $x, 'y' => $y];
                }
            }
        }
        
        // Se non trova spazio, metti in coda
        return ['x' => 0, 'y' => $maxRow];
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
        
        // Salva prima le nuove dimensioni
        $block->size = array_merge($block->size ?? [], [
            'width' => $data['width'],
            'height' => $data['height'],
        ]);
        $block->save();
        
        // DOPO aver salvato, recupera tutti i blocchi della pagina per controllare sovrapposizioni
        $pageId = $block->page_id;
        $allBlocks = PageBlock::where('page_id', $pageId)->get()->toArray();
        
        // Controlla se ci sono sovrapposizioni
        if ($this->hasOverlaps($allBlocks)) {
            // Solo se ci sono sovrapposizioni, riorganizza
            $resolvedBlocks = $this->resolveOverlaps($allBlocks);
            
            // Salva i blocchi riorganizzati
            foreach ($resolvedBlocks as $b) {
                if (is_array($b)) {
                    PageBlock::where('id', $b['id'])->update([
                        'position' => $b['position'] ?? ['x' => 0, 'y' => 0],
                        'size' => $b['size'] ?? ['width' => 1, 'height' => 2]
                    ]);
                } else {
                    $b->save();
                }
            }
        }
        
        return response()->json(['success' => true]);
    }
    
    /**
     * Fetch metadata (title, favicon) from a URL.
     */
    public function fetchUrlMetadata(Request $request)
    {
        $url = $request->input('url');
        
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return response()->json(['error' => 'Invalid URL'], 400);
        }
        
        try {
            // Fetch the page with timeout
            $response = Http::timeout(10)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (compatible; MagLink/1.0; +https://maglink.app)'
                ])
                ->get($url);
            
            if (!$response->successful()) {
                return response()->json(['error' => 'Failed to fetch URL'], 400);
            }
            
            $html = $response->body();
            $metadata = $this->extractMetadata($html, $url);
            
            return response()->json($metadata);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch metadata'], 500);
        }
    }
    
    /**
     * Extract metadata from HTML content.
     */
    private function extractMetadata(string $html, string $url): array
    {
        $metadata = [
            'title' => '',
            'description' => '',
            'favicon' => '',
            'image' => '',
        ];
        
        // Parse HTML
        libxml_use_internal_errors(true);
        $doc = new \DOMDocument();
        $doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        libxml_clear_errors();
        
        $xpath = new \DOMXPath($doc);
        
        // Extract title
        $titleNodes = $xpath->query('//meta[@property="og:title"]/@content');
        if ($titleNodes->length > 0) {
            $metadata['title'] = $titleNodes->item(0)->nodeValue;
        } else {
            $titleNodes = $xpath->query('//title');
            if ($titleNodes->length > 0) {
                $metadata['title'] = $titleNodes->item(0)->nodeValue;
            }
        }
        
        // Extract description
        $descNodes = $xpath->query('//meta[@property="og:description"]/@content');
        if ($descNodes->length > 0) {
            $metadata['description'] = $descNodes->item(0)->nodeValue;
        } else {
            $descNodes = $xpath->query('//meta[@name="description"]/@content');
            if ($descNodes->length > 0) {
                $metadata['description'] = $descNodes->item(0)->nodeValue;
            }
        }
        
        // Extract image
        $imageNodes = $xpath->query('//meta[@property="og:image"]/@content');
        if ($imageNodes->length > 0) {
            $metadata['image'] = $this->normalizeUrl($imageNodes->item(0)->nodeValue, $url);
        }
        
        // Extract favicon
        $faviconNodes = $xpath->query('//link[@rel="icon" or @rel="shortcut icon"]/@href');
        if ($faviconNodes->length > 0) {
            $metadata['favicon'] = $this->normalizeUrl($faviconNodes->item(0)->nodeValue, $url);
        } else {
            // Fallback to default favicon location
            $parsedUrl = parse_url($url);
            $metadata['favicon'] = $parsedUrl['scheme'] . '://' . $parsedUrl['host'] . '/favicon.ico';
        }
        
        return $metadata;
    }
    
    /**
     * Normalize relative URLs to absolute.
     */
    private function normalizeUrl(string $relativeUrl, string $baseUrl): string
    {
        if (filter_var($relativeUrl, FILTER_VALIDATE_URL)) {
            return $relativeUrl;
        }
        
        $parsedBase = parse_url($baseUrl);
        $scheme = $parsedBase['scheme'] ?? 'https';
        $host = $parsedBase['host'] ?? '';
        
        if (strpos($relativeUrl, '//') === 0) {
            return $scheme . ':' . $relativeUrl;
        }
        
        if (strpos($relativeUrl, '/') === 0) {
            return $scheme . '://' . $host . $relativeUrl;
        }
        
        return $scheme . '://' . $host . '/' . ltrim($relativeUrl, '/');
    }
}
