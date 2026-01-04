<template>
  <div class="w-100">
    <apexchart
      type="donut"
      :options="chartOptions"
      :series="chartSeries"
      height="350"
    />
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import VueApexCharts from 'vue3-apexcharts'

interface Props {
  internalCoverage: number
  externalGap: number
  title?: string
}

const props = withDefaults(defineProps<Props>(), {
  title: 'Coverage Analysis'
})

const chartSeries = computed(() => [
  props.internalCoverage,
  props.externalGap
])

const chartOptions = computed(() => ({
  chart: {
    type: 'donut',
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
  labels: ['Internal Coverage', 'External Gap'],
  legend: {
    position: 'bottom'
  },
  dataLabels: {
    enabled: true,
    formatter: (val: number) => val.toFixed(1) + '%'
  },
  colors: ['#66BB6A', '#FFA726'],
  plotOptions: {
    pie: {
      donut: {
        size: '65%',
        labels: {
          show: true,
          name: {
            fontSize: '16px'
          },
          value: {
            fontSize: '16px',
            formatter: (val: number) => val.toFixed(1) + '%'
          }
        }
      }
    }
  },
  tooltip: {
    y: {
      formatter: (val: number) => val.toFixed(1) + '%'
    }
  }
}))
</script>
