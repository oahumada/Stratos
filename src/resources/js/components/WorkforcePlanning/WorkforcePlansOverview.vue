<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useWorkforcePlanning } from '@/composables/useStrategicPlanningScenarios'
import { useNotification } from '@/composables/useNotification'
import type { WorkforcePlan } from '@/types/workforcePlanning'

const { list } = useWorkforcePlanning()
const { showError } = useNotification()

const loading = ref(false)
const plans = ref<WorkforcePlan[]>([])

const load = async () => {
  loading.value = true
  try {
    const res: any = await list()
    // compatible with paginated or direct data
    const data = res?.data?.data ?? res?.data ?? res
    plans.value = Array.isArray(data) ? data : data?.data ?? []
  } catch (e) {
    void e
    showError('No fue posible cargar los planes')
  } finally {
    loading.value = false
  }
}

onMounted(load)
</script>

<template>
  <div class="pa-4">
    <h3>Planes de Dotación (Overview)</h3>
    <v-card>
      <v-data-table :items="plans" :loading="loading" density="comfortable">
        <!-- eslint-disable-next-line vue/valid-v-slot -->
        <template #item.name="{ item }">
          <RouterLink :to="{ name: 'workforce-plans.show', params: { id: item.id } }">{{ item.name }}</RouterLink>
        </template>
        <!-- eslint-disable-next-line vue/valid-v-slot -->
        <template #item.status="{ item }">
          <v-chip size="small" :color="item.status === 'active' ? 'success' : (item.status === 'approved' ? 'info' : 'grey')">{{ item.status }}</v-chip>
        </template>
      </v-data-table>
    </v-card>
  </div>
</template>

<style scoped>
.pa-4 { padding: 16px; }
</style>
