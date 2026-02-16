<template>
  <div>
    <v-menu offset-y>
      <template #activator="{ props }">
        <v-icon v-bind="props" small class="ml-2 cursor-pointer">mdi-information-outline</v-icon>
      </template>
      <v-card style="max-width:320px;">
        <v-card-text>
          <div class="help-title"><strong>{{ title }}</strong></div>
          <div class="help-desc" v-if="description">{{ description }}</div>
          <div class="help-example" v-if="example">
            <div style="margin-top:8px"><em>Ejemplo:</em></div>
            <pre style="background:#f5f5f7;padding:8px;border-radius:4px;margin:6px 0;white-space:pre-wrap">{{ example }}</pre>
          </div>
        </v-card-text>
      </v-card>
    </v-menu>
  </div>
</template>

<script setup lang="ts">

defineProps({
  title: { type: String, required: true },
  description: { type: String, required: false },
  example: { type: String, required: false },
})
</script>

<style scoped>
.help-title { margin-bottom: 6px }
.help-desc { color: #444; font-size: 0.95rem }
.help-example pre { font-size: 0.9rem }
</style>
