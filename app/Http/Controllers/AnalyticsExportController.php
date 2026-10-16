<?php

namespace App\Http\Controllers;

use App\Models\Link;
use App\Services\AnalyticsReportService;
use App\Support\ReportCharts;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Export delle statistiche dei link in PDF (con grafici) o CSV.
 * Funzionalità riservata ai piani premium (feature "analytics_export").
 */
class AnalyticsExportController extends Controller
{
    private const MAX_PERIOD_DAYS = 731;

    private const MAX_LINKS = 10;

    public function __construct(private AnalyticsReportService $reports)
    {
    }

    /**
     * Export dalla lista link: uno o più link da confrontare.
     */
    public function links(Request $request)
    {
        $validated = $request->validate([
            'link_ids' => ['required', 'array', 'min:1', 'max:'.self::MAX_LINKS],
            'link_ids.*' => ['integer'],
        ]);

        return $this->export($request, $validated['link_ids']);
    }

    /**
     * Export dalla pagina analytics del singolo link (eventualmente confrontato con altri).
     */
    public function link(Request $request, Link $link)
    {
        $request->validate([
            'link_ids' => ['nullable', 'array', 'max:'.(self::MAX_LINKS - 1)],
            'link_ids.*' => ['integer'],
        ]);

        $ids = collect([$link->id])->merge($request->input('link_ids', []))->unique()->values()->all();

        return $this->export($request, $ids);
    }

    private function export(Request $request, array $linkIds)
    {
        abort_unless($request->user()->canExportAnalytics(), 403, 'L\'export delle statistiche è disponibile solo con un piano premium.');

        $validated = $request->validate([
            'format' => ['required', 'in:pdf,csv'],
            'from' => ['required', 'date'],
            'to' => ['required', 'date', 'after_or_equal:from'],
            'dataset' => ['nullable', 'in:daily,views'],
        ]);

        $from = Carbon::parse($validated['from'])->startOfDay();
        $to = Carbon::parse($validated['to'])->endOfDay();
        if ($to->isFuture()) {
            $to = now()->endOfDay();
        }
        abort_if($from->diffInDays($to) > self::MAX_PERIOD_DAYS, 422, 'Il periodo massimo esportabile è di 2 anni.');

        // Link del tenant corrente (scope BelongsToTenant), nell'ordine selezionato
        $links = Link::whereIn('id', $linkIds)->get()
            ->sortBy(fn (Link $l) => array_search($l->id, $linkIds))
            ->values();
        abort_if($links->isEmpty(), 404);

        $filename = $this->filename($links, $from, $to);

        if ($validated['format'] === 'csv') {
            return ($validated['dataset'] ?? 'daily') === 'views'
                ? $this->csvViews($links, $from, $to, $filename)
                : $this->csvDaily($links, $from, $to, $filename);
        }

        return $this->pdf($links, $from, $to, $filename);
    }

    private function pdf(Collection $links, Carbon $from, Carbon $to, string $filename)
    {
        $report = $this->reports->build($links, $from, $to);

        $pdf = Pdf::loadView('reports.links-analytics', [
            'report' => $report,
            'charts' => new ReportCharts(),
            'company' => tenant('name') ?? tenant('id'),
            'generatedAt' => now(),
        ])->setPaper('a4');

        return $pdf->download($filename.'.pdf');
    }

    /**
     * CSV "tidy": una riga per giorno e per link, pronto per pivot/Excel.
     */
    private function csvDaily(Collection $links, Carbon $from, Carbon $to, string $filename): StreamedResponse
    {
        $report = $this->reports->build($links, $from, $to);
        $byId = collect($report['links'])->keyBy('id');

        return $this->streamCsv($filename, function ($out) use ($report, $byId) {
            fputcsv($out, ['Data', 'Giorno', 'Link', 'Slug', 'URL breve', 'Visite'], ';');
            foreach ($report['series'] as $serie) {
                $info = $byId[$serie['link_id']];
                foreach ($report['labels'] as $i => $date) {
                    fputcsv($out, [
                        $date,
                        Carbon::parse($date)->locale('it')->isoFormat('ddd'),
                        $info['title'],
                        $info['slug'],
                        $info['short_url'],
                        $serie['values'][$i],
                    ], ';');
                }
            }
        });
    }

    /**
     * CSV con il dettaglio delle singole visite (i dati del visitatore solo se ha dato il consenso).
     * L'indirizzo IP non viene esportato.
     */
    private function csvViews(Collection $links, Carbon $from, Carbon $to, string $filename): StreamedResponse
    {
        $titles = $links->mapWithKeys(fn (Link $l) => [$l->id => [$l->title ?: $l->slug, $l->slug]]);
        $query = $this->reports->viewsQuery($links->pluck('id'), $from, $to)->orderBy('created_at');

        return $this->streamCsv($filename.'-visite', function ($out) use ($query, $titles) {
            fputcsv($out, ['Data e ora', 'Link', 'Slug', 'Consenso', 'Paese', 'Città', 'Lingua', 'Browser', 'Sistema operativo', 'Dispositivo', 'Referrer'], ';');
            foreach ($query->lazy(1000) as $view) {
                [$title, $slug] = $titles[$view->viewable_id];
                fputcsv($out, [
                    $view->created_at->format('Y-m-d H:i:s'),
                    $title,
                    $slug,
                    $view->consent_given ? 'Sì' : 'No',
                    $view->country,
                    $view->city,
                    $view->language,
                    $view->browser,
                    $view->platform,
                    $view->device_type,
                    $view->referrer,
                ], ';');
            }
        });
    }

    private function streamCsv(string $filename, callable $write): StreamedResponse
    {
        return response()->streamDownload(function () use ($write) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM: Excel riconosce l'UTF-8
            $write($out);
            fclose($out);
        }, $filename.'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function filename(Collection $links, Carbon $from, Carbon $to): string
    {
        $name = $links->count() === 1
            ? Str::slug($links->first()->title ?: $links->first()->slug)
            : 'confronto-'.$links->count().'-link';

        return 'maglink-'.$name.'-'.$from->format('Ymd').'-'.$to->format('Ymd');
    }
}
