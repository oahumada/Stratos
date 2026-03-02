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
    criticalGaps: number;
    highGaps: number;
    mediumGaps: number;
    lowGaps?: number;
    title?: string;
}

const props = withDefaults(defineProps<Props>(), {
    lowGaps: 0,
    title: 'Skill Gaps by Priority',
});

const loading = ref(false);

const hasData = computed(
    () =>
        props.criticalGaps > 0 ||
        props.highGaps > 0 ||
        props.mediumGaps > 0 ||
        props.lowGaps > 0,
);

const chartSeries = computed(() => [
    {
        name: 'Gaps',
        data: [
            { x: 'Critical', y: props.criticalGaps },
            { x: 'High', y: props.highGaps },
            { x: 'Medium', y: props.mediumGaps },
            { x: 'Low', y: props.lowGaps },
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
            columnWidth: '60%',
            borderRadius: 8,
            distributed: true,
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
            formatter: (val: number) => val + ' gaps',
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
    xaxis: {
        labels: {
            style: { colors: 'rgba(255, 255, 255, 0.5)', fontWeight: 600 },
        },
        axisBorder: { show: false },
        axisTicks: { show: false },
    },
    yaxis: {
        labels: {
            style: { colors: 'rgba(255, 255, 255, 0.5)' },
        },
    },
    grid: {
        borderColor: 'rgba(255, 255, 255, 0.05)',
        strokeDashArray: 4,
    },
    legend: {
        show: false,
    },
    colors: ['#f43f5e', '#fb923c', '#6366f1', '#10b981'], // Rose (Critical), Orange (High), Indigo (Medium), Emerald (Low)
}));
</script>
