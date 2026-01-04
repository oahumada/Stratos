<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import { useNotification } from '@kyvg/vue3-notification';
import { usePage } from '@inertiajs/vue3';
import { useTheme as useVuetifyTheme } from 'vuetify';

defineOptions({ layout: AppLayout });

const { notify } = useNotification();
const page = usePage();
const vuetifyTheme = useVuetifyTheme();

const headerGradient = computed(() => {
  const theme = vuetifyTheme.global.current.value;
  return `linear-gradient(135deg, ${theme.colors.primary} 0%, ${theme.colors.secondary} 100%)`;
});

interface JobOpening {
  id: number;
  title: string;
  description: string;
  required_skills: Skill[];
  match_percentage?: number;
  time_to_productivity?: number;
}

interface Skill {
  id: number;
  name: string;
  required_level?: number;
  current_level?: number;
}

interface Application {
  id: number;
  job_opening_id: number;
  status: string;
  created_at: string;
}

interface Candidate {
  id: number;
  name: string;
  current_role: string;
  match_percentage: number;
  time_to_productivity: number;
  category: string;
  missing_skills_count: number;
}

interface Position {
  id: number;
  title: string;
  role: string;
  department: string;
  deadline: string;
  status: string;
  candidates: Candidate[];
  total_candidates: number;
}

// State
const activeTab = ref<string>('recruiter'); // 'recruiter' o 'employee'
const opportunities = ref<JobOpening[]>([]);
const positions = ref<Position[]>([]);
const applications = ref<Application[]>([]);
const loading = ref(false);
const loadingRecruiter = ref(false);
const applying = ref<number | null>(null);
const filterStatus = ref<string>('open');

// Current user (get from inertia props)
const currentUserId = computed(() => {
  return (page.props as any).auth?.user?.id;
});

// Load recruiter view (positions with candidates)
const loadRecruiterView = async () => {
  loadingRecruiter.value = true;
  try {
    const response = await axios.get('/api/marketplace/recruiter');
    const data = response.data.data || response.data;
    positions.value = data.positions || [];
  } catch (err) {
    console.error('Failed to load recruiter view', err);
    notify({
      type: 'error',
      text: 'Error cargando vista de reclutador'
    });
  } finally {
    loadingRecruiter.value = false;
  }
};

// Load opportunities for current people
const loadOpportunities = async () => {
  if (!currentUserId.value) {
    notify({
      type: 'warning',
      text: 'Please log in to view opportunities'
    });
    return;
  }

  loading.value = true;
  try {
    const response = await axios.get(`/api/people/${currentUserId.value}/marketplace`);
    const data = response.data.data || response.data;
    opportunities.value = data.opportunities || [];
  } catch (err) {
    console.error('Failed to load opportunities', err);
    notify({
      type: 'error',
      text: 'Error loading opportunities'
    });
  } finally {
    loading.value = false;
  }
};

// Load user's applications
const loadApplications = async () => {
  if (!currentUserId.value) return;

  try {
    const response = await axios.get('/api/applications');
    applications.value = response.data.data || response.data;
  } catch (err) {
    console.error('Failed to load applications', err);
  }
};

// Apply for position
const applyForPosition = async (jobOpeningId: number) => {
  if (!currentUserId.value) {
    notify({
      type: 'error',
      text: 'You must be logged in to apply'
    });
    return;
  }

  applying.value = jobOpeningId;
  try {
    await axios.post('/api/applications', {
      job_opening_id: jobOpeningId,
      people_id: currentUserId.value
    });
    notify({
      type: 'success',
      text: 'Application submitted successfully'
    });
    await loadApplications();
  } catch (err: any) {
    console.error('Failed to apply', err);
    notify({
      type: 'error',
      text: err.response?.data?.message || 'Error submitting application'
    });
  } finally {
    applying.value = null;
  }
};

// Check if already applied
const hasApplied = (jobOpeningId: number): boolean => {
  return applications.value.some(app => app.job_opening_id === jobOpeningId);
};

// Get match color
const getMatchColor = (percentage: number | undefined): string => {
  if (!percentage) return 'grey';
  if (percentage >= 80) return 'success';
  if (percentage >= 60) return 'warning';
  return 'error';
};

// Get status color
const getStatusColor = (status: string): string => {
  const statusMap: Record<string, string> = {
    'open': 'success',
    'closed': 'error',
    'filled': 'warning'
  };
  return statusMap[status] || 'grey';
};

onMounted(() => {
  loadRecruiterView(); // Vista por defecto para admin
  // loadOpportunities(); // Se cargará cuando se cambie al tab de empleado
  // loadApplications();
});
</script>

<template>
  <div class="pa-4">
    <!-- Header -->
    <div class="d-flex justify-space-between align-center mb-4" :style="{ background: headerGradient }" style="padding: 1.5rem; border-radius: 8px;">
      <div>
        <h1 class="text-h4 font-weight-bold mb-2" style="color: white;">Marketplace de Oportunidades</h1>
        <p class="text-subtitle-2" style="color: rgba(255,255,255,0.85);">
          Explora oportunidades internas y postula a roles que se ajusten a tu perfil
        </p>
      </div>
    </div>

    <!-- Description Section -->
    <v-card class="mb-6" elevation="0" variant="outlined">
      <v-card-text class="pa-6">
        <div class="d-flex align-start gap-4">
          <v-icon size="48" color="primary" class="mt-1">mdi-briefcase-search</v-icon>
          <div class="flex-grow-1">
            <h2 class="text-h6 font-weight-bold mb-3">¿Qué es el Marketplace Interno?</h2>
            <p class="text-body-2 mb-3">
              El <strong>Marketplace de Oportunidades</strong> facilita la movilidad interna conectando posiciones abiertas
              con el talento disponible en tu organización.
            </p>
            <v-alert type="info" variant="tonal" class="mt-4" density="compact">
              <template #prepend>
                <v-icon>mdi-information</v-icon>
              </template>
              <strong>Vista actual:</strong> Como administrador, puedes ver qué candidatos tienen mejor match para cada posición abierta
            </v-alert>
          </div>
        </div>
      </v-card-text>
    </v-card>

    <!-- Tabs -->
    <v-tabs v-model="activeTab" class="mb-4" color="primary">
      <v-tab value="recruiter">
        <v-icon start>mdi-account-search</v-icon>
        Buscar Talento
      </v-tab>
      <v-tab value="employee" disabled>
        <v-icon start>mdi-briefcase-search-outline</v-icon>
        Mis Oportunidades
        <v-chip size="x-small" class="ml-2" color="grey" variant="flat">Próximamente</v-chip>
      </v-tab>
    </v-tabs>

    <!-- Vista de Reclutador: Posiciones con Candidatos -->
    <div v-if="activeTab === 'recruiter'">
      <!-- Loading State -->
      <v-card v-if="loadingRecruiter" class="mb-6" elevation="0" variant="outlined">
        <v-card-text class="text-center py-12">
          <v-progress-circular indeterminate color="primary" size="48" />
          <p class="mt-4 text-medium-emphasis">Analizando candidatos...</p>
        </v-card-text>
      </v-card>

      <!-- Empty State -->
      <v-card v-else-if="positions.length === 0" class="mb-6" elevation="0" variant="outlined">
        <v-card-text class="text-center py-12">
          <v-icon size="80" class="mb-6 text-medium-emphasis">mdi-briefcase-search</v-icon>
          <div class="text-h6 mb-2">No hay posiciones abiertas</div>
          <div class="text-body-2 text-medium-emphasis">
            Crea nuevas posiciones para comenzar a buscar talento interno
          </div>
        </v-card-text>
      </v-card>

      <!-- Positions List -->
      <div v-else>
        <v-card v-for="position in positions" :key="position.id" class="mb-4" elevation="0" variant="outlined">
          <v-card-title class="pa-6">
            <div class="d-flex align-center justify-space-between w-100">
              <div class="flex-grow-1">
                <div class="text-h6 font-weight-bold">{{ position.title }}</div>
                <div class="text-body-2 text-medium-emphasis mt-1">
                  {{ position.role }} · {{ position.department }}
                </div>
              </div>
              <v-chip color="success" variant="flat" prepend-icon="mdi-account-group">
                {{ position.total_candidates }} candidatos
              </v-chip>
            </div>
          </v-card-title>

          <v-divider />

          <v-card-text class="pa-6">
            <h3 class="text-subtitle-1 font-weight-bold mb-4">Top Candidatos</h3>
            
            <v-card v-if="position.candidates.length === 0" variant="tonal" color="warning">
              <v-card-text class="text-center py-6">
                <v-icon size="48" class="mb-2">mdi-alert-circle-outline</v-icon>
                <div class="text-body-2">No hay candidatos disponibles en la organización</div>
              </v-card-text>
            </v-card>

            <v-list v-else class="pa-0">
              <v-list-item
                v-for="(candidate, index) in position.candidates.slice(0, 5)"
                :key="candidate.id"
                class="px-4 py-3"
                :class="{ 'border-b': index < Math.min(4, position.candidates.length - 1) }"
              >
                <template #prepend>
                  <v-avatar :color="getMatchColor(candidate.match_percentage)" size="40" class="mr-3">
                    <span class="text-white font-weight-bold">#{{ index + 1 }}</span>
                  </v-avatar>
                </template>

                <v-list-item-title class="font-weight-medium">
                  {{ candidate.name }}
                </v-list-item-title>
                <v-list-item-subtitle class="text-caption">
                  {{ candidate.current_role }}
                </v-list-item-subtitle>

                <template #append>
                  <div class="d-flex align-center gap-3">
                    <div class="text-center">
                      <div :class="`text-h6 text-${getMatchColor(candidate.match_percentage)}`">
                        {{ candidate.match_percentage }}%
                      </div>
                      <div class="text-caption text-medium-emphasis">Match</div>
                    </div>
                    <v-divider vertical />
                    <div class="text-center">
                      <div class="text-subtitle-2">{{ candidate.time_to_productivity }}</div>
                      <div class="text-caption text-medium-emphasis">días</div>
                    </div>
                    <v-chip
                      size="small"
                      :color="getMatchColor(candidate.match_percentage)"
                      variant="tonal"
                    >
                      {{ candidate.category }}
                    </v-chip>
                  </div>
                </template>
              </v-list-item>
            </v-list>

            <v-btn
              v-if="position.candidates.length > 5"
              variant="text"
              color="primary"
              class="mt-4"
              block
            >
              Ver todos los {{ position.candidates.length }} candidatos
              <v-icon end>mdi-chevron-down</v-icon>
            </v-btn>
          </v-card-text>
        </v-card>
      </div>
    </div>

    <!-- Vista de Empleado: Mis Oportunidades (Vista actual - para futuro) -->
    <div v-if="activeTab === 'employee'">
    <!-- Loading State -->
    <v-card v-if="loading" class="mb-6">
      <v-card-text class="text-center py-8">
        <v-progress-circular indeterminate color="primary" />
        <p class="mt-4">Loading opportunities...</p>
      </v-card-text>
    </v-card>

    <!-- Opportunities Grid -->
    <div v-if="!loading">
      <v-card v-if="opportunities.length === 0" class="mb-6">
        <v-card-text class="text-center py-12">
          <v-icon size="64" class="mb-4 text-grey">mdi-briefcase-search</v-icon>
          <p class="text-body1 text-grey">No opportunities available at the moment</p>
        </v-card-text>
      </v-card>

      <v-row v-else no-gutters class="gap-4">
        <v-col v-for="opportunity in opportunities" :key="opportunity.id" cols="12" sm="6" md="4">
          <v-card class="h-100 d-flex flex-column" elevation="0" border>
            <!-- Header -->
            <v-card-title class="pb-2">
              <div class="d-flex justify-space-between align-center w-100">
                <div class="flex-grow-1">
                  <h3 class="text-h6 font-weight-bold mb-0">{{ opportunity.title }}</h3>
                </div>
                <v-chip
                  :color="getStatusColor('open')"
                  text-color="white"
                  size="small"
                  label
                >
                  Open
                </v-chip>
              </div>
            </v-card-title>

            <!-- Description -->
            <v-card-text class="pb-2">
              <p class="text-body2 text-grey mb-4">
                {{ opportunity.description }}
              </p>

              <!-- Match Indicator -->
              <div v-if="opportunity.match_percentage !== undefined" class="mb-4">
                <div class="d-flex justify-space-between mb-2">
                  <span class="text-caption font-weight-medium">Your Match</span>
                  <span :class="`text-caption text-${getMatchColor(opportunity.match_percentage)} font-weight-bold`">
                    {{ opportunity.match_percentage }}%
                  </span>
                </div>
                <v-progress-linear
                  :value="opportunity.match_percentage"
                  :color="getMatchColor(opportunity.match_percentage)"
                  height="6"
                />
              </div>

              <!-- Time to Productivity -->
              <div v-if="opportunity.time_to_productivity !== undefined" class="mb-4">
                <p class="text-caption text-grey">
                  <v-icon size="16" class="mr-1">mdi-clock-outline</v-icon>
                  ~{{ opportunity.time_to_productivity }} days to full productivity
                </p>
              </div>

              <!-- Required Skills -->
              <div v-if="opportunity.required_skills?.length > 0">
                <p class="text-caption font-weight-medium mb-2">Required Skills:</p>
                <div class="d-flex flex-wrap gap-2">
                  <v-chip
                    v-for="skill in opportunity.required_skills.slice(0, 3)"
                    :key="skill.id"
                    size="small"
                    variant="outlined"
                  >
                    {{ skill.name }}
                  </v-chip>
                  <v-chip
                    v-if="opportunity.required_skills.length > 3"
                    size="small"
                    variant="outlined"
                    disabled
                  >
                    +{{ opportunity.required_skills.length - 3 }} more
                  </v-chip>
                </div>
              </div>
            </v-card-text>

            <!-- Actions -->
            <v-card-actions class="mt-auto">
              <v-spacer />
              <v-btn
                v-if="!hasApplied(opportunity.id)"
                color="primary"
                @click="applyForPosition(opportunity.id)"
                :loading="applying === opportunity.id"
              >
                Apply Now
              </v-btn>
              <v-chip
                v-else
                color="success"
                text-color="white"
                label
              >
                Applied
              </v-chip>
            </v-card-actions>
          </v-card>
        </v-col>
      </v-row>
    </div>
  </div>
</template>

<style scoped>
.gap-4 {
  gap: 16px;
}

.h-100 {
  height: 100%;
}

.d-flex {
  display: flex;
}

.flex-column {
  flex-direction: column;
}

.flex-grow-1 {
  flex-grow: 1;
}

.justify-space-between {
  justify-content: space-between;
}

.align-center {
  align-items: center;
}

.gap-2 {
  gap: 8px;
}

.mb-2 {
  margin-bottom: 8px;
}

.mb-4 {
  margin-bottom: 16px;
}

.mb-0 {
  margin-bottom: 0;
}

.pb-2 {
  padding-bottom: 8px;
}

.w-100 {
  width: 100%;
}

.mr-1 {
  margin-right: 4px;
}

.text-grey {
  color: #757575;
}

.flex-wrap {
  flex-wrap: wrap;
}

.mt-auto {
  margin-top: auto;
}
</style>
