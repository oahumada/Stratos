<template>
  <layout>
    <div class="pa-4">
      <!-- Header -->
      <div class="mb-4">
        <h1 class="text-h4 font-weight-bold mb-2">Roles Management</h1>
        <p class="text-subtitle2 text-grey">View and manage company roles</p>
      </div>

      <!-- Filters -->
      <v-card class="mb-4" variant="outlined">
        <v-card-text>
          <v-text-field
            v-model="search"
            label="Search"
            placeholder="Role name"
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

      <!-- Data Table -->
      <v-card v-if="!loading">
        <v-data-table
          :headers="headers"
          :items="filteredRoles"
          :search="search"
          class="elevation-0"
        >
          <!-- Employees Count -->
          <template #item.employees_count="{ item }">
            <v-chip color="info" size="small" variant="outlined">
              {{ item.people_count || 0 }} employees
            </v-chip>
          </template>

          <!-- Skills Count -->
          <template #item.skills_count="{ item }">
            <v-chip color="primary" size="small" variant="outlined">
              {{ item.skills_count || 0 }} skills
            </v-chip>
          </template>

          <!-- Actions -->
          <template #item.actions="{ item }">
            <v-btn
              icon
              size="small"
              color="primary"
              variant="text"
              @click="viewRole(item)"
            >
              <v-icon>mdi-eye</v-icon>
            </v-btn>
          </template>

          <!-- No data -->
          <template #no-data>
            <div class="text-center py-8 text-grey">
              <v-icon size="48" class="mb-4">mdi-inbox-outline</v-icon>
              <p>No roles found</p>
            </div>
          </template>
        </v-data-table>
      </v-card>
    </div>

    <!-- Role Detail Dialog -->
    <v-dialog
      v-model="detailDialogOpen"
      max-width="600px"
    >
      <v-card v-if="selectedRole">
        <v-card-title>{{ selectedRole.name }}</v-card-title>

        <v-card-text>
          <!-- Description -->
          <div v-if="selectedRole.description" class="mb-6">
            <h3 class="text-subtitle2 font-weight-bold mb-2">Description</h3>
            <p class="text-body2">{{ selectedRole.description }}</p>
          </div>

          <v-divider class="my-4" />

          <!-- Required Skills -->
          <div class="mb-6">
            <h3 class="text-subtitle2 font-weight-bold mb-3">Required Skills</h3>
            <v-table v-if="selectedRoleSkills.length > 0">
              <thead>
                <tr>
                  <th>Skill</th>
                  <th>Required Level</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="skill in selectedRoleSkills" :key="skill.id">
                  <td>{{ skill.name }}</td>
                  <td>
                    <v-chip :color="getSkillLevelColor(skill.level)" size="small">
                      Level {{ skill.level }}
                    </v-chip>
                  </td>
                </tr>
              </tbody>
            </v-table>
            <div v-else class="text-grey text-body2">
              No required skills defined
            </div>
          </div>

          <v-divider class="my-4" />

          <!-- Employees in Role -->
          <div>
            <h3 class="text-subtitle2 font-weight-bold mb-3">
              Employees ({{ selectedRolePeople.length }})
            </h3>
            <v-list v-if="selectedRolePeople.length > 0" class="bg-transparent">
              <v-list-item
                v-for="person in selectedRolePeople"
                :key="person.id"
                :title="person.name"
                :subtitle="person.email"
              >
                <template #prepend>
                  <v-avatar color="primary" text="small">
                    {{ person.name.charAt(0) }}
                  </v-avatar>
                </template>
              </v-list-item>
            </v-list>
            <div v-else class="text-grey text-body2">
              No employees in this role
            </div>
          </div>
        </v-card-text>

        <v-card-actions>
          <v-spacer />
          <v-btn
            color="secondary"
            variant="text"
            @click="detailDialogOpen = false"
          >
            Close
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

interface Role {
  id: number
  name: string
  description?: string
  skills_count?: number
  people_count?: number
}

interface RoleSkill {
  id: number
  name: string
  level: number
}

interface Person {
  id: number
  name: string
  email: string
}

const roles = ref<Role[]>([])
const loading = ref(false)
const search = ref('')
const detailDialogOpen = ref(false)
const selectedRole = ref<Role | null>(null)
const selectedRoleSkills = ref<RoleSkill[]>([])
const selectedRolePeople = ref<Person[]>([])

const headers = [
  { title: 'Role Name', key: 'name', width: '300px' },
  { title: 'Employees', key: 'employees_count', width: '150px' },
  { title: 'Skills Required', key: 'skills_count', width: '150px' },
  { title: 'Actions', key: 'actions', sortable: false },
]

const filteredRoles = computed(() => {
  if (!search.value) return roles.value
  return roles.value.filter(r =>
    r.name.toLowerCase().includes(search.value.toLowerCase())
  )
})

const fetchRoles = async () => {
  loading.value = true
  try {
    const response = await axios.get('/api/roles')
    roles.value = response.data.data || response.data
  } catch (err) {
    console.error('Failed to load roles', err)
  } finally {
    loading.value = false
  }
}

const fetchRoleDetails = async (roleId: number) => {
  try {
    // Fetch skills
    const skillsResponse = await axios.get(`/api/roles/${roleId}/skills`)
    selectedRoleSkills.value = skillsResponse.data.data || skillsResponse.data

    // Fetch people in role
    const peopleResponse = await axios.get(`/api/roles/${roleId}/people`)
    selectedRolePeople.value = peopleResponse.data.data || peopleResponse.data
  } catch (err) {
    console.error('Failed to load role details', err)
  }
}

const viewRole = async (role: Role) => {
  selectedRole.value = role
  await fetchRoleDetails(role.id)
  detailDialogOpen.value = true
}

const getSkillLevelColor = (level: number) => {
  if (level <= 1) return 'grey'
  if (level <= 2) return 'orange'
  if (level <= 3) return 'blue'
  if (level <= 4) return 'green'
  return 'purple'
}

onMounted(() => {
  fetchRoles()
})
</script>

<style scoped>
:deep(.v-data-table) {
  background: transparent;
}
</style>
