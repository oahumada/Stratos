<template>
  <layout>
    <div class="pa-4">
      <!-- Header with Back Button -->
      <div class="d-flex align-center gap-2 mb-4">
        <v-btn
          icon
          variant="text"
          @click="goBack"
        >
          <v-icon>mdi-arrow-left</v-icon>
        </v-btn>
        <h1 class="text-h4 font-weight-bold">{{ people?.name }}</h1>
      </div>

      <!-- Loading State -->
      <v-card v-if="loading" class="mb-4">
        <v-card-text class="text-center py-8">
          <v-progress-circular indeterminate color="primary" />
          <p class="mt-4">Loading people details...</p>
        </v-card-text>
      </v-card>

      <!-- Error State -->
      <v-alert
        v-if="error"
        type="error"
        closable
        class="mb-4"
        @click:close="error = null"
      >
        {{ error }}
      </v-alert>

      <div v-if="!loading && people" class="space-y-4">
        <!-- Basic Info -->
        <v-card>
          <v-card-title>Peopleal Information</v-card-title>
          <v-card-text>
            <v-row dense>
              <v-col cols="12" sm="6">
                <div class="mb-4">
                  <span class="text-caption text-grey">Full Name</span>
                  <p class="text-body2 font-weight-medium">{{ people.name }}</p>
                </div>
              </v-col>
              <v-col cols="12" sm="6">
                <div class="mb-4">
                  <span class="text-caption text-grey">Email</span>
                  <p class="text-body2 font-weight-medium">
                    <a :href="`mailto:${people.email}`">{{ people.email }}</a>
                  </p>
                </div>
              </v-col>
              <v-col cols="12" sm="6">
                <div class="mb-4">
                  <span class="text-caption text-grey">Department</span>
                  <v-chip
                    :color="getDepartmentColor(people.department)"
                    size="small"
                    class="mt-2"
                  >
                    {{ people.department }}
                  </v-chip>
                </div>
              </v-col>
              <v-col cols="12" sm="6">
                <div class="mb-4">
                  <span class="text-caption text-grey">Hired Date</span>
                  <p class="text-body2 font-weight-medium">
                    {{ formatDate(people.hired_at) }}
                  </p>
                </div>
              </v-col>
              <v-col cols="12">
                <div>
                  <span class="text-caption text-grey">Current Role</span>
                  <v-chip
                    v-if="people.role"
                    color="primary"
                    size="small"
                    class="mt-2"
                  >
                    {{ people.role.name }}
                  </v-chip>
                  <p v-else class="text-body2 text-grey mt-2">No role assigned</p>
                </div>
              </v-col>
            </v-row>

            <v-divider class="my-4" />

            <!-- Action Buttons -->
            <v-row dense>
              <v-col cols="12" sm="6">
                <v-btn
                  color="primary"
                  variant="outlined"
                  block
                  prepend-icon="mdi-pencil"
                  @click="editPeople"
                >
                  Edit
                </v-btn>
              </v-col>
              <v-col cols="12" sm="6">
                <v-btn
                  color="error"
                  variant="outlined"
                  block
                  prepend-icon="mdi-trash-can"
                  @click="openDeleteDialog"
                >
                  Delete
                </v-btn>
              </v-col>
            </v-row>
          </v-card-text>
        </v-card>

        <!-- Skills Section -->
        <v-card>
          <v-card-title>
            <div class="d-flex justify-space-between align-center w-100">
              <span>Skills ({{ peopleSkills.length }})</span>
              <v-btn
                color="primary"
                size="small"
                prepend-icon="mdi-plus"
                @click="openSkillDialog"
              >
                Add Skill
              </v-btn>
            </div>
          </v-card-title>

          <v-card-text>
            <v-data-table
              v-if="peopleSkills.length > 0"
              :headers="skillHeaders"
              :items="peopleSkills"
              class="elevation-0"
            >
              <!-- Level Badge -->
              <template #item.level="{ item }">
                <v-chip
                  :color="getSkillLevelColor(item.level)"
                  size="small"
                  :label="`Level ${item.level}`"
                />
              </template>

              <!-- Actions -->
              <template #item.actions="{ item }">
                <v-btn
                  icon
                  size="small"
                  color="error"
                  variant="text"
                  @click="removeSkill(item.id)"
                >
                  <v-icon>mdi-delete</v-icon>
                </v-btn>
              </template>
            </v-data-table>
            <div
              v-else
              class="text-center py-8 text-grey"
            >
              <v-icon size="48" class="mb-4">mdi-briefcase-outline</v-icon>
              <p>No skills assigned yet</p>
            </div>
          </v-card-text>
        </v-card>

        <!-- Action Buttons -->
        <v-row dense>
          <v-col cols="12" sm="6">
            <v-btn
              color="info"
              variant="outlined"
              block
              prepend-icon="mdi-chart-bar"
              @click="viewGapAnalysis"
            >
              View Gap Analysis
            </v-btn>
          </v-col>
          <v-col cols="12" sm="6">
            <v-btn
              color="success"
              variant="outlined"
              block
              prepend-icon="mdi-map-marker-path"
              @click="viewLearningPath"
            >
              View Learning Path
            </v-btn>
          </v-col>
        </v-row>
      </div>
    </div>

    <!-- Edit Dialog -->
    <v-dialog
      v-model="editDialogOpen"
      max-width="500px"
      persistent
    >
      <v-card>
        <v-card-title>Edit People</v-card-title>
        <v-card-text>
          <v-form ref="editForm" @submit.prevent="savePeople">
            <v-text-field
              v-model="formData.name"
              label="Full Name"
              variant="outlined"
              :rules="[required, minLength(3)]"
              class="mb-3"
            />

            <v-text-field
              v-model="formData.email"
              label="Email"
              type="email"
              variant="outlined"
              :rules="[required, isEmail]"
              class="mb-3"
            />

            <v-text-field
              v-model="formData.department"
              label="Department"
              variant="outlined"
              :rules="[required]"
              class="mb-3"
            />

            <v-select
              v-model="formData.role_id"
              label="Role"
              :items="roles"
              item-title="name"
              item-value="id"
              variant="outlined"
              clearable
              class="mb-3"
            />

            <v-text-field
              v-model="formData.hired_at"
              label="Hired Date"
              type="date"
              variant="outlined"
            />
          </v-form>
        </v-card-text>

        <v-card-actions>
          <v-spacer />
          <v-btn
            color="secondary"
            variant="text"
            @click="editDialogOpen = false"
          >
            Cancel
          </v-btn>
          <v-btn
            color="primary"
            @click="savePeople"
            :loading="saving"
          >
            Update
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Add Skill Dialog -->
    <v-dialog
      v-model="skillDialogOpen"
      max-width="400px"
      persistent
    >
      <v-card>
        <v-card-title>Add Skill</v-card-title>
        <v-card-text>
          <v-select
            v-model="skillFormData.skill_id"
            label="Select Skill"
            :items="availableSkills"
            item-title="name"
            item-value="id"
            variant="outlined"
            class="mb-3"
          />

          <v-slider
            v-model="skillFormData.level"
            :min="1"
            :max="5"
            label="Level"
            class="mt-6"
          />
          <p class="text-caption text-grey mt-2">
            Selected: <strong>Level {{ skillFormData.level }}</strong>
          </p>
        </v-card-text>

        <v-card-actions>
          <v-spacer />
          <v-btn
            color="secondary"
            variant="text"
            @click="skillDialogOpen = false"
          >
            Cancel
          </v-btn>
          <v-btn
            color="primary"
            @click="addSkill"
            :loading="saving"
          >
            Add
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Delete Dialog -->
    <v-dialog
      v-model="deleteDialogOpen"
      max-width="400px"
    >
      <v-card>
        <v-card-title>Confirm Delete</v-card-title>
        <v-card-text>
          Are you sure you want to delete <strong>{{ people?.name }}</strong>?
          <br>
          <small class="text-error">This action cannot be undone.</small>
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn
            color="secondary"
            variant="text"
            @click="deleteDialogOpen = false"
          >
            Cancel
          </v-btn>
          <v-btn
            color="error"
            @click="deletePeople"
            :loading="saving"
          >
            Delete
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </layout>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import axios from 'axios'
import Layout from '@/layouts/AppLayout.vue'

interface People {
  id: number
  name: string
  email: string
  department: string
  role_id?: number | null
  role?: { id: number; name: string } | null
  hired_at?: string
}

interface Skill {
  id: number
  name: string
}

interface PeopleSkill {
  id: number
  skill_id: number
  name: string
  level: number
}

interface Role {
  id: number
  name: string
}

const page = usePage()
const peopleId = computed(() => parseInt((page.props as any).peopleId || '0'))

// State
const people = ref<People | null>(null)
const peopleSkills = ref<PeopleSkill[]>([])
const roles = ref<Role[]>([])
const allSkills = ref<Skill[]>([])
const loading = ref(false)
const saving = ref(false)
const error = ref<string | null>(null)
const editDialogOpen = ref(false)
const skillDialogOpen = ref(false)
const deleteDialogOpen = ref(false)

// Form
const editForm = ref()
const formData = ref({
  name: '',
  email: '',
  department: '',
  role_id: null as number | null,
  hired_at: '',
})

const skillFormData = ref({
  skill_id: null as number | null,
  level: 3,
})

// Computed
const skillHeaders = [
  { title: 'Skill', key: 'name', width: '200px' },
  { title: 'Level', key: 'level', width: '100px' },
  { title: 'Actions', key: 'actions', width: '80px', sortable: false },
]

const availableSkills = computed(() => {
  const assignedIds = new Set(peopleSkills.value.map(s => s.skill_id))
  return allSkills.value.filter(s => !assignedIds.has(s.id))
})

// Methods
const goBack = () => {
  window.history.back()
}

const fetchPeople = async () => {
  loading.value = true
  error.value = null

  try {
    const response = await axios.get(`/api/People/${peopleId.value}`)
    people.value = response.data.data || response.data

    // Load form data
    if (people.value) {
      formData.value = {
        name: people.value.name,
        email: people.value.email,
        department: people.value.department,
        role_id: people.value.role_id || null,
        hired_at: people.value.hired_at || '',
      }
    }
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Failed to load people'
  } finally {
    loading.value = false
  }
}

const fetchPeopleSkills = async () => {
  try {
    const response = await axios.get(`/api/People/${peopleId.value}/skills`)
    peopleSkills.value = response.data.data || response.data
  } catch (err) {
    console.error('Failed to load skills', err)
  }
}

const fetchRoles = async () => {
  try {
    const response = await axios.get('/api/roles')
    roles.value = response.data.data || response.data
  } catch (err) {
    console.error('Failed to load roles', err)
  }
}

const fetchSkills = async () => {
  try {
    const response = await axios.get('/api/skills')
    allSkills.value = response.data.data || response.data
  } catch (err) {
    console.error('Failed to load skills', err)
  }
}

const editPeople = () => {
  editDialogOpen.value = true
}

const savePeople = async () => {
  if (!editForm.value?.validate()) return

  saving.value = true
  error.value = null

  try {
    await axios.put(`/api/People/${people.value?.id}`, formData.value)
    editDialogOpen.value = false
    fetchPeople()
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Failed to save people'
  } finally {
    saving.value = false
  }
}

const openSkillDialog = () => {
  skillFormData.value = {
    skill_id: null,
    level: 3,
  }
  skillDialogOpen.value = true
}

const addSkill = async () => {
  if (!skillFormData.value.skill_id) return

  saving.value = true
  error.value = null

  try {
    await axios.post(`/api/People/${people.value?.id}/skills`, {
      skill_id: skillFormData.value.skill_id,
      level: skillFormData.value.level,
    })
    skillDialogOpen.value = false
    fetchPeopleSkills()
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Failed to add skill'
  } finally {
    saving.value = false
  }
}

const removeSkill = async (skillId: number) => {
  if (!confirm('Remove this skill?')) return

  saving.value = true
  error.value = null

  try {
    await axios.delete(`/api/People/${people.value?.id}/skills/${skillId}`)
    fetchPeopleSkills()
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Failed to remove skill'
  } finally {
    saving.value = false
  }
}

const openDeleteDialog = () => {
  deleteDialogOpen.value = true
}

const deletePeople = async () => {
  saving.value = true
  error.value = null

  try {
    await axios.delete(`/api/People/${people.value?.id}`)
    router.visit('/People')
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Failed to delete people'
  } finally {
    saving.value = false
  }
}

const viewGapAnalysis = () => {
  router.visit(`/gap-analysis/${people.value?.id}`)
}

const viewLearningPath = () => {
  router.visit(`/learning-paths?people_id=${people.value?.id}`)
}

const formatDate = (date?: string) => {
  if (!date) return '—'
  return new Date(date).toLocaleDateString()
}

const getDepartmentColor = (dept: string) => {
  const colors: Record<string, string> = {
    engineering: 'blue',
    sales: 'green',
    marketing: 'orange',
    hr: 'purple',
    finance: 'teal',
  }
  return colors[dept.toLowerCase()] || 'grey'
}

const getSkillLevelColor = (level: number) => {
  if (level <= 1) return 'grey'
  if (level <= 2) return 'orange'
  if (level <= 3) return 'blue'
  if (level <= 4) return 'green'
  return 'purple'
}

// Validation Rules
const required = (value: any) => !!value || 'This field is required'
const minLength = (len: number) => (value: any) =>
  !value || value.length >= len || `Minimum ${len} characters`
const isEmail = (value: any) =>
  !value ||
  /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value) ||
  'Please enter a valid email'

// Lifecycle
onMounted(() => {
  fetchPeople()
  fetchPeopleSkills()
  fetchRoles()
  fetchSkills()
})
</script>

<style scoped>
:deep(.v-data-table) {
  background: transparent;
}
</style>
