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
            height="300"
        />
        <div v-else class="pa-4 text-center">
            <p class="text-subtitle-2 text-white/50">No data available</p>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue';

interface Props {
    departments: string[];
    gapCounts: number[];
    title?: string;
}

const props = withDefaults(defineProps<Props>(), {
    title: 'Skill Gaps by Department',
});

const loading = ref(false);

const hasData = computed(
    () =>
        props.departments.length > 0 &&
        props.gapCounts.length > 0 &&
        props.gapCounts.some((count) => count > 0),
);

const chartSeries = computed(() => [
    {
        name: 'Number of Gaps',
        data: props.gapCounts,
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
            horizontal: true,
            borderRadius: 6,
            dataLabels: {
                position: 'right',
            },
        },
    },
    dataLabels: {
        enabled: true,
        formatter: (val: number) => val.toString(),
        offsetX: 10,
        style: {
            fontSize: '12px',
            colors: ['#fff'],
            fontWeight: 'bold',
        },
    },
    stroke: { show: false },
    xaxis: {
        categories: props.departments,
        labels: {
            style: { colors: 'rgba(255, 255, 255, 0.5)', fontWeight: 500 },
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
        xaxis: { lines: { show: true } },
        yaxis: { lines: { show: false } },
    },
    fill: {
        type: 'gradient',
        gradient: {
            shade: 'dark',
            type: 'horizontal',
            gradientToColors: ['#10b981'], // emerald
            stops: [0, 100],
        },
    },
    colors: ['#6366f1'], // indigo base
    legend: {
        show: false,
    },
    theme: { mode: 'dark' },
    tooltip: {
        theme: 'dark',
        y: {
            formatter: (val: number) => val.toString() + ' gaps',
        },
    },
}));
</script>
