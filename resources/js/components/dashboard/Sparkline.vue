<script setup lang="ts">
import { computed, useId } from 'vue';

const props = withDefaults(
    defineProps<{
        values: number[];
        color: string;
        width?: number;
        height?: number;
    }>(),
    { width: 96, height: 32 },
);

const gradientId = `spark-${useId()}`;
const padding = 2;

const points = computed(() => {
    const values = props.values.length > 1 ? props.values : [0, ...(props.values.length ? props.values : [0])];
    const max = Math.max(...values);
    const min = Math.min(...values);
    const range = max - min || 1;
    const stepX = (props.width - padding * 2) / (values.length - 1);
    const usableHeight = props.height - padding * 2;

    return values.map((value, index) => ({
        x: padding + index * stepX,
        // Serie piatta: linea a metà altezza invece che schiacciata sul fondo
        y: max === min ? props.height / 2 : padding + usableHeight - ((value - min) / range) * usableHeight,
    }));
});

// Curva morbida (Catmull-Rom convertita in Bézier cubiche)
const linePath = computed(() => {
    const p = points.value;
    let d = `M ${p[0].x} ${p[0].y}`;
    for (let i = 0; i < p.length - 1; i++) {
        const p0 = p[i - 1] ?? p[i];
        const p1 = p[i];
        const p2 = p[i + 1];
        const p3 = p[i + 2] ?? p2;
        const cp1x = p1.x + (p2.x - p0.x) / 6;
        const cp1y = p1.y + (p2.y - p0.y) / 6;
        const cp2x = p2.x - (p3.x - p1.x) / 6;
        const cp2y = p2.y - (p3.y - p1.y) / 6;
        d += ` C ${cp1x} ${cp1y}, ${cp2x} ${cp2y}, ${p2.x} ${p2.y}`;
    }
    return d;
});

const areaPath = computed(() => {
    const p = points.value;
    return `${linePath.value} L ${p[p.length - 1].x} ${props.height} L ${p[0].x} ${props.height} Z`;
});
</script>

<template>
    <svg :width="width" :height="height" :viewBox="`0 0 ${width} ${height}`" fill="none" aria-hidden="true" class="overflow-visible">
        <defs>
            <linearGradient :id="gradientId" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0%" :stop-color="color" stop-opacity="0.25" />
                <stop offset="100%" :stop-color="color" stop-opacity="0" />
            </linearGradient>
        </defs>
        <path :d="areaPath" :fill="`url(#${gradientId})`" />
        <path :d="linePath" :stroke="color" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" />
    </svg>
</template>
