<template>
    <div class="w-100">
        <v-progress-linear
            v-if="loading"
            indeterminate
            class="mb-4"
        ></v-progress-linear>
        <apexchart
            v-if="hasData"
            type="bar"
            :options="chartOptions"
            :series="chartSeries"
            height="350"
        />
        <div v-else class="pa-4 text-center">
            <p class="text-subtitle-2 text-white/50">No data available</p>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue';

interface Props {
    currentHeadcount: number;
    projectedHeadcount: number;
    title?: string;
}

const props = withDefaults(defineProps<Props>(), {
    title: 'Headcount Planning',
});

const loading = ref(false);

const hasData = computed(
    () => props.currentHeadcount > 0 || props.projectedHeadcount > 0,
);

const chartSeries = computed(() => [
    {
        name: 'Headcount Evolution',
        data: [
            { x: 'Current', y: props.currentHeadcount },
            { x: 'Projected', y: props.projectedHeadcount },
        ],
    },
]);

const chartOptions = computed(() => ({
    chart: {
        type: 'bar',
        toolbar: { show: false },
        background: 'transparent',
    },
    plotOptions: {
        bar: {
            horizontal: false,
            columnWidth: '55%',
            borderRadius: 8,
            distributed: true, // Importante para colores individuales
            dataLabels: {
                position: 'top',
            },
        },
    },
    dataLabels: {
        enabled: true,
        offsetY: -20,
        style: {
            fontSize: '14px',
            colors: ['#fff'],
            fontWeight: 'bold',
        },
    },
    stroke: { show: false },
    theme: { mode: 'dark' },
    tooltip: {
        theme: 'dark',
        y: {
            formatter: (val: number) => val + ' people',
        },
    },
    fill: {
        type: 'gradient',
        gradient: {
            shade: 'dark',
            type: 'vertical',
            opacityFrom: 0.8,
            opacityTo: 0.9,
            stops: [0, 100],
        },
    },
    legend: {
        show: false,
    },
    xaxis: {
        labels: {
            style: { colors: 'rgba(255, 255, 255, 0.5)', fontWeight: 600 },
        },
        axisBorder: { show: false },
        axisTicks: { show: false },
    },
    yaxis: {
        title: {
            text: 'Headcount',
            style: { color: 'rgba(255, 255, 255, 0.4)', fontWeight: 600 },
        },
        labels: {
            style: { colors: 'rgba(255, 255, 255, 0.5)' },
        },
    },
    grid: {
        borderColor: 'rgba(255, 255, 255, 0.05)',
        strokeDashArray: 4,
    },
    colors: ['#6366f1', '#10b981'], // Current (indigo), Projected (emerald)
}));
</script>
