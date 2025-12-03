<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\PageBlock;
use Illuminate\Database\Seeder;

class PageBlockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pages = Page::all();

        foreach ($pages as $page) {
            $this->createBlocksForPage($page);
        }
    }

    /**
     * Crea blocchi ordinati per una pagina specifica
     */
    protected function createBlocksForPage(Page $page): void
    {
        // Definisco i blocchi da creare con posizioni fisse e ordinate
        $blocksConfig = [
            [
                'type' => 'text',
                'title' => 'Benvenuto',
                'content' => 'Questa è una pagina di esempio creata con MagLink. Puoi personalizzarla aggiungendo blocchi di contenuto.',
                'position' => ['x' => 0, 'y' => 0],
                'size' => ['width' => 2, 'height' => 2],
            ],
            [
                'type' => 'html',
                'title' => 'Contenuto HTML',
                'content' => '<h3>Blocco HTML</h3><p>Puoi inserire codice HTML personalizzato qui.</p><ul><li>Feature 1</li><li>Feature 2</li><li>Feature 3</li></ul>',
                'position' => ['x' => 0, 'y' => 2],
                'size' => ['width' => 1, 'height' => 3],
            ],
            [
                'type' => 'image',
                'title' => 'Immagine di esempio',
                'content' => json_encode(['url' => 'https://picsum.photos/seed/' . $page->slug . '/800/600']),
                'position' => ['x' => 1, 'y' => 2],
                'size' => ['width' => 1, 'height' => 3],
            ],
            [
                'type' => 'video',
                'title' => 'Video YouTube',
                'content' => 'https://www.youtube.com/watch?v=QgrgK6Z6Lgc',
                'position' => ['x' => 0, 'y' => 5],
                'size' => ['width' => 2, 'height' => 3],
            ],
            [
                'type' => 'link',
                'title' => 'Visita il nostro sito',
                'content' => 'https://maglink.io',
                'position' => ['x' => 0, 'y' => 8],
                'size' => ['width' => 1, 'height' => 2],
            ],
        ];

        foreach ($blocksConfig as $blockData) {
            PageBlock::create([
                'page_id' => $page->id,
                'type' => $blockData['type'],
                'title' => $blockData['title'],
                'content' => $blockData['content'],
                'position' => $blockData['position'],
                'size' => $blockData['size'],
                'style' => [],
                'settings' => [],
                'is_active' => true,
            ]);
        }
    }
}
