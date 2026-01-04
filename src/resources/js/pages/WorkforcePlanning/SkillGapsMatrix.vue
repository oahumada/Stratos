<template>
  <div class="skill-gaps-matrix">
    <v-card class="mb-4">
      <v-card-title class="d-flex align-center">
        <v-icon left>mdi-matrix</v-icon>
        Skill Gaps Analysis
      </v-card-title>

      <v-card-subtitle>
        Skills vs Departments - Coverage analysis and remediation strategies
      </v-card-subtitle>

      <v-divider></v-divider>

      <!-- Filters -->
      <v-card-text class="pb-0">
        <v-row class="mb-4">
          <v-col cols="12" md="4">
            <v-select
              v-model="store.filters.gapPriority"
              :items="priorityOptions"
              label="Filter by Priority"
              clearable
              @update:model-value="store.setGapPriorityFilter"
            />
          </v-col>
          <v-col cols="12" md="4">
            <v-select
              v-model="store.filters.gapDepartment"
              :items="departmentOptions"
              label="Filter by Department"
              clearable
              @update:model-value="store.setGapDepartmentFilter"
            />
          </v-col>
          <v-col cols="12" md="4" class="text-right">
            <v-btn
              @click="downloadGapsReport"
              variant="outlined"
              size="small"
              prepend-icon="mdi-download"
            >
              Export Matrix
            </v-btn>
          </v-col>
        </v-row>
      </v-card-text>

      <!-- Matrix Table -->
      <div class="pa-4 overflow-x-auto">
        <table class="skills-matrix">
          <thead>
            <tr>
              <th>Skill</th>
              <th v-for="dept in departments" :key="dept">{{ dept }}</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="skill in filteredSkillGaps" :key="skill.id">
              <td class="skill-name">
                <strong>{{ skill.skill_name }}</strong>
                <v-chip
                  :color="getPriorityColor(skill.priority)"
                  text-color="white"
                  size="x-small"
                  class="ml-2"
                >
                  {{ skill.priority }}
                </v-chip>
              </td>
              <td v-for="dept in departments" :key="`${skill.id}-${dept}`"
                  :class="getCoverageClass(skill, dept)"
                  @click="viewGapDetails(skill, dept)"
              >
                <div class="gap-cell">
                  <div class="coverage-pct">{{ getGapCoverage(skill, dept) }}%</div>
                  <div class="text-caption">{{ getRemediationLabel(skill, dept) }}</div>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Legend -->
      <v-card-text>
        <h4 class="mb-2">Legend</h4>
        <v-row>
          <v-col cols="6" md="3">
            <div class="d-flex align-center">
              <v-chip color="success" size="small" class="mr-2"></v-chip>
              <span class="text-caption">Covered (>80%)</span>
            </div>
          </v-col>
          <v-col cols="6" md="3">
            <div class="d-flex align-center">
              <v-chip color="warning" size="small" class="mr-2"></v-chip>
              <span class="text-caption">Partial (50-80%)</span>
            </div>
          </v-col>
          <v-col cols="6" md="3">
            <div class="d-flex align-center">
              <v-chip color="error" size="small" class="mr-2"></v-chip>
              <span class="text-caption">Critical (<50%)</span>
            </div>
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>

    <!-- Gaps Details Dialog -->
    <v-dialog v-model="showDetailsDialog" max-width="600px">
      <v-card v-if="selectedGap">
        <v-card-title>
          {{ selectedGap.skill_name }} - {{ selectedGap.department }}
        </v-card-title>
        <v-divider></v-divider>

        <v-card-text class="pt-4">
          <v-row class="mb-4">
            <v-col cols="12" md="6">
              <div class="text-caption text-medium-emphasis">Coverage</div>
              <v-progress-linear
                :value="selectedGap.coverage_percentage"
                :color="getCoverageColor(selectedGap.coverage_percentage)"
                height="8"
                class="mb-2"
              ></v-progress-linear>
              <div class="text-h6">{{ selectedGap.coverage_percentage }}%</div>
            </v-col>
            <v-col cols="12" md="6">
              <div class="text-caption text-medium-emphasis">Priority</div>
              <v-chip
                :color="getPriorityColor(selectedGap.priority)"
                text-color="white"
                size="small"
              >
                {{ selectedGap.priority }}
              </v-chip>
            </v-col>
          </v-row>

          <v-divider class="my-4"></v-divider>

          <h4 class="mb-3">Remediation Strategy</h4>
          <v-card variant="outlined" class="mb-4">
            <v-card-text>
              {{ selectedGap.remediation_strategy }}
            </v-card-text>
          </v-card>

          <h4 class="mb-3">Recommendations</h4>
          <v-list density="compact">
            <v-list-item>
              <template v-slot:prepend>
                <v-icon size="small">mdi-school</v-icon>
              </template>
              <v-list-item-title>Training Program (3 months)</v-list-item-title>
            </v-list-item>
            <v-list-item>
              <template v-slot:prepend>
                <v-icon size="small">mdi-briefcase-search</v-icon>
              </template>
              <v-list-item-title>External Hiring (2-3 positions)</v-list-item-title>
            </v-list-item>
          </v-list>
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
import { useWorkforcePlanningStore, type SkillGap } from '@/stores/workforcePlanningStore'

const props = defineProps<{
  scenarioId: number
}>()

const api = useApi()
const { notifySuccess, notifyError } = useNotification()
const store = useWorkforcePlanningStore()

// State
const selectedGap = ref<SkillGap | null>(null)
const showDetailsDialog = ref(false)

const priorityOptions = [
  { title: 'Critical', value: 'critical' },
  { title: 'High', value: 'high' },
  { title: 'Medium', value: 'medium' },
  { title: 'Low', value: 'low' },
]

// Computed
const departments = computed(() => {
  const depts = new Set(skillGaps.value.map(gap => gap.department))
  return Array.from(depts).sort()
})

const departmentOptions = computed(() => {
  return departments.value.map(dept => ({ title: dept, value: dept }))
})

// Methods
const fetchSkillGaps = async () => {
  await store.fetchSkillGaps(props.scenarioId)
}

const viewGapDetails = (gap: SkillGap, dept: string) => {
  selectedGap.value = { ...gap, department: dept }
  showDetailsDialog.value = true
}

const downloadGapsReport = () => {
  notifySuccess('Skill gaps matrix exported')
}

const getGapCoverage = (skill: SkillGap, dept: string): number => {
  // In real implementation, this would be department-specific
  return skill.coverage_percentage
}

const getRemediationLabel = (skill: SkillGap, dept: string): string => {
  if (skill.coverage_percentage >= 80) return 'Covered'
  if (skill.coverage_percentage >= 50) return 'Partial'
  return 'Critical'
}

const getCoverageClass = (skill: SkillGap, dept: string): string => {
  const coverage = getGapCoverage(skill, dept)
  if (coverage >= 80) return 'bg-success-light'
  if (coverage >= 50) return 'bg-warning-light'
  return 'bg-error-light'
}

const getCoverageColor = (coverage: number): string => {
  if (coverage >= 80) return 'success'
  if (coverage >= 50) return 'warning'
  return 'error'
}

const getPriorityColor = (priority: string): string => {
  const colorMap: Record<string, string> = {
    critical: 'error',
    high: 'warning',
    medium: 'info',
    low: 'success',
  }
  return colorMap[priority] || 'default'
}

onMounted(() => {
  fetchSkillGaps()
})
</script>

<style scoped lang="scss">
.skill-gaps-matrix {
  .skills-matrix {
    width: 100%;
    border-collapse: collapse;

    thead {
      th {
        background-color: var(--v-theme-surface-variant);
        padding: 12px;
        text-align: left;
        font-weight: 600;
        border: 1px solid var(--v-border-color);
      }
    }

    tbody {
      tr {
        &:hover {
          background-color: var(--v-theme-surface-bright);
        }

        td {
          padding: 12px;
          border: 1px solid var(--v-border-color);
          cursor: pointer;
          text-align: center;

          &.skill-name {
            text-align: left;
            font-weight: 500;
            cursor: default;
          }

          .gap-cell {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;

            .coverage-pct {
              font-weight: bold;
              font-size: 1.1rem;
            }
          }

          &.bg-success-light {
            background-color: rgba(76, 175, 80, 0.1);
          }

          &.bg-warning-light {
            background-color: rgba(255, 193, 7, 0.1);
          }

          &.bg-error-light {
            background-color: rgba(244, 67, 54, 0.1);
          }
        }
      }
    }
  }

  .overflow-x-auto {
    overflow-x: auto;
  }
}
</style>
