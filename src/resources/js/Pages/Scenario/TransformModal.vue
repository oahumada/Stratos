<template>
  <div class="transform-modal">
    <form @submit.prevent="submit">
      <div>
        <label for="transform-name">Nombre</label>
        <input id="transform-name" v-model="form.name" required />
      </div>
      <div>
        <label for="transform-desc">Descripción</label>
        <textarea id="transform-desc" v-model="form.description"></textarea>
      </div>
          <div>
            <BarsEditor v-model="form.bars" />
          </div>
          <div>
            <button type="submit">Transformar</button>
            <button type="button" @click="$emit('close')">Cancelar</button>
          </div>
    </form>
  </div>
</template>

<script setup lang="ts">
import { reactive, ref, onMounted } from 'vue'
import { useTransformStore } from '@/stores/transformStore'
import BarsEditor from '@/components/BarsEditor.vue'

const props = defineProps<{ competencyId: number }>()
const emit = defineEmits<{
  (e: 'transformed', payload: any): void
  (e: 'close'): void
}>()

const form = reactive({ name: '', description: '' })
const versions = ref<any[]>([])
const loading = ref(false)
const store = useTransformStore()

onMounted(async () => {
  loading.value = true
  try {
    const v = await store.getVersions(props.competencyId)
    versions.value = v || []
  } catch (err) {
    // ignore
  } finally {
    loading.value = false
  }
})

async function submit() {
  try {
    const data = await store.transformCompetency(props.competencyId, form)
    const v = await store.getVersions(props.competencyId)
    versions.value = v || []
    emit('transformed', data)
  } catch (err) {
    console.error(err)
    alert('Error al transformar')
  }
}
</script>

<style scoped>
.transform-modal { padding: 1rem; }
label { display:block; margin-top:0.5rem }
input,textarea { width:100% }
</style>
