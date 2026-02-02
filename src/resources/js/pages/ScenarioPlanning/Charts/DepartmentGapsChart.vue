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
            <p class="text-subtitle-2">No data available</p>
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
            horizontal: true,
            dataLabels: {
                position: 'top',
            },
        },
    },
    dataLabels: {
        enabled: true,
        formatter: (val: number) => val.toString(),
        offsetX: -6,
        style: {
            fontSize: '12px',
            colors: ['#fff'],
        },
    },
    stroke: {
        show: true,
        width: 1,
        colors: ['#fff'],
    },
    xaxis: {
        categories: props.departments,
        title: {
            text: 'Department',
        },
    },
    yaxis: {
        title: {
            text: 'Number of Gaps',
        },
    },
    fill: {
        colors: ['#FF6B6B'],
    },
    legend: {
        position: 'bottom',
    },
}));
</script>
