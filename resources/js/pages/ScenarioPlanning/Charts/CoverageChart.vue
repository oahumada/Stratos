<template>
    <div class="w-100">
        <v-progress-linear
            v-if="loading"
            indeterminate
            class="mb-4"
        ></v-progress-linear>
        <apexchart
            v-if="chartSeries.length > 0"
            type="donut"
            :options="chartOptions"
            :series="chartSeries"
            height="350"
        />
        <div v-else class="pa-4 text-center">
            <p class="text-subtitle-2">No data available</p>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue';

interface Props {
    internalCoverage: number;
    externalGap: number;
    title?: string;
}

const props = withDefaults(defineProps<Props>(), {
    title: 'Coverage Analysis',
});

const loading = ref(false);

const chartSeries = computed(() => {
    return [props.internalCoverage, props.externalGap];
});

const chartOptions = computed(() => ({
    chart: {
        type: 'donut',
        toolbar: { show: false },
        background: 'transparent',
    },
    labels: ['Internal Coverage', 'External Gap'],
    legend: {
        position: 'bottom',
        labels: { colors: 'rgba(255, 255, 255, 0.7)' },
    },
    dataLabels: {
        enabled: true,
        formatter: (val: number) => val.toFixed(1) + '%',
        style: { fontSize: '14px', fontWeight: 'bold' },
    },
    stroke: { width: 0 },
    colors: ['#6366f1', '#f43f5e'], // indigo, rose
    plotOptions: {
        pie: {
            donut: {
                size: '65%',
                labels: {
                    show: true,
                    name: {
                        show: true,
                        fontSize: '14px',
                        color: 'rgba(255, 255, 255, 0.5)',
                    },
                    value: {
                        show: true,
                        fontSize: '20px',
                        fontWeight: 'bold',
                        color: '#fff',
                        formatter: (val: string) => val + '%',
                    },
                    total: {
                        show: true,
                        label: 'Total',
                        color: 'rgba(255, 255, 255, 0.5)',
                        formatter: () => '100%',
                    },
                },
            },
        },
    },
    theme: { mode: 'dark' },
    tooltip: {
        theme: 'dark',
        y: {
            formatter: (val: number) => val.toFixed(1) + '%',
        },
    },
}));
</script>
