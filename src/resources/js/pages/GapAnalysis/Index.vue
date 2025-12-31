<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useNotification } from '@kyvg/vue3-notification';

defineOptions({ layout: AppLayout });

const { notify } = useNotification();

interface People {
  id: number;
  name: string;
}

interface Role {
  id: number;
  name: string;
}

interface Gap {
  skill_id: number;
  skill_name: string;
  required_level: number;
  current_level: number;
  gap: number;
  status: string;
}

interface GapAnalysisResult {
  people_id: number;
  role_id: number;
  match_percentage: number;
  gaps: Gap[];
}

// State
const peoples = ref<People[]>([]);
const roles = ref<Role[]>([]);
const selectedPeopleId = ref<number | null>(null);
const selectedRoleId = ref<number | null>(null);
const loading = ref(false);
const analyzing = ref(false);
const result = ref<GapAnalysisResult | null>(null);

// Load initial data
const loadPeoplesAndRoles = async () => {
  loading.value = true;
  try {
    const [peoplesRes, rolesRes] = await Promise.all([
      axios.get('/api/people'),
      axios.get('/api/roles')
    ]);
    peoples.value = peoplesRes.data.data || peoplesRes.data;
    roles.value = rolesRes.data.data || rolesRes.data;
  } catch (err) {
    console.error('Failed to load data', err);
    notify({
      type: 'error',
      text: 'Error loading peoples and roles'
    });
  } finally {
    loading.value = false;
  }
};

// Analyze gap
const analyzeGap = async () => {
  if (!selectedPeopleId.value || !selectedRoleId.value) {
    notify({
      type: 'warning',
      text: 'Please select both people and role'
    });
    return;
  }

  analyzing.value = true;
  try {
    const response = await axios.post('/api/gap-analysis', {
      people_id: selectedPeopleId.value,
      role_id: selectedRoleId.value
    });
    result.value = response.data.data || response.data;
    notify({
      type: 'success',
      text: 'Gap analysis completed successfully'
    });
  } catch (err: any) {
    console.error('Gap analysis failed', err);
    notify({
      type: 'error',
      text: err.response?.data?.message || 'Error analyzing gap'
    });
  } finally {
    analyzing.value = false;
  }
};

// Get status color
const getStatusColor = (status: string): string => {
  const statusMap: Record<string, string> = {
    'critical': 'error',
    'developing': 'warning',
    'ok': 'success'
  };
  return statusMap[status] || 'info';
};

// Get match color
const getMatchColor = (percentage: number): string => {
  if (percentage >= 80) return 'success';
  if (percentage >= 60) return 'warning';
  return 'error';
};

onMounted(() => {
  loadPeoplesAndRoles();
});
</script>

<template>
  <div class="pa-4">
    <div class="mb-6">
      <h1 class="text-h4 font-weight-bold mb-2">Gap Analysis</h1>
      <p class="text-body2 text-grey">Analyze skill gaps between a people and a role</p>
    </div>

    <!-- Form Section -->
    <v-card class="mb-6">
      <v-card-title>Select People and Role</v-card-title>
      <v-card-text>
        <v-row>
          <v-col cols="12" sm="6">
            <v-select
              v-model="selectedPeopleId"
              :items="peoples"
              item-title="name"
              item-value="id"
              label="Select People"
              :loading="loading"
              outlined
            />
          </v-col>
          <v-col cols="12" sm="6">
            <v-select
              v-model="selectedRoleId"
              :items="roles"
              item-title="name"
              item-value="id"
              label="Select Role"
              :loading="loading"
              outlined
            />
          </v-col>
        </v-row>
        <v-row>
          <v-col cols="12">
            <v-btn
              color="primary"
              @click="analyzeGap"
              :loading="analyzing"
              :disabled="!selectedPeopleId || !selectedRoleId"
            >
              <v-icon left>mdi-analysis</v-icon>
              Analyze Gap
            </v-btn>
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>

    <!-- Results Section -->
    <v-card v-if="result" class="mb-6">
      <v-card-title>Analysis Results</v-card-title>
      <v-card-text>
        <!-- Match Percentage -->
        <div class="mb-6">
          <div class="d-flex align-center justify-space-between mb-2">
            <span class="text-body1 font-weight-medium">Overall Match</span>
            <span :class="`text-h6 text-${getMatchColor(result.match_percentage)}`">
              {{ result.match_percentage }}%
            </span>
          </div>
          <v-progress-linear
            :value="result.match_percentage"
            :color="getMatchColor(result.match_percentage)"
            height="8"
          />
        </div>

        <!-- Skills Gap Table -->
        <div class="text-body2 font-weight-medium mb-4">Skills Assessment</div>
        <v-table v-if="result.gaps.length > 0" class="elevation-1">
          <thead>
            <tr>
              <th class="text-left">Skill</th>
              <th class="text-center">Required Level</th>
              <th class="text-center">Current Level</th>
              <th class="text-center">Gap</th>
              <th class="text-left">Status</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="gap in result.gaps" :key="`${gap.skill_id}`">
              <td>{{ gap.skill_name }}</td>
              <td class="text-center">
                <v-chip size="small" variant="outlined">{{ gap.required_level }}</v-chip>
              </td>
              <td class="text-center">
                <v-chip size="small" variant="outlined">{{ gap.current_level }}</v-chip>
              </td>
              <td class="text-center">
                <span :class="`text-${getStatusColor(gap.status)}`">
                  {{ gap.gap }}
                </span>
              </td>
              <td>
                <v-chip
                  :color="getStatusColor(gap.status)"
                  text-color="white"
                  size="small"
                >
                  {{ gap.status }}
                </v-chip>
              </td>
            </tr>
          </tbody>
        </v-table>
        <div v-else class="text-center py-6 text-grey">
          No skill gaps found. Perfect match!
        </div>
      </v-card-text>
    </v-card>

    <!-- Empty State -->
    <div v-if="!result" class="text-center py-12">
      <v-icon size="64" class="mb-4 text-grey">mdi-chart-box-outline</v-icon>
      <p class="text-body1 text-grey">Select a people and role to analyze gaps</p>
    </div>
  </div>
</template>

<style scoped>
</style>
