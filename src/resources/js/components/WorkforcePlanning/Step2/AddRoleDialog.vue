<template>
  <v-dialog
    :model-value="visible"
    max-width="500px"
    @update:model-value="$emit('close')"
  >
    <v-card>
      <v-card-title>Agregar Nuevo Rol</v-card-title>

      <v-card-text class="py-6">
        <div class="space-y-4">
          <!-- Role Selection or Create -->
          <div>
            <label class="block text-sm font-semibold mb-2">
              Seleccionar o crear rol:
            </label>
            <v-radio-group v-model="formData.type">
              <v-radio value="existing" label="Usar rol existente" />
              <v-radio value="new" label="Crear rol nuevo" />
            </v-radio-group>
          </div>

          <!-- Existing Role Selection -->
          <div v-if="formData.type === 'existing'">
            <v-select
              v-model="formData.role_id"
              :items="availableRoles"
              item-title="name"
              item-value="id"
              label="Seleccionar rol"
              density="compact"
            />
          </div>

          <!-- New Role Form -->
          <div v-if="formData.type === 'new'" class="space-y-3">
            <v-text-field
              v-model="formData.role_name"
              label="Nombre del rol"
              density="compact"
            />
            <v-text-field
              v-model.number="formData.fte"
              type="number"
              label="FTE (personas)"
              density="compact"
            />
            <v-select
              v-model="formData.evolution_type"
              :items="evolutionTypes"
              label="Tipo de evolución"
              density="compact"
            />
          </div>

          <!-- Common Fields -->
          <v-select
            v-model="formData.role_change"
            :items="roleChangeTypes"
            label="Cambio del rol"
            density="compact"
          />

          <v-select
            v-model="formData.impact_level"
            :items="impactLevels"
            label="Nivel de impacto"
            density="compact"
          />

          <v-textarea
            v-model="formData.rationale"
            label="Justificación"
            density="compact"
            rows="3"
          />
        </div>
      </v-card-text>

      <v-card-actions class="flex justify-end gap-3 px-6 pb-4">
        <v-btn variant="text" @click="$emit('close')">Cancelar</v-btn>
        <v-btn
          color="primary"
          @click="handleSave"
          :loading="saving"
          variant="elevated"
        >
          Agregar Rol
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { usePage } from '@inertiajs/vue3';

interface Props {
  visible: boolean;
}

interface Emits {
  (e: 'save', data: any): void;
  (e: 'close'): void;
}

defineProps<Props>();
const emit = defineEmits<Emits>();

const saving = ref(false);
const availableRoles = ref<any[]>([]);

const formData = ref({
  type: 'existing' as 'existing' | 'new',
  role_id: null as number | null,
  role_name: '',
  fte: 1,
  role_change: 'create' as string,
  impact_level: 'medium' as string,
  evolution_type: 'new_role' as string,
  rationale: '',
});

const roleChangeTypes = ['create', 'modify', 'eliminate', 'maintain'];
const impactLevels = ['critical', 'high', 'medium', 'low'];
const evolutionTypes = [
  'new_role',
  'upgrade_skills',
  'transformation',
  'downsize',
  'elimination',
];

const handleSave = async () => {
  saving.value = true;
  try {
    emit('save', {
      ...formData.value,
      role_id: formData.value.type === 'existing' ? formData.value.role_id : null,
      role_name: formData.value.type === 'new' ? formData.value.role_name : null,
    });
  } finally {
    saving.value = false;
  }
};

// Load available roles: prefer API `/api/roles`, fallback to Inertia page props
const loadAvailableRoles = async () => {
  try {
    // Try API first
    try {
      const r = await fetch('/api/roles');
      if (r.ok) {
        const body = await r.json();
        // API may return { data: [...] } or an array
        availableRoles.value = Array.isArray(body?.data) ? body.data : (Array.isArray(body) ? body : body?.data ?? []);
        return;
      }
    } catch (errApi) {
      // ignore and fallback to page props
    }

    // Fallback: use Inertia page props if provided server-side
    try {
      const page = usePage();
      availableRoles.value = (page.props as any).roles || [];
    } catch (err) {
      console.error('Error loading roles from page props:', err);
      availableRoles.value = [];
    }
  } catch (err) {
    console.error('Error loading roles:', err);
    availableRoles.value = [];
  }
};

loadAvailableRoles();
</script>
