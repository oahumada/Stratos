<template>
  <layout>
    <div class="pa-4">
      <!-- Header -->
      <div class="d-flex justify-space-between align-center mb-4">
        <div>
          <h1 class="text-h4 font-weight-bold mb-2">Skills Management</h1>
          <p class="text-subtitle2 text-grey">Manage skills catalog</p>
        </div>
        <v-btn
          color="primary"
          prepend-icon="mdi-plus"
          @click="openDialog('create')"
        >
          New Skill
        </v-btn>
      </div>

      <!-- Filters -->
      <v-card class="mb-4" variant="outlined">
        <v-card-text>
          <v-text-field
            v-model="search"
            label="Search"
            placeholder="Skill name"
            prepend-icon="mdi-magnify"
            variant="outlined"
            density="compact"
            clearable
          />
        </v-card-text>
      </v-card>

      <!-- Loading State -->
      <v-card v-if="loading" class="mb-4">
        <v-card-text class="text-center py-8">
          <v-progress-circular indeterminate color="primary" />
        </v-card-text>
      </v-card>

      <!-- Error Alert -->
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
          :items="filteredSkills"
          :search="search"
          class="elevation-0"
        >
          <!-- Category -->
          <template #item.category="{ item }">
            <v-chip size="small" variant="outlined">
              {{ item.category || 'General' }}
            </v-chip>
          </template>

          <!-- Actions -->
          <template #item.actions="{ item }">
            <div class="d-flex gap-1">
              <v-btn
                icon
                size="small"
                color="warning"
                variant="text"
                @click="openDialog('edit', item)"
              >
                <v-icon>mdi-pencil</v-icon>
              </v-btn>
              <v-btn
                icon
                size="small"
                color="error"
                variant="text"
                @click="openDeleteDialog(item)"
              >
                <v-icon>mdi-trash-can</v-icon>
              </v-btn>
            </div>
          </template>

          <!-- No data -->
          <template #no-data>
            <div class="text-center py-8 text-grey">
              <v-icon size="48" class="mb-4">mdi-inbox-outline</v-icon>
              <p>No skills found</p>
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
          {{ dialogMode === 'create' ? 'New Skill' : 'Edit Skill' }}
        </v-card-title>

        <v-card-text>
          <v-form ref="form" @submit.prevent="saveSkill">
            <v-text-field
              v-model="formData.name"
              label="Skill Name"
              variant="outlined"
              :rules="[required, minLength(2)]"
              class="mb-3"
            />

            <v-text-field
              v-model="formData.category"
              label="Category"
              variant="outlined"
              class="mb-3"
              placeholder="e.g., Frontend, Backend, Soft Skills"
            />

            <v-textarea
              v-model="formData.description"
              label="Description"
              variant="outlined"
              rows="3"
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
            @click="saveSkill"
            :loading="saving"
          >
            {{ dialogMode === 'create' ? 'Create' : 'Update' }}
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
          Are you sure you want to delete <strong>{{ skillToDelete?.name }}</strong>?
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
            @click="deleteSkill"
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
import axios from 'axios'
import Layout from '@/layouts/AppLayout.vue'

interface Skill {
  id: number
  name: string
  category?: string
  description?: string
}

const skills = ref<Skill[]>([])
const loading = ref(false)
const saving = ref(false)
const error = ref<string | null>(null)
const search = ref('')
const dialogOpen = ref(false)
const deleteDialogOpen = ref(false)
const dialogMode = ref<'create' | 'edit'>('create')
const skillToDelete = ref<Skill | null>(null)
const form = ref()

const formData = ref({
  name: '',
  category: '',
  description: '',
})

const headers = [
  { title: 'Name', key: 'name', width: '300px' },
  { title: 'Category', key: 'category', width: '200px' },
  { title: 'Description', key: 'description' },
  { title: 'Actions', key: 'actions', sortable: false },
]

const filteredSkills = computed(() => {
  if (!search.value) return skills.value
  return skills.value.filter(s =>
    s.name.toLowerCase().includes(search.value.toLowerCase())
  )
})

const fetchSkills = async () => {
  loading.value = true
  error.value = null
  try {
    const response = await axios.get('/api/skills')
    skills.value = response.data.data || response.data
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Failed to load skills'
  } finally {
    loading.value = false
  }
}

const openDialog = (mode: 'create' | 'edit', skill?: Skill) => {
  dialogMode.value = mode
  if (mode === 'create') {
    formData.value = { name: '', category: '', description: '' }
  } else if (skill) {
    formData.value = { ...skill }
  }
  dialogOpen.value = true
}

const closeDialog = () => {
  dialogOpen.value = false
  form.value?.reset()
}

const saveSkill = async () => {
  if (!form.value?.validate()) return

  saving.value = true
  error.value = null

  try {
    if (dialogMode.value === 'create') {
      await axios.post('/api/skills', formData.value)
    } else {
      const skillId = skills.value.find(s => s.name === formData.value.name)?.id
      if (skillId) {
        await axios.put(`/api/skills/${skillId}`, formData.value)
      }
    }
    closeDialog()
    fetchSkills()
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Failed to save skill'
  } finally {
    saving.value = false
  }
}

const openDeleteDialog = (skill: Skill) => {
  skillToDelete.value = skill
  deleteDialogOpen.value = true
}

const deleteSkill = async () => {
  if (!skillToDelete.value) return

  saving.value = true
  error.value = null

  try {
    await axios.delete(`/api/skills/${skillToDelete.value.id}`)
    deleteDialogOpen.value = false
    fetchSkills()
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Failed to delete skill'
  } finally {
    saving.value = false
  }
}

const required = (value: any) => !!value || 'Required'
const minLength = (len: number) => (value: any) =>
  !value || value.length >= len || `Minimum ${len} characters`

onMounted(() => {
  fetchSkills()
})
</script>

<style scoped>
:deep(.v-data-table) {
  background: transparent;
}
</style>
