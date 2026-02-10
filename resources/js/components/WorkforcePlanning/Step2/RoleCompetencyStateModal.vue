<template>
  <v-dialog
    :model-value="props.visible"
    max-width="700px"
    @update:model-value="$emit('close')"
  >
    <v-card>
      <v-card-title class="text-lg font-bold">
        {{ props.competencyName }} - {{ props.roleName }}
      </v-card-title>

      <v-card-text class="py-6">
        <div v-if="notify.message" class="mb-4">
          <v-alert :color="notify.color" variant="tonal" density="comfortable" dismissible>
            {{ notify.message }}
          </v-alert>
        </div>
        <!-- Current State Display -->
        <div class="mb-6 p-4 bg-gray-50 rounded">
          <p class="text-sm text-gray-600 mb-2">Estado actual del rol:</p>
          <p class="font-semibold">
            {{ props.mapping ? changeTypeLabel(props.mapping.change_type) : 'Sin asociación' }}
          </p>
        </div>

        <!-- If transform created skills in incubation, show them here -->
        <div v-if="incubatedSkills.length" class="mb-4 p-3 bg-yellow-50 border-l-4 border-yellow-400 rounded">
          <label class="block text-sm font-semibold mb-2">Skills creadas (incubación):</label>
          <div class="flex flex-wrap gap-2">
            <span v-for="s in incubatedSkills" :key="s.id || s.name" class="px-2 py-1 bg-yellow-100 text-sm rounded-full">{{ s.name }} <span class="text-xs text-gray-600" style="margin-left:6px">(incubation)</span></span>
          </div>
        </div>

        <!-- State Selection -->
        <div class="mb-6">
          <div class="flex items-center gap-2 mb-3">
            <label class="block text-sm font-semibold">
              Seleccionar estado (una única opción):
            </label>
            <div>
              <v-btn variant="text" size="medium" class="text-info" aria-label="Leyenda tipo de asociación" title="ver información" @click="showLegend = true">
                <v-icon class="align-middle text-3xl text-info-darken-1">mdi-information-variant-circle</v-icon>
              </v-btn>

              <InfoLegend v-model="showLegend" title="Leyenda: Tipos de asociación" :items="legendItems" icon="mdi-information-variant-circle" />
            </div>
          </div>
          <v-radio-group
            v-model="formData.change_type"
            column
            class="mt-4"
          >
            <div class="p-3 border border-gray-200 rounded mb-2 hover:bg-gray-50 cursor-pointer">
              <v-radio
                value="maintenance"
                class="mb-1"
              >
                <template #label>
                  <span class="font-medium">Mantención</span>
                  <span class="text-xs text-gray-500 ml-2">(Sin cambios esperados)</span>
                </template>
              </v-radio>
            </div>
            <div class="p-3 border border-gray-200 rounded mb-2 hover:bg-gray-50 cursor-pointer">
              <v-radio
                value="transformation"
                class="mb-1"
              >
                <template #label>
                  <span class="font-medium">Transformación</span>
                  <span class="text-xs text-gray-500 ml-2">(Requiere upskilling)</span>
                </template>
              </v-radio>
            </div>
            <div class="p-3 border border-gray-200 rounded mb-2 hover:bg-gray-50 cursor-pointer">
              <v-radio
                value="enrichment"
                class="mb-1"
              >
                <template #label>
                  <span class="font-medium">Enriquecimiento</span>
                  <span class="text-xs text-gray-500 ml-2">(Nueva o mejorada)</span>
                </template>
              </v-radio>
            </div>
            <div class="p-3 border border-gray-200 rounded mb-2 hover:bg-gray-50 cursor-pointer">
              <v-radio
                value="extinction"
                class="mb-1"
              >
                <template #label>
                  <span class="font-medium">Extinción</span>
                  <span class="text-xs text-gray-500 ml-2">(Desaparecerá)</span>
                </template>
              </v-radio>
            </div>
          </v-radio-group>
        </div>

        <!-- Conditional Fields -->

        <!-- TRANSFORMATION -->
        <div v-if="formData.change_type === 'transformation'" class="mb-6">
          <div class="p-4 bg-blue-50 border-l-4 border-blue-500 rounded">
            <label class="block text-sm font-semibold mb-3">
              Nivel actual → Futuro:
            </label>
            <div class="flex items-center gap-4">
              <div class="flex-1">
                <label class="text-xs text-gray-600 block mb-1">
                  Actual
                </label>
                <v-select
                  v-model="formData.current_level"
                  :items="[1, 2, 3, 4, 5]"
                  density="compact"
                  label="Nivel"
                />
              </div>
              <div class="pt-4">→</div>
              <div class="flex-1">
                <label class="text-xs text-gray-600 block mb-1">Futuro</label>
                <v-select
                  v-model="formData.required_level"
                  :items="[1, 2, 3, 4, 5]"
                  density="compact"
                  label="Nivel"
                />
              </div>
            </div>
            <div class="mt-3">
              <label class="text-xs text-gray-600 block mb-1">
                Timeline (meses)
              </label>
              <v-select
                v-model="formData.timeline_months"
                :items="[6, 12, 18, 24]"
                density="compact"
              />
            </div>
            <div class="mt-3">
              <v-checkbox
                v-model="formData.suggest_learning_path"
                label="Proponer learning path automático"
              />
            </div>
            <div class="mt-3" v-if="formData.change_type === 'transformation' || formData.change_type === 'enrichment'">
              <v-btn text @click="handleOpenTransform">Editar Transformación (Crear versión)</v-btn>
              <span v-if="formData.competency_version_id" class="text-sm text-gray-600 ml-2">Versión: {{ formData.competency_version_id }}</span>
            </div>
          </div>
        </div>

        <!-- ENRICHMENT -->
        <div v-if="formData.change_type === 'enrichment'" class="mb-6">
          <div class="p-4 bg-green-50 border-l-4 border-green-500 rounded">
            <label class="block text-sm font-semibold mb-3">
              Competencia Nueva:
            </label>
            <div>
              <label class="text-xs text-gray-600 block mb-1">
                Nivel requerido
              </label>
              <v-select
                v-model="formData.required_level"
                :items="[1, 2, 3, 4, 5]"
                density="compact"
              />
            </div>
            <div class="mt-3">
              <label class="text-xs text-gray-600 block mb-1">
                Timeline (meses)
              </label>
              <v-select
                v-model="formData.timeline_months"
                :items="[6, 12, 18, 24]"
                density="compact"
              />
            </div>
            <div class="mt-3">
              <v-checkbox
                v-model="formData.suggest_learning_path"
                label="Proponer learning path automático"
              />
            </div>
          </div>
        </div>

        <!-- EXTINCTION -->
        <div v-if="formData.change_type === 'extinction'" class="mb-6">
          <div class="p-4 bg-red-50 border-l-4 border-red-500 rounded">
            <label class="block text-sm font-semibold mb-3">
              Plan de Transición:
            </label>
            <div class="mb-3">
              <label class="text-xs text-gray-600 block mb-2">
                Timeline desaparición (meses)
              </label>
              <v-select
                v-model="formData.extinction_timeline"
                :items="[6, 12, 18, 24]"
                density="compact"
              />
            </div>
            <label class="text-xs text-gray-600 block mb-2">
              Plan de transición:
            </label>
            <div class="space-y-2">
              <div class="flex items-center">
                <v-radio
                  v-model="formData.transition_plan"
                  value="reskilling"
                  label="Reskilling a otra competencia"
                />
              </div>
              <div class="flex items-center">
                <v-radio
                  v-model="formData.transition_plan"
                  value="role_change"
                  label="Cambio de rol"
                />
              </div>
              <div class="flex items-center">
                <v-radio
                  v-model="formData.transition_plan"
                  value="devinculacion"
                  label="Desvincular"
                />
              </div>
            </div>
          </div>
        </div>

        <!-- MAINTENANCE -->
        <div v-if="formData.change_type === 'maintenance'" class="mb-6">
          <div class="p-4 bg-blue-50 border-l-4 border-blue-500 rounded">
            <p class="text-sm text-gray-600">
              ✅ Esta competencia se mantiene en el mismo nivel. No requiere
              cambios de entrenamiento.
            </p>
          </div>
        </div>

        <!-- Common Fields -->
        <div class="mb-4">
          <label class="block text-sm font-semibold mb-2">
            ¿Es crítica para el rol?
          </label>
          <v-checkbox v-model="formData.is_core" label="Sí, es crítica" />
        </div>

        <div class="mb-6">
          <label class="block text-sm font-semibold mb-2">Notas:</label>
          <v-textarea
            v-model="formData.rationale"
            placeholder="Justificación de esta asociación..."
            density="compact"
            rows="3"
          />
        </div>
        <!-- TransformModal moved to a separate dialog to avoid nested dialogs and show BARS in its own modal -->
      </v-card-text>

      <v-card-actions class="flex justify-end gap-3 px-6 pb-4">
        <v-btn variant="text" @click="$emit('close')"> Cancelar </v-btn>
        <v-btn
          color="primary"
          @click="handleSave"
          :loading="saving"
          variant="elevated"
        >
          Guardar
        </v-btn>
      </v-card-actions>
    </v-card>
    </v-dialog>

    <!-- Separate dialog for transformation (contains Bars/Transform form) -->
    <v-dialog
      :model-value="showTransform"
      max-width="900px"
      @update:model-value="(val) => (showTransform = val)"
    >
      <v-card>
        <v-card-text>
          <TransformModal :competencyId="props.competencyId" @transformed="handleTransformed" @close="showTransform = false" />
        </v-card-text>
      </v-card>
    </v-dialog>
</template>

<script setup lang="ts">
import { ref, watch, onMounted } from 'vue';
import TransformModal from '@/Pages/Scenario/TransformModal.vue';
import InfoLegend from '@/components/Ui/InfoLegend.vue';
import { useTransformStore } from '@/stores/transformStore';
import { useRoleCompetencyStore } from '@/stores/roleCompetencyStore';

interface Props {
  visible: boolean;
  roleId: number;
  roleName: string;
  competencyId: number;
  competencyName: string;
  mapping?: any;
}

interface Emits {
  (e: 'save', data: any): void;
  (e: 'close'): void;
}

const props = defineProps<Props>();
const emit = defineEmits<Emits>();

const saving = ref(false);

const showTransform = ref(false);
const showLegend = ref(false);

const transformStore = useTransformStore();
const roleCompetencyStore = useRoleCompetencyStore();
const versions = ref<any[]>([]);

const legendItems = [
  { title: 'Mantención', text: 'cambios pequeños en el mapeo (por ejemplo, ajustar nivel requerido) que no generan una nueva versión de la competencia.' },
  { title: 'Transformación', text: 'cambios en la definición de la competencia (BARS, skills, nombre o descripción). Al guardar se creará una nueva versión de la competencia.' },
  { title: 'Enriquecimiento', text: 'agregar o mejorar la competencia; puede generar una versión inicial si la competencia es nueva.' },
  { title: 'Extinción', text: 'plan para descontinuar la competencia (se define timeline y plan de transición).' },
];

// Load versions for the competency and set sensible defaults
async function loadVersions() {
  try {
    const v = await transformStore.getVersions(props.competencyId);
    versions.value = v || [];
    // default selection logic
    if (!props.mapping) {
      // No mapping yet: if no versions -> creation/enrichment, else transformation
      formData.value.change_type = versions.value.length > 0 ? 'transformation' : 'enrichment';
    } else {
      // Mapping exists: if versions exist prefer transformation default
      if (!props.mapping.change_type && versions.value.length > 0) {
        formData.value.change_type = 'transformation';
      }
    }
  } catch (e) {
    versions.value = [];
  }
}

// Run on mount and when competencyId changes
onMounted(() => loadVersions());
watch(() => props.competencyId, () => loadVersions());
const notify = ref({ message: '', color: '' });
const incubatedSkills = ref<any[]>([]);

const formData = ref({
  id: null as number | null,
  change_type: 'maintenance' as
    | 'maintenance'
    | 'transformation'
    | 'enrichment'
    | 'extinction',
  required_level: 3,
  current_level: 1,
  is_core: true,
  rationale: '',
  timeline_months: 12,
  extinction_timeline: 12,
  transition_plan: 'reskilling' as 'reskilling' | 'role_change' | 'devinculacion',
  suggest_learning_path: false,
  competency_version_id: null as number | null,
});

const changeTypeLabel = (type: string) => {
  const labels: Record<string, string> = {
    maintenance: '✅ Mantención',
    transformation: '🔄 Transformación',
    enrichment: '📈 Enriquecimiento',
    extinction: '📉 Extinción',
  };
  return labels[type] || type;
};

const handleSave = async () => {
  // If user requested a transformation but no version exists yet, open transform modal first
  if (formData.value.change_type === 'transformation' && !formData.value.competency_version_id) {
    showTransform.value = true;
    return;
  }

  saving.value = true;
  try {
    emit('save', {
      ...formData.value,
      id: props.mapping?.id,
    });
  } finally {
    saving.value = false;
  }
};

watch(
  () => props.mapping,
  (mapping) => {
    if (mapping) {
        formData.value = {
        id: mapping.id,
        change_type: mapping.change_type || 'maintenance',
        required_level: mapping.required_level || 3,
        current_level: mapping.current_level || 1,
        is_core: mapping.is_core || false,
        rationale: mapping.rationale || '',
        timeline_months: mapping.timeline_months || 12,
        extinction_timeline: mapping.extinction_timeline || 12,
        transition_plan: mapping.transition_plan || 'reskilling',
        suggest_learning_path: mapping.suggest_learning_path || false,
          competency_version_id: mapping.competency_version_id || null,
      };
    }
  },
  { immediate: true }
);

const handleOpenTransform = () => {
  showTransform.value = true;
};

const handleTransformed = async (data: any) => {
  const newVersionId = data?.id ?? data?.version_id ?? null;
  if (newVersionId) {
    formData.value.competency_version_id = newVersionId;
  }
  // capture any created skills returned by the transform endpoint
  const created = data?.created_skills ?? data?.createdSkills ?? []
  incubatedSkills.value = Array.isArray(created)
    ? created.filter((s: any) => s && (s.is_incubated === true || s.status === 'incubation'))
    : []
  showTransform.value = false;
  // Auto-save mapping using current formData and props
  try {
    const payload = {
      id: formData.value.id,
      role_id: props.roleId,
      competency_id: props.competencyId,
      required_level: formData.value.required_level,
      is_core: formData.value.is_core,
      change_type: formData.value.change_type,
      rationale: formData.value.rationale,
      competency_version_id: formData.value.competency_version_id,
      timeline_months: formData.value.timeline_months,
      current_level: formData.value.current_level,
    };
    emit('save', payload);
    // Also perform the save here to get immediate feedback
    try {
      await roleCompetencyStore.saveMapping(payload as any);
      notify.value = { message: 'Mapeo guardado correctamente', color: 'success' };
      setTimeout(() => (notify.value.message = ''), 3000);
    } catch (err: any) {
      notify.value = { message: err?.message || 'Error guardando el mapeo', color: 'error' };
      setTimeout(() => (notify.value.message = ''), 5000);
    }
  } catch (err) {
    // swallowing; UI save will still be available
    console.error('Auto-save mapping failed', err);
  }
};
</script>

