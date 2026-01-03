<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import { useNotification } from '@kyvg/vue3-notification';
import { useTheme as useVuetifyTheme } from 'vuetify';

defineOptions({ layout: AppLayout });

const vuetifyTheme = useVuetifyTheme();

const headerGradient = computed(() => {
  const theme = vuetifyTheme.global.current.value;
  return `linear-gradient(135deg, ${theme.colors.primary} 0%, ${theme.colors.secondary} 100%)`;
});

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
    <!-- Header -->
    <div class="d-flex justify-space-between align-center mb-4" :style="{ background: headerGradient }" style="padding: 1.5rem; border-radius: 8px;">
      <div>
        <h1 class="text-h4 font-weight-bold mb-2" style="color: white;">Learning Paths</h1>
        <p class="text-subtitle-2" style="color: rgba(255,255,255,0.85);">
          Development paths for skill growth and career progression
        </p>
      </div>
    </div>

    <!-- Loading State -->
    <v-card v-if="loading" class="mb-6" elevation="0" variant="outlined">
      <v-card-text class="text-center py-12">
        <v-progress-circular indeterminate color="primary" size="48" />
        <p class="mt-4 text-medium-emphasis">Cargando rutas de aprendizaje...</p>
      </v-card-text>
    </v-card>

    <!-- Paths List -->
    <div v-if="!loading">
      <v-card v-if="paths.length === 0" elevation="0" variant="outlined" class="py-16">
        <v-card-text class="text-center">
          <v-icon size="80" class="mb-6 text-medium-emphasis">mdi-map-marker-path</v-icon>
          <div class="text-h6 mb-2">No hay rutas de aprendizaje</div>
          <div class="text-body-2 text-medium-emphasis">
            Las rutas de desarrollo se crean desde el módulo de análisis de brechas (Gap Analysis)
          </div>
        </v-card-text>
      </v-card>

      <v-card v-for="path in paths" :key="path.id" class="mb-4" elevation="0" variant="outlined">
        <!-- Header -->
        <v-card-title
          @click="expandedPath = expandedPath === path.id ? null : path.id"
          style="cursor: pointer;"
          class="pa-6"
        >
          <div class="d-flex align-center justify-space-between w-100">
            <div class="flex-grow-1">
              <div class="text-h6 font-weight-bold">{{ path.people_name }}</div>
              <div class="text-body-2 text-medium-emphasis d-flex align-center mt-1">
                <v-icon size="small" class="mr-1">mdi-arrow-right</v-icon>
                {{ path.target_role_name }}
              </div>
            </div>
            <div class="text-right mr-4">
              <v-chip size="small" color="primary" variant="tonal" class="mb-1">
                <v-icon start size="small">mdi-clock-outline</v-icon>
                {{ calculateTotalDuration(path.steps) }} días
              </v-chip>
              <div class="text-caption text-medium-emphasis">
                {{ path.steps.length }} pasos
              </div>
            </div>
            <v-btn icon variant="text" size="small">
              <v-icon>
                {{ expandedPath === path.id ? 'mdi-chevron-up' : 'mdi-chevron-down' }}
              </v-icon>
            </v-btn>
          </div>
        </v-card-title>

        <!-- Timeline of Steps -->
        <v-expand-transition>
          <div v-if="expandedPath === path.id">
            <v-divider />
            <v-card-text class="pa-6">
              <div class="ml-2">
                <v-timeline side="end" density="compact" line-inset="12">
                  <v-timeline-item
                    v-for="(step, index) in path.steps"
                    :key="index"
                    :dot-color="getActionColor(step.action_type)"
                    size="small"
                  >
                    <template #icon>
                      <v-icon size="small" :icon="getActionIcon(step.action_type)" />
                    </template>
                    
                    <v-card elevation="0" variant="tonal" :color="getActionColor(step.action_type)">
                      <v-card-text class="pa-4">
                        <div class="d-flex align-center justify-space-between mb-2">
                          <div class="text-subtitle-1 font-weight-bold">{{ step.skill_name }}</div>
                          <v-chip
                            size="small"
                            variant="flat"
                            :color="getActionColor(step.action_type)"
                          >
                            {{ step.action_type }}
                          </v-chip>
                        </div>
                        <p class="text-body-2 text-medium-emphasis mb-3">
                          {{ step.description }}
                        </p>
                        <div class="d-flex gap-4">
                          <v-chip size="x-small" variant="outlined">
                            <v-icon start size="x-small">mdi-clock-outline</v-icon>
                            {{ step.estimated_duration_days }} días
                          </v-chip>
                          <v-chip size="x-small" variant="outlined">
                            <v-icon start size="x-small">mdi-order-numeric-ascending</v-icon>
                            Paso {{ step.order }}
                          </v-chip>
                        </div>
                      </v-card-text>
                    </v-card>
                  </v-timeline-item>
                </v-timeline>
              </div>
            </v-card-text>
          </div>
        </v-expand-transition>
      </v-card>
    </div>
  </div>
</template>

<style scoped>
/* Removed custom styles - using Vuetify utilities */
</style>
