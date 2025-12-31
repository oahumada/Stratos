# Guía de Inicio - Día 6: Frontend Pages

**Objetivo:** Crear páginas Vue que consuman los 17 endpoints API completados en Días 3-5.

---

## 📋 Pre-requisitos

✅ Verificar que todo está funcionando:

```bash
cd /workspaces/talentia/src

# 1. API server funcionando
php artisan serve --port=8000

# 2. Verificar rutas
php artisan route:list | grep api

# 3. Verificar datos en BD
php artisan tinker
>>> App\Models\People::count()  # Debe retornar 20
>>> App\Models\Role::count()    # Debe retornar 8
>>> App\Models\Skill::count()   # Debe retornar 30
```

---

## 📁 Estructura de Carpetas para Día 6

```
resources/js/
├── pages/
│   ├── People/
│   │   ├── PeopleList.vue       ← NEW (GET /api/People)
│   │   ├── PeopleDetail.vue     ← NEW (GET /api/People/{id})
│   │   └── PeopleSkills.vue     ← Component
│   ├── roles/
│   │   ├── RolesList.vue        ← NEW (GET /api/roles)
│   │   └── RoleDetail.vue       ← NEW (GET /api/roles/{id})
│   ├── skills/
│   │   └── SkillsList.vue       ← NEW (GET /api/skills)
│   ├── gap-analysis/
│   │   └── GapAnalysis.vue      ← NEW (POST /api/gap-analysis)
│   ├── development-paths/
│   │   └── DevelopmentPaths.vue ← NEW (POST /api/development-paths/generate)
│   ├── job-openings/
│   │   ├── JobOpeningsList.vue  ← NEW (GET /api/job-openings)
│   │   └── JobOpeningDetail.vue ← NEW (GET /api/job-openings/{id})
│   ├── applications/
│   │   └── Applications.vue     ← NEW (GET /api/applications)
│   └── marketplace/
│       └── Marketplace.vue      ← NEW (GET /api/People/{id}/marketplace)
├── components/
│   ├── SkillsTable.vue          ← NEW (reusable)
│   ├── SkillsRadarChart.vue     ← NEW (chart component)
│   ├── GapAnalysisCard.vue      ← NEW (card component)
│   └── ... (others)
└── composables/
    └── useApi.ts               ← EXISTS (use for API calls)
```

---

## 🔌 API Integration Pattern

### Use Composable for API Calls

**Create or Update `resources/js/composables/useApi.ts`:**

```typescript
import { ref, Ref } from 'vue';
import axios from 'axios';

export function useApi() {
    const loading = ref(false);
    const error: Ref<string | null> = ref(null);

    const apiClient = axios.create({
        baseURL: '/api',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
    });

    // GET
    const get = async (endpoint: string) => {
        loading.value = true;
        error.value = null;
        try {
            const response = await apiClient.get(endpoint);
            return response.data;
        } catch (err: any) {
            error.value = err.response?.data?.message || 'Error fetching data';
            throw err;
        } finally {
            loading.value = false;
        }
    };

    // POST
    const post = async (endpoint: string, data: any) => {
        loading.value = true;
        error.value = null;
        try {
            const response = await apiClient.post(endpoint, data);
            return response.data;
        } catch (err: any) {
            error.value = err.response?.data?.message || 'Error posting data';
            throw err;
        } finally {
            loading.value = false;
        }
    };

    // PATCH
    const patch = async (endpoint: string, data: any) => {
        loading.value = true;
        error.value = null;
        try {
            const response = await apiClient.patch(endpoint, data);
            return response.data;
        } catch (err: any) {
            error.value = err.response?.data?.message || 'Error updating data';
            throw err;
        } finally {
            loading.value = false;
        }
    };

    return { get, post, patch, loading, error };
}
```

---

## 📄 Page Template Example

### People List Page

**`resources/js/pages/People/PeopleList.vue`:**

```vue
<template>
    <div>
        <h1 class="text-h4 mb-6">Peopleas</h1>

        <!-- Loading -->
        <v-progress-linear v-if="loading" indeterminate></v-progress-linear>

        <!-- Error -->
        <v-alert v-if="error" type="error" class="mb-4">{{ error }}</v-alert>

        <!-- Table -->
        <v-data-table :items="People" :headers="headers">
            <template #item.actions="{ item }">
                <v-btn small color="primary" :to="`/People/${item.id}`">
                    Ver Detalle
                </v-btn>
            </template>
        </v-data-table>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useApi } from '@/composables/useApi';

interface People {
    id: number;
    first_name: string;
    last_name: string;
    email: string;
    skills_count: number;
}

const { get, loading, error } = useApi();
const People = ref<People[]>([]);

const headers = [
    { title: 'Nombre', value: 'first_name' },
    { title: 'Email', value: 'email' },
    { title: 'Skills', value: 'skills_count' },
    { title: 'Acciones', value: 'actions', sortable: false },
];

onMounted(async () => {
    try {
        People.value = await get('/People');
    } catch (err) {
        console.error('Failed to load People:', err);
    }
});
</script>
```

---

## 🗺️ Router Configuration

Add routes to `resources/js/routes/` (or wherever your router is configured):

```typescript
// Example routes
{
  path: '/People',
  component: () => import('@/pages/People/PeopleList.vue'),
  meta: { title: 'Peopleas' }
},
{
  path: '/People/:id',
  component: () => import('@/pages/People/PeopleDetail.vue'),
  meta: { title: 'Detalle de Peoplea' }
},
{
  path: '/roles',
  component: () => import('@/pages/roles/RolesList.vue'),
  meta: { title: 'Roles' }
},
{
  path: '/gap-analysis',
  component: () => import('@/pages/gap-analysis/GapAnalysis.vue'),
  meta: { title: 'Análisis de Brecha' }
},
{
  path: '/marketplace',
  component: () => import('@/pages/marketplace/Marketplace.vue'),
  meta: { title: 'Marketplace de Oportunidades' }
},
// ... más rutas
```

---

## 🎨 Components to Create (for reusability)

### 1. SkillsTable.vue

```vue
<template>
    <v-data-table :items="skills" :headers="headers">
        <template #item.level="{ item }">
            <v-progress-linear
                :value="item.level * 20"
                :color="getLevelColor(item.level)"
            ></v-progress-linear>
        </template>
    </v-data-table>
</template>

<script setup lang="ts">
const props = defineProps<{
    skills: Array<{ id: number; name: string; level: number }>;
}>();

const headers = [
    { title: 'Competencia', value: 'name' },
    { title: 'Nivel', value: 'level' },
];

const getLevelColor = (level: number) => {
    if (level <= 1) return 'red';
    if (level <= 2) return 'orange';
    if (level <= 3) return 'yellow';
    if (level <= 4) return 'light-green';
    return 'green';
};
</script>
```

### 2. GapAnalysisCard.vue

```vue
<template>
    <v-card class="mb-4">
        <v-card-title>Análisis de Brecha</v-card-title>
        <v-card-text>
            <v-row>
                <v-col cols="6">
                    <div class="text-center">
                        <div
                            class="text-h3"
                            :class="getCategoryColor(gap.summary.category)"
                        >
                            {{ gap.match_percentage }}%
                        </div>
                        <p>Compatibilidad</p>
                    </div>
                </v-col>
                <v-col cols="6">
                    <p>
                        {{ gap.summary.skills_ok }} /
                        {{ gap.summary.total_skills }} habilidades
                    </p>
                    <p class="text-caption text-grey">
                        {{ gap.summary.category }}
                    </p>
                </v-col>
            </v-row>

            <!-- Gaps list -->
            <v-divider class="my-4"></v-divider>
            <div v-for="gap in gap.gaps" :key="gap.skill_id" class="mb-3">
                <div class="d-flex justify-space-between">
                    <span>{{ gap.skill_name }}</span>
                    <span :class="getGapColor(gap.status)">{{
                        gap.status
                    }}</span>
                </div>
                <v-progress-linear
                    :value="(gap.current_level / gap.required_level) * 100"
                    :color="getGapColor(gap.status)"
                ></v-progress-linear>
            </div>
        </v-card-text>
    </v-card>
</template>

<script setup lang="ts">
const props = defineProps<{
    gap: any;
}>();

const getCategoryColor = (category: string) => {
    const colors: Record<string, string> = {
        excellent: 'text-green',
        good: 'text-light-green',
        developing: 'text-orange',
        'not-recommended': 'text-red',
    };
    return colors[category] || 'text-grey';
};

const getGapColor = (status: string) => {
    const colors: Record<string, string> = {
        ok: 'text-green',
        developing: 'text-orange',
        critical: 'text-red',
    };
    return colors[status] || 'text-grey';
};
</script>
```

---

## 🚀 Quick Start Commands

```bash
# 1. Start API server
cd /workspaces/talentia/src && php artisan serve --port=8000

# 2. In another terminal, start Vite dev server
cd /workspaces/talentia/src && npm run dev

# 3. Open browser
http://localhost:5173

# 4. Test API with Postman (import TalentIA_API_Postman.json)
```

---

## 📚 API Endpoints Quick Reference

| Method | Endpoint                          | Purpose                |
| ------ | --------------------------------- | ---------------------- |
| GET    | `/api/People`                     | Lista de peopleas      |
| GET    | `/api/People/{id}`                | Detalle de peoplea     |
| GET    | `/api/roles`                      | Lista de roles         |
| GET    | `/api/roles/{id}`                 | Detalle de rol         |
| GET    | `/api/skills`                     | Lista de skills        |
| POST   | `/api/gap-analysis`               | Analizar brecha        |
| POST   | `/api/development-paths/generate` | Generar ruta           |
| GET    | `/api/job-openings`               | Vacantes               |
| POST   | `/api/applications`               | Crear postulación      |
| GET    | `/api/People/{id}/marketplace`    | Oportunidades internas |

---

## ✅ Checklist for Día 6

- [ ] Create PeopleList.vue with data-table
- [ ] Create PeopleDetail.vue with skills display
- [ ] Create RolesList.vue
- [ ] Create GapAnalysis.vue with form
- [ ] Create DevelopmentPaths.vue
- [ ] Create JobOpeningsList.vue
- [ ] Create Applications.vue
- [ ] Create Marketplace.vue
- [ ] Add routes to router config
- [ ] Verify all pages load data from API
- [ ] Test navigation between pages
- [ ] Ensure responsive design works

---

**Remember:** All backend endpoints are functional and tested. Focus on consuming them correctly with Vuetify components.

Good luck! 🚀
