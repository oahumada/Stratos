<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import axios from 'axios';
import { onMounted, ref } from 'vue';

// Components
import StCardGlass from '@/components/StCardGlass.vue';
import StButtonGlass from '@/components/StButtonGlass.vue';
import StBadgeGlass from '@/components/StBadgeGlass.vue';
import DevelopmentTab from '@/components/Talent/DevelopmentTab.vue';

// Icons
import {
    PhBuildings,
    PhEnvelope,
    PhCalendar,
    PhStar,
    PhChartPolar,
    PhGraduationCap,
    PhRocketLaunch,
    PhBrain,
    PhEyeSlash,
    PhPlus,
    PhArrowsClockwise,
    PhWarningCircle,
    //PhAtom,
    PhShieldCheck,
    PhArrowLeft
} from '@phosphor-icons/vue';

const props = defineProps<{
    id: string | number;
}>();

const loading = ref(true);
const personData = ref<any>(null);
const activeTab = ref('profile');

const fetchProfile = async () => {
    try {
        loading.value = true;
        const response = await axios.get(`/api/people/profile/${props.id}`);
        personData.value = response.data.data;
    } catch (error) {
        console.error('Error fetching profile:', error);
    } finally {
        loading.value = false;
    }
};

const formatDate = (dateString: string) => {
    if (!dateString) return 'N/A';
    return new Date(dateString).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};

const getStatusVariant = (status: string): any => {
    const variants: Record<string, string> = {
        'ready': 'success',
        'potencial': 'primary',
        'Gap significativo': 'warning',
        'no recomendado': 'error',
    };
    return variants[status] || 'glass';
};

onMounted(() => {
    fetchProfile();
});

defineOptions({ layout: AppLayout });
</script>

<template>
    <div class="people-profile-container relative min-h-screen bg-[#020617] p-8!">
        <!-- Background Decoration -->
        <div class="pointer-events-none fixed inset-0 z-0 overflow-hidden text-indigo-500">
            <div class="absolute -top-[10%] -left-[10%] h-[40%] w-[40%] rounded-full bg-current opacity-5 blur-[120px]"></div>
            <div class="absolute -bottom-[10%] right-[10%] h-[35%] w-[35%] rounded-full bg-fuchsia-500/5 blur-[120px]"></div>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/50 backdrop-blur-sm">
            <div class="h-12 w-12 animate-spin rounded-full border-4 border-indigo-500 border-t-transparent"></div>
        </div>

        <div v-else-if="personData" class="relative z-10 mx-auto max-w-7xl animate-in fade-in slide-in-from-bottom-4 duration-700">
            <!-- Navigation Back -->
            <div class="mb-6">
                <StButtonGlass variant="ghost" size="sm" @click="$inertia.visit('/people')" class="group rounded-xl">
                    <PhArrowLeft :size="16" class="transition-transform group-hover:-translate-x-1" />
                    Regresar a People
                </StButtonGlass>
            </div>

            <!-- Header Section: Premium Glass 2.0 -->
            <StCardGlass class="relative mb-8 overflow-hidden p-8!">
                <!-- Indicator Light (Top) -->
                <div class="absolute top-0 left-0 h-px w-full bg-linear-to-r from-transparent via-indigo-500 to-transparent shadow-[0_0_20px_rgba(99,102,241,0.6)]"></div>

                <div class="flex flex-col items-center gap-8 md:flex-row">
                    <!-- Avatar with Glow -->
                    <div class="relative shrink-0">
                        <div class="absolute inset-0 animate-pulse rounded-3xl bg-indigo-500/20 blur-xl"></div>
                        <div class="relative h-32 w-32 overflow-hidden rounded-3xl border-2 border-white/20 shadow-2xl">
                            <img 
                                :src="personData.person.photo_url || '/placeholder-avatar.png'" 
                                class="h-full w-full object-cover" 
                                :alt="personData.person.first_name + ' ' + personData.person.last_name" 
                            />
                        </div>
                    </div>

                    <!-- Profile Info -->
                    <div class="grow text-center md:text-left">
                        <div class="mb-2 flex flex-wrap items-center justify-center gap-3 md:justify-start">
                            <h1 class="text-4xl font-black tracking-tight text-white md:text-5xl">
                                {{ personData.person.first_name }} {{ personData.person.last_name }}
                            </h1>
                            <StBadgeGlass v-if="personData.person.is_high_potential" variant="warning" class="animate-pulse">
                                <PhStar :size="12" weight="fill" class="mr-1" />
                                HIGH POTENTIAL
                            </StBadgeGlass>
                        </div>
                        <p class="mb-4 text-xl font-light text-indigo-300">
                            {{ personData.person.role?.name || 'Cargo no asignado' }}
                        </p>
                        
                        <div class="flex flex-wrap justify-center gap-x-6 gap-y-2 text-sm text-white/50 md:justify-start">
                            <span class="flex items-center gap-2">
                                <PhBuildings :size="18" class="text-indigo-400" />
                                {{ personData.person.department?.name || 'N/A' }}
                            </span>
                            <span class="flex items-center gap-2">
                                <PhEnvelope :size="18" class="text-indigo-400" />
                                {{ personData.person.email }}
                            </span>
                            <span class="flex items-center gap-2">
                                <PhCalendar :size="18" class="text-indigo-400" />
                                Hired: {{ formatDate(personData.person.hire_date) }}
                            </span>
                        </div>
                    </div>

                    <!-- Match KPI Card -->
                    <div class="flex flex-col items-center justify-center rounded-3xl border border-white/10 bg-white/5 p-6 backdrop-blur-md">
                        <span class="text-[10px] font-black tracking-widest text-white/30 uppercase">Match Actual</span>
                        <span class="text-5xl font-black tracking-tighter text-white">
                            {{ personData.gap_analysis?.match_percentage || 0 }}%
                        </span>
                        <StBadgeGlass 
                            :variant="getStatusVariant(personData.gap_analysis?.summary.category)"
                            class="mt-2"
                        >
                            {{ personData.gap_analysis?.summary.category || 'Sin calificar' }}
                        </StBadgeGlass>
                    </div>
                </div>

                <!-- Tabs Navigation -->
                <div class="mt-10 flex flex-wrap justify-center gap-2 border-t border-white/5 pt-6 md:justify-start">
                    <button 
                        @click="activeTab = 'profile'"
                        :class="[
                            'flex items-center gap-2 rounded-xl px-6 py-3 text-sm font-bold tracking-tight transition-all duration-300',
                            activeTab === 'profile' ? 'bg-indigo-500/20 text-indigo-300 shadow-[0_0_15px_rgba(99,102,241,0.2)] ring-1 ring-indigo-500/30' : 'text-white/40 hover:bg-white/5 hover:text-white'
                        ]"
                    >
                        <PhBrain :size="20" />
                        Perfil Potencial
                    </button>
                    <button 
                        @click="activeTab = 'gaps'"
                        :class="[
                            'flex items-center gap-2 rounded-xl px-6 py-3 text-sm font-bold tracking-tight transition-all duration-300',
                            activeTab === 'gaps' ? 'bg-indigo-500/20 text-indigo-300 shadow-[0_0_15px_rgba(99,102,241,0.2)] ring-1 ring-indigo-500/30' : 'text-white/40 hover:bg-white/5 hover:text-white'
                        ]"
                    >
                        <PhChartPolar :size="20" />
                        Competencias & Gaps
                    </button>
                    <button 
                        @click="activeTab = 'learning'"
                        :class="[
                            'flex items-center gap-2 rounded-xl px-6 py-3 text-sm font-bold tracking-tight transition-all duration-300',
                            activeTab === 'learning' ? 'bg-indigo-500/20 text-indigo-300 shadow-[0_0_15px_rgba(99,102,241,0.2)] ring-1 ring-indigo-500/30' : 'text-white/40 hover:bg-white/5 hover:text-white'
                        ]"
                    >
                        <PhGraduationCap :size="20" />
                        Desarrollo
                    </button>
                    <button 
                        @click="activeTab = 'strategic'"
                        :class="[
                            'flex items-center gap-2 rounded-xl px-6 py-3 text-sm font-bold tracking-tight transition-all duration-300',
                            activeTab === 'strategic' ? 'bg-indigo-500/20 text-indigo-300 shadow-[0_0_15px_rgba(99,102,241,0.2)] ring-1 ring-indigo-500/30' : 'text-white/40 hover:bg-white/5 hover:text-white'
                        ]"
                    >
                        <PhRocketLaunch :size="20" />
                        Impacto Estratégico
                    </button>
                </div>
            </StCardGlass>

            <!-- Tab Content -->
            <div class="animate-in fade-in slide-in-from-top-4 duration-500">
                <!-- Potential Tab -->
                <div v-if="activeTab === 'profile'" class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                    <div class="lg:col-span-2">
                        <StCardGlass class="h-100 p-10!">
                            <div class="mb-8 flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-500/10 text-indigo-400">
                                        <PhBrain :size="28" weight="duotone" />
                                    </div>
                                    <h3 class="text-2xl font-black text-white">Análisis de Potencial IA</h3>
                                </div>
                                <StButtonGlass variant="ghost" size="sm" circle @click="fetchProfile">
                                    <PhArrowsClockwise :size="20" />
                                </StButtonGlass>
                            </div>

                            <div v-if="personData.person.psychometric_profiles?.length">
                                <div class="mb-8 rounded-2xl border border-indigo-500/10 bg-indigo-500/5 p-6 backdrop-blur-md">
                                    <h4 class="mb-2 text-xs font-black tracking-widest text-indigo-400 uppercase">Síntesis Cerbero AI</h4>
                                    <p class="text-sm leading-relaxed text-white/70">
                                        {{ personData.person.metadata?.summary_report || 'Análisis consolidado de rasgos y aptitudes basado en interacciones 360 y evaluaciones.' }}
                                    </p>
                                </div>

                                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                    <div v-for="trait in personData.person.psychometric_profiles" :key="trait.id" class="group rounded-2xl border border-white/5 bg-white/2 p-5 transition-all hover:bg-white/5">
                                        <div class="mb-3 flex items-center justify-between">
                                            <span class="text-xs font-bold text-white/60 uppercase">{{ trait.trait_name }}</span>
                                            <span class="text-lg font-black text-white">{{ (trait.score * 100).toFixed(0) }}%</span>
                                        </div>
                                        <div class="mb-3 h-2 w-full overflow-hidden rounded-full bg-white/5">
                                            <div class="h-full bg-linear-to-r from-indigo-500 to-indigo-400 transition-all duration-1000" :style="{ width: `${trait.score * 100}%` }"></div>
                                        </div>
                                        <p class="text-xs italic leading-relaxed text-white/40">"{{ trait.rationale }}"</p>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="flex flex-col items-center justify-center py-20 opacity-40">
                                <PhBrain :size="64" weight="thin" class="mb-4" />
                                <p class="text-sm">No hay perfil psicométrico disponible.</p>
                            </div>
                        </StCardGlass>
                    </div>

                    <!-- Sidebar content -->
                    <div class="space-y-8">
                        <!-- Blind Spots -->
                        <div v-if="personData.person.metadata?.blind_spots?.length" class="rounded-3xl border border-rose-500/20 bg-linear-to-br from-rose-500/5 to-transparent p-8! backdrop-blur-xl">
                            <h4 class="mb-6 flex items-center gap-3 text-lg font-bold text-rose-300">
                                <PhEyeSlash :size="24" weight="duotone" />
                                Puntos Ciegos
                            </h4>
                            <ul class="space-y-4">
                                <li v-for="(spot, i) in personData.person.metadata.blind_spots" :key="i" class="flex gap-3 text-sm leading-relaxed text-white/60">
                                    <PhWarningCircle :size="18" weight="fill" class="mt-0.5 shrink-0 text-rose-500/60" />
                                    {{ spot }}
                                </li>
                            </ul>
                        </div>

                        <!-- 360 Relationships -->
                        <StCardGlass class="p-8!">
                            <h4 class="mb-6 text-lg font-bold text-white">Red de Influencia 360</h4>
                            <div class="space-y-4">
                                <div v-for="rel in personData.person.relations" :key="rel.id" class="group flex items-center gap-4 rounded-2xl border border-white/5 bg-white/2 p-3 transition-all hover:bg-white/5">
                                    <div class="h-10 w-10 overflow-hidden rounded-xl border border-white/10">
                                        <img 
                                            :src="rel.related_person.photo_url || '/placeholder-avatar.png'" 
                                            class="h-full w-full object-cover" 
                                            :alt="rel.related_person.first_name + ' ' + rel.related_person.last_name" 
                                        />
                                    </div>
                                    <div class="grow">
                                        <div class="text-sm font-bold text-white">{{ rel.related_person.first_name }} {{ rel.related_person.last_name }}</div>
                                        <div class="text-[10px] font-black tracking-widest text-indigo-400 uppercase opacity-60">{{ rel.relationship_type }}</div>
                                    </div>
                                </div>
                                <StButtonGlass variant="ghost" size="sm" block class="mt-4">
                                    <PhPlus :size="14" class="mr-2" />
                                    Vincular Nodo
                                </StButtonGlass>
                            </div>
                        </StCardGlass>
                    </div>
                </div>

                <!-- Gaps Tab -->
                <div v-if="activeTab === 'gaps'" class="grid grid-cols-1 gap-8 lg:grid-cols-5">
                    <div class="lg:col-span-3">
                         <StCardGlass class="p-10!">
                            <div class="mb-10 flex items-center gap-4">
                                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-fuchsia-500/10 text-fuchsia-400">
                                    <PhChartPolar :size="28" weight="duotone" />
                                </div>
                                <h3 class="text-2xl font-black text-white">Matriz de Competencias</h3>
                            </div>

                            <div v-if="personData.competencies?.length" class="space-y-12">
                                <div v-for="comp in personData.competencies" :key="comp.id">
                                    <div class="mb-6 flex items-center justify-between border-b border-white/5 pb-2">
                                        <h4 class="text-lg font-bold text-indigo-300">{{ comp.name }}</h4>
                                        <span class="text-[10px] font-black tracking-widest text-white/20 uppercase">Core Competency</span>
                                    </div>

                                    <div class="space-y-6">
                                        <div v-for="skill in comp.skills" :key="skill.id" class="group py-2">
                                            <div class="mb-2 flex items-center justify-between">
                                                <div class="flex items-center gap-2">
                                                    <PhWarningCircle v-if="skill.is_critical" weight="fill" class="text-rose-500" :size="16" />
                                                    <span class="text-sm font-bold text-white/80 group-hover:text-white">{{ skill.name }}</span>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <span class="text-[10px] font-black text-white/30 uppercase">Nivel:</span>
                                                    <span class="text-xs font-black" :class="skill.current_level >= skill.required_level ? 'text-emerald-400' : 'text-amber-400'">
                                                        {{ skill.current_level }} / {{ skill.required_level }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="h-2 w-full overflow-hidden rounded-full bg-white/10">
                                                <div class="h-full transition-all duration-1000" 
                                                    :class="skill.current_level >= skill.required_level ? 'bg-emerald-500' : 'bg-amber-500'" 
                                                    :style="{ width: `${Math.min(100, (skill.current_level / skill.required_level) * 100)}%` }">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                         </StCardGlass>
                    </div>

                    <div class="lg:col-span-2 space-y-8">
                         <div class="flex flex-col items-center justify-center rounded-3xl border border-white/10 bg-white/5 p-10 text-center backdrop-blur-xl">
                            <h4 class="text-xs font-black tracking-widest text-white/30 uppercase mb-4">Eficiencia de Rol</h4>
                            <div class="relative flex items-center justify-center mb-6">
                                <svg class="h-32 w-32 -rotate-90">
                                    <circle class="text-white/5" stroke-width="10" stroke="currentColor" fill="transparent" r="56" cx="64" cy="64" />
                                    <circle class="text-indigo-500 transition-all duration-1000 shadow-[0_0_15px_rgba(99,102,241,0.5)]" stroke-width="10" stroke-dasharray="351" :stroke-dashoffset="351 - (351 * (personData.gap_analysis?.match_percentage || 0)) / 100" stroke-linecap="round" stroke="currentColor" fill="transparent" r="56" cx="64" cy="64" />
                                </svg>
                                <div class="absolute text-3xl font-black text-white">{{ personData.gap_analysis?.match_percentage || 0 }}%</div>
                            </div>
                            <p class="text-[10px] text-white/40 uppercase font-black tracking-widest">
                                {{ personData.gap_analysis?.summary.skills_ok || 0 }} DE {{ personData.gap_analysis?.summary.total_skills || 0 }} SKILLS CUMPLIDAS
                            </p>
                         </div>

                         <div class="rounded-3xl border border-indigo-500/20 bg-linear-to-br from-indigo-500/10 to-transparent p-8! backdrop-blur-xl">
                            <h4 class="text-[10px] font-black tracking-widest text-indigo-400 uppercase mb-4">Recomendación Estratégica</h4>
                            <p class="text-sm leading-relaxed text-white/70 italic">
                                "{{ personData.gap_analysis?.match_percentage > 85
                                    ? 'El colaborador demuestra excelencia operativa. Perfil listo para rotaciones críticas o mentoring de alto nivel.'
                                    : personData.gap_analysis?.match_percentage > 60
                                      ? 'Talento en desarrollo. Se recomienda focalizar la capacitación en las skills críticas marcadas en rojo.'
                                      : 'Priorizar programas intensivos de reskilling antes de considerar movimientos internos.'
                                }}"
                            </p>
                         </div>
                    </div>
                </div>

                <!-- Learning Tab -->
                <div v-if="activeTab === 'learning'">
                    <DevelopmentTab :person-id="personData.person.id" :skills="personData.person.skills" />
                </div>

                <!-- Strategic Tab -->
                <div v-if="activeTab === 'strategic'" class="grid grid-cols-1 gap-8 md:grid-cols-2">
                    <StCardGlass class="p-10!">
                        <div class="mb-8 flex items-center gap-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-fuchsia-500/10 text-fuchsia-400">
                                <PhAtoms :size="28" weight="duotone" />
                            </div>
                            <h3 class="text-2xl font-black text-white">Escenarios de Impacto</h3>
                        </div>
                        <div v-if="personData.scenarios?.length" class="space-y-4">
                            <div v-for="scn in personData.scenarios" :key="scn.id" class="rounded-2xl border border-fuchsia-500/20 bg-fuchsia-500/5 p-5">
                                <div class="mb-2 flex items-center justify-between">
                                    <div class="font-black text-fuchsia-300">{{ scn.name }}</div>
                                    <StBadgeGlass variant="primary" size="sm">{{ scn.status }}</StBadgeGlass>
                                </div>
                                <div class="text-[10px] font-black tracking-widest text-white/30 uppercase mb-3">Impacto en rol: <strong class="text-white">{{ scn.impact_level }}</strong></div>
                                <div class="h-1 w-full bg-white/10 rounded-full overflow-hidden">
                                     <div class="h-full bg-fuchsia-400" style="width: 100%"></div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="flex flex-col items-center justify-center py-16 opacity-30">
                            <PhAtoms :size="64" weight="thin" class="mb-4" />
                            <p class="text-sm">Sin participación en escenarios activos.</p>
                        </div>
                    </StCardGlass>

                    <StCardGlass class="p-10!">
                        <div class="mb-8 flex items-center gap-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-500/10 text-emerald-400">
                                <PhShieldCheck :size="28" weight="duotone" />
                            </div>
                            <h3 class="text-2xl font-black text-white">Plan de Sucesión</h3>
                        </div>
                        <div class="flex flex-col items-center justify-center rounded-3xl border border-white/5 bg-white/2 py-20 text-center">
                            <PhShieldCheck :size="64" weight="thin" class="mb-4 text-white/10" />
                            <div class="text-lg font-bold text-white/60 mb-2">Estado de Respaldo</div>
                            <p class="max-w-xs text-sm text-white/30 font-light">Actualmente no está listado como sucesor primario en posiciones críticas.</p>
                            <StButtonGlass variant="ghost" size="sm" class="mt-8 rounded-xl font-black uppercase tracking-widest text-[10px]">Vincular a Plan Estratégico</StButtonGlass>
                        </div>
                    </StCardGlass>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.people-profile-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
    min-height: 100vh;
}

.profile-header {
    border-radius: 16px;
    background: white;
}

.header-gradient {
    background: linear-gradient(135deg, #1867c0 0%, #5cbbf6 100%);
    min-height: 200px;
}

.animate-fade-in {
    animation: fadeIn 0.6s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.gap-item {
    transition: transform 0.2s;
}

.gap-item:hover {
    transform: translateX(4px);
}

.no-wrap {
    white-space: nowrap;
}

.opacity-90 {
    opacity: 0.9;
}
.opacity-80 {
    opacity: 0.8;
}
.opacity-70 {
    opacity: 0.7;
}
</style>
