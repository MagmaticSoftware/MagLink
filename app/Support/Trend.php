<?php

namespace App\Support;

use App\Models\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Serie giornaliere e variazioni percentuali per card, sparkline e liste.
 */
class Trend
{
    /**
     * Elementi creati per giorno negli ultimi $days giorni e variazione percentuale
     * rispetto ai $days giorni precedenti (null se il periodo precedente è vuoto).
     *
     * @param  \Closure(): Builder  $query
     * @return array{series: list<int>, current: int, previous: int, change: float|null}
     */
    public static function daily(\Closure $query, int $days): array
    {
        $from = static::from($days);
        $previousFrom = $from->copy()->subDays($days);

        $daily = $query()
            ->where('created_at', '>=', $from)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->pluck('total', 'date');

        $series = static::fill($days, fn (string $date) => (int) ($daily[$date] ?? 0));
        $current = array_sum($series);
        $previous = $query()->where('created_at', '>=', $previousFrom)->where('created_at', '<', $from)->count();

        return [
            'series' => $series,
            'current' => $current,
            'previous' => $previous,
            'change' => static::change($current, $previous),
        ];
    }

    /**
     * Visite giornaliere degli ultimi $days giorni per ciascun elemento (link o QR code).
     *
     * @param  class-string  $viewableType
     * @return array<int, list<int>> id elemento => serie giornaliera
     */
    public static function viewsPerItem(string $viewableType, Collection $ids, int $days): array
    {
        if ($ids->isEmpty()) {
            return [];
        }

        $rows = View::query()
            ->where('viewable_type', $viewableType)
            ->whereIn('viewable_id', $ids)
            ->where('created_at', '>=', static::from($days))
            ->selectRaw('viewable_id, DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('viewable_id', 'date')
            ->get()
            ->groupBy('viewable_id');

        return $ids->mapWithKeys(function ($id) use ($rows, $days) {
            $byDate = ($rows[$id] ?? collect())->pluck('total', 'date');

            return [$id => static::fill($days, fn (string $date) => (int) ($byDate[$date] ?? 0))];
        })->all();
    }

    public static function change(int|float $current, int|float $previous): ?float
    {
        return $previous > 0 ? round(($current - $previous) / $previous * 100, 1) : null;
    }

    private static function from(int $days): \Illuminate\Support\Carbon
    {
        return now()->subDays($days - 1)->startOfDay();
    }

    /**
     * @return list<int>
     */
    private static function fill(int $days, \Closure $value): array
    {
        $series = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $series[] = $value(now()->subDays($i)->format('Y-m-d'));
        }

        return $series;
    }
}
