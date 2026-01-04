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
  departments: string[]
  gapCounts: number[]
  title?: string
}

const props = withDefaults(defineProps<Props>(), {
  title: 'Skill Gaps by Department'
})

const chartSeries = computed(() => [
  {
    name: 'Number of Gaps',
    data: props.gapCounts
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
      horizontal: true,
      dataLabels: {
        position: 'top'
      }
    }
  },
  dataLabels: {
    enabled: true,
    formatter: (val: number) => val.toString(),
    offsetX: -6,
    style: {
      fontSize: '12px',
      colors: ['#fff']
    }
  },
  stroke: {
    show: true,
    width: 1,
    colors: ['#fff']
  },
  xaxis: {
    categories: props.departments,
    title: {
      text: 'Department'
    }
  },
  yaxis: {
    title: {
      text: 'Number of Gaps'
    }
  },
  fill: {
    colors: ['#FF6B6B']
  },
  legend: {
    position: 'bottomRight'
  }
}))
</script>
