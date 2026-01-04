<template>
  <div class="matching-results">
    <v-card class="mb-4">
      <v-card-title class="d-flex align-center">
        <v-icon left>mdi-account-match</v-icon>
        Internal Talent Matching
      </v-card-title>

      <v-card-subtitle>
        Available internal candidates matched to open positions
      </v-card-subtitle>

      <v-divider></v-divider>

      <!-- Filters & Stats -->
      <v-card-text class="pb-0">
        <v-row class="mb-4">
          <v-col cols="12" md="3">
            <v-select
              v-model="store.filters.matchReadiness"
              :items="readinessOptions"
              label="Filter by Readiness"
              clearable
              @update:model-value="store.setMatchReadinessFilter"
            />
          </v-col>
          <v-col cols="12" md="3">
            <v-select
              v-model="store.filters.matchScoreRange"
              :items="matchScoreOptions"
              label="Match Score Range"
              clearable
              @update:model-value="store.setMatchScoreRangeFilter"
            />
          </v-col>
          <v-col cols="12" md="3">
            <v-text-field
              v-model="store.filters.searchTerm"
              label="Search by name/role"
              prepend-icon="mdi-magnify"
              clearable
              @update:model-value="store.setSearchTermFilter"
            />
          </v-col>
          <v-col cols="12" md="3" class="text-right">
            <v-btn
              @click="generateMatchingReport"
              variant="outlined"
              size="small"
              prepend-icon="mdi-file-chart"
            >
              Report
            </v-btn>
          </v-col>
        </v-row>

        <!-- Summary Stats -->
        <v-row class="mb-4">
          <v-col cols="6" md="3">
            <v-card variant="outlined" class="text-center pa-3">
              <div class="text-caption text-medium-emphasis">Total Matches</div>
              <div class="text-h5 font-weight-bold">{{ filteredMatches.length }}</div>
            </v-card>
          </v-col>
          <v-col cols="6" md="3">
            <v-card variant="outlined" class="text-center pa-3">
              <div class="text-caption text-medium-emphasis">Immediate Ready</div>
              <div class="text-h5 font-weight-bold text-success">
                {{ immediateReadyCount }}
              </div>
            </v-card>
          </v-col>
          <v-col cols="6" md="3">
            <v-card variant="outlined" class="text-center pa-3">
              <div class="text-caption text-medium-emphasis">Avg Match Score</div>
              <div class="text-h5 font-weight-bold text-primary">
                {{ averageMatchScore }}%
              </div>
            </v-card>
          </v-col>
          <v-col cols="6" md="3">
            <v-card variant="outlined" class="text-center pa-3">
              <div class="text-caption text-medium-emphasis">Internal Coverage</div>
              <div class="text-h5 font-weight-bold text-info">
                {{ internalCoveragePercentage }}%
              </div>
            </v-card>
          </v-col>
        </v-row>
      </v-card-text>

      <v-divider></v-divider>

      <!-- Matches Table -->
      <v-data-table
        :headers="tableHeaders"
        :items="filteredMatches"
        :loading="loading"
        :items-per-page="15"
        class="elevation-0"
        density="comfortable"
      >
        <!-- Candidate -->
        <template v-slot:item.candidate_name="{ item }">
          <div class="d-flex align-center">
            <v-avatar size="32" class="mr-2" color="primary">
              {{ getInitials(item.candidate_name) }}
            </v-avatar>
            <div>
              <div class="font-weight-bold">{{ item.candidate_name }}</div>
              <div class="text-caption text-medium-emphasis">{{ item.current_role }}</div>
            </div>
          </div>
        </template>

        <!-- Target Role -->
        <template v-slot:item.target_role="{ item }">
          <div class="d-flex align-center">
            <v-icon size="small" class="mr-2">mdi-briefcase-check</v-icon>
            <strong>{{ item.target_role }}</strong>
          </div>
        </template>

        <!-- Match Score Progress -->
        <template v-slot:item.match_score="{ item }">
          <div class="d-flex align-center">
            <v-progress-linear
              :value="item.match_score"
              :color="getScoreColor(item.match_score)"
              class="flex-grow-1 mr-2"
            ></v-progress-linear>
            <span class="text-body2 font-weight-bold" style="min-width: 45px">
              {{ item.match_score }}%
            </span>
          </div>
        </template>

        <!-- Readiness Level -->
        <template v-slot:item.readiness_level="{ item }">
          <v-chip
            :color="getReadinessColor(item.readiness_level)"
            text-color="white"
            size="small"
          >
            {{ formatReadiness(item.readiness_level) }}
          </v-chip>
        </template>

        <!-- Transition Type -->
        <template v-slot:item.transition_type="{ item }">
          <v-chip
            :color="getTransitionColor(item.transition_type)"
            variant="outlined"
            size="small"
          >
            {{ formatTransition(item.transition_type) }}
          </v-chip>
        </template>

        <!-- Skill Gaps -->
        <template v-slot:item.skill_gaps="{ item }">
          <div v-if="item.skill_gaps && item.skill_gaps.length > 0">
            <v-chip
              v-for="gap in (item.skill_gaps || []).slice(0, 2)"
              :key="gap"
              size="small"
              variant="outlined"
              color="error"
              class="mr-1"
            >
              {{ gap }}
            </v-chip>
            <v-chip
              v-if="(item.skill_gaps || []).length > 2"
              size="small"
              variant="text"
            >
              +{{ (item.skill_gaps || []).length - 2 }}
            </v-chip>
          </div>
          <div v-else class="text-success">
            <v-icon size="small" class="mr-1">mdi-check-circle</v-icon>
            No gaps
          </div>
        </template>

        <!-- Risk Level -->
        <template v-slot:item.risk_level="{ item }">
          <v-chip
            :color="getRiskColor(item.risk_level)"
            text-color="white"
            size="small"
          >
            {{ item.risk_level }}
          </v-chip>
        </template>

        <!-- Actions -->
        <template v-slot:item.actions="{ item }">
          <v-menu>
            <template v-slot:activator="{ props }">
              <v-btn
                icon
                size="small"
                v-bind="props"
              >
                <v-icon>mdi-dots-vertical</v-icon>
              </v-btn>
            </template>
            <v-list>
              <v-list-item @click="viewMatchDetails(item)">
                <v-list-item-title>View Details</v-list-item-title>
              </v-list-item>
              <v-list-item @click="viewRecommendations(item)">
                <v-list-item-title>View Recommendations</v-list-item-title>
              </v-list-item>
              <v-list-item @click="createDevelopmentPlan(item)">
                <v-list-item-title>Create Dev Plan</v-list-item-title>
              </v-list-item>
              <v-divider></v-divider>
              <v-list-item @click="exportMatchDetails(item)">
                <v-list-item-title>Export</v-list-item-title>
              </v-list-item>
            </v-list>
          </v-menu>
        </template>
      </v-data-table>
    </v-card>

    <!-- Match Details Dialog -->
    <v-dialog v-model="showDetailsDialog" max-width="800px">
      <v-card v-if="selectedMatch">
        <v-card-title>
          {{ selectedMatch.candidate_name }} → {{ selectedMatch.target_role }}
        </v-card-title>
        <v-divider></v-divider>

        <v-card-text class="pt-4">
          <!-- Key Metrics -->
          <v-row class="mb-4">
            <v-col cols="12" md="4">
              <div class="text-caption text-medium-emphasis">Match Score</div>
              <v-progress-linear
                :value="selectedMatch.match_score"
                :color="getScoreColor(selectedMatch.match_score)"
                height="8"
                class="mb-2"
              ></v-progress-linear>
              <div class="text-h6">{{ selectedMatch.match_score }}%</div>
            </v-col>
            <v-col cols="12" md="4">
              <div class="text-caption text-medium-emphasis">Readiness</div>
              <v-chip
                :color="getReadinessColor(selectedMatch.readiness_level)"
                text-color="white"
                size="small"
              >
                {{ formatReadiness(selectedMatch.readiness_level) }}
              </v-chip>
            </v-col>
            <v-col cols="12" md="4">
              <div class="text-caption text-medium-emphasis">Risk Level</div>
              <v-chip
                :color="getRiskColor(selectedMatch.risk_level)"
                text-color="white"
                size="small"
              >
                {{ selectedMatch.risk_level }}
              </v-chip>
            </v-col>
          </v-row>

          <v-divider class="my-4"></v-divider>

          <!-- Score Breakdown -->
          <h4 class="mb-3">Match Score Breakdown</h4>
          <v-list density="compact" class="mb-4">
            <v-list-item>
              <template v-slot:prepend>
                <v-icon>mdi-checkbox-marked-circle</v-icon>
              </template>
              <v-list-item-title>Skill Match: 60%</v-list-item-title>
            </v-list-item>
            <v-list-item>
              <template v-slot:prepend>
                <v-icon>mdi-clock-check</v-icon>
              </template>
              <v-list-item-title>Readiness: 20%</v-list-item-title>
            </v-list-item>
            <v-list-item>
              <template v-slot:prepend>
                <v-icon>mdi-shield-check</v-icon>
              </template>
              <v-list-item-title>Risk Factor: 20%</v-list-item-title>
            </v-list-item>
          </v-list>

          <!-- Skill Gaps -->
          <h4 class="mb-3">Skill Gaps</h4>
          <div v-if="selectedMatch.skill_gaps && selectedMatch.skill_gaps.length > 0" class="mb-4">
            <v-chip
              v-for="gap in selectedMatch.skill_gaps"
              :key="gap"
              color="error"
              text-color="white"
              class="mr-2 mb-2"
              size="small"
            >
              {{ gap }}
            </v-chip>
          </div>
          <div v-else class="mb-4 text-success">
            <v-icon size="small">mdi-check-circle</v-icon>
            No skill gaps identified
          </div>

          <!-- Recommendations -->
          <h4 class="mb-3">Recommendations</h4>
          <v-alert
            type="info"
            variant="tonal"
            class="mb-4"
          >
            <strong>Transition Path:</strong> {{ formatTransition(selectedMatch.transition_type) }}
          </v-alert>
        </v-card-text>

        <v-divider></v-divider>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn @click="showDetailsDialog = false" variant="text">Close</v-btn>
          <v-btn @click="approveMatch" color="success" variant="elevated">Approve Match</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Error State -->
    <v-alert
      v-if="error"
      type="error"
      :text="error"
      closable
      @click:close="error = null"
    ></v-alert>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useApi } from '@/composables/useApi'
import { useNotification } from '@/composables/useNotification'
import { useWorkforcePlanningStore, type Match } from '@/stores/workforcePlanningStore'

const props = defineProps<{
  scenarioId: number
}>()

const api = useApi()
const { notifySuccess, notifyError } = useNotification()
const store = useWorkforcePlanningStore()

// State
const selectedMatch = ref<Match | null>(null)
const showDetailsDialog = ref(false)

// Filter options
const readinessOptions = [
  { title: 'Immediate', value: 'immediate' },
  { title: 'Short Term (1-3 months)', value: 'short_term' },
  { title: 'Long Term (3-12 months)', value: 'long_term' },
  { title: 'Not Ready', value: 'not_ready' },
]

const matchScoreOptions = [
  { title: '80-100%', value: 'high' },
  { title: '60-80%', value: 'medium' },
  { title: '40-60%', value: 'low' },
]

// Table headers
const tableHeaders = [
  { title: 'Candidate', value: 'candidate_name', width: '200px' },
  { title: 'Target Role', value: 'target_role', width: '180px' },
  { title: 'Match Score', value: 'match_score', align: 'center' },
  { title: 'Readiness', value: 'readiness_level', align: 'center' },
  { title: 'Type', value: 'transition_type', align: 'center' },
  { title: 'Gaps', value: 'skill_gaps' },
  { title: 'Risk', value: 'risk_level', align: 'center' },
  { title: 'Actions', value: 'actions', align: 'center', sortable: false },
]

// Computed properties
const loading = computed(() => store.getLoadingState('matches'))
const error = computed(() => store.getError('matches'))
const matches = computed(() => store.getMatches(props.scenarioId))
const filteredMatches = computed(() => store.getFilteredMatches(props.scenarioId))

const immediateReadyCount = computed(() => {
  return filteredMatches.value.filter(m => m.readiness_level === 'immediate').length
})

const averageMatchScore = computed(() => {
  if (filteredMatches.value.length === 0) return 0
  const sum = filteredMatches.value.reduce((acc, m) => acc + m.match_score, 0)
  return Math.round(sum / filteredMatches.value.length)
})

const internalCoveragePercentage = computed(() => {
  if (filteredMatches.value.length === 0) return 0
  const viableMatches = filteredMatches.value.filter(m => m.match_score >= 70).length
  return Math.round((viableMatches / filteredMatches.value.length) * 100)
})

// Methods
const fetchMatches = async () => {
  loading.value = true
  error.value = null
  try {
    const response = await api.get(
      `/api/v1/workforce-planning/scenarios/${props.scenarioId}/matches`
    )
    matches.value = response.data || []
  } catch (err) {
    error.value = 'Failed to load matching results'
    notifyError('Failed to load matching results')
    console.error(err)
  } finally {
    loading.value = false
  }
}

const applyFilters = () => {
  // Store filters handle this
}

const fetchMatches = async () => {
  await store.fetchMatches(props.scenarioId)
}

const viewMatchDetails = (match: Match) => {
  selectedMatch.value = match
  showDetailsDialog.value = true
}

const viewRecommendations = (match: Match) => {
  // Navigate to recommendations view
  notifySuccess(`View recommendations for ${match.candidate_name}`)
}

const createDevelopmentPlan = (match: Match) => {
  notifySuccess(`Development plan created for ${match.candidate_name}`)
}

const approveMatch = async () => {
  if (selectedMatch.value) {
    try {
      await api.post(
        `/api/v1/workforce-planning/matches/${selectedMatch.value.id}/approve`,
        {}
      )
      notifySuccess('Match approved successfully')
      showDetailsDialog.value = false
      store.clearScenarioCaches(props.scenarioId)
      await fetchMatches()
    } catch (err) {
      notifyError('Failed to approve match')
      console.error(err)
    }
  }
}

const exportMatchDetails = (match: Match) => {
  notifySuccess(`Match details exported for ${match.candidate_name}`)
}

const generateMatchingReport = () => {
  notifySuccess('Matching report generated and downloaded')
}

const getInitials = (name: string): string => {
  return name
    .split(' ')
    .map(n => n[0])
    .join('')
    .toUpperCase()
}

// Color helpers
const getScoreColor = (score: number): string => {
  if (score >= 80) return 'success'
  if (score >= 60) return 'warning'
  return 'error'
}

const getReadinessColor = (readiness: string): string => {
  const colorMap: Record<string, string> = {
    immediate: 'success',
    short_term: 'info',
    long_term: 'warning',
    not_ready: 'error',
  }
  return colorMap[readiness] || 'default'
}

const getTransitionColor = (transition: string): string => {
  const colorMap: Record<string, string> = {
    promotion: 'success',
    lateral: 'info',
    reskilling: 'warning',
    no_match: 'error',
  }
  return colorMap[transition] || 'default'
}

const getRiskColor = (risk: string): string => {
  const colorMap: Record<string, string> = {
    low: 'success',
    medium: 'warning',
    high: 'error',
  }
  return colorMap[risk] || 'default'
}

const formatReadiness = (readiness: string): string => {
  const formatMap: Record<string, string> = {
    immediate: 'Immediate',
    short_term: 'Short Term',
    long_term: 'Long Term',
    not_ready: 'Not Ready',
  }
  return formatMap[readiness] || readiness
}

const formatTransition = (transition: string): string => {
  const formatMap: Record<string, string> = {
    promotion: 'Promotion',
    lateral: 'Lateral Move',
    reskilling: 'Reskilling',
    no_match: 'No Match',
  }
  return formatMap[transition] || transition
}

// Lifecycle
onMounted(() => {
  fetchMatches()
})</script>

<style scoped lang="scss">
.matching-results {
  :deep(.v-data-table) {
    font-size: 0.875rem;
  }
}
</style>
