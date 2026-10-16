<?php

namespace App\Http\Controllers;

use App\Models\Link;
use App\Models\Page;
use App\Models\QrCode;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SearchController extends Controller
{
    private const MIN_LENGTH = 2;

    private const LIMIT = 6;

    /**
     * Ricerca globale della topbar su link, QR code e pagine del tenant corrente.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $query = trim((string) $request->query('q', ''));

        if (mb_strlen($query) < self::MIN_LENGTH) {
            return response()->json(['query' => $query, 'groups' => []]);
        }

        $shortUrl = rtrim(config('app.short_url'), '/');

        $groups = [
            [
                'key' => 'links',
                'items' => $this->search(Link::class, $query, ['title', 'slug', 'description', 'url'])
                    ->map(fn (Link $link) => [
                        'id' => $link->id,
                        'title' => $link->title,
                        'subtitle' => "{$shortUrl}/{$link->slug} → {$link->url}",
                        'href' => route('links.edit', ['link' => $link->id]),
                        'active' => (bool) $link->is_active,
                    ]),
            ],
            [
                'key' => 'qrcodes',
                'items' => $this->search(QrCode::class, $query, ['name', 'slug', 'description'])
                    ->map(fn (QrCode $qr) => [
                        'id' => $qr->id,
                        'title' => $qr->name ?: $qr->slug,
                        'subtitle' => $qr->description ?: "{$shortUrl}/q/{$qr->slug}",
                        'href' => route('qrcodes.edit', $qr),
                        'active' => (bool) $qr->is_active,
                    ]),
            ],
            [
                'key' => 'pages',
                'items' => $this->search(Page::class, $query, ['title', 'slug', 'description'])
                    ->map(fn (Page $page) => [
                        'id' => $page->id,
                        'title' => $page->title,
                        'subtitle' => '/'.$page->slug,
                        'href' => route('pages.edit', $page),
                        'active' => (bool) $page->is_active,
                    ]),
            ],
        ];

        return response()->json([
            'query' => $query,
            'groups' => array_values(array_filter($groups, fn ($group) => $group['items']->isNotEmpty())),
        ]);
    }

    /**
     * Cerca con Scout (Meilisearch) e, se il motore non risponde, ripiega su una ricerca nel database.
     *
     * @param  class-string<Link|QrCode|Page>  $model
     * @param  list<string>  $fields
     */
    private function search(string $model, string $query, array $fields): Collection
    {
        $tenantId = tenant()->getTenantKey();

        try {
            return $model::search($query)->where('tenant_id', $tenantId)->take(self::LIMIT)->get();
        } catch (\Throwable $e) {
            Log::warning('Ricerca non disponibile, uso il database', ['model' => $model, 'error' => $e->getMessage()]);

            return $model::query()
                ->where(function (Builder $builder) use ($fields, $query) {
                    foreach ($fields as $field) {
                        $builder->orWhere($field, 'like', '%'.addcslashes($query, '%_\\').'%');
                    }
                })
                ->latest()
                ->take(self::LIMIT)
                ->get();
        }
    }
}
