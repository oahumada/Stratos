<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { useNotification } from '@/composables/useNotification'
import { useWorkforcePlanning } from '@/composables/useWorkforcePlanning'

defineOptions({ layout: AppLayout })

// use Inertia router for navigation
const { showSuccess, showError } = useNotification()
const { createPlan } = useWorkforcePlanning()

const form = ref({
  organization_id: undefined as number | undefined,
  name: '',
  code: '',
  description: '',
  start_date: '',
  end_date: '',
  planning_horizon_months: 12,
  scope_type: 'organization_wide' as 'organization_wide' | 'business_unit' | 'department' | 'critical_roles_only',
  scope_notes: '',
  owner_user_id: undefined as number | undefined,
  sponsor_user_id: undefined as number | undefined,
  created_by: undefined as number | undefined,
})

const saving = ref(false)

const submit = async () => {
  saving.value = true
  try {
    const res: any = await createPlan(form.value)
    const created = res?.data || res
    showSuccess('Plan creado')
    // Navigate to the created plan detail using Inertia
    router.visit(`/workforce-plans/${created.id}`)
  } catch (e: any) {
    const msg = e?.response?.data?.message || 'No fue posible crear el plan'
    showError(msg)
  } finally {
    saving.value = false
  }
}
</script>

<template>
  <div class="pa-4">
    <h2>Nuevo Plan de Dotación</h2>
    <v-card class="mt-4">
      <v-card-text>
        <v-row>
          <v-col cols="12" md="6">
            <v-text-field label="Nombre" v-model="form.name" required />
          </v-col>
          <v-col cols="12" md="3">
            <v-text-field label="Código" v-model="form.code" required />
          </v-col>
          <v-col cols="12" md="3">
            <v-select
              label="Ámbito"
              :items="[
                { title: 'Organización', value: 'organization_wide' },
                { title: 'Unidad de Negocio', value: 'business_unit' },
                { title: 'Departamento', value: 'department' },
                { title: 'Roles Críticos', value: 'critical_roles_only' },
              ]"
              v-model="form.scope_type"
            />
          </v-col>
        </v-row>
        <v-row>
          <v-col cols="12">
            <v-textarea label="Descripción" v-model="form.description" />
          </v-col>
        </v-row>
        <v-row>
          <v-col cols="12" md="3">
            <v-text-field type="date" label="Inicio" v-model="form.start_date" />
          </v-col>
          <v-col cols="12" md="3">
            <v-text-field type="date" label="Término" v-model="form.end_date" />
          </v-col>
          <v-col cols="12" md="3">
            <v-text-field type="number" label="Horizonte (meses)" v-model.number="form.planning_horizon_months" />
          </v-col>
          <v-col cols="12" md="3">
            <v-text-field label="Notas de Ámbito" v-model="form.scope_notes" />
          </v-col>
        </v-row>
      </v-card-text>
      <v-card-actions>
        <v-spacer />
        <v-btn color="primary" :loading="saving" @click="submit">
          <v-icon start>mdi-content-save</v-icon>
          Crear Plan
        </v-btn>
      </v-card-actions>
    </v-card>
  </div>
</template>
