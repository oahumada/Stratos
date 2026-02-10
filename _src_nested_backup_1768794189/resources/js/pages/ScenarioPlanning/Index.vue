<template>
  <div class="prototype-map-root">
    <p>Prototype Map placeholder</p>
    <pre v-if="scenario">{{ JSON.stringify(scenario, null, 2) }}</pre>
  </div>
</template>

<script setup lang="ts">
defineProps({ scenario: { type: Object, required: false } })
</script>

<style scoped>
.prototype-map-root {
  padding: 16px;
}
</style>
