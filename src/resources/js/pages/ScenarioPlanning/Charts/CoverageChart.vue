<template>
  <div class="w-100">
    <v-progress-linear v-if="loading" indeterminate class="mb-4"></v-progress-linear>
    <apexchart
      v-if="chartSeries.length > 0"
      type="donut"
      :options="chartOptions"
      :series="chartSeries"
      height="350"
    />
    <div v-else class="text-center pa-4">
      <p class="text-subtitle-2">No data available</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue'

interface Props {
  internalCoverage: number
  externalGap: number
  title?: string
}

const props = withDefaults(defineProps<Props>(), {
  title: 'Coverage Analysis'
})

const loading = ref(false)

const chartSeries = computed(() => {
  // Siempre retornar datos, incluso si son 0
  return [props.internalCoverage, props.externalGap]
})

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
