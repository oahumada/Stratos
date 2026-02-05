<template>
  <div class="bars-editor">
    <label>BARS (JSON)</label>
    <textarea v-model="text" @input="onInput" rows="6" />
  </div>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue'
const props = defineProps<{ modelValue: any }>()
const emit = defineEmits(['update:modelValue'])

const text = ref(JSON.stringify(props.modelValue ?? {}, null, 2))

watch(() => props.modelValue, (v) => {
  text.value = JSON.stringify(v ?? {}, null, 2)
})

function onInput() {
  try {
    const parsed = JSON.parse(text.value)
    emit('update:modelValue', parsed)
  } catch (e) {
    // If invalid JSON, still emit raw text to allow saving/validation upstream
    emit('update:modelValue', text.value)
  }
}
</script>

<style scoped>
.bars-editor textarea { width: 100%; font-family: monospace; }
.bars-editor label { display:block; margin-bottom:0.25rem }
</style>
