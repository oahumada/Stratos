<template>
  <div class="rounded-lg border border-gray-200 p-6 dark:border-gray-700">
    <div class="flex items-start justify-between">
      <div>
        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ label }}</p>
        <p class="mt-3 text-3xl font-bold text-gray-900 dark:text-white">{{ value }}</p>
      </div>
      <div
        class="rounded-lg p-3"
        :class="{
          'bg-blue-100 text-blue-600 dark:bg-blue-950 dark:text-blue-300': color === 'blue',
          'bg-green-100 text-green-600 dark:bg-green-950 dark:text-green-300': color === 'green',
          'bg-yellow-100 text-yellow-600 dark:bg-yellow-950 dark:text-yellow-300': color === 'yellow',
          'bg-red-100 text-red-600 dark:bg-red-950 dark:text-red-300': color === 'red',
          'bg-purple-100 text-purple-600 dark:bg-purple-950 dark:text-purple-300': color === 'purple',
        }"
      >
        <Icon :icon="icon" :size="24" />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { Icon } from '@phosphor-icons/vue'

defineProps<{
  label: string
  value: string | number
  icon: string
  color?: 'blue' | 'green' | 'yellow' | 'red' | 'purple'
}>()
</script>
