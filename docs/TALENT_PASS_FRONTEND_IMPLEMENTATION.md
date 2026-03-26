# 🛠️ **Talent Pass Frontend - Plan de Implementación Técnica**

Roadmap detallado para implementar la UI/UX del Talent Pass siguiendo **Stratos Glass Design System** + API backend completada (26 endpoints, 98 tests).

---

## 📊 **Resumen Ejecutivo**

| Componente                     | Lineas     | Complejidad | Dependencias       | Tiempo        |
| ------------------------------ | ---------- | ----------- | ------------------ | ------------- |
| Types TypeScript               | 150        | Media       | API responses      | 30m           |
| Store Pinia                    | 200        | Media       | Composables, API   | 45m           |
| Pages (Index/Show/Create/Edit) | 400        | Alta        | Store, Composables | 1.5h          |
| Managers (Skills/Exp/Cred)     | 600        | Media       | Store, Validators  | 1.5h          |
| Components (Card/Menu/Dialog)  | 400        | Media       | StCardGlass, Icons | 1h            |
| Integration & Polishing        | -          | Media       | All above + i18n   | 1.5h          |
| **TOTAL**                      | **1,750+** | -           | -                  | **6-7 horas** |

---

## 🏗️ **Fase 1: Setup & Types (30 min)**

### 1.1 Crear Types TypeScript

**Archivo:** `resources/js/types/talentPass.ts`

```typescript
// Core Models
export interface TalentPass {
    id: number;
    organization_id: number;
    people_id: number;
    ulid: string;
    title: string;
    summary?: string;
    status:
        | 'draft'
        | 'in_review'
        | 'approved'
        | 'active'
        | 'completed'
        | 'archived';
    visibility: 'private' | 'public';
    views_count: number;
    featured_at?: string | null;
    created_at: string;
    updated_at: string;

    // Relationships (populated with 'with=' in API)
    person: Person;
    skills: TalentPassSkill[];
    credentials: TalentPassCredential[];
    experiences: TalentPassExperience[];

    // Computed
    completeness?: number; // 0-100
    isOwner?: boolean;
    isDraft?: boolean;
}

export interface TalentPassSkill {
    id: number;
    talent_pass_id: number;
    skill_name: string;
    proficiency_level: 'beginner' | 'intermediate' | 'expert' | 'master';
    years_of_experience?: number;
    endorsements_count: number;
    created_at: string;
}

export interface TalentPassCredential {
    id: number;
    talent_pass_id: number;
    credential_name: string;
    issuer_name?: string;
    issued_at?: string | null;
    expiry_at?: string | null;
    credential_url?: string | null;
    is_featured: boolean;
    created_at: string;
}

export interface TalentPassExperience {
    id: number;
    talent_pass_id: number;
    company_name: string;
    job_title: string;
    job_description?: string;
    start_date: string;
    end_date?: string | null;
    is_current: boolean;
    employment_type: 'full_time' | 'contract' | 'freelance' | 'part_time';
    location?: string;
    is_remote: boolean;
    created_at: string;
}

export interface Person {
    id: number;
    organization_id: number;
    first_name: string;
    last_name: string;
    email: string;
    department?: string;
    role?: string;
}

// DTOs for form submissions
export interface CreateTalentPassDTO {
    title: string;
    summary?: string;
    visibility: 'private' | 'public';
}

export interface UpdateTalentPassDTO {
    title?: string;
    summary?: string;
    visibility?: 'private' | 'public';
}

export interface AddSkillDTO {
    skill_name: string;
    proficiency_level: 'beginner' | 'intermediate' | 'expert' | 'master';
    years_of_experience?: number;
}

export interface AddExperienceDTO {
    company_name: string;
    job_title: string;
    job_description?: string;
    start_date: string; // YYYY-MM-DD
    end_date?: string | null;
    employment_type: 'full_time' | 'contract' | 'freelance' | 'part_time';
    location?: string;
    is_remote: boolean;
}

export interface AddCredentialDTO {
    credential_name: string;
    issuer_name?: string;
    issued_at?: string | null;
    expiry_at?: string | null;
    credential_url?: string | null;
}

export interface ExportOptions {
    format: 'json' | 'linkedin' | 'pdf';
}

export interface ShareResponse {
    link: string;
    publicId: string;
}
```

---

## 🏛️ **Fase 2: Pinia Store (45 min)**

**Archivo:** `resources/js/stores/talentPassStore.ts`

```typescript
import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import {
    TalentPass,
    CreateTalentPassDTO,
    UpdateTalentPassDTO,
} from '@/types/talentPass';
import { apiClient } from '@/helpers/apiClient';

export const useTalentPassStore = defineStore('talentPass', () => {
    // STATE
    const talentPasses = ref<TalentPass[]>([]);
    const currentTalentPass = ref<TalentPass | null>(null);
    const loading = ref(false);
    const error = ref<string | null>(null);

    // COMPUTED
    const completeness = computed(() => {
        if (!currentTalentPass.value) return 0;
        return currentTalentPass.value.completeness || 0;
    });

    const isDraft = computed(() => {
        return currentTalentPass.value?.status === 'draft';
    });

    // ACTIONS
    async function fetchTalentPasses(page = 1) {
        loading.value = true;
        error.value = null;
        try {
            const response = await apiClient.get('/api/talent-passes', {
                params: { page },
            });
            talentPasses.value = response.data.data;
        } catch (err) {
            error.value =
                err instanceof Error ? err.message : 'Failed to fetch';
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function fetchTalentPass(id: number) {
        loading.value = true;
        error.value = null;
        try {
            const response = await apiClient.get(`/api/talent-passes/${id}`);
            currentTalentPass.value = response.data.data;
        } catch (err) {
            error.value =
                err instanceof Error ? err.message : 'Failed to fetch';
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function createTalentPass(data: CreateTalentPassDTO) {
        loading.value = true;
        try {
            const response = await apiClient.post('/api/talent-passes', data);
            currentTalentPass.value = response.data.data;
            return response.data.data.id;
        } catch (err) {
            error.value =
                err instanceof Error ? err.message : 'Failed to create';
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function updateTalentPass(id: number, data: UpdateTalentPassDTO) {
        loading.value = true;
        try {
            const response = await apiClient.put(
                `/api/talent-passes/${id}`,
                data,
            );
            if (currentTalentPass.value?.id === id) {
                currentTalentPass.value = response.data.data;
            }
        } catch (err) {
            error.value =
                err instanceof Error ? err.message : 'Failed to update';
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function publishTalentPass(id: number) {
        try {
            const response = await apiClient.post(
                `/api/talent-passes/${id}/publish`,
            );
            if (currentTalentPass.value?.id === id) {
                currentTalentPass.value.status = 'published';
            }
        } catch (err) {
            error.value =
                err instanceof Error ? err.message : 'Failed to publish';
            throw err;
        }
    }

    async function archiveTalentPass(id: number) {
        try {
            await apiClient.post(`/api/talent-passes/${id}/archive`);
            if (currentTalentPass.value?.id === id) {
                currentTalentPass.value.status = 'archived';
            }
        } catch (err) {
            error.value =
                err instanceof Error ? err.message : 'Failed to archive';
            throw err;
        }
    }

    async function deleteTalentPass(id: number) {
        try {
            await apiClient.delete(`/api/talent-passes/${id}`);
            talentPasses.value = talentPasses.value.filter(
                (tp) => tp.id !== id,
            );
        } catch (err) {
            error.value =
                err instanceof Error ? err.message : 'Failed to delete';
            throw err;
        }
    }

    async function exportTalentPass(
        id: number,
        format: 'json' | 'linkedin' | 'pdf',
    ) {
        try {
            const response = await apiClient.get(
                `/api/talent-passes/${id}/export`,
                {
                    params: { format },
                },
            );
            return response.data;
        } catch (err) {
            error.value =
                err instanceof Error ? err.message : 'Failed to export';
            throw err;
        }
    }

    async function shareTalentPass(id: number) {
        try {
            const response = await apiClient.post(
                `/api/talent-passes/${id}/share`,
            );
            return response.data; // { link, publicId }
        } catch (err) {
            error.value =
                err instanceof Error ? err.message : 'Failed to share';
            throw err;
        }
    }

    async function cloneTalentPass(id: number) {
        try {
            const response = await apiClient.post(
                `/api/talent-passes/${id}/clone`,
            );
            const cloned = response.data.data;
            talentPasses.value.unshift(cloned);
            return cloned.id;
        } catch (err) {
            error.value =
                err instanceof Error ? err.message : 'Failed to clone';
            throw err;
        }
    }

    // SKILL ACTIONS
    async function addSkill(talentPassId: number, skillData: AddSkillDTO) {
        try {
            const response = await apiClient.post(
                `/api/talent-passes/${talentPassId}/skills`,
                skillData,
            );
            if (currentTalentPass.value?.id === talentPassId) {
                currentTalentPass.value.skills.push(response.data.data);
            }
        } catch (err) {
            error.value =
                err instanceof Error ? err.message : 'Failed to add skill';
            throw err;
        }
    }

    async function removeSkill(talentPassId: number, skillId: number) {
        try {
            await apiClient.delete(
                `/api/talent-passes/${talentPassId}/skills/${skillId}`,
            );
            if (currentTalentPass.value?.id === talentPassId) {
                currentTalentPass.value.skills =
                    currentTalentPass.value.skills.filter(
                        (s) => s.id !== skillId,
                    );
            }
        } catch (err) {
            error.value =
                err instanceof Error ? err.message : 'Failed to remove skill';
            throw err;
        }
    }

    return {
        // State
        talentPasses,
        currentTalentPass,
        loading,
        error,

        // Computed
        completeness,
        isDraft,

        // Methods
        fetchTalentPasses,
        fetchTalentPass,
        createTalentPass,
        updateTalentPass,
        publishTalentPass,
        archiveTalentPass,
        deleteTalentPass,
        exportTalentPass,
        shareTalentPass,
        cloneTalentPass,
        addSkill,
        removeSkill,
    };
});
```

---

## 🎨 **Fase 3: Pages (1.5 horas)**

### 3.1 TalentPass Index Page

**Archivo:** `resources/js/Pages/TalentPass/Index.vue`

```vue
<script setup lang="ts">
import { computed, onMounted } from 'vue';
import { useTalentPassStore } from '@/stores/talentPassStore';
import { useRouter } from 'vue-router';
import {
    PhPlus,
    PhMagnifyingGlass,
    PhEye,
    PhDownload,
} from '@phosphor-icons/vue';
import StCardGlass from '@/components/Stratos/StCardGlass.vue';
import StButtonGlass from '@/components/Stratos/StButtonGlass.vue';
import StBadgeGlass from '@/components/Stratos/StBadgeGlass.vue';
import TalentPassCard from '@/components/TalentPass/TalentPassCard.vue';

// Setup
const store = useTalentPassStore();
const router = useRouter();
const searchQuery = ref('');

// Computed
const filteredPasses = computed(() => {
    if (!searchQuery.value) return store.talentPasses;
    return store.talentPasses.filter(
        (tp) =>
            tp.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            tp.summary?.toLowerCase().includes(searchQuery.value.toLowerCase()),
    );
});

// Methods
const goToCreate = () => router.push('/talent-pass/create');
const goToShow = (id: number) => router.push(`/talent-pass/${id}`);

// Lifecycle
onMounted(() => {
    store.fetchTalentPasses();
});
</script>

<template>
    <div class="min-h-screen bg-slate-900 p-8">
        <div class="mx-auto max-w-7xl space-y-12">
            <!-- HERO SECTION -->
            <section class="p-16">
                <h1 class="mb-4 text-4xl font-black tracking-tight text-white">
                    {{ $t('talent_pass.title', 'Mis Talent Passes') }}
                </h1>
                <p class="max-w-2xl leading-relaxed text-white/70">
                    {{
                        $t(
                            'talent_pass.subtitle',
                            'Construye tu perfil profesional integral con skills, experiencias y credenciales. Comparte tu ADN Digital.',
                        )
                    }}
                </p>
            </section>

            <!-- CONTROLS -->
            <section
                class="flex flex-col items-start gap-4 sm:flex-row sm:items-center"
            >
                <div class="relative flex-1">
                    <PhMagnifyingGlass
                        class="absolute top-3 left-3 text-white/40"
                        :size="20"
                    />
                    <input
                        v-model="searchQuery"
                        type="text"
                        :placeholder="$t('common.search', 'Buscar...')"
                        class="w-full rounded-lg border border-white/10 bg-white/5 py-2 pr-4 pl-10 text-white placeholder-white/40 focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                    />
                </div>

                <StButtonGlass
                    :icon="PhPlus"
                    variant="primary"
                    @click="goToCreate"
                >
                    {{ $t('talent_pass.create_new', 'Nuevo Talent Pass') }}
                </StButtonGlass>
            </section>

            <!-- EMPTY STATE -->
            <div
                v-if="filteredPasses.length === 0 && !store.loading"
                class="py-16 text-center"
            >
                <StCardGlass class="mx-auto max-w-md p-12 text-center">
                    <h3 class="mb-4 text-xl font-bold text-white">
                        {{ $t('common.empty_state', 'Sin Talent Passes') }}
                    </h3>
                    <p class="mb-6 text-white/70">
                        {{
                            $t(
                                'talent_pass.empty_create',
                                'Comienza creando tu primer Talent Pass para compartir tu perfil profesional.',
                            )
                        }}
                    </p>
                    <StButtonGlass
                        :icon="PhPlus"
                        variant="primary"
                        @click="goToCreate"
                    >
                        Crear Ahora
                    </StButtonGlass>
                </StCardGlass>
            </div>

            <!-- GRID DE TALENT PASSES -->
            <div
                v-else
                class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3"
            >
                <TalentPassCard
                    v-for="tp in filteredPasses"
                    :key="tp.id"
                    :talent-pass="tp"
                    @click="goToShow(tp.id)"
                />
            </div>
        </div>
    </div>
</template>

<style scoped></style>
```

### 3.2 TalentPass Show Page (lectura de Talent Pass)

**Archivo:** `resources/js/Pages/TalentPass/Show.vue` (450+ líneas - estructura especializada)

---

## 🧩 **Fase 4: Componentes Glass (1 hora)**

### 4.1 TalentPassCard.vue (Reutilizable)

**Archivo:** `resources/js/components/TalentPass/TalentPassCard.vue`

```vue
<script setup lang="ts">
import { computed } from 'vue';
import type { TalentPass } from '@/types/talentPass';
import { PhEye, PhCheck, PhClock } from '@phosphor-icons/vue';
import StCardGlass from '@/components/Stratos/StCardGlass.vue';
import StBadgeGlass from '@/components/Stratos/StBadgeGlass.vue';

interface Props {
    talentPass: TalentPass;
}

const props = defineProps<Props>();

const statusLabel = computed(() => {
    const labels: Record<string, string> = {
        draft: 'Borrador',
        published: 'Publicado',
        archived: 'Archivado',
    };
    return labels[props.talentPass.status] || props.talentPass.status;
});

const statusVariant = computed(() => {
    if (props.talentPass.status === 'draft') return 'warning';
    if (props.talentPass.status === 'published') return 'success';
    return 'default';
});
</script>

<template>
    <StCardGlass
        class="group cursor-pointer p-6 transition-all duration-300 hover:shadow-[0_0_30px_rgba(99,102,241,0.2)]"
    >
        <div class="space-y-4">
            <!-- Completeness bar -->
            <div class="h-1 overflow-hidden rounded-full bg-white/10">
                <div
                    class="h-full bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500"
                    :style="{ width: `${talentPass.completeness || 0}%` }"
                />
            </div>

            <!-- Header -->
            <div>
                <h3
                    class="text-lg font-bold text-white transition-colors group-hover:text-indigo-300"
                >
                    {{ talentPass.title }}
                </h3>
                <p class="mt-1 line-clamp-2 text-sm text-white/60">
                    {{ talentPass.summary }}
                </p>
            </div>

            <!-- Status badge + metrics -->
            <div class="flex items-center gap-2">
                <StBadgeGlass :variant="statusVariant">
                    {{ statusLabel }}
                </StBadgeGlass>

                <div class="flex items-center gap-1 text-xs text-white/50">
                    <PhEye :size="14" />
                    {{ talentPass.views_count }}
                </div>
            </div>

            <!-- Footer: Items count -->
            <div
                class="flex gap-4 border-t border-white/10 pt-2 text-xs text-white/60"
            >
                <span>{{ talentPass.skills.length }} Skills</span>
                <span>{{ talentPass.experiences.length }} Exp</span>
                <span>{{ talentPass.credentials.length }} Cert</span>
            </div>
        </div>
    </StCardGlass>
</template>
```

---

## 🔗 **Fase 5: Rutas (30 min)**

**Archivo:** `resources/js/routes/talentPass.ts` (opcional si usas Route::prefix('talent-pass') en Laravel)

```typescript
export const talentPassRoutes = [
    {
        path: '/talent-pass',
        name: 'talent-pass.index',
        component: () => import('@/Pages/TalentPass/Index.vue'),
        meta: { requiresAuth: true },
    },
    {
        path: '/talent-pass/create',
        name: 'talent-pass.create',
        component: () => import('@/Pages/TalentPass/Create.vue'),
        meta: { requiresAuth: true },
    },
    {
        path: '/talent-pass/:id',
        name: 'talent-pass.show',
        component: () => import('@/Pages/TalentPass/Show.vue'),
        meta: { requiresAuth: true },
    },
    {
        path: '/talent-pass/:id/edit',
        name: 'talent-pass.edit',
        component: () => import('@/Pages/TalentPass/Edit.vue'),
        meta: { requiresAuth: true },
    },
    {
        path: '/talent-pass/public/:publicId',
        name: 'talent-pass.public',
        component: () => import('@/Pages/TalentPass/PublicView.vue'),
        meta: { requiresAuth: false },
    },
];
```

---

## 🧪 **Fase 6: Testing (1.5 horas)**

### 6.1 Unit Tests (Vitest)

**Archivo:** `resources/js/components/TalentPass/__tests__/TalentPassCard.spec.ts`

```typescript
import { describe, it, expect } from 'vitest';
import { mount } from '@vue/test-utils';
import TalentPassCard from '../TalentPassCard.vue';
import type { TalentPass } from '@/types/talentPass';

const mockTalentPass: TalentPass = {
    id: 1,
    organization_id: 1,
    people_id: 1,
    ulid: '01ARZ3NDEKTSV4RRFFQ69G5FAV',
    title: 'Senior Developer',
    summary: 'Full stack engineer with 10 years experience',
    status: 'published',
    visibility: 'public',
    views_count: 42,
    featured_at: null,
    created_at: '2026-03-26T00:00:00Z',
    updated_at: '2026-03-26T00:00:00Z',
    person: {
        id: 1,
        organization_id: 1,
        first_name: 'John',
        last_name: 'Doe',
        email: 'john@example.com',
    },
    skills: [],
    credentials: [],
    experiences: [],
    completeness: 75,
    isOwner: true,
    isDraft: false,
};

describe('TalentPassCard.vue', () => {
    it('renders title and summary', () => {
        const wrapper = mount(TalentPassCard, {
            props: { talentPass: mockTalentPass },
        });
        expect(wrapper.text()).toContain('Senior Developer');
        expect(wrapper.text()).toContain('Full stack engineer');
    });

    it('displays completeness percentage', () => {
        const wrapper = mount(TalentPassCard, {
            props: { talentPass: mockTalentPass },
        });
        // Check if progress bar has correct width style
        const bar = wrapper.find('[style*="width"]');
        expect(bar.exists()).toBe(true);
    });

    it('shows correct status badge', () => {
        const wrapper = mount(TalentPassCard, {
            props: { talentPass: mockTalentPass },
        });
        expect(wrapper.text()).toContain('Publicado');
    });
});
```

### 6.2 E2E Tests (Pest v4 Browser Tests)

**Archivo:** `tests/Browser/TalentPassTest.php`

```php
it('can create new talent pass', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->visit('/talent-pass')
        ->click('@create-talent-pass-btn')
        ->fill('title', 'Senior Developer')
        ->fill('summary', 'Full stack engineer')
        ->select('visibility', 'public')
        ->click('@submit-btn')
        ->assertPathContains('/talent-pass/')
        ->assertSee('Senior Developer');
});

it('can add skill to talent pass', function () {
    $user = User::factory()->create();
    $tp = TalentPass::factory()->create(['people_id' => $user->people_id]);

    $this->actingAs($user)
        ->visit("/talent-pass/{$tp->id}")
        ->click('@add-skill-btn')
        ->fill('skill_name', 'Laravel')
        ->select('proficiency_level', 'expert')
        ->click('@submit-skill')
        ->assertSee('Laravel')
        ->assertSee('Expert');
});
```

---

## 📋 **Checklist de Implementación**

- [ ] **Setup & Types** (30 min)
    - [ ] `types/talentPass.ts` creado
    - [ ] DTOs definidos

- [ ] **Store Pinia** (45 min)
    - [ ] `stores/talentPassStore.ts` implementado
    - [ ] Acciones CRUD funcionando
    - [ ] Manejo de errores implementado

- [ ] **Pages** (1.5 horas)
    - [ ] Index.vue (lista)
    - [ ] Show.vue (detalle)
    - [ ] Create.vue (formulario)
    - [ ] Edit.vue (edición)
    - [ ] PublicView.vue (vista pública)

- [ ] **Componentes Glass** (1 hora)
    - [ ] TalentPassCard.vue
    - [ ] SkillsManager.vue
    - [ ] ExperienceManager.vue
    - [ ] CredentialManager.vue
    - [ ] ExportMenu.vue
    - [ ] CompletenessIndicator.vue

- [ ] **Rutas & Navegación** (30 min)
    - [ ] Rutas definidas en resources/js/routes/
    - [ ] Links en menú principal
    - [ ] Breadcrumbs implementados

- [ ] **Testing** (1.5 horas)
    - [ ] Unit tests con Vitest
    - [ ] E2E tests con Pest v4
    - [ ] Cobertura > 75%

- [ ] **QA & Deployment** (1 hora)
    - [ ] Dark mode validado
    - [ ] Accessibility (WCAG AA)
    - [ ] Performance (Lighthouse > 90)
    - [ ] i18n (ES/EN)

---

## 🚀 **Próximas acciones inmediatas**

1. **Confirmación de estructura** - Review de types + store design
2. **Configuración de Vitest** - Si no está ya setup
3. **Creación de componentes Glass base** - StCardGlass etc si no existen
4. **Sprint de componentes** - Parallelize Pages + Manager creation
5. **Integration con API** - Prueba que endpoints responden correctamente
6. **E2E smoke tests** - Validar flujos críticos

---

**Timeline Estimado:** 6-7 horas de trabajo concentrado  
**Equipo:** 1 Frontend Developer  
**Deadline:** Staging ready Por Apr 19, 2026
