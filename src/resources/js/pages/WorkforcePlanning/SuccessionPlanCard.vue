<template>
  <div class="succession-plan-card">
    <v-card class="mb-4">
      <v-card-title class="d-flex align-center">
        <v-icon left>mdi-crown</v-icon>
        Succession Planning
      </v-card-title>

      <v-card-subtitle>
        Critical role succession strategies and successor readiness
      </v-card-subtitle>

      <v-divider></v-divider>

      <!-- Summary Metrics -->
      <v-card-text class="pb-0">
        <v-row class="mb-4">
          <v-col cols="6" md="3">
            <v-card variant="outlined" class="text-center pa-3">
              <div class="text-caption">Critical Roles</div>
              <div class="text-h5 font-weight-bold">{{ successionPlans.length }}</div>
            </v-card>
          </v-col>
          <v-col cols="6" md="3">
            <v-card variant="outlined" class="text-center pa-3">
              <div class="text-caption">Ready Now</div>
              <div class="text-h5 font-weight-bold text-success">{{ readyNowCount }}</div>
            </v-card>
          </v-col>
          <v-col cols="6" md="3">
            <v-card variant="outlined" class="text-center pa-3">
              <div class="text-caption">At Risk</div>
              <div class="text-h5 font-weight-bold text-error">{{ atRiskCount }}</div>
            </v-card>
          </v-col>
          <v-col cols="6" md="3">
            <v-card variant="outlined" class="text-center pa-3">
              <div class="text-caption">Avg Readiness</div>
              <div class="text-h5 font-weight-bold text-primary">{{ avgReadiness }}%</div>
            </v-card>
          </v-col>
        </v-row>
      </v-card-text>

      <v-divider></v-divider>

      <!-- Succession Plan Cards -->
      <v-card-text>
        <v-row>
          <v-col
            v-for="plan in successionPlans"
            :key="plan.id"
            cols="12"
            md="6"
            lg="4"
          >
            <v-card
              :class="['role-card', { 'at-risk': plan.criticality_level === 'critical' && !plan.primary_successor }]"
              variant="outlined"
            >
              <!-- Role Header -->
              <v-card-title class="pb-2">
                <div class="d-flex align-center">
                  <div class="flex-grow-1">
                    <div class="font-weight-bold">{{ plan.role_name }}</div>
                    <div class="text-caption text-medium-emphasis">
                      {{ plan.current_holder }}
                    </div>
                  </div>
                  <v-chip
                    :color="getRiskColor(plan)"
                    text-color="white"
                    size="x-small"
                  >
                    {{ getRiskLevel(plan) }}
                  </v-chip>
                </div>
              </v-card-title>

              <v-divider></v-divider>

              <!-- Successors -->
              <v-card-text class="pt-3">
                <!-- Primary Successor -->
                <div class="mb-3">
                  <div class="text-caption font-weight-bold">PRIMARY SUCCESSOR</div>
                  <div v-if="plan.primary_successor" class="mt-2">
                    <v-card variant="flat" color="success-light" class="pa-3">
                      <div class="d-flex align-center">
                        <v-avatar size="32" color="success" class="mr-2">
                          {{ getInitials(plan.primary_successor.name) }}
                        </v-avatar>
                        <div class="flex-grow-1">
                          <div class="font-weight-bold">{{ plan.primary_successor.name }}</div>
                          <div class="text-caption">Readiness: {{ plan.primary_successor.readiness }}%</div>
                        </div>
                        <v-icon
                          v-if="plan.primary_successor.readiness >= 80"
                          color="success"
                          size="small"
                        >
                          mdi-check-circle
                        </v-icon>
                      </div>
                      <v-progress-linear
                        :value="plan.primary_successor.readiness"
                        color="success"
                        height="4"
                        class="mt-2"
                      ></v-progress-linear>
                    </v-card>
                  </div>
                  <div v-else class="text-error text-body2 mt-2">
                    <v-icon size="small">mdi-alert-circle</v-icon>
                    No successor identified
                  </div>
                </div>

                <!-- Secondary Successors -->
                <div v-if="plan.secondary_successors && plan.secondary_successors.length > 0" class="mb-3">
                  <div class="text-caption font-weight-bold">SECONDARY SUCCESSORS</div>
                  <div
                    v-for="succ in plan.secondary_successors"
                    :key="succ.id"
                    class="mt-2"
                  >
                    <v-card variant="flat" color="info-light" class="pa-2">
                      <div class="d-flex align-center">
                        <v-avatar size="24" color="info" class="mr-2">
                          {{ getInitials(succ.name) }}
                        </v-avatar>
                        <div class="flex-grow-1">
                          <div class="text-body2 font-weight-bold">{{ succ.name }}</div>
                          <v-progress-linear
                            :value="succ.readiness"
                            color="info"
                            height="3"
                            class="mt-1"
                          ></v-progress-linear>
                        </div>
                        <span class="text-caption">{{ succ.readiness }}%</span>
                      </div>
                    </v-card>
                  </div>
                </div>

                <!-- Development Actions -->
                <div class="mt-4 pt-3 border-t">
                  <div class="text-caption font-weight-bold mb-2">DEVELOPMENT ACTIONS</div>
                  <v-chip
                    v-if="plan.development_plan"
                    color="primary"
                    text-color="white"
                    size="small"
                    class="mr-1"
                    prepend-icon="mdi-school"
                  >
                    Training {{ plan.development_plan.duration }}
                  </v-chip>
                  <v-chip
                    v-if="plan.mentoring_assigned"
                    color="primary"
                    text-color="white"
                    size="small"
                    class="mr-1"
                    prepend-icon="mdi-human-greeting"
                  >
                    Mentoring
                  </v-chip>
                </div>
              </v-card-text>

              <!-- Actions -->
              <v-divider></v-divider>
              <v-card-actions>
                <v-btn
                  @click="viewDetails(plan)"
                  size="small"
                  variant="text"
                >
                  Details
                </v-btn>
                <v-spacer></v-spacer>
                <v-btn
                  @click="editPlan(plan)"
                  size="small"
                  variant="text"
                  color="primary"
                >
                  Edit
                </v-btn>
              </v-card-actions>
            </v-card>
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>

    <!-- Plan Details Dialog -->
    <v-dialog v-model="showDetailsDialog" max-width="600px">
      <v-card v-if="selectedPlan">
        <v-card-title>{{ selectedPlan.role_name }} - Succession Plan</v-card-title>
        <v-divider></v-divider>

        <v-card-text class="pt-4">
          <!-- Current Holder -->
          <h4 class="mb-2">Current Holder</h4>
          <v-card variant="outlined" class="mb-4 pa-3">
            <div class="font-weight-bold">{{ selectedPlan.current_holder }}</div>
            <div class="text-caption text-medium-emphasis">
              Tenure: {{ selectedPlan.tenure_years }} years
            </div>
          </v-card>

          <!-- Retirement Info -->
          <h4 class="mb-2">Timeline</h4>
          <v-card variant="outlined" class="mb-4 pa-3">
            <v-row>
              <v-col cols="6">
                <div class="text-caption text-medium-emphasis">Planned Retirement</div>
                <div class="text-body2 font-weight-bold">{{ selectedPlan.planned_retirement }}</div>
              </v-col>
              <v-col cols="6">
                <div class="text-caption text-medium-emphasis">Months to Prepare</div>
                <div class="text-body2 font-weight-bold">{{ selectedPlan.months_to_retirement }}</div>
              </v-col>
            </v-row>
          </v-card>

          <!-- Primary Successor Full Info -->
          <h4 class="mb-2">Primary Successor</h4>
          <v-card v-if="selectedPlan.primary_successor" variant="outlined" class="mb-4 pa-3">
            <div class="font-weight-bold mb-2">{{ selectedPlan.primary_successor.name }}</div>
            <v-row class="mb-3">
              <v-col cols="6">
                <div class="text-caption text-medium-emphasis">Readiness</div>
                <v-progress-linear
                  :value="selectedPlan.primary_successor.readiness"
                  color="primary"
                  class="mb-2"
                ></v-progress-linear>
                <div class="text-body2">{{ selectedPlan.primary_successor.readiness }}%</div>
              </v-col>
              <v-col cols="6">
                <div class="text-caption text-medium-emphasis">Current Role</div>
                <div class="text-body2">{{ selectedPlan.primary_successor.current_role }}</div>
              </v-col>
            </v-row>
            <div class="text-caption text-medium-emphasis">Development Plan</div>
            <div class="text-body2">{{ selectedPlan.development_plan?.description }}</div>
          </v-card>
        </v-card-text>

        <v-divider></v-divider>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn @click="showDetailsDialog = false" variant="text">Close</v-btn>
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
import { useWorkforcePlanningStore, type SuccessionPlan } from '@/stores/workforcePlanningStore'

const props = defineProps<{
  scenarioId: number
}>()

const api = useApi()
const { showSuccess, showError } = useNotification()
const store = useWorkforcePlanningStore()

// State
const selectedPlan = ref<SuccessionPlan | null>(null)
const showDetailsDialog = ref(false)

// Computed
const loading = computed(() => store.getLoadingState('succession'))
const error = computed(() => store.getError('succession'))
const successionPlans = computed(() => store.getSuccessionPlans(props.scenarioId))

// Computed
const readyNowCount = computed(() => {
  const plans = successionPlans.value
  if (!Array.isArray(plans)) return 0
  return plans.filter(
    p => p.primary_successor && p.primary_successor.readiness >= 80
  ).length
})

const atRiskCount = computed(() => {
  const plans = successionPlans.value
  if (!Array.isArray(plans)) return 0
  return plans.filter(
    p => !p.primary_successor || (p.primary_successor && (p.months_to_retirement ?? 0) < 6)
  ).length
})

const avgReadiness = computed(() => {
  const plans = successionPlans.value
  if (!Array.isArray(plans) || plans.length === 0) return 0
  const sum = plans.reduce((acc, p) => {
    return acc + (p.primary_successor?.readiness || 0)
  }, 0)
  return Math.round(sum / plans.length)
})

// Methods
const fetchSuccessionPlans = async () => {
  await store.fetchSuccessionPlans(props.scenarioId)
}

const viewDetails = (plan: SuccessionPlan) => {
  selectedPlan.value = plan
  showDetailsDialog.value = true
}

const editPlan = (plan: SuccessionPlan) => {
  showSuccess(`Editing succession plan for ${plan.role_name}`)
}

const getInitials = (name: string): string => {
  return name
    .split(' ')
    .map(n => n[0])
    .join('')
    .toUpperCase()
}

const getRiskColor = (plan: SuccessionPlan): string => {
  if (!plan.primary_successor) return 'error'
  if (plan.primary_successor.readiness < 50) return 'error'
  if (plan.primary_successor.readiness < 80) return 'warning'
  return 'success'
}

const getRiskLevel = (plan: SuccessionPlan): string => {
  if (!plan.primary_successor) return 'At Risk'
  if (plan.primary_successor.readiness < 50) return 'High Risk'
  if (plan.primary_successor.readiness < 80) return 'Medium Risk'
  return 'Ready'
}

onMounted(() => {
  fetchSuccessionPlans()
})
</script>

<style scoped lang="scss">
.succession-plan-card {
  .role-card {
    height: 100%;
    transition: all 0.3s ease;

    &.at-risk {
      border-color: rgb(244, 67, 54);
      border-width: 2px;
      background-color: rgba(244, 67, 54, 0.02);
    }

    &:hover {
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
      transform: translateY(-2px);
    }
  }

  .border-t {
    border-top: 1px solid var(--v-border-color);
  }

  :deep(.color-light) {
    background-color: rgba(244, 67, 54, 0.1);
  }

  :deep(.success-light) {
    background-color: rgba(76, 175, 80, 0.1);
  }

  :deep(.info-light) {
    background-color: rgba(33, 150, 243, 0.1);
  }
}
</style>
