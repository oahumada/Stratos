<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import { useNotification } from '@kyvg/vue3-notification';
import { usePage } from '@inertiajs/vue3';

defineOptions({ layout: AppLayout });

const { notify } = useNotification();
const page = usePage();

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

// State
const opportunities = ref<JobOpening[]>([]);
const applications = ref<Application[]>([]);
const loading = ref(false);
const applying = ref<number | null>(null);
const filterStatus = ref<string>('open');

// Current user (get from inertia props)
const currentUserId = computed(() => {
  return (page.props as any).auth?.user?.id;
});

// Load opportunities for current person
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
    const response = await axios.get(`/api/person/${currentUserId.value}/marketplace`);
    opportunities.value = response.data.data || response.data;
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
      person_id: currentUserId.value
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
  loadOpportunities();
  loadApplications();
});
</script>

<template>
  <div class="pa-4">
    <div class="mb-6">
      <h1 class="text-h4 font-weight-bold mb-2">Internal Opportunities</h1>
      <p class="text-body2 text-grey">Explore and apply for internal job openings</p>
    </div>

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
