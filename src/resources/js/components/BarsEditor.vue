<template>
  <div class="bars-editor">
    <div class="mode-toggle">
      <button type="button" :class="{active: mode==='structured'}" @click="mode='structured'">Estructurado</button>
      <button type="button" :class="{active: mode==='json'}" @click="mode='json'">JSON</button>
    </div>

    <div v-if="mode==='structured'" class="structured">
      <div class="section" v-for="key in keys" :key="key">
        <label :for="key">{{ titleFor(key) }}</label>
        <div class="items">
          <div v-for="(item, idx) in bars[key]" :key="idx" class="item-row">
            <input :data-testid="`${key}-${idx}`" :placeholder="titleFor(key) + ' item ' + (idx+1)" v-model="bars[key][idx]" @input="onStructuredChange" />
            <button type="button" class="remove" @click="removeItem(key, idx)">Eliminar</button>
          </div>
        </div>
        <button type="button" :data-testid="`add-${key}`" class="add" @click="addItem(key)">Agregar {{ titleFor(key) }}</button>
      </div>
    </div>

    <div v-else class="json-mode">
      <label>JSON</label>
      <textarea data-testid="json-textarea" v-model="text" @input="onInput" rows="8"></textarea>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch, reactive, computed } from 'vue'

const props = defineProps<{ modelValue?: any }>()
const emit = defineEmits(['update:modelValue'])

const mode = ref<'structured'|'json'>('structured')

const defaultBars = () => ({
  behaviour: [] as string[],
  attitude: [] as string[],
  responsibility: [] as string[],
  skills: [] as string[],
})

const bars = reactive<any>(props.modelValue ? normalize(props.modelValue) : defaultBars())

const text = ref(JSON.stringify(props.modelValue ?? bars, null, 2))

function normalize(value: any) {
  if (!value) return defaultBars()
  // If value is a string (maybe JSON) try parse
  if (typeof value === 'string') {
    try { return JSON.parse(value) } catch { return defaultBars() }
  }
  // Ensure keys exist and are arrays
  return {
    behaviour: Array.isArray(value.behaviour) ? value.behaviour.slice() : defaultBars().behaviour,
    attitude: Array.isArray(value.attitude) ? value.attitude.slice() : defaultBars().attitude,
    responsibility: Array.isArray(value.responsibility) ? value.responsibility.slice() : defaultBars().responsibility,
    skills: Array.isArray(value.skills) ? value.skills.slice() : defaultBars().skills,
  }
}

watch(() => props.modelValue, (v) => {
  if (mode.value === 'json') {
    text.value = JSON.stringify(v ?? bars, null, 2)
  } else {
    const n = normalize(v)
    Object.keys(n).forEach(k => { bars[k] = n[k] })
  }
})

function onInput() {
  try {
    const parsed = JSON.parse(text.value)
    emit('update:modelValue', parsed)
  } catch (e) {
    emit('update:modelValue', text.value)
  }
}

const keys = ['behaviour','attitude','responsibility','skills'] as const

function titleFor(k: string) {
  if (k === 'behaviour') return 'Behaviour'
  if (k === 'attitude') return 'Attitude'
  if (k === 'responsibility') return 'Responsibility'
  return 'Skills'
}

function addItem(key: string) {
  bars[key].push('')
  emit('update:modelValue', structuredValue())
}

function removeItem(key: string, idx: number) {
  bars[key].splice(idx, 1)
  emit('update:modelValue', structuredValue())
}

function onStructuredChange() {
  emit('update:modelValue', structuredValue())
}

function structuredValue() {
  return {
    behaviour: bars.behaviour.slice(),
    attitude: bars.attitude.slice(),
    responsibility: bars.responsibility.slice(),
    skills: bars.skills.slice(),
  }
}

const invalid = computed(() => {
  return keys.some(k => bars[k].some((v: string) => !v || String(v).trim() === ''))
})
</script>

<style scoped>
.bars-editor textarea { width: 100%; font-family: monospace; }
.bars-editor label { display:block; margin-bottom:0.25rem }
</style>
