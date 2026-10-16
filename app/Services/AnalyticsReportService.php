<?php

namespace App\Services;

use App\Models\Link;
use App\Models\View;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Calcola i dati per i report di analisi (export PDF/CSV) di uno o più link
 * in un periodo, con confronto rispetto al periodo precedente di pari durata.
 */
class AnalyticsReportService
{
    /** Palette condivisa da grafici PDF e legende */
    public const COLORS = ['#ea580c', '#2563eb', '#16a34a', '#9333ea', '#db2777', '#0891b2', '#ca8a04', '#dc2626', '#4f46e5', '#65a30d'];

    /**
     * @param  Collection<int, Link>  $links
     */
    public function build(Collection $links, Carbon $from, Carbon $to): array
    {
        $from = $from->copy()->startOfDay();
        $to = $to->copy()->endOfDay();
        $linkIds = $links->pluck('id');

        $days = collect(CarbonPeriod::create($from, '1 day', $to->copy()->startOfDay()))
            ->map(fn (Carbon $d) => $d->format('Y-m-d'));

        // Visite giornaliere per link
        $dailyRows = $this->viewsQuery($linkIds, $from, $to)
            ->selectRaw('viewable_id, DATE(created_at) as date, COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN consent_given = 1 THEN 1 ELSE 0 END) as with_consent')
            ->groupBy('viewable_id', 'date')
            ->get();

        // Periodo precedente di pari durata, per il confronto
        $periodDays = $days->count();
        $prevFrom = $from->copy()->subDays($periodDays);
        $prevTo = $from->copy()->subSecond();
        $previousTotals = $this->viewsQuery($linkIds, $prevFrom, $prevTo)
            ->selectRaw('viewable_id, COUNT(*) as total')
            ->groupBy('viewable_id')
            ->pluck('total', 'viewable_id');

        $series = [];
        $summaries = [];
        foreach ($links->values() as $index => $link) {
            $rows = $dailyRows->where('viewable_id', $link->id)->keyBy('date');
            $values = $days->map(fn ($d) => (int) ($rows[$d]->total ?? 0))->values();
            $total = $values->sum();
            $withConsent = (int) $rows->sum('with_consent');
            $peakIndex = $total > 0 ? $values->search($values->max()) : null;
            $previous = (int) ($previousTotals[$link->id] ?? 0);

            $series[] = [
                'link_id' => $link->id,
                'label' => $link->title ?: $link->slug,
                'color' => self::COLORS[$index % count(self::COLORS)],
                'values' => $values->all(),
            ];

            $summaries[] = [
                'id' => $link->id,
                'title' => $link->title ?: $link->slug,
                'slug' => $link->slug,
                'short_url' => $link->short_url,
                'url' => $link->url,
                'is_active' => (bool) $link->is_active,
                'created_at' => $link->created_at,
                'color' => self::COLORS[$index % count(self::COLORS)],
                'total' => $total,
                'with_consent' => $withConsent,
                'anonymous' => $total - $withConsent,
                'consent_rate' => $total > 0 ? round($withConsent / $total * 100, 1) : 0,
                'avg_per_day' => $periodDays > 0 ? round($total / $periodDays, 1) : 0,
                'active_days' => $values->filter()->count(),
                'peak_date' => $peakIndex !== null ? $days[$peakIndex] : null,
                'peak_value' => $peakIndex !== null ? $values[$peakIndex] : 0,
                'previous_total' => $previous,
                'change' => $this->percentChange($total, $previous),
            ];
        }

        $grandTotal = array_sum(array_column($summaries, 'total'));
        foreach ($summaries as &$summary) {
            $summary['share'] = $grandTotal > 0 ? round($summary['total'] / $grandTotal * 100, 1) : 0;
        }
        unset($summary);

        // Totali giornalieri (tutti i link) e distribuzione per giorno della settimana
        $dailyTotals = $days->map(fn ($d) => (int) $dailyRows->where('date', $d)->sum('total'))->values();
        $dailyConsent = $days->map(fn ($d) => (int) $dailyRows->where('date', $d)->sum('with_consent'))->values();
        $weekdays = array_fill(1, 7, 0);
        foreach ($days as $i => $d) {
            $weekdays[Carbon::parse($d)->dayOfWeekIso] += $dailyTotals[$i];
        }

        $withConsentTotal = array_sum(array_column($summaries, 'with_consent'));
        $previousGrandTotal = array_sum(array_column($summaries, 'previous_total'));
        $peakIndex = $grandTotal > 0 ? $dailyTotals->search($dailyTotals->max()) : null;

        return [
            'period' => [
                'from' => $from,
                'to' => $to,
                'days' => $periodDays,
                'previous_from' => $prevFrom,
                'previous_to' => $prevTo,
            ],
            'labels' => $days->all(),
            'overview' => [
                'links' => $links->count(),
                'total' => $grandTotal,
                'with_consent' => $withConsentTotal,
                'anonymous' => $grandTotal - $withConsentTotal,
                'consent_rate' => $grandTotal > 0 ? round($withConsentTotal / $grandTotal * 100, 1) : 0,
                'avg_per_day' => $periodDays > 0 ? round($grandTotal / $periodDays, 1) : 0,
                'peak_date' => $peakIndex !== null ? $days[$peakIndex] : null,
                'peak_value' => $peakIndex !== null ? $dailyTotals[$peakIndex] : 0,
                'previous_total' => $previousGrandTotal,
                'change' => $this->percentChange($grandTotal, $previousGrandTotal),
            ],
            'links' => $summaries,
            'series' => $series,
            'daily_totals' => $dailyTotals->all(),
            'daily_consent' => $dailyConsent->all(),
            'weekdays' => array_values($weekdays),
            'hours' => $this->hourDistribution($linkIds, $from, $to),
            'countries' => $this->breakdown($linkIds, $from, $to, 'country'),
            'devices' => $this->breakdown($linkIds, $from, $to, 'device_type'),
            'browsers' => $this->breakdown($linkIds, $from, $to, 'browser'),
            'platforms' => $this->breakdown($linkIds, $from, $to, 'platform'),
            'referrers' => $this->referrers($linkIds, $from, $to),
        ];
    }

    /**
     * Query di base sulle visite dei link nel periodo.
     */
    public function viewsQuery(Collection $linkIds, Carbon $from, Carbon $to): Builder
    {
        return View::query()
            ->where('viewable_type', Link::class)
            ->whereIn('viewable_id', $linkIds)
            ->whereBetween('created_at', [$from, $to]);
    }

    /**
     * Distribuzione (solo visite con consenso) per una colonna, con percentuali.
     */
    private function breakdown(Collection $linkIds, Carbon $from, Carbon $to, string $column, int $limit = 8): array
    {
        $rows = $this->viewsQuery($linkIds, $from, $to)
            ->where('consent_given', true)
            ->whereNotNull($column)
            ->where($column, '!=', '')
            ->select($column.' as label', DB::raw('COUNT(*) as count'))
            ->groupBy($column)
            ->orderByDesc('count')
            ->get();

        return $this->withPercentages($rows, $limit);
    }

    /**
     * Sorgenti di traffico (dominio del referrer) per le visite con consenso.
     */
    private function referrers(Collection $linkIds, Carbon $from, Carbon $to): array
    {
        $rows = $this->viewsQuery($linkIds, $from, $to)
            ->where('consent_given', true)
            ->select('referrer', DB::raw('COUNT(*) as count'))
            ->groupBy('referrer')
            ->get()
            ->groupBy(function ($row) {
                $host = $row->referrer ? parse_url($row->referrer, PHP_URL_HOST) : null;

                return $host ? preg_replace('/^www\./', '', $host) : 'Diretto / sconosciuto';
            })
            ->map(fn ($group, $label) => (object) ['label' => $label, 'count' => $group->sum('count')])
            ->sortByDesc('count')
            ->values();

        return $this->withPercentages($rows, 8);
    }

    private function withPercentages(Collection $rows, int $limit): array
    {
        $total = $rows->sum('count');
        $top = $rows->take($limit)->map(fn ($r) => [
            'label' => (string) $r->label,
            'count' => (int) $r->count,
            'percentage' => $total > 0 ? round($r->count / $total * 100, 1) : 0,
        ]);

        $others = $rows->slice($limit)->sum('count');
        if ($others > 0) {
            $top->push(['label' => 'Altro', 'count' => (int) $others, 'percentage' => round($others / $total * 100, 1)]);
        }

        return $top->values()->all();
    }

    /**
     * Visite per fascia oraria (0-23).
     */
    private function hourDistribution(Collection $linkIds, Carbon $from, Carbon $to): array
    {
        $hourExpression = DB::connection()->getDriverName() === 'sqlite'
            ? "CAST(strftime('%H', created_at) AS INTEGER)"
            : 'HOUR(created_at)';

        $rows = $this->viewsQuery($linkIds, $from, $to)
            ->selectRaw("$hourExpression as hour, COUNT(*) as total")
            ->groupBy('hour')
            ->pluck('total', 'hour');

        return collect(range(0, 23))->map(fn ($h) => (int) ($rows[$h] ?? 0))->all();
    }

    private function percentChange(int $current, int $previous): ?float
    {
        if ($previous === 0) {
            return $current > 0 ? null : 0.0;
        }

        return round(($current - $previous) / $previous * 100, 1);
    }
}
