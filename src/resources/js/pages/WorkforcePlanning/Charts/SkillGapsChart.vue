<template>
  <div class="w-100">
    <v-progress-linear v-if="loading" indeterminate class="mb-4"></v-progress-linear>
    <apexchart
      v-if="hasData"
      type="bar"
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
  criticalGaps: number
  highGaps: number
  mediumGaps: number
  lowGaps?: number
  title?: string
}

const props = withDefaults(defineProps<Props>(), {
  lowGaps: 0,
  title: 'Skill Gaps by Priority'
})

const loading = ref(false)

const hasData = computed(() => 
  props.criticalGaps > 0 || props.highGaps > 0 || 
  props.mediumGaps > 0 || props.lowGaps > 0
)

const chartSeries = computed(() => [
  {
    name: 'Number of Gaps',
    data: [props.criticalGaps, props.highGaps, props.mediumGaps, props.lowGaps]
  }
])

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
        reset: true
      }
    }
  },
  plotOptions: {
    bar: {
      horizontal: false,
      columnWidth: '60%',
      borderRadius: 4,
      dataLabels: {
        position: 'top'
      }
    }
  },
  dataLabels: {
    enabled: true,
    offsetY: -20,
    style: {
      fontSize: '12px',
      colors: ['#304758']
    }
  },
  stroke: {
    show: true,
    width: 2,
    colors: ['transparent']
  },
  xaxis: {
    categories: ['Critical', 'High', 'Medium', 'Low'],
    axisBorder: {
      show: true
    }
  },
  yaxis: {
    title: {
      text: 'Number of Gaps'
    }
  },
  fill: {
    opacity: 1
  },
  colors: ['#EF5350'],
  tooltip: {
    y: {
      formatter: (val: number) => val + ' gaps'
    }
  }
}))
</script>
