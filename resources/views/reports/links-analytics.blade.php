@php
    $o = $report['overview'];
    $p = $report['period'];
    $isComparison = count($report['links']) > 1;
    $fmt = fn ($n) => number_format($n, 0, ',', '.');
    $dec = fn ($n) => number_format($n, 1, ',', '.');
    $date = fn ($d) => $d ? \Carbon\Carbon::parse($d)->locale('it')->isoFormat('D MMM YYYY') : '—';
    $change = function ($value) {
        if ($value === null) return ['nuovo', 'up'];
        if ($value == 0) return ['0%', 'flat'];
        return [($value > 0 ? '+' : '').number_format($value, 1, ',', '.').'%', $value > 0 ? 'up' : 'down'];
    };
    $weekdayLabels = ['Lun', 'Mar', 'Mer', 'Gio', 'Ven', 'Sab', 'Dom'];
    $hourLabels = array_map(fn ($h) => str_pad($h, 2, '0', STR_PAD_LEFT), range(0, 23));
    $bestWeekday = max($report['weekdays']) > 0 ? array_search(max($report['weekdays']), $report['weekdays']) : null;
    $bestHour = max($report['hours']) > 0 ? array_search(max($report['hours']), $report['hours']) : null;
    $breakdowns = [
        'Paesi' => $report['countries'],
        'Dispositivi' => $report['devices'],
        'Browser' => $report['browsers'],
        'Sistemi operativi' => $report['platforms'],
        'Sorgenti di traffico' => $report['referrers'],
    ];
    [$totalChange, $totalChangeDir] = $change($o['change']);
@endphp
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <title>Report MagLink</title>
    <style>
        @page { margin: 90px 36px 56px 36px; }
        * { font-family: 'DejaVu Sans', sans-serif; }
        body { font-size: 10px; color: #1f2937; }
        header { position: fixed; top: -66px; left: 0; right: 0; height: 48px; border-bottom: 2px solid #ea580c; }
        footer { position: fixed; bottom: -38px; left: 0; right: 0; height: 20px; font-size: 8px; color: #9ca3af; border-top: 1px solid #e5e7eb; padding-top: 6px; }
        footer .page:after { content: counter(page); }
        .brand { font-size: 20px; font-weight: bold; color: #111827; }
        .brand span { color: #ea580c; }
        .muted { color: #6b7280; }
        h1 { font-size: 17px; margin: 0 0 2px 0; }
        h2 { font-size: 12px; margin: 18px 0 8px 0; color: #111827; border-left: 3px solid #ea580c; padding-left: 6px; }
        table { width: 100%; border-collapse: collapse; }
        .kpis td { width: 25%; padding: 0 4px; vertical-align: top; }
        .kpis td:first-child { padding-left: 0; } .kpis td:last-child { padding-right: 0; }
        .keep { page-break-inside: avoid; }
        .kpi { border: 1px solid #e5e7eb; border-radius: 6px; padding: 9px 10px; background: #fafafa; }
        .kpi .label { font-size: 8px; text-transform: uppercase; letter-spacing: 0.5px; color: #6b7280; }
        .kpi .value { font-size: 18px; font-weight: bold; margin-top: 3px; color: #111827; }
        .kpi .sub { font-size: 8px; margin-top: 2px; color: #6b7280; }
        .up { color: #16a34a; } .down { color: #dc2626; } .flat { color: #6b7280; }
        .data th { text-align: left; font-size: 8px; text-transform: uppercase; color: #6b7280; border-bottom: 1px solid #d1d5db; padding: 5px 4px; }
        .data td { border-bottom: 1px solid #f3f4f6; padding: 5px 4px; vertical-align: middle; }
        .num { text-align: right; white-space: nowrap; }
        .dot { display: inline-block; width: 8px; height: 8px; border-radius: 4px; margin-right: 4px; }
        .bar-bg { background: #f3f4f6; height: 7px; border-radius: 3px; width: 100%; }
        .bar { height: 7px; border-radius: 3px; background: #ea580c; }
        .two-col td.col { width: 50%; vertical-align: top; padding-right: 10px; }
        .two-col td.col + td.col { padding-right: 0; padding-left: 10px; }
        .box { border: 1px solid #e5e7eb; border-radius: 6px; padding: 8px 10px; }
        .legend span { margin-right: 12px; font-size: 9px; }
        .insights li { margin-bottom: 3px; }
        .note { font-size: 8px; color: #6b7280; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 4px; padding: 6px 8px; margin-top: 16px; }
        .small { font-size: 8px; }
        .break { page-break-before: always; }
        .nowrap { white-space: nowrap; }
    </style>
</head>
<body>
<header>
    <table>
        <tr>
            <td><div class="brand">Mag<span>Link</span></div><div class="muted small">Report statistiche link</div></td>
            <td style="text-align: right;" class="small muted">
                @if($company){{ $company }}<br>@endif
                Generato il {{ $generatedAt->locale('it')->isoFormat('D MMM YYYY, HH:mm') }}
            </td>
        </tr>
    </table>
</header>
<footer>
    <table><tr>
        <td>MagLink · {{ $date($p['from']) }} – {{ $date($p['to']) }}</td>
        <td style="text-align: right;">Pagina <span class="page"></span></td>
    </tr></table>
</footer>

<main>
    <h1>{{ $isComparison ? 'Confronto di '.count($report['links']).' link' : $report['links'][0]['title'] }}</h1>
    <div class="muted">
        Periodo: <strong>{{ $date($p['from']) }} – {{ $date($p['to']) }}</strong> ({{ $p['days'] }} giorni)
        · confrontato con {{ $date($p['previous_from']) }} – {{ $date($p['previous_to']) }}
    </div>
    @unless($isComparison)
        <div class="muted small" style="margin-top: 3px;">
            {{ $report['links'][0]['short_url'] }} → {{ \Illuminate\Support\Str::limit($report['links'][0]['url'], 90) }}
        </div>
    @endunless

    {{-- KPI --}}
    <table class="kpis" style="margin-top: 14px;">
        <tr>
            <td style="width: 25%;"><div class="kpi">
                <div class="label">Visite totali</div>
                <div class="value">{{ $fmt($o['total']) }}</div>
                <div class="sub"><span class="{{ $totalChangeDir }}">{{ $totalChange }}</span> vs periodo prec. ({{ $fmt($o['previous_total']) }})</div>
            </div></td>
            <td style="width: 25%;"><div class="kpi">
                <div class="label">Media giornaliera</div>
                <div class="value">{{ $dec($o['avg_per_day']) }}</div>
                <div class="sub">visite al giorno</div>
            </div></td>
            <td style="width: 25%;"><div class="kpi">
                <div class="label">Giorno di picco</div>
                <div class="value">{{ $fmt($o['peak_value']) }}</div>
                <div class="sub">{{ $date($o['peak_date']) }}</div>
            </div></td>
            <td style="width: 25%;"><div class="kpi">
                <div class="label">Tasso di consenso</div>
                <div class="value">{{ $dec($o['consent_rate']) }}%</div>
                <div class="sub">{{ $fmt($o['with_consent']) }} con consenso · {{ $fmt($o['anonymous']) }} anonime</div>
            </div></td>
        </tr>
    </table>

    {{-- Andamento --}}
    <h2>Andamento giornaliero</h2>
    <div class="box">
        <img src="{{ $charts->trend($report['series'], $report['labels']) }}" style="width: 100%;">
        @if($isComparison)
            <div class="legend" style="margin-top: 4px;">
                @foreach($report['links'] as $link)
                    <span><span class="dot" style="background: {{ $link['color'] }};"></span>{{ \Illuminate\Support\Str::limit($link['title'], 28) }}</span>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Insight automatici --}}
    <h2>In sintesi</h2>
    <ul class="insights" style="margin: 0; padding-left: 14px;">
        @if($o['total'] === 0)
            <li>Nessuna visita registrata nel periodo selezionato.</li>
        @else
            <li>
                {{ $fmt($o['total']) }} visite in {{ $p['days'] }} giorni
                @if($o['change'] === null) (nessuna visita nel periodo precedente).
                @else, <span class="{{ $totalChangeDir }}">{{ $totalChange }}</span> rispetto al periodo precedente.
                @endif
            </li>
            @if($bestWeekday !== null)
                <li>Il giorno della settimana più performante è il <strong>{{ ['lunedì','martedì','mercoledì','giovedì','venerdì','sabato','domenica'][$bestWeekday] }}</strong>; la fascia oraria con più visite è <strong>{{ $hourLabels[$bestHour] }}:00–{{ $hourLabels[$bestHour] }}:59</strong>.</li>
            @endif
            @if($isComparison)
                @php $top = collect($report['links'])->sortByDesc('total')->first(); @endphp
                <li><strong>{{ $top['title'] }}</strong> è il link con più visite ({{ $fmt($top['total']) }}, {{ $dec($top['share']) }}% del totale).</li>
            @endif
            @if(!empty($report['countries']))
                <li>Paese principale (visite con consenso): <strong>{{ $report['countries'][0]['label'] }}</strong> ({{ $dec($report['countries'][0]['percentage']) }}%).</li>
            @endif
            @if(!empty($report['devices']))
                <li>Dispositivo più usato: <strong>{{ ucfirst($report['devices'][0]['label']) }}</strong> ({{ $dec($report['devices'][0]['percentage']) }}%).</li>
            @endif
        @endif
    </ul>

    {{-- Confronto / dettaglio link --}}
    <h2>{{ $isComparison ? 'Confronto tra link' : 'Dettaglio link' }}</h2>
    <table class="data keep">
        <thead>
            <tr>
                <th>Link</th>
                <th class="num">Visite</th>
                @if($isComparison)<th style="width: 18%;">Quota</th>@endif
                <th class="num">Media/g</th>
                <th class="num">Giorni attivi</th>
                <th class="num">Picco</th>
                <th class="num">Consenso</th>
                <th class="num">vs prec.</th>
            </tr>
        </thead>
        <tbody>
            @foreach($report['links'] as $link)
                @php [$c, $dir] = $change($link['change']); @endphp
                <tr>
                    <td>
                        @if($isComparison)<span class="dot" style="background: {{ $link['color'] }};"></span>@endif
                        <strong>{{ \Illuminate\Support\Str::limit($link['title'], 34) }}</strong><br>
                        <span class="muted small">/{{ $link['slug'] }}{{ $link['is_active'] ? '' : ' · disattivo' }}</span>
                    </td>
                    <td class="num"><strong>{{ $fmt($link['total']) }}</strong></td>
                    @if($isComparison)
                        <td>
                            <div class="bar-bg"><div class="bar" style="width: {{ max($link['share'], 0.5) }}%; background: {{ $link['color'] }};"></div></div>
                            <span class="small muted">{{ $dec($link['share']) }}%</span>
                        </td>
                    @endif
                    <td class="num">{{ $dec($link['avg_per_day']) }}</td>
                    <td class="num">{{ $link['active_days'] }}/{{ $p['days'] }}</td>
                    <td class="num">{{ $fmt($link['peak_value']) }}<br><span class="small muted">{{ $link['peak_date'] ? \Carbon\Carbon::parse($link['peak_date'])->format('d/m') : '—' }}</span></td>
                    <td class="num">{{ $dec($link['consent_rate']) }}%</td>
                    <td class="num {{ $dir }}">{{ $c }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Distribuzione temporale --}}
    <div class="keep">
    <h2>Quando arrivano le visite</h2>
    <table class="two-col">
        <tr>
            <td class="col"><div class="box">
                <div class="small muted" style="margin-bottom: 4px;">Per giorno della settimana</div>
                <img src="{{ $charts->columns($report['weekdays'], $weekdayLabels) }}" style="width: 100%;">
            </div></td>
            <td class="col"><div class="box">
                <div class="small muted" style="margin-bottom: 4px;">Per fascia oraria</div>
                <img src="{{ $charts->columns($report['hours'], $hourLabels, '#2563eb') }}" style="width: 100%;">
            </div></td>
        </tr>
    </table>
    </div>

    {{-- Pubblico (solo consenso): il titolo resta unito al primo blocco --}}
    @php $chunks = array_chunk($breakdowns, 2, true); @endphp
    @foreach($chunks as $pair)
        @if($loop->first)
        <div class="keep">
        <h2>Pubblico e sorgenti</h2>
        <div class="muted small" style="margin-bottom: 8px;">
            Basato sulle {{ $fmt($o['with_consent']) }} visite con consenso al tracciamento ({{ $dec($o['consent_rate']) }}% del totale).
        </div>
        @endif
        <table class="two-col keep" style="margin-bottom: 14px;">
            <tr>
                @foreach($pair as $title => $rows)
                    <td class="col"><div class="box">
                        <div style="font-weight: bold; margin-bottom: 6px;">{{ $title }}</div>
                        @if(empty($rows))
                            <div class="muted small">Nessun dato disponibile</div>
                        @else
                            <table class="data">
                                @foreach($rows as $row)
                                    <tr>
                                        <td style="width: 38%;">{{ \Illuminate\Support\Str::limit($title === 'Dispositivi' ? ucfirst($row['label']) : $row['label'], 22) }}</td>
                                        <td style="width: 40%;"><div class="bar-bg"><div class="bar" style="width: {{ max($row['percentage'], 0.5) }}%;"></div></div></td>
                                        <td class="num">{{ $fmt($row['count']) }}</td>
                                        <td class="num muted" style="width: 14%;">{{ $dec($row['percentage']) }}%</td>
                                    </tr>
                                @endforeach
                            </table>
                        @endif
                    </div></td>
                @endforeach
                @if(count($pair) === 1)<td class="col"></td>@endif
            </tr>
        </table>
        @if($loop->first)</div>@endif
    @endforeach

    <div class="note">
        <strong>Privacy e GDPR.</strong> Paese, dispositivo, browser, sistema operativo e sorgente vengono raccolti solo quando il visitatore
        fornisce il consenso esplicito. Le {{ $fmt($o['anonymous']) }} visite anonime sono conteggiate nei totali ma non contengono dati personali.
        Le visite dei bot e dei crawler sono escluse.
    </div>
</main>
</body>
</html>
