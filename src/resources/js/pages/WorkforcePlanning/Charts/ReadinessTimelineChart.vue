<template>
  <div class="w-100">
    <apexchart
      type="bar"
      :options="chartOptions"
      :series="chartSeries"
      height="300"
    />
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import VueApexCharts from 'vue3-apexcharts'

interface Props {
  immediatelyReady: number
  readyWithinSix: number
  readyWithinTwelve: number
  beyondTwelve: number
  title?: string
}

const props = withDefaults(defineProps<Props>(), {
  title: 'Candidate Readiness Timeline'
})

const chartSeries = computed(() => [
  {
    name: 'Candidates',
    data: [
      props.immediatelyReady,
      props.readyWithinSix,
      props.readyWithinTwelve,
      props.beyondTwelve
    ]
  }
])

const chartOptions = computed(() => ({
  chart: {
    type: 'bar',
    stacked: true,
    toolbar: {
      show: true,
      tools: {
        download: true,
        selection: true,
        zoom: true,
        zoomin: true,
        zoomout: true,
        pan: true,
        reset: true
      }
    }
  },
  plotOptions: {
    bar: {
      horizontal: false,
      columnWidth: '55%',
      borderRadius: 4,
      dataLabels: {
        position: 'top'
      }
    }
  },
  dataLabels: {
    enabled: true,
    formatter: (val: number) => val.toString(),
    offsetY: -20,
    style: {
      fontSize: '12px',
      colors: ['#304050']
    }
  },
  stroke: {
    show: true,
    width: 1,
    colors: ['#fff']
  },
  tooltip: {
    shared: true,
    intersect: false,
    y: {
      formatter: (val: number) => val.toString() + ' candidates'
    }
  },
  xaxis: {
    categories: ['Immediately Ready', 'Within 6 Months', 'Within 12 Months', 'Beyond 12 Months']
  },
  yaxis: {
    title: {
      text: 'Number of Candidates'
    }
  },
  fill: {
    colors: ['#42A5F5']
  },
  legend: {
    position: 'topRight'
  }
}))
</script>
