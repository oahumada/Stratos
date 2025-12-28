<template>
  <layout>
    <div class="pa-4">
      <!-- Header -->
      <div class="d-flex justify-space-between align-center mb-4">
        <div>
          <h1 class="text-h4 font-weight-bold mb-2">People Management</h1>
          <p class="text-subtitle2 text-grey">Manage employees and their skills</p>
        </div>
        <v-btn
          color="primary"
          prepend-icon="mdi-plus"
          @click="openDialog('create')"
        >
          New Person
        </v-btn>
      </div>

      <!-- Filters -->
      <v-card class="mb-4" variant="outlined">
        <v-card-text>
          <v-row dense>
            <v-col cols="12" sm="6" md="3">
              <v-text-field
                v-model="filters.search"
                label="Search"
                placeholder="Name or email"
                prepend-icon="mdi-magnify"
                variant="outlined"
                density="compact"
                clearable
              />
            </v-col>
            <v-col cols="12" sm="6" md="3">
              <v-select
                v-model="filters.department"
                label="Department"
                :items="departments"
                variant="outlined"
                density="compact"
                clearable
              />
            </v-col>
            <v-col cols="12" sm="6" md="3">
              <v-select
                v-model="filters.role"
                label="Role"
                :items="roles"
                item-title="name"
                item-value="id"
                variant="outlined"
                density="compact"
                clearable
              />
            </v-col>
            <v-col cols="12" sm="6" md="3">
              <v-btn
                color="secondary"
                variant="outlined"
                block
                @click="resetFilters"
              >
                Reset Filters
              </v-btn>
            </v-col>
          </v-row>
        </v-card-text>
      </v-card>

      <!-- Loading & Error States -->
      <v-card v-if="loading" class="mb-4">
        <v-card-text class="text-center py-8">
          <v-progress-circular indeterminate color="primary" />
          <p class="mt-4">Loading people...</p>
        </v-card-text>
      </v-card>

      <v-alert
        v-if="error"
        type="error"
        closable
        class="mb-4"
        @click:close="error = null"
      >
        {{ error }}
      </v-alert>

      <!-- Data Table -->
      <v-card v-if="!loading">
        <v-data-table
          :headers="headers"
          :items="filteredPeople"
          :search="filters.search"
          :loading="loading"
          class="elevation-0"
        >
          <!-- Department Badge -->
          <template #item.department="{ item }">
            <v-chip
              :color="getDepartmentColor(item.department)"
              size="small"
              variant="outlined"
            >
              {{ item.department }}
            </v-chip>
          </template>

          <!-- Role Badge -->
          <template #item.role="{ item }">
            <v-chip
              v-if="item.role"
              size="small"
            >
              {{ item.role.name }}
            </v-chip>
            <span v-else class="text-grey">—</span>
          </template>

          <!-- Skills Count -->
          <template #item.skills_count="{ item }">
            <v-chip
              color="info"
              size="small"
              variant="outlined"
            >
              {{ item.skills_count || 0 }} skills
            </v-chip>
          </template>

          <!-- Hired Date -->
          <template #item.hired_at="{ item }">
            {{ formatDate(item.hired_at) }}
          </template>

          <!-- Actions -->
          <template #item.actions="{ item }">
            <div class="d-flex gap-1">
              <v-btn
                icon
                size="small"
                color="primary"
                variant="text"
                @click="viewPerson(item)"
                title="View"
              >
                <v-icon>mdi-eye</v-icon>
              </v-btn>
              <v-btn
                icon
                size="small"
                color="warning"
                variant="text"
                @click="openDialog('edit', item)"
                title="Edit"
              >
                <v-icon>mdi-pencil</v-icon>
              </v-btn>
              <v-btn
                icon
                size="small"
                color="danger"
                variant="text"
                @click="openDeleteDialog(item)"
                title="Delete"
              >
                <v-icon>mdi-trash-can</v-icon>
              </v-btn>
            </div>
          </template>

          <!-- No data -->
          <template #no-data>
            <div class="text-center py-8 text-grey">
              <v-icon size="48" class="mb-4">mdi-inbox-outline</v-icon>
              <p>No people found</p>
            </div>
          </template>
        </v-data-table>
      </v-card>
    </div>

    <!-- Form Dialog -->
    <v-dialog
      v-model="dialogOpen"
      max-width="500px"
      persistent
    >
      <v-card>
        <v-card-title>
          {{ dialogMode === 'create' ? 'New Person' : 'Edit Person' }}
        </v-card-title>

        <v-card-text>
          <v-form ref="form" @submit.prevent="savePerson">
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
              class="mb-3"
            />
          </v-form>
        </v-card-text>

        <v-card-actions>
          <v-spacer />
          <v-btn
            color="secondary"
            variant="text"
            @click="closeDialog"
          >
            Cancel
          </v-btn>
          <v-btn
            color="primary"
            @click="savePerson"
            :loading="saving"
          >
            {{ dialogMode === 'create' ? 'Create' : 'Update' }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Delete Confirmation Dialog -->
    <v-dialog
      v-model="deleteDialogOpen"
      max-width="400px"
    >
      <v-card>
        <v-card-title>Confirm Delete</v-card-title>
        <v-card-text>
          Are you sure you want to delete <strong>{{ personToDelete?.name }}</strong>?
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
            @click="deletePerson"
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
import { usePage } from '@inertiajs/vue3'
import axios from 'axios'
import Layout from '@/layouts/AppLayout.vue'

interface Person {
  id: number
  name: string
  email: string
  department: string
  role_id?: number | null
  role?: { id: number; name: string } | null
  hired_at?: string
  skills_count?: number
}

interface Role {
  id: number
  name: string
}

// State
const people = ref<Person[]>([])
const roles = ref<Role[]>([])
const loading = ref(false)
const saving = ref(false)
const error = ref<string | null>(null)
const dialogOpen = ref(false)
const deleteDialogOpen = ref(false)
const dialogMode = ref<'create' | 'edit'>('create')
const personToDelete = ref<Person | null>(null)
const form = ref()

// Filters
const filters = ref({
  search: '',
  department: null,
  role: null,
})

// Form Data
const formData = ref({
  name: '',
  email: '',
  department: '',
  role_id: null as number | null,
  hired_at: '',
})

// Computed
const departments = computed(() => {
  const depts = [...new Set(people.value.map(p => p.department))]
  return depts.filter(Boolean)
})

const filteredPeople = computed(() => {
  return people.value.filter(person => {
    const matchesSearch =
      !filters.value.search ||
      person.name.toLowerCase().includes(filters.value.search.toLowerCase()) ||
      person.email.toLowerCase().includes(filters.value.search.toLowerCase())

    const matchesDept =
      !filters.value.department ||
      person.department === filters.value.department

    const matchesRole =
      !filters.value.role ||
      person.role_id === filters.value.role

    return matchesSearch && matchesDept && matchesRole
  })
})

const headers = [
  { title: 'Name', key: 'name', width: '200px' },
  { title: 'Email', key: 'email', width: '200px' },
  { title: 'Department', key: 'department', width: '140px' },
  { title: 'Role', key: 'role', width: '140px' },
  { title: 'Skills', key: 'skills_count', width: '100px' },
  { title: 'Hired', key: 'hired_at', width: '120px' },
  { title: 'Actions', key: 'actions', width: '100px', sortable: false },
]

// Methods
const fetchPeople = async () => {
  loading.value = true
  error.value = null
  try {
    const response = await axios.get('/api/people')
    people.value = response.data.data || response.data
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Failed to load people'
    console.error(err)
  } finally {
    loading.value = false
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

const openDialog = (mode: 'create' | 'edit', person?: Person) => {
  dialogMode.value = mode
  if (mode === 'create') {
    formData.value = {
      name: '',
      email: '',
      department: '',
      role_id: null,
      hired_at: new Date().toISOString().split('T')[0],
    }
  } else if (person) {
    formData.value = {
      name: person.name,
      email: person.email,
      department: person.department,
      role_id: person.role_id || null,
      hired_at: person.hired_at || '',
    }
  }
  dialogOpen.value = true
}

const closeDialog = () => {
  dialogOpen.value = false
  form.value?.reset()
}

const savePerson = async () => {
  if (!form.value?.validate()) return

  saving.value = true
  error.value = null

  try {
    if (dialogMode.value === 'create') {
      await axios.post('/api/people', formData.value)
    } else {
      const personId = people.value.find(
        p => p.email === formData.value.email
      )?.id
      if (personId) {
        await axios.put(`/api/people/${personId}`, formData.value)
      }
    }
    closeDialog()
    fetchPeople()
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Failed to save person'
    console.error(err)
  } finally {
    saving.value = false
  }
}

const viewPerson = (person: Person) => {
  // TODO: Navigate to detail page
  console.log('View person:', person)
}

const openDeleteDialog = (person: Person) => {
  personToDelete.value = person
  deleteDialogOpen.value = true
}

const deletePerson = async () => {
  if (!personToDelete.value) return

  saving.value = true
  error.value = null

  try {
    await axios.delete(`/api/people/${personToDelete.value.id}`)
    deleteDialogOpen.value = false
    fetchPeople()
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Failed to delete person'
    console.error(err)
  } finally {
    saving.value = false
  }
}

const resetFilters = () => {
  filters.value = {
    search: '',
    department: null,
    role: null,
  }
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
  fetchRoles()
})
</script>

<style scoped>
:deep(.v-data-table) {
  background: transparent;
}
</style>
