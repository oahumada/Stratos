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
            <p class="text-subtitle-2">No data available</p>
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
        name: 'Current',
        data: [props.currentHeadcount],
    },
    {
        name: 'Projected',
        data: [props.projectedHeadcount],
    },
]);

const chartOptions = computed(() => ({
    chart: {
        type: 'bar',
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
        bar: {
            horizontal: false,
            columnWidth: '55%',
            borderRadius: 4,
            dataLabels: {
                position: 'top',
            },
        },
    },
    dataLabels: {
        enabled: true,
        offsetY: -20,
        style: {
            fontSize: '12px',
            colors: ['#304758'],
        },
    },
    stroke: {
        show: true,
        width: 2,
        colors: ['transparent'],
    },
    tooltip: {
        y: {
            formatter: (val: number) => val + ' people',
        },
    },
    fill: {
        opacity: 1,
    },
    legend: {
        position: 'top',
        horizontalAlign: 'right',
    },
    xaxis: {
        categories: [props.title],
        axisBorder: {
            show: true,
        },
        axisTicks: {
            show: true,
        },
    },
    yaxis: {
        title: {
            text: 'Headcount',
        },
    },
    colors: ['#42A5F5', '#EF5350'],
}));
</script>
