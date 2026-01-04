<template>
  <div class="w-100">
    <apexchart
      type="area"
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
  scores: number[]
  title?: string
}

const props = withDefaults(defineProps<Props>(), {
  title: 'Match Score Distribution'
})

const getDistribution = () => {
  const bins = {
    '90-100': 0,
    '80-89': 0,
    '70-79': 0,
    '60-69': 0,
    '50-59': 0,
    'Below 50': 0
  }

  props.scores.forEach(score => {
    if (score >= 90) bins['90-100']++
    else if (score >= 80) bins['80-89']++
    else if (score >= 70) bins['70-79']++
    else if (score >= 60) bins['60-69']++
    else if (score >= 50) bins['50-59']++
    else bins['Below 50']++
  })

  return bins
}

const chartSeries = computed(() => {
  const dist = getDistribution()
  return [
    {
      name: 'Candidates',
      data: Object.values(dist)
    }
  ]
})

const chartOptions = computed(() => ({
  chart: {
    type: 'area',
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
  dataLabels: {
    enabled: false
  },
  stroke: {
    curve: 'smooth',
    width: 2,
    colors: ['#42A5F5']
  },
  fill: {
    type: 'gradient',
    gradient: {
      opacityFrom: 0.6,
      opacityTo: 0.1
    }
  },
  xaxis: {
    categories: ['90-100', '80-89', '70-79', '60-69', '50-59', 'Below 50'],
    title: {
      text: 'Match Score Range'
    }
  },
  yaxis: {
    title: {
      text: 'Number of Candidates'
    }
  },
  tooltip: {
    shared: true,
    intersect: false,
    y: {
      formatter: (val: number) => val.toString() + ' candidates'
    }
  }
}))
</script>
