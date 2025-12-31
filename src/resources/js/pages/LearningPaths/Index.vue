<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useNotification } from '@kyvg/vue3-notification';

defineOptions({ layout: AppLayout });

const { notify } = useNotification();

interface DevelopmentStep {
  order: number;
  action_type: string;
  skill_name: string;
  description: string;
  estimated_duration_days: number;
}

interface DevelopmentPath {
  id: number;
  people_id: number;
  people_name: string;
  target_role_id: number;
  target_role_name: string;
  steps: DevelopmentStep[];
  created_at: string;
}

// State
const paths = ref<DevelopmentPath[]>([]);
const loading = ref(false);
const expandedPath = ref<number | null>(null);

// Load development paths
const loadPaths = async () => {
  loading.value = true;
  try {
    const response = await axios.get('/api/development-paths');
    paths.value = response.data.data || response.data;
    if (paths.value.length > 0) {
      expandedPath.value = paths.value[0].id;
    }
  } catch (err) {
    console.error('Failed to load development paths', err);
    notify({
      type: 'error',
      text: 'Error loading development paths'
    });
  } finally {
    loading.value = false;
  }
};

// Get action icon
const getActionIcon = (actionType: string): string => {
  const iconMap: Record<string, string> = {
    'course': 'mdi-book-open-variant',
    'mentorship': 'mdi-human-greeting-variant',
    'project': 'mdi-folder-multiple',
    'certification': 'mdi-certificate',
    'workshop': 'mdi-presentation',
    'reading': 'mdi-book',
    'practice': 'mdi-code-braces'
  };
  return iconMap[actionType] || 'mdi-checkbox-marked-circle';
};

// Get action color
const getActionColor = (actionType: string): string => {
  const colorMap: Record<string, string> = {
    'course': 'primary',
    'mentorship': 'info',
    'project': 'success',
    'certification': 'warning',
    'workshop': 'secondary',
    'reading': 'accent',
    'practice': 'error'
  };
  return colorMap[actionType] || 'grey';
};

// Calculate total duration
const calculateTotalDuration = (steps: DevelopmentStep[]): number => {
  return steps.reduce((total, step) => total + (step.estimated_duration_days || 0), 0);
};

onMounted(() => {
  loadPaths();
});
</script>

<template>
  <div class="pa-4">
    <div class="mb-6">
      <h1 class="text-h4 font-weight-bold mb-2">Learning Paths</h1>
      <p class="text-body2 text-grey">Development paths for skill growth and career progression</p>
    </div>

    <!-- Loading State -->
    <v-card v-if="loading" class="mb-6">
      <v-card-text class="text-center py-8">
        <v-progress-circular indeterminate color="primary" />
        <p class="mt-4">Loading development paths...</p>
      </v-card-text>
    </v-card>

    <!-- Paths List -->
    <div v-if="!loading">
      <v-card v-if="paths.length === 0" class="mb-6">
        <v-card-text class="text-center py-12">
          <v-icon size="64" class="mb-4 text-grey">mdi-map-marker-path</v-icon>
          <p class="text-body1 text-grey">No development paths created yet</p>
        </v-card-text>
      </v-card>

      <v-card v-for="path in paths" :key="path.id" class="mb-4">
        <!-- Header -->
        <v-card-title
          @click="expandedPath = expandedPath === path.id ? null : path.id"
          class="cursor-pointer hover:bg-grey-100"
        >
          <div class="d-flex align-center justify-space-between w-100">
            <div class="flex-grow-1">
              <div class="font-weight-bold">{{ path.people_name }}</div>
              <div class="text-body2 text-grey">
                → {{ path.target_role_name }}
              </div>
            </div>
            <div class="text-right mr-4">
              <div class="text-caption text-grey">
                {{ calculateTotalDuration(path.steps) }} days
              </div>
              <div class="text-body2 font-weight-medium">
                {{ path.steps.length }} steps
              </div>
            </div>
            <v-icon>
              {{ expandedPath === path.id ? 'mdi-chevron-up' : 'mdi-chevron-down' }}
            </v-icon>
          </div>
        </v-card-title>

        <!-- Timeline of Steps -->
        <v-expand-transition>
          <v-card-text v-if="expandedPath === path.id">
            <div class="ml-4">
              <div class="relative">
                <div v-for="(step, index) in path.steps" :key="index" class="mb-6">
                  <!-- Step Item -->
                  <div class="d-flex gap-4">
                    <!-- Timeline Dot -->
                    <div class="position-relative pt-1">
                      <v-chip
                        size="small"
                        :color="getActionColor(step.action_type)"
                        text-color="white"
                        :icon="getActionIcon(step.action_type)"
                      />
                      <!-- Connector Line -->
                      <div
                        v-if="index < path.steps.length - 1"
                        class="position-absolute"
                        style="
                          left: 50%;
                          top: 40px;
                          width: 2px;
                          height: 40px;
                          background-color: #e0e0e0;
                          transform: translateX(-50%);
                        "
                      />
                    </div>

                    <!-- Content -->
                    <div class="flex-grow-1 pb-4">
                      <div class="d-flex align-center gap-2 mb-2">
                        <h4 class="font-weight-bold">{{ step.skill_name }}</h4>
                        <v-chip
                          size="x-small"
                          variant="outlined"
                          :color="getActionColor(step.action_type)"
                        >
                          {{ step.action_type }}
                        </v-chip>
                      </div>
                      <p class="text-body2 text-grey mb-2">
                        {{ step.description }}
                      </p>
                      <div class="d-flex gap-4 text-caption">
                        <div>
                          <span class="text-grey">Duration:</span>
                          <strong>{{ step.estimated_duration_days }} days</strong>
                        </div>
                        <div>
                          <span class="text-grey">Order:</span>
                          <strong>Step {{ step.order }}</strong>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </v-card-text>
        </v-expand-transition>
      </v-card>
    </div>
  </div>
</template>

<style scoped>
.cursor-pointer {
  cursor: pointer;
}

.hover\:bg-grey-100:hover {
  background-color: #f5f5f5;
}

.relative {
  position: relative;
}

.position-relative {
  position: relative;
}

.position-absolute {
  position: absolute;
}

.pt-1 {
  padding-top: 4px;
}

.w-100 {
  width: 100%;
}

.flex-grow-1 {
  flex-grow: 1;
}

.d-flex {
  display: flex;
}

.align-center {
  align-items: center;
}

.gap-4 {
  gap: 16px;
}

.gap-2 {
  gap: 8px;
}

.mb-6 {
  margin-bottom: 24px;
}

.mb-4 {
  margin-bottom: 16px;
}

.mb-2 {
  margin-bottom: 8px;
}

.ml-4 {
  margin-left: 16px;
}

.mr-4 {
  margin-right: 16px;
}

.text-right {
  text-align: right;
}

.justify-space-between {
  justify-content: space-between;
}

.pb-4 {
  padding-bottom: 16px;
}
</style>
