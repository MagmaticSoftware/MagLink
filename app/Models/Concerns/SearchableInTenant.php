<?php

namespace App\Models\Concerns;

use Illuminate\Support\Facades\Log;
use Laravel\Scout\Searchable;

/**
 * Indicizzazione per la ricerca globale (Meilisearch via Scout), filtrabile per tenant.
 *
 * Un errore del motore di ricerca non deve mai impedire il salvataggio del modello:
 * viene loggato e l'indice si riallinea con `php artisan scout:import`.
 */
trait SearchableInTenant
{
    use Searchable {
        queueMakeSearchable as protected scoutQueueMakeSearchable;
        queueRemoveFromSearch as protected scoutQueueRemoveFromSearch;
    }

    /**
     * Campi testuali indicizzati, in ordine di rilevanza.
     *
     * @return list<string>
     */
    abstract protected function searchableFields(): array;

    public function toSearchableArray(): array
    {
        return array_merge(
            ['id' => $this->getKey(), 'tenant_id' => $this->tenant_id],
            $this->only($this->searchableFields()),
        );
    }

    public function queueMakeSearchable($models)
    {
        $this->guardSearchEngine(fn () => $this->scoutQueueMakeSearchable($models));
    }

    public function queueRemoveFromSearch($models)
    {
        $this->guardSearchEngine(fn () => $this->scoutQueueRemoveFromSearch($models));
    }

    private function guardSearchEngine(\Closure $callback): void
    {
        try {
            $callback();
        } catch (\Throwable $e) {
            Log::warning('Aggiornamento indice di ricerca non riuscito', [
                'model' => static::class,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
