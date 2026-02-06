<template>
  <div class="transform-modal">
    <form @submit.prevent="submit">
      <div class="transform-header">
        <h3>{{ versions.length === 0 ? 'Crear versión 1.0' : 'Transformar competencia' }}</h3>
        <p v-if="versions.length > 0" class="text-sm">Versiones existentes: {{ versions.length }}</p>
      </div>

      <div class="guide-toggle-row" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.5rem">
        <div>
          <strong>Guía rápida</strong>
          <small class="text-sm text-gray-500" style="margin-left:0.5rem">(¿Necesitas más detalle?)</small>
        </div>
        <div style="display:flex;gap:0.5rem;align-items:center">
          <a href="/docs/COMIENZA_AQUI_WORKFORCE_PLANNING.md" target="_blank" rel="noopener" class="text-sm" style="color:#1f79ff">Guía completa</a>
          <v-btn variant="text" size="small" class="text-info" title="ver información" @click="showLegend = true">
            <v-icon class="align-middle text-4xl text-info-darken-1">mdi-information-variant-circle</v-icon>
          </v-btn>
          <InfoLegend v-model="showLegend" title="Guía: Transformación / BARS" :items="legendItems" icon="mdi-information-variant-circle" />
        </div>
      </div>
      <div>
        <label for="transform-name">Nombre</label>
        <input id="transform-name" v-model="form.name" required />
      </div>
      <div>
        <label for="transform-desc">Descripción</label>
        <textarea id="transform-desc" v-model="form.description"></textarea>
      </div>
      <div style="margin-top:0.5rem">
        <label style="display:flex;align-items:center;gap:0.5rem">
          <input type="checkbox" v-model="form.create_skills_incubated" />
          <span>Crear skills en incubación</span>
        </label>
        <p class="text-sm text-gray-500" style="margin:0.25rem 0 0 1.8rem">Si está activado, las skills nuevas se crearán con estado 'incubation' cuando se cree la versión.</p>
      </div>
            <div class="bars-container">
              <!-- Force structured mode and ensure editable fields are enabled -->
              <BarsEditor v-model="form.bars" initialMode="structured" :readOnly="false" />
            </div>
          <div class="modal-footer">
            <button type="button" class="btn-secondary" @click="$emit('close')">Cancelar</button>
            <button type="submit" class="btn-primary">{{ versions.length === 0 ? 'Crear versión 1.0' : 'Transformar' }}</button>
          </div>
    </form>
  </div>
</template>

<script setup lang="ts">
import { reactive, ref, onMounted } from 'vue'
import { useTransformStore } from '@/stores/transformStore'
import BarsEditor from '@/components/BarsEditor.vue'
import InfoLegend from '@/components/Ui/InfoLegend.vue'

const props = defineProps<{ competencyId: number }>()
const emit = defineEmits<{
  (e: 'transformed', payload: any): void
  (e: 'close'): void
}>()

const form = reactive({ name: '', description: '', bars: null as any, create_skills_incubated: true })
const showLegend = ref(false)
const legendItems = [
  {
    title: 'Descripción',
    text: `¿Para qué sirve esta pantalla?</strong> Para crear o transformar la definición de una competencia asociada a un rol. Al guardar se crea una nueva versión de la competencia (p. ej. 1.0, 1.1) que puede ser referenciada por mappings de roles. Esto permite mantener historial de definiciones y aplicar cambios sin romper referencias existentes. Rellena un <em>Nombre</em> y una <em>Descripción</em> para esta versión. El editor <strong>BARS</strong> contiene cuatro secciones: <strong>Behaviour</strong>, <strong>Attitude</strong>, <strong>Responsibility</strong> y <strong>Skills</strong>. Describe ejemplos concretos y observables (p. ej. "Comunica ideas claramente", "Aplica metodologías ágiles").`,
  },
  {
    title: 'Consejo',
    text: `Usa el modo <strong>Estructurado</strong> para editar por secciones o <strong>JSON</strong> si vas a pegar una definición completa. Si estás creando la versión inicial (1.0), piensa en la definición base que usará este rol; si estás transformando, documenta los cambios esperados y evidencias.`,
  },
  {
    title: 'Skills incubadas',
    text: `Activa "Crear skills en incubación" para que las skills nuevas se creen con estado <code>incubation</code> al crear la versión. Estas aparecerán en el modal principal como "Skills creadas (incubación)" y pueden revisarse antes de cerrar.`,
  },
  {
    title: 'Ejemplo (JSON)',
    text: 'En este ejemplo la versión define evidencias concretas y una lista de skills relevantes; al crear la versión se guarda esta definición y el mapping del rol puede apuntar a ella. Ejemplo de payload BARS que se guardará en metadata.bars:',
    example: `{
  "behaviour": ["Comunica resultados claramente","Documenta análisis"],
  "attitude": ["Proactivo","Orientado a detalle"],
  "responsibility": ["Mantener pipelines de datos","Revisar calidad de datos"],
  "skills": ["SQL","Python","ETL"]
}`
  },
]
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
    // build payload: place bars inside metadata and separate skill ids/new skills
    const bars = form.bars ?? null
    const skillObjs = Array.isArray(bars?.skills) ? bars.skills : []
    const skill_ids = skillObjs.filter((s: any) => s && s.id).map((s: any) => s.id)
    const new_skills = skillObjs.filter((s: any) => s && !s.id).map((s: any) => s.name)
    const payload: any = {
      name: form.name,
      description: form.description,
      metadata: {
        bars: bars,
      },
      create_skills_incubated: form.create_skills_incubated,
    }
    if (skill_ids.length) payload.skill_ids = skill_ids
    if (new_skills.length) payload.new_skills = new_skills

    // DEBUG: log payload so we can inspect it in browser console/network
    // Useful when backend returns 500 to verify payload shape
     
    console.debug('transform-payload', payload)

    const data = await store.transformCompetency(props.competencyId, payload)
    const v = await store.getVersions(props.competencyId)
    versions.value = v || []
    emit('transformed', data)
  } catch (err) {
    // Show more detailed server error when available
     
    console.error('transform error', err)
    const axiosErr: any = err as any
    const serverMessage = axiosErr?.response?.data?.message || axiosErr?.response?.data || axiosErr?.message
    try {
      alert('Error al transformar: ' + (typeof serverMessage === 'string' ? serverMessage : JSON.stringify(serverMessage)))
    } catch (e) {
      alert('Error al transformar')
    }
  }
}
</script>

<style scoped>
.transform-modal { padding: 1rem; }
label { display:block; margin-top:0.5rem }
input,textarea { width:100% }
.bars-container { margin-top: 1rem; min-height: 260px; }
/* ensure bars area can scroll and doesn't push footer content */
.bars-container { max-height: 360px; overflow:auto; }

.guide-box { background:#f8fafc; border-left:4px solid #cfe8ff; padding:0.75rem; margin-bottom:0.75rem; border-radius:4px }
.guide-text { margin:0.25rem 0; color:#475569; font-size:0.9rem }
.example-box { margin-top:0.5rem; background:#ffffff; border:1px solid #e6eefc; padding:0.5rem; border-radius:4px }
.example { background:#0f1724; color:#e6f0ff; padding:0.5rem; border-radius:4px; font-size:0.85rem; overflow:auto }

.modal-footer { display:flex; justify-content:flex-end; gap:0.5rem; margin-top:1rem }
.modal-footer .btn-primary { background:#1f79ff; color:white; padding:0.5rem 0.75rem; border-radius:4px; border:none }
.modal-footer .btn-secondary { background:transparent; border:1px solid #ccc; padding:0.45rem 0.7rem; border-radius:4px }
</style>
