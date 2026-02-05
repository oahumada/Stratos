<template>
  <v-dialog
    :model-value="visible"
    max-width="700px"
    @update:model-value="$emit('close')"
  >
    <v-card>
      <v-card-title class="text-lg font-bold">
        {{ competencyName }} - {{ roleName }}
      </v-card-title>

      <v-card-text class="py-6">
        <!-- Current State Display -->
        <div class="mb-6 p-4 bg-gray-50 rounded">
          <p class="text-sm text-gray-600 mb-2">Estado actual del rol:</p>
          <p class="font-semibold">
            {{
              mapping
                ? changeTypeLabel(mapping.change_type)
                : 'Sin asociación'
            }}
          </p>
        </div>

        <!-- State Selection -->
        <div class="mb-6">
          <label class="block text-sm font-semibold mb-3">
            Seleccionar estado (una única opción):
          </label>
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
            <div class="mt-3">
              <v-btn text @click="handleOpenTransform">Editar Transformación</v-btn>
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
        <TransformModal v-if="showTransform" :competencyId="props.competencyId" @transformed="handleTransformed" @close="showTransform = false" />
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
</template>

<script setup lang="ts">
import { ref, watch } from 'vue';
import TransformModal from '@/Pages/Scenario/TransformModal.vue';
import { useTransformStore } from '@/stores/transformStore';

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

const transformStore = useTransformStore();

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
  } catch (err) {
    // swallowing; UI save will still be available
    console.error('Auto-save mapping failed', err);
  }
};
</script>
