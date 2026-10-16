<?php

namespace App\Support;

use Carbon\Carbon;

/**
 * Grafici SVG semplici per i report PDF (dompdf non esegue JavaScript).
 * Restituiscono data URI da usare come <img src="...">.
 * Si usano solo primitive supportate da php-svg-lib: rect, line, polyline, circle, text.
 */
class ReportCharts
{
    private const FONT = 'font-family="DejaVu Sans, sans-serif"';

    /**
     * Andamento giornaliero: barre per un solo link, linee per il confronto tra più link.
     *
     * @param  array<int, array{label: string, color: string, values: array<int, int>}>  $series
     * @param  array<int, string>  $labels  date Y-m-d
     */
    public function trend(array $series, array $labels, int $width = 720, int $height = 240): string
    {
        $pad = ['l' => 40, 'r' => 12, 't' => 12, 'b' => 28];
        $plotW = $width - $pad['l'] - $pad['r'];
        $plotH = $height - $pad['t'] - $pad['b'];
        $count = max(count($labels), 1);
        $max = $this->niceMax(max(1, ...array_map(fn ($s) => max([0, ...$s['values']]), $series ?: [['values' => [0]]])));

        $svg = $this->grid($pad, $plotW, $plotH, $max, $width);

        $step = $plotW / $count;
        if (count($series) === 1) {
            $barW = max(1, $step * 0.7);
            foreach ($series[0]['values'] as $i => $v) {
                if ($v <= 0) {
                    continue;
                }
                $h = $v / $max * $plotH;
                $x = $pad['l'] + $i * $step + ($step - $barW) / 2;
                $y = $pad['t'] + $plotH - $h;
                $svg .= sprintf('<rect x="%.1f" y="%.1f" width="%.1f" height="%.1f" fill="%s" rx="1.5"/>', $x, $y, $barW, $h, $series[0]['color']);
            }
        } else {
            foreach ($series as $s) {
                $points = [];
                foreach ($s['values'] as $i => $v) {
                    $points[] = sprintf('%.1f,%.1f', $pad['l'] + $i * $step + $step / 2, $pad['t'] + $plotH - $v / $max * $plotH);
                }
                $svg .= sprintf('<polyline points="%s" fill="none" stroke="%s" stroke-width="2"/>', implode(' ', $points), $s['color']);
            }
        }

        // Etichette asse X: al massimo ~10 date
        $every = (int) ceil($count / 10);
        foreach ($labels as $i => $date) {
            if ($i % $every !== 0) {
                continue;
            }
            $x = $pad['l'] + $i * $step + $step / 2;
            $svg .= sprintf('<text x="%.1f" y="%d" font-size="9" fill="#6b7280" text-anchor="middle" %s>%s</text>',
                $x, $height - 10, self::FONT, Carbon::parse($date)->format('d/m'));
        }

        return $this->wrap($svg, $width, $height);
    }

    /**
     * Istogramma verticale con etichette (giorni della settimana, fasce orarie...).
     *
     * @param  array<int, int>  $values
     * @param  array<int, string>  $labels
     */
    public function columns(array $values, array $labels, string $color = '#ea580c', int $width = 340, int $height = 170): string
    {
        $pad = ['l' => 32, 'r' => 6, 't' => 10, 'b' => 22];
        $plotW = $width - $pad['l'] - $pad['r'];
        $plotH = $height - $pad['t'] - $pad['b'];
        $count = max(count($values), 1);
        $max = $this->niceMax(max(1, ...$values));

        $svg = $this->grid($pad, $plotW, $plotH, $max, $width);
        $step = $plotW / $count;
        $barW = $step * 0.68;
        $peak = max($values);
        $every = $count > 12 ? 3 : 1;

        foreach ($values as $i => $v) {
            $x = $pad['l'] + $i * $step + ($step - $barW) / 2;
            if ($v > 0) {
                $h = $v / $max * $plotH;
                $fill = $v === $peak ? $color : $color.'99';
                $svg .= sprintf('<rect x="%.1f" y="%.1f" width="%.1f" height="%.1f" fill="%s" rx="1.5"/>', $x, $pad['t'] + $plotH - $h, $barW, $h, $fill);
            }
            if ($i % $every === 0) {
                $svg .= sprintf('<text x="%.1f" y="%d" font-size="9" fill="#6b7280" text-anchor="middle" %s>%s</text>',
                    $x + $barW / 2, $height - 8, self::FONT, htmlspecialchars($labels[$i]));
            }
        }

        return $this->wrap($svg, $width, $height);
    }

    private function grid(array $pad, float $plotW, float $plotH, int $max, int $width): string
    {
        $svg = '';
        for ($i = 0; $i <= 4; $i++) {
            $y = $pad['t'] + $plotH - $plotH * $i / 4;
            $svg .= sprintf('<line x1="%d" y1="%.1f" x2="%.1f" y2="%.1f" stroke="#e5e7eb" stroke-width="0.8"/>', $pad['l'], $y, $pad['l'] + $plotW, $y);
            $svg .= sprintf('<text x="%d" y="%.1f" font-size="9" fill="#9ca3af" text-anchor="end" %s>%s</text>',
                $pad['l'] - 6, $y + 3, self::FONT, number_format($max * $i / 4, 0, ',', '.'));
        }

        return $svg;
    }

    /** Arrotonda il massimo dell'asse a un valore "pulito" divisibile per 4 */
    private function niceMax(int $value): int
    {
        if ($value <= 4) {
            return 4;
        }
        $magnitude = 10 ** (strlen((string) $value) - 1);
        foreach ([1, 1.2, 1.6, 2, 2.4, 3, 4, 5, 6, 8, 10] as $m) {
            $candidate = (int) ($m * $magnitude);
            if ($candidate >= $value && $candidate % 4 === 0) {
                return $candidate;
            }
        }

        return (int) ceil($value / 4) * 4;
    }

    private function wrap(string $content, int $width, int $height): string
    {
        $svg = sprintf('<svg xmlns="http://www.w3.org/2000/svg" width="%d" height="%d" viewBox="0 0 %d %d">%s</svg>', $width, $height, $width, $height, $content);

        return 'data:image/svg+xml;base64,'.base64_encode($svg);
    }
}
