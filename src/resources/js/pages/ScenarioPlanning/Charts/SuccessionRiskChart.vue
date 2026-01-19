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
            <p class="text-subtitle-2">No data available</p>
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
    // Siempre retornar datos, incluso si es 0
    return [props.riskPercentage];
});

const chartOptions = computed(() => ({
    chart: {
        type: 'radialBar',
        toolbar: {
            show: true,
            tools: {
                download: true,
                selection: true,
                zoom: true,
                zoomin: true,
                zoomout: true,
                pan: true,
                reset: true,
            },
        },
    },
    plotOptions: {
        radialBar: {
            startAngle: -90,
            endAngle: 90,
            track: {
                background: '#f2f2f2',
                strokeWidth: '97%',
                margin: 5,
                dropShadow: {
                    enabled: true,
                    top: 2,
                    left: 0,
                    color: '#999',
                    opacity: 1,
                    blur: 2,
                },
            },
            dataLabels: {
                name: {
                    show: true,
                    offsetY: -10,
                    color: '#888',
                    fontSize: '13px',
                },
                value: {
                    offsetY: 0,
                    color: '#111',
                    fontSize: '16px',
                    show: true,
                    formatter: (val: number) => val.toFixed(0) + '%',
                },
            },
        },
    },
    fill: {
        type: 'gradient',
        gradient: {
            shade: 'light',
            shadeIntensity: 0.1,
            inverseColors: false,
            opacityFrom: 1,
            opacityTo: 1,
            stops: [0, 50, 100],
        },
    },
    stroke: {
        dashArray: 4,
    },
    labels: [props.title],
    colors: props.riskPercentage > 25 ? ['#EF5350'] : '#66BB6A',
}));
</script>
