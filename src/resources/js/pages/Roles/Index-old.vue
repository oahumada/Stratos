<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import FormSchema from '../form-template/FormSchema.vue';
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';

// Import JSON configs
import configJson from './roles-form/config.json';
import tableConfigJson from './roles-form/tableConfig.json';
import itemFormJson from './roles-form/itemForm.json';
import filtersJson from './roles-form/filters.json';

defineOptions({ layout: AppLayout });

interface FormField {
  key: string;
  label: string;
  type: 'text' | 'email' | 'number' | 'password' | 'select' | 'checkbox' | 'textarea' | 'date' | 'time' | 'switch';
  rules?: ((v: any) => boolean | string)[];
  placeholder?: string;
  items?: any[];
}

interface TableHeader {
  text: string;
  value: string;
  type?: 'date' | 'text' | 'number';
  sortable?: boolean;
  filterable?: boolean;
}

interface Config {
  endpoints: {
    index: string;
    apiUrl: string;
  };
  titulo: string;
  descripcion?: string;
  permisos?: {
    crear: boolean;
    editar: boolean;
    eliminar: boolean;
  };
}

interface TableConfig {
  headers: TableHeader[];
  options?: Record<string, any>;
}

interface ItemForm {
  fields: FormField[];
  catalogs?: string[];
  layout?: string;
}

interface FilterConfig {
  field: string;
  type: 'text' | 'select' | 'date';
  label: string;
  items?: any[];
  placeholder?: string;
}

// Load configs from JSON files
const config: Config = configJson as Config;
const tableConfig: TableConfig = tableConfigJson as TableConfig;
const itemForm: ItemForm = itemFormJson as ItemForm;
const filters: FilterConfig[] = filtersJson as FilterConfig[];

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
              Employees ({{ selectedRolePerson.length }})
            </h3>
            <v-list v-if="selectedRolePerson.length > 0" class="bg-transparent">
              <v-list-item
                v-for="person in selectedRolePerson"
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
  Person_count?: number
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
const selectedRolePerson = ref<Person[]>([])

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

    // Fetch Person in role
    const PersonResponse = await axios.get(`/api/roles/${roleId}/Person`)
    selectedRolePerson.value = PersonResponse.data.data || PersonResponse.data
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
