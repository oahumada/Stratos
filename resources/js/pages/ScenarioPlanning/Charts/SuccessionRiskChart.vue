<template>
    <div class="w-100">
        <v-progress-linear
            v-if="loading"
            indeterminate
            class="mb-4"
        ></v-progress-linear>
        <apexchart
            v-if="chartSeries.length > 0"
            type="radialBar"
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
    riskPercentage: number;
    title?: string;
}

const props = withDefaults(defineProps<Props>(), {
    title: 'Succession Risk',
});

const loading = ref(false);

const chartSeries = computed(() => {
    return [props.riskPercentage];
});

const chartOptions = computed(() => ({
    chart: {
        type: 'radialBar',
        toolbar: { show: false },
        background: 'transparent',
    },
    plotOptions: {
        radialBar: {
            startAngle: -90,
            endAngle: 90,
            track: {
                background: 'rgba(255, 255, 255, 0.05)',
                strokeWidth: '97%',
                margin: 5,
            },
            dataLabels: {
                name: {
                    show: true,
                    offsetY: -10,
                    color: 'rgba(255, 255, 255, 0.5)',
                    fontSize: '13px',
                },
                value: {
                    offsetY: 0,
                    color: '#fff',
                    fontSize: '24px',
                    fontWeight: 'bold',
                    show: true,
                    formatter: (val: number) => val.toFixed(0) + '%',
                },
            },
        },
    },
    fill: {
        type: 'gradient',
        gradient: {
            shade: 'dark',
            type: 'horizontal',
            gradientToColors: ['#f43f5e'], // rose
            stops: [0, 100],
        },
    },
    stroke: {
        dashArray: 4,
    },
    labels: [props.title],
    theme: { mode: 'dark' },
    colors: ['#6366f1'], // indigo
}));
</script>
