<?php

use App\Models\Link;
use App\Models\View;
use App\Services\AnalyticsReportService;
use App\Support\ReportCharts;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

beforeEach(function () {
    DB::table('users')->insert(['id' => 1, 'first_name' => 'Test', 'last_name' => 'User', 'email' => 't@example.com', 'password' => 'x']);
    DB::table('companies')->insert(['id' => 1, 'slug' => 'acme', 'name' => 'Acme']);
});

function makeLink(string $title, string $slug): Link
{
    return Link::withoutEvents(fn () => Link::forceCreate([
        'user_id' => 1, 'company_id' => 1, 'slug' => $slug, 'url' => 'https://example.com', 'title' => $title, 'is_active' => true,
    ]));
}

function addView(Link $link, string $at, bool $consent = false, array $data = []): void
{
    $view = View::create(array_merge([
        'viewable_type' => Link::class, 'viewable_id' => $link->id, 'consent_given' => $consent,
    ], $data));
    $view->forceFill(['created_at' => $at])->save();
}

it('builds a comparison report with daily series, previous period and breakdowns', function () {
    $a = makeLink('Campagna A', 'camp-a');
    $b = makeLink('Campagna B', 'camp-b');

    $from = now()->subDays(6)->startOfDay();
    addView($a, $from->copy()->addHours(10), true, ['country' => 'Italy', 'device_type' => 'mobile', 'referrer' => 'https://www.instagram.com/x']);
    addView($a, $from->copy()->addDays(2)->addHours(15));
    addView($a, $from->copy()->addDays(2)->addHours(15), true, ['country' => 'Italy', 'device_type' => 'desktop']);
    addView($b, $from->copy()->addDays(6)->addHours(9));
    // periodo precedente
    addView($a, $from->copy()->subDays(3));
    // fuori periodo
    addView($a, $from->copy()->subDays(30));

    $report = app(AnalyticsReportService::class)->build(collect([$a, $b]), $from, now());

    expect($report['period']['days'])->toBe(7)
        ->and($report['overview']['total'])->toBe(4)
        ->and($report['overview']['with_consent'])->toBe(2)
        ->and($report['links'][0]['total'])->toBe(3)
        ->and($report['links'][0]['previous_total'])->toBe(1)
        ->and($report['links'][0]['change'])->toBe(200.0)
        ->and($report['links'][1]['change'])->toBeNull()
        ->and($report['series'][0]['values'])->toBe([1, 0, 2, 0, 0, 0, 0])
        ->and($report['hours'][15])->toBe(2)
        ->and($report['countries'][0])->toMatchArray(['label' => 'Italy', 'count' => 2])
        ->and($report['referrers'][0]['label'])->toBe('Diretto / sconosciuto')
        ->and(collect($report['referrers'])->pluck('label'))->toContain('instagram.com');

    $pdf = Pdf::loadView('reports.links-analytics', [
        'report' => $report, 'charts' => new ReportCharts(), 'company' => 'Test', 'generatedAt' => now(),
    ])->output();

    expect(substr($pdf, 0, 4))->toBe('%PDF');
});
