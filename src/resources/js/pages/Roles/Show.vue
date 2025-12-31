<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useNotification } from '@kyvg/vue3-notification';
import { usePage } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';

defineOptions({ layout: AppLayout });

const { notify } = useNotification();
const page = usePage();

interface Skill {
  id: number;
  name: string;
  required_level: number;
}

interface Role {
  id: number;
  name: string;
  description: string;
  skills: Skill[];
}

const role = ref<Role | null>(null);
const loading = ref(false);
const roleId = ref<number | null>(null);

const loadRole = async () => {
  if (!roleId.value) return;

  loading.value = true;
  try {
    const response = await axios.get(`/api/roles/${roleId.value}`);
    role.value = response.data.data || response.data;
  } catch (err) {
    console.error('Failed to load role', err);
    notify({
      type: 'error',
      text: 'Error loading role details'
    });
  } finally {
    loading.value = false;
  }
};

const getSkillLevelColor = (level: number): string => {
  if (level >= 4) return 'success';
  if (level >= 3) return 'info';
  if (level >= 2) return 'warning';
  return 'error';
};

onMounted(() => {
  const id = (page.props as any).id;
  if (id) {
    roleId.value = Number(id);
    loadRole();
  }
});
</script>

<template>
  <div class="pa-4">
    <!-- Header with Back Button -->
    <div class="d-flex align-center gap-2 mb-6">
      <Link href="/roles" class="text-decoration-none">
        <v-btn icon variant="text">
          <v-icon>mdi-arrow-left</v-icon>
        </v-btn>
      </Link>
      <h1 class="text-h4 font-weight-bold">{{ role?.name || 'Role Details' }}</h1>
    </div>

    <!-- Loading State -->
    <v-card v-if="loading" class="mb-4">
      <v-card-text class="text-center py-8">
        <v-progress-circular indeterminate color="primary" />
        <p class="mt-4">Loading role details...</p>
      </v-card-text>
    </v-card>

    <!-- Role Details -->
    <div v-if="!loading && role" class="space-y-4">
      <!-- Basic Info -->
      <v-card>
        <v-card-title>Role Information</v-card-title>
        <v-card-text>
          <v-row dense>
            <v-col cols="12">
              <div class="mb-4">
                <span class="text-caption text-grey">Name</span>
                <p class="text-body1 font-weight-medium">{{ role.name }}</p>
              </div>
            </v-col>
            <v-col cols="12">
              <div class="mb-4">
                <span class="text-caption text-grey">Description</span>
                <p class="text-body2">{{ role.description || 'No description available' }}</p>
              </div>
            </v-col>
          </v-row>
        </v-card-text>
      </v-card>

      <!-- Required Skills -->
      <v-card>
        <v-card-title>
          <div class="d-flex justify-space-between align-center w-100">
            <span>Required Skills ({{ role.skills?.length || 0 }})</span>
          </div>
        </v-card-title>

        <v-card-text>
          <v-table v-if="role.skills && role.skills.length > 0" class="elevation-0">
            <thead>
              <tr>
                <th class="text-left">Skill Name</th>
                <th class="text-center">Required Level</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="skill in role.skills" :key="skill.id">
                <td>{{ skill.name }}</td>
                <td class="text-center">
                  <v-chip
                    :color="getSkillLevelColor(skill.required_level)"
                    size="small"
                    :label="`Level ${skill.required_level}`"
                  />
                </td>
              </tr>
            </tbody>
          </v-table>
          <div v-else class="text-center py-8 text-grey">
            <v-icon size="48" class="mb-4">mdi-briefcase-outline</v-icon>
            <p>No skills required</p>
          </div>
        </v-card-text>
      </v-card>

      <!-- Action Buttons -->
      <v-row dense>
        <v-col cols="12">
          <Link href="/roles" class="text-decoration-none">
            <v-btn color="info" variant="outlined" block prepend-icon="mdi-arrow-left">
              Back to Roles
            </v-btn>
          </Link>
        </v-col>
      </v-row>
    </div>

    <!-- Empty State -->
    <v-card v-if="!loading && !role" class="mb-4">
      <v-card-text class="text-center py-12">
        <v-icon size="64" class="mb-4 text-grey">mdi-briefcase</v-icon>
        <p class="text-body1 text-grey">Role not found</p>
      </v-card-text>
    </v-card>
  </div>
</template>

<style scoped>
.gap-2 {
  gap: 8px;
}

.mb-6 {
  margin-bottom: 24px;
}

.mb-4 {
  margin-bottom: 16px;
}

.mt-4 {
  margin-top: 16px;
}

.d-flex {
  display: flex;
}

.align-center {
  align-items: center;
}

.justify-space-between {
  justify-content: space-between;
}

.w-100 {
  width: 100%;
}

.space-y-4 > * + * {
  margin-top: 16px;
}

.text-grey {
  color: #757575;
}
</style>
