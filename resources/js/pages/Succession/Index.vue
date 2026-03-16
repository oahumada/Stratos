<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import { get, post } from '@/apiHelper';
import { type SuccessionCandidate, type SuccessionResponse } from '@/types/succession';
import { PhArrowLeft, PhArrowRight, PhChartLineUp, PhCheckCircle, PhGraduationCap, PhInfo, PhMagnifyingGlass, PhPlayCircle, PhShootingStar, PhSpinner, PhTimer, PhTrendUp, PhUsersThree, PhWarning } from '@phosphor-icons/vue';

import StCardGlass from '@/components/StCardGlass.vue';
import StBadgeGlass from '@/components/StBadgeGlass.vue';
import StButtonGlass from '@/components/StButtonGlass.vue';

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Talent Management', href: '/people' },
    { title: 'Plan de Sucesión', href: '/succession' },
];

const roles = ref<any[]>([]);
const selectedRoleId = ref<number | null>(null);
const loadingRoles = ref(false);
const loadingSuccessors = ref(false);
const candidates = ref<SuccessionCandidate[]>([]);
const activeAnalysis = ref<SuccessionCandidate | null>(null);
const savingPlan = ref(false);
const showSuccessAlert = ref(false);
const savedPlans = ref<any[]>([]);
const loadingPlans = ref(false);

const fetchRoles = async () => {
    loadingRoles.value = true;
    try {
        const response = await get('/api/catalogs', { 'endpoints[]': 'roles' });
        roles.value = response.roles || [];
    } catch (error) {
        console.error('Error fetching roles:', error);
    } finally {
        loadingRoles.value = false;
    }
};

const fetchSuccessors = async () => {
    if (!selectedRoleId.value) return;
    
    loadingSuccessors.value = true;
    try {
        const response: SuccessionResponse = await get(`/api/talent/succession/role/${selectedRoleId.value}`);
        candidates.value = response.candidates || [];
    } catch (error) {
        console.error('Error fetching successors:', error);
    } finally {
        loadingSuccessors.value = false;
    }
};

const fetchSavedPlans = async () => {
    loadingPlans.value = true;
    try {
        const response = await get('/api/talent/succession/plans');
        savedPlans.value = response.plans || [];
    } catch (error) {
        console.error('Error fetching saved plans:', error);
    } finally {
        loadingPlans.value = false;
    }
};

const navigateToCandidate = async (candidateId: number) => {
    // Para el "Efecto Dominó", analizamos al candidato para su rol actual (o el que estamos viendo)
    // Esto crea la cadena de sucesión visual
    try {
        const response = await post('/api/talent/succession/analyze-candidate', {
            person_id: candidateId,
            role_id: selectedRoleId.value
        });
        activeAnalysis.value = response.analysis;
    } catch (error) {
        console.error('Error navigating to candidate:', error);
    }
};

const getReadinessLevelBadge = (level: string) => {
    if (level.includes('A')) return 'primary';
    if (level.includes('Corto')) return 'secondary';
    if (level.includes('Desarrollo')) return 'glass';
    return 'glass';
};

const getMetricColor = (score: number) => {
    if (score >= 80) return 'text-emerald-400';
    if (score >= 60) return 'text-indigo-400';
    if (score >= 40) return 'text-amber-400';
    return 'text-rose-400';
};

const formatMovementType = (type: string) => {
    const map: Record<string, string> = {
        'promotion': 'Promoción',
        'lateral_move': 'Mov. Lateral',
        'transfer': 'Traslado',
        'hiring': 'Contratación',
        'exit': 'Egreso',
        'none': 'Sin registros'
    };
    return map[type] || type;
};

const formalizePlan = async () => {
    if (!activeAnalysis.value || !selectedRoleId.value) return;

    savingPlan.value = true;
    try {
        await post('/api/talent/succession/store-plan', {
            person_id: activeAnalysis.value.person.id,
            target_role_id: selectedRoleId.value,
            readiness_score: activeAnalysis.value.readiness_score,
            readiness_level: activeAnalysis.value.readiness_level,
            estimated_months: activeAnalysis.value.estimated_months,
            metrics: activeAnalysis.value.metrics,
            gaps: activeAnalysis.value.gaps,
            suggested_courses: activeAnalysis.value.recommended_courses
        });
        
        showSuccessAlert.value = true;
        fetchSavedPlans(); // Recargar lista
        setTimeout(() => {
            showSuccessAlert.value = false;
            activeAnalysis.value = null;
        }, 3000);
    } catch (error) {
        console.error('Error formalizing plan:', error);
    } finally {
        savingPlan.value = false;
    }
};

onMounted(() => {
    fetchRoles();
    fetchSavedPlans();
});
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Planificación de Sucesión Predictiva" />

        <div class="relative min-h-screen p-6 lg:p-10 text-white">
            <!-- Background Decorations -->
            <div class="pointer-events-none fixed inset-0 overflow-hidden">
                <div class="absolute -top-[10%] -left-[10%] h-[50%] w-[50%] rounded-full bg-indigo-500/5 blur-[120px]"></div>
                <div class="absolute bottom-[10%] right-[10%] h-[40%] w-[40%] rounded-full bg-blue-500/5 blur-[120px]"></div>
            </div>

            <div class="relative z-10 mx-auto max-w-7xl">
                <!-- Header Section -->
                <div class="mb-10 flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
                    <div class="space-y-2">
                        <div class="flex items-center gap-3">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl border border-indigo-500/20 bg-indigo-500/10 shadow-lg shadow-indigo-500/5">
                                <PhUsersThree :size="28" class="text-indigo-400" weight="duotone" />
                            </div>
                            <h1 class="text-3xl font-black tracking-tight text-white md:text-4xl">
                                Planificación de <span class="bg-linear-to-r from-indigo-400 to-blue-400 bg-clip-text text-transparent">Sucesión</span>
                            </h1>
                        </div>
                        <p class="max-w-2xl text-lg font-medium text-white/40">
                            Identifica proactivamente el relevo estratégico utilizando el motor de <span class="text-indigo-300">Trayectoria Inversa</span> y Velocidad de Aprendizaje.
                        </p>
                    </div>

                    <div class="flex flex-col gap-2 min-w-[300px]">
                        <label for="role-selector" class="text-[10px] font-black tracking-widest text-white/30 uppercase">Seleccionar Rol Objetivo</label>
                        <div class="relative">
                            <select 
                                id="role-selector"
                                v-model="selectedRoleId" 
                                @change="fetchSuccessors"
                                class="w-full appearance-none rounded-2xl border border-white/10 bg-white/5 p-4 pr-10 text-sm font-bold text-white backdrop-blur-md transition-all hover:border-white/20 focus:outline-none focus:ring-2 focus:ring-indigo-500/50"
                            >
                                <option :value="null" disabled>Elegir un rol para analizar...</option>
                                <option v-for="role in roles" :key="role.id" :value="role.id">{{ role.name }} (Lvl {{ role.level }})</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-4 flex items-center">
                                <PhArrowRight :size="16" class="rotate-90 text-white/30" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content Area -->
                <div v-if="loadingSuccessors" class="flex flex-col items-center justify-center py-40">
                    <div class="relative h-20 w-20">
                        <div class="absolute inset-0 rounded-full border-2 border-indigo-500/20"></div>
                        <div class="absolute inset-0 animate-spin rounded-full border-t-2 border-indigo-500"></div>
                        <PhSpinner :size="40" class="absolute inset-0 m-auto animate-pulse text-indigo-400" />
                    </div>
                    <p class="mt-6 text-sm font-black tracking-[0.2em] text-indigo-400/60 uppercase">Analizando ADN Organizacional...</p>
                </div>

                <div v-else-if="selectedRoleId && candidates.length > 0" class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
                    <div v-for="(candidate, index) in candidates" :key="candidate.person.id" class="group relative">
                        <!-- Rank Number -->
                        <div class="absolute -top-3 -left-3 z-20 flex h-10 w-10 items-center justify-center rounded-xl border border-white/10 bg-[#0f172a] font-black text-white shadow-xl">
                            #{{ index + 1 }}
                        </div>

                        <StCardGlass 
                            :indicator="candidate.person.is_high_potential ? 'amber' : 'indigo'" 
                            class="h-full overflow-hidden border-white/10 transition-all duration-500 hover:-translate-y-2 hover:border-indigo-500/40 hover:shadow-2xl hover:shadow-indigo-500/10 p-0"
                        >
                            <!-- Top Info -->
                            <div class="p-6 pb-0">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-center gap-4">
                                        <div class="relative">
                                            <v-avatar size="64" class="border-2 border-white/10 bg-indigo-500/5 ring-4 ring-indigo-500/5">
                                                <img v-if="candidate.person.photo_url" :src="candidate.person.photo_url" :alt="candidate.person.name" />
                                                <span v-else class="text-xl font-black text-indigo-300">{{ candidate.person.name.substring(0, 2).toUpperCase() }}</span>
                                            </v-avatar>
                                            <div v-if="candidate.person.is_high_potential" class="absolute -bottom-1 -right-1 flex h-6 w-6 items-center justify-center rounded-full bg-amber-400 text-slate-900 shadow-lg" title="High Potential Asset">
                                                <PhShootingStar :size="14" weight="fill" />
                                            </div>
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-black leading-tight text-white group-hover:text-indigo-300 transition-colors">{{ candidate.person.name }}</h3>
                                            <p class="text-xs font-bold text-white/40 uppercase tracking-wider">{{ candidate.person.current_role }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-6 flex items-center justify-between border-t border-white/5 pt-4">
                                    <div class="space-y-1">
                                        <p class="text-[9px] font-black tracking-widest text-white/20 uppercase">Readiness Score</p>
                                        <div class="flex items-baseline gap-1">
                                            <span class="text-3xl font-black text-white">{{ candidate.readiness_score }}</span>
                                            <span class="text-xs font-bold text-indigo-400">%</span>
                                        </div>
                                    </div>
                                    <StBadgeGlass :variant="getReadinessLevelBadge(candidate.readiness_level)" size="sm">
                                        {{ candidate.readiness_level }}
                                    </StBadgeGlass>
                                </div>

                                <!-- Mini Progress Bars -->
                                <div class="mt-6 grid grid-cols-2 gap-4">
                                    <div class="space-y-1.5">
                                        <div class="flex items-center justify-between px-1">
                                            <span class="text-[9px] font-bold text-white/30 uppercase tracking-tighter">Skill Fit</span>
                                            <span class="text-[9px] font-black text-white/60">{{ candidate.metrics.skill_match }}%</span>
                                        </div>
                                        <div class="h-1.5 w-full overflow-hidden rounded-full bg-white/5">
                                            <div class="h-full bg-emerald-500/60 shadow-[0_0_8px_rgba(16,185,129,0.3)] transition-all duration-1000" :style="{ width: `${candidate.metrics.skill_match}%` }"></div>
                                        </div>
                                    </div>
                                    <div class="space-y-1.5">
                                        <div class="flex items-center justify-between px-1">
                                            <span class="text-[9px] font-bold text-white/30 uppercase tracking-tighter">Velocity</span>
                                            <span class="text-[9px] font-black text-white/60">{{ candidate.metrics.learning_velocity }}%</span>
                                        </div>
                                        <div class="h-1.5 w-full overflow-hidden rounded-full bg-white/5">
                                            <div class="h-full bg-indigo-500/60 shadow-[0_0_8px_rgba(99,102,241,0.3)] transition-all duration-1000" :style="{ width: `${candidate.metrics.learning_velocity}%` }"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Bottom Metrics & Action -->
                            <div class="mt-6 bg-black/20 p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center gap-2">
                                        <PhTimer :size="18" class="text-amber-400" />
                                        <span class="text-xs font-bold text-white/60 uppercase">{{ candidate.estimated_months }} Meses para Readiness</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <PhTrendUp :size="18" class="text-blue-400" />
                                        <span class="text-xs font-bold text-white/60 uppercase">Fit {{ candidate.metrics.trajectory_fit }}%</span>
                                    </div>
                                </div>
                                <StButtonGlass 
                                    block 
                                    variant="glass" 
                                    size="sm" 
                                    class="border-white/5 bg-white/5 py-3 hover:bg-white/10"
                                    :icon="PhChartLineUp"
                                    @click="activeAnalysis = candidate"
                                >
                                    Ver Deep Dive Predictivo
                                </StButtonGlass>
                            </div>
                        </StCardGlass>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else-if="selectedRoleId" class="flex flex-col items-center justify-center rounded-4xl border-2 border-dashed border-white/5 bg-white/2 py-32 text-center backdrop-blur-sm">
                    <div class="mb-6 flex h-20 w-20 items-center justify-center rounded-3xl bg-white/5">
                        <PhWarning :size="40" class="text-white/20" />
                    </div>
                    <h3 class="text-2xl font-black text-white">Sin Candidatos Viables</h3>
                    <p class="mt-2 max-w-sm text-lg font-medium text-white/30">
                        El motor de inteligencia no ha identificado sucesores con un score mayor a 50% para este rol.
                    </p>
                    <StButtonGlass variant="primary" size="lg" class="mt-8" @click="selectedRoleId = null">
                        Probar con otro rol
                    </StButtonGlass>
                </div>

                <!-- Initial State -->
                <div v-else class="flex flex-col items-center justify-center rounded-4xl border-2 border-dashed border-white/5 bg-white/2 py-32 text-center backdrop-blur-sm">
                    <div class="mb-6 flex h-20 w-20 items-center justify-center rounded-3xl bg-indigo-500/10">
                        <PhMagnifyingGlass :size="40" class="text-indigo-400" weight="duotone" />
                    </div>
                    <h3 class="text-2xl font-black text-white">Análisis de Relevo</h3>
                    <p class="mt-2 max-w-sm text-lg font-medium text-white/30">
                        Selecciona un rol crítico para buscar sucesores potenciales en toda la organización.
                    </p>
                </div>
            </div>
        </div>

        <!-- Deep Dive Modal -->
        <v-dialog :model-value="!!activeAnalysis" fullscreen hide-overlay transition="dialog-bottom-transition" @update:model-value="activeAnalysis = null">
            <div v-if="activeAnalysis" class="fixed inset-0 z-100 overflow-y-auto bg-[#020617]/95 backdrop-blur-2xl text-white">
                <!-- Modal Header -->
                <div class="sticky top-0 z-10 border-b border-white/10 bg-slate-900/50 p-6 backdrop-blur-xl">
                    <div class="mx-auto flex max-w-7xl items-center justify-between">
                        <div class="flex items-center gap-6">
                            <StButtonGlass variant="ghost" circle @click="activeAnalysis = null">
                                <PhArrowLeft :size="24" />
                            </StButtonGlass>
                            <div>
                                <h2 class="text-2xl font-black text-white">Análisis Profundo de Sucesión</h2>
                                <p class="text-sm font-medium text-white/40">Reporte predictivo para <span class="text-white">{{ activeAnalysis.person.name }}</span></p>
                            </div>
                        </div>
                        <StButtonGlass variant="glass" @click="activeAnalysis = null">Cerrar Análisis</StButtonGlass>
                    </div>
                </div>

                <!-- Modal Content -->
                <div class="mx-auto max-w-7xl p-6 lg:p-12">
                    <div class="grid grid-cols-1 gap-10 lg:grid-cols-3">
                        <!-- Left: Profile & Overall Score -->
                        <div class="space-y-8">
                            <StCardGlass :indicator="activeAnalysis.person.is_high_potential ? 'amber' : 'indigo'" class="p-8">
                                <div class="flex flex-col items-center text-center">
                                    <div class="relative mb-6">
                                        <v-avatar size="128" class="border-4 border-white/10 ring-8 ring-indigo-500/10 shadow-2xl">
                                            <img v-if="activeAnalysis.person.photo_url" :src="activeAnalysis.person.photo_url" :alt="activeAnalysis.person.name" />
                                            <span v-else class="text-4xl font-black text-indigo-300">{{ activeAnalysis.person.name.substring(0, 2).toUpperCase() }}</span>
                                        </v-avatar>
                                        <div v-if="activeAnalysis.person.is_high_potential" class="absolute -bottom-2 -right-2 flex h-10 w-10 items-center justify-center rounded-2xl bg-amber-400 text-slate-900 shadow-xl ring-4 ring-[#0f172a]">
                                            <PhShootingStar :size="24" weight="fill" />
                                        </div>
                                    </div>
                                    <h3 class="text-2xl font-black text-white">{{ activeAnalysis.person.name }}</h3>
                                    <p class="text-sm font-bold text-white/40 uppercase tracking-widest">{{ activeAnalysis.person.current_role }}</p>
                                    
                                    <div class="mt-10 w-full space-y-2">
                                        <p class="text-[10px] font-black tracking-widest text-white/20 uppercase">Vector de Preparación Final</p>
                                        <div class="flex items-center justify-center gap-4">
                                            <span class="text-6xl font-black text-white tracking-tighter">{{ activeAnalysis.readiness_score }}%</span>
                                            <div class="h-12 w-px bg-white/10"></div>
                                            <div class="text-left">
                                                <StBadgeGlass :variant="getReadinessLevelBadge(activeAnalysis.readiness_level)" size="sm" class="mb-1">
                                                    {{ activeAnalysis.readiness_level }}
                                                </StBadgeGlass>
                                                <p class="text-xs font-bold text-amber-400">{{ activeAnalysis.estimated_months }} Meses est.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </StCardGlass>

                            <StCardGlass class="p-6">
                                <h4 class="mb-6 text-[11px] font-black tracking-widest text-white/30 uppercase">Resumen de Trayectoria Org.</h4>
                                <div class="space-y-6">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/5"><PhChartLineUp :size="20" class="text-blue-400" /></div>
                                            <span class="text-sm font-bold text-white/60">Movimientos totales</span>
                                        </div>
                                        <span class="text-xl font-black text-white">{{ activeAnalysis.trajectory_summary?.total_movements || 0 }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/5"><PhTrendUp :size="20" class="text-indigo-400" /></div>
                                            <span class="text-sm font-bold text-white/60">Último cambio</span>
                                        </div>
                                        <span class="text-sm font-black text-indigo-300">{{ formatMovementType(activeAnalysis.trajectory_summary?.last_movement || 'none') }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/5"><PhTimer :size="20" class="text-amber-400" /></div>
                                            <span class="text-sm font-bold text-white/60">Antigüedad</span>
                                        </div>
                                        <span class="text-xl font-black text-white">{{ activeAnalysis.trajectory_summary?.years_in_org || 0 }} Años</span>
                                    </div>
                                </div>
                            </StCardGlass>
                        </div>

                        <!-- Middle: Dimensions Analysis -->
                        <div class="lg:col-span-2 space-y-8">
                            <StCardGlass indicator="indigo" class="p-8">
                                <h4 class="mb-8 text-xl font-black text-white flex items-center gap-3">
                                    <PhChartLineUp class="text-indigo-400" />
                                    Análisis Dimensional de Potencial
                                </h4>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                                    <!-- Skill Fit -->
                                    <div class="space-y-4">
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm font-black text-white/80 uppercase tracking-widest">Skill Fit Técnico (40%)</span>
                                            <span :class="['text-2xl font-black', getMetricColor(activeAnalysis.metrics.skill_match)]">{{ activeAnalysis.metrics.skill_match }}%</span>
                                        </div>
                                        <p class="text-xs font-medium text-white/30 leading-relaxed">
                                            Nivel de compatibilidad actual entre las competencias del colaborador y los requerimientos críticos del rol objetivo.
                                        </p>
                                        <div class="h-2 w-full overflow-hidden rounded-full bg-white/5 p-0.5">
                                            <div class="h-full rounded-full bg-linear-to-r from-emerald-600 to-emerald-400 shadow-[0_0_15px_rgba(16,185,129,0.4)] transition-all duration-1000" :style="{ width: `${activeAnalysis.metrics.skill_match}%` }"></div>
                                        </div>
                                    </div>

                                    <!-- Learning Velocity -->
                                    <div class="space-y-4">
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm font-black text-white/80 uppercase tracking-widest">Velocidad de Aprendizaje (25%)</span>
                                            <span :class="['text-2xl font-black', getMetricColor(activeAnalysis.metrics.learning_velocity)]">{{ activeAnalysis.metrics.learning_velocity }}%</span>
                                        </div>
                                        <p class="text-xs font-medium text-white/30 leading-relaxed">
                                            Predicción de qué tan rápido el individuo puede adquirir las nuevas habilidades necesarias basada en su historial de cierre de gaps.
                                        </p>
                                        <div class="h-2 w-full overflow-hidden rounded-full bg-white/5 p-0.5">
                                            <div class="h-full rounded-full bg-linear-to-r from-indigo-600 to-indigo-400 shadow-[0_0_15px_rgba(99,102,241,0.4)] transition-all duration-1000" :style="{ width: `${activeAnalysis.metrics.learning_velocity}%` }"></div>
                                        </div>
                                    </div>

                                    <!-- Stability -->
                                    <div class="space-y-4">
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm font-black text-white/80 uppercase tracking-widest">Índice de Estabilidad (20%)</span>
                                            <span :class="['text-2xl font-black', getMetricColor(activeAnalysis.metrics.stability)]">{{ activeAnalysis.metrics.stability }}%</span>
                                        </div>
                                        <p class="text-xs font-medium text-white/30 leading-relaxed">
                                            Mide el riesgo de rotación. Una baja puntuación indica demasiados movimientos recientes (riesgo de inestabilidad).
                                        </p>
                                        <div class="h-2 w-full overflow-hidden rounded-full bg-white/5 p-0.5">
                                            <div class="h-full rounded-full bg-linear-to-r from-amber-600 to-amber-400 shadow-[0_0_15px_rgba(245,158,11,0.4)] transition-all duration-1000" :style="{ width: `${activeAnalysis.metrics.stability}%` }"></div>
                                        </div>
                                    </div>

                                    <!-- Trajectory -->
                                    <div class="space-y-4">
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm font-black text-white/80 uppercase tracking-widest">Alineación de Trayectoria (15%)</span>
                                            <span :class="['text-2xl font-black', getMetricColor(activeAnalysis.metrics.trajectory_fit)]">{{ activeAnalysis.metrics.trajectory_fit }}%</span>
                                        </div>
                                        <p class="text-xs font-medium text-white/30 leading-relaxed">
                                            Evalúa si el camino de carrera previo es un predictor lógico para el éxito en esta nueva posición.
                                        </p>
                                        <div class="h-2 w-full overflow-hidden rounded-full bg-white/5 p-0.5">
                                            <div class="h-full rounded-full bg-linear-to-r from-blue-600 to-blue-400 shadow-[0_0_15px_rgba(59,130,246,0.4)] transition-all duration-1000" :style="{ width: `${activeAnalysis.metrics.trajectory_fit}%` }"></div>
                                        </div>
                                    </div>

                                    <!-- Legacy Risk (New) -->
                                    <div class="space-y-4">
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm font-black text-white/80 uppercase tracking-widest">Riesgo de Legado (Org. Health)</span>
                                            <span :class="['text-2xl font-black', activeAnalysis.metrics.legacy_risk > 60 ? 'text-rose-400' : 'text-emerald-400']">{{ activeAnalysis.metrics.legacy_risk }}%</span>
                                        </div>
                                        <p class="text-xs font-medium text-white/30 leading-relaxed">
                                            Impacto negativo en el departamento actual si esta persona es promovida. Un valor alto sugiere desprotección del equipo de origen.
                                        </p>
                                        <div class="h-2 w-full overflow-hidden rounded-full bg-white/5 p-0.5">
                                            <div class="h-full rounded-full bg-linear-to-r transition-all duration-1000" :class="[activeAnalysis.metrics.legacy_risk > 60 ? 'from-rose-600 to-rose-400 shadow-[0_0_15px_rgba(244,63,94,0.4)]' : 'from-emerald-600 to-emerald-400 shadow-[0_0_15px_rgba(16,185,129,0.4)]']" :style="{ width: `${activeAnalysis.metrics.legacy_risk}%` }"></div>
                                        </div>
                                    </div>
                                </div>
                            </StCardGlass>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <!-- Gaps Section -->
                                <StCardGlass class="p-8">
                                    <h4 class="mb-6 text-lg font-black text-white flex items-center gap-3">
                                        <PhWarning class="text-amber-400" weight="bold" />
                                        Gaps Críticos a Mitigar
                                    </h4>
                                    <div v-if="activeAnalysis.gaps && activeAnalysis.gaps.length > 0" class="space-y-4">
                                        <div v-for="gap in activeAnalysis.gaps" :key="gap.skill_name" class="flex items-center justify-between rounded-xl border border-white/5 bg-white/5 p-4 transition-colors hover:bg-white/10">
                                            <div class="flex items-start gap-4">
                                                <div :class="['mt-1 h-3 w-3 rounded-full', gap.priority === 'high' ? 'bg-rose-500 shadow-[0_0_8px_rgba(244,63,94,0.5)]' : 'bg-amber-500 shadow-[0_0_8px_rgba(245,158,11,0.5)]']"></div>
                                                <div>
                                                    <p class="text-sm font-black text-white">{{ gap.skill_name }}</p>
                                                    <p class="text-[10px] font-bold text-white/30 uppercase">Faltan {{ gap.missing_level }} niveles</p>
                                                </div>
                                            </div>
                                            <StBadgeGlass :variant="gap.priority === 'high' ? 'secondary' : 'glass'" size="sm">
                                                Prioridad {{ gap.priority === 'high' ? 'ALTA' : (gap.priority === 'medium' ? 'MEDIA' : 'BAJA') }}
                                            </StBadgeGlass>
                                        </div>

                                        <!-- LMS Recommendations (New) -->
                                        <div v-if="activeAnalysis.recommended_courses?.length" class="mt-6 space-y-3">
                                            <div class="text-[10px] font-black uppercase tracking-widest text-indigo-400">Rutas de Cierre de Gaps (LMS)</div>
                                            <div v-for="rec in activeAnalysis.recommended_courses" :key="rec.course.id" class="flex items-center gap-3 rounded-lg border border-indigo-500/10 bg-indigo-500/5 p-3">
                                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-500/20 text-indigo-300">
                                                    <PhChartLineUp :size="18" />
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <div class="truncate text-[11px] font-black text-white/90">{{ rec.course.title }}</div>
                                                    <div class="text-[9px] font-bold text-white/30 truncate">Para: {{ rec.skill }} • {{ rec.course.provider }}</div>
                                                </div>
                                                <StButtonGlass size="sm" variant="glass" class="shrink-0">
                                                    Explorar
                                                </StButtonGlass>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-else class="flex flex-col items-center justify-center py-10 text-center text-white/40">
                                        <PhCheckCircle :size="48" class="text-emerald-500/30 mb-4" />
                                        <p class="text-sm font-bold">Sin gaps técnicos significativos.</p>
                                    </div>
                                </StCardGlass>

                                <!-- Recommended Actions -->
                                <StCardGlass indicator="indigo" class="p-8 border-indigo-500/20 bg-indigo-500/3">
                                    <h4 class="mb-6 text-lg font-black text-white flex items-center gap-3">
                                        <PhInfo class="text-indigo-400" weight="bold" />
                                        Plan de Acción Recomendado
                                    </h4>
                                    <div class="space-y-4">
                                        <div class="flex items-start gap-4">
                                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-indigo-500/20 text-indigo-300 font-black text-xs">01</div>
                                            <div>
                                                <p class="text-sm font-bold text-white">Inmersión Estratégica</p>
                                                <p class="text-[11px] text-white/50 leading-snug mt-1">Asignar al candidato a 3 comités del rol objetivo para ganar contexto de negocio.</p>
                                            </div>
                                        </div>
                                        <div class="flex items-start gap-4">
                                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-indigo-500/20 text-indigo-300 font-black text-xs">02</div>
                                            <div>
                                                <p class="text-sm font-bold text-white">Mentoring con Holder Actual</p>
                                                <p class="text-[11px] text-white/50 leading-snug mt-1">Transferencia de "Know-How" tácito mediante 2 sesiones mensuales.</p>
                                            </div>
                                        </div>
                                        <div v-if="activeAnalysis.recommended_courses?.length" class="space-y-3 mt-4">
                                            <div v-for="rec in activeAnalysis.recommended_courses" :key="rec.course.id" class="flex items-center gap-3 rounded-2xl border border-emerald-500/20 bg-emerald-500/5 p-4 group hover:bg-emerald-500/10 transition-all">
                                                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-500/20 text-emerald-400">
                                                    <PhPlayCircle :size="24" weight="fill" />
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-xs font-black text-white truncate">{{ rec.course.title }}</p>
                                                    <p class="text-[10px] text-emerald-300/60 font-bold uppercase tracking-widest mt-0.5">Focus: {{ rec.skill }} • {{ rec.course.provider }}</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div v-else class="flex items-start gap-4">
                                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-indigo-500/20 text-indigo-300 font-black text-xs">03</div>
                                            <div>
                                                <p class="text-sm font-bold text-white">Plan de Desarrollo Base</p>
                                                <p class="text-[11px] text-white/50 leading-snug mt-1">Se requiere un plan personalizado para cerrar las {{ activeAnalysis.gaps.length }} brechas detectadas.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <StButtonGlass 
                                        block 
                                        variant="primary" 
                                        class="mt-8 shadow-indigo-500/20"
                                        :loading="savingPlan"
                                        :disabled="savingPlan"
                                        @click="formalizePlan"
                                    >
                                        {{ savingPlan ? 'Formalizando...' : 'Formalizar Plan de Sucesión' }}
                                    </StButtonGlass>

                                    <!-- Success Alert -->
                                    <div v-if="showSuccessAlert" class="mt-4 flex items-center gap-2 rounded-xl border border-emerald-500/20 bg-emerald-500/5 p-4 text-xs font-bold text-emerald-300">
                                        <PhCheckCircle :size="20" weight="fill" class="shrink-0" />
                                        PLAN FORMALIZADO: Se ha guardado el plan estratégicamente.
                                    </div>

                                    <div v-if="activeAnalysis.metrics.legacy_risk > 70" class="mt-4 flex items-center gap-2 rounded-xl border border-rose-500/20 bg-rose-500/5 p-4 text-xs font-bold text-rose-300">
                                        <PhWarning :size="20" weight="fill" class="shrink-0" />
                                        RIESGO CRÍTICO: Mover a este colaborador dejará su depto. actual vulnerable.
                                    </div>

                                    <!-- Domino Effect / Succession Chain -->
                                    <div v-if="activeAnalysis.potential_replacements?.length" class="mt-8">
                                        <div class="mb-4 flex items-center gap-2">
                                            <PhTrendUp :size="20" class="text-indigo-400" />
                                            <h4 class="text-sm font-black uppercase tracking-widest text-white/90">Efecto Dominó: El Siguiente en Fila</h4>
                                        </div>
                                        
                                        <div class="space-y-3">
                                            <div 
                                                v-for="repl in activeAnalysis.potential_replacements" 
                                                :key="repl.name" 
                                                class="flex items-center gap-3 rounded-xl border border-white/5 bg-white/5 p-3 transition-all hover:bg-white/10 cursor-pointer group"
                                                @click="navigateToCandidate(repl.id)"
                                            >
                                                <div class="relative">
                                                    <img :src="repl.photo_url || `https://ui-avatars.com/api/?name=${repl.name}&background=6366f1&color=fff`" class="h-10 w-10 rounded-lg object-cover" />
                                                    <div class="absolute -right-1 -top-1 h-3 w-3 rounded-full border-2 border-slate-900 bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]"></div>
                                                </div>
                                                <div class="min-w-0 flex-1">
                                                    <div class="truncate text-xs font-black text-white/90 group-hover:text-indigo-300 transition-colors">{{ repl.name }}</div>
                                                    <div class="text-[10px] font-bold text-white/40 uppercase tracking-tighter">{{ repl.readiness_level }}</div>
                                                </div>
                                                <div class="text-right">
                                                    <div class="text-xs font-black text-indigo-400">{{ repl.readiness_score }}%</div>
                                                    <PhArrowRight :size="14" class="text-white/20 group-hover:translate-x-1 group-hover:text-indigo-400 transition-all ml-auto" />
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <p class="mt-3 text-[10px] font-medium text-white/30 italic">
                                            * Estos candidatos podrían cubrir el puesto de {{ activeAnalysis.person.name.split(' ')[0] }} de forma inmediata.
                                        </p>
                                    </div>
                                </StCardGlass>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </v-dialog>

            <!-- Saved Plans Tracking (Persistence Check) -->
            <div class="mt-12">
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-black text-white">Planes de Sucesión Formalizados</h3>
                        <p class="text-sm font-medium text-white/40">Registro histórico de decisiones estratégicas de talento.</p>
                    </div>
                    <StBadgeGlass variant="primary" size="sm" class="uppercase">Fase de Simulación Activa</StBadgeGlass>
                </div>

                <div v-if="loadingPlans" class="flex items-center justify-center py-20">
                    <PhSpinner :size="32" class="animate-spin text-indigo-500" />
                </div>
                
                <div v-else-if="savedPlans.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <StCardGlass v-for="plan in savedPlans" :key="plan.id" class="p-6 border-indigo-500/10 hover:border-indigo-500/30 transition-all group">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <img :src="plan.person.photo_url || `https://ui-avatars.com/api/?name=${plan.person.first_name}+${plan.person.last_name}&background=6366f1&color=fff`" class="h-12 w-12 rounded-xl object-cover" />
                                <div>
                                    <h5 class="text-sm font-black text-white group-hover:text-indigo-400 transition-colors">{{ plan.person.first_name }} {{ plan.person.last_name }}</h5>
                                    <p class="text-[10px] font-bold text-white/30 uppercase tracking-widest">Sucesor para: {{ plan.target_role.name }}</p>
                                </div>
                            </div>
                            <StBadgeGlass variant="secondary" size="sm">{{ plan.status }}</StBadgeGlass>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div class="rounded-lg bg-white/5 p-3">
                                <span class="block text-[9px] font-bold text-white/20 uppercase">Afinidad</span>
                                <span class="text-lg font-black text-indigo-400">{{ plan.readiness_score }}%</span>
                            </div>
                            <div class="rounded-lg bg-white/5 p-3">
                                <span class="block text-[9px] font-bold text-white/20 uppercase">Tiempo Est.</span>
                                <span class="text-lg font-black text-white">{{ plan.estimated_months || 0 }}m</span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between text-[10px] font-bold text-white/40 mb-4">
                            <span>Nivel: {{ plan.readiness_level }}</span>
                            <span>{{ new Date(plan.created_at).toLocaleDateString() }}</span>
                        </div>

                        <StButtonGlass 
                            block 
                            variant="glass" 
                            size="sm"
                            class="text-xs font-black group-hover:bg-indigo-500/10 group-hover:border-indigo-500/20"
                            @click="activeAnalysis = {
                                person: {
                                    id: plan.person_id,
                                    name: `${plan.person.first_name} ${plan.person.last_name}`,
                                    email: plan.person.email,
                                    current_role: 'Cargado desde historial',
                                    photo_url: plan.person.photo_url,
                                    is_high_potential: !!plan.person.is_high_potential
                                },
                                readiness_score: plan.readiness_score,
                                readiness_level: plan.readiness_level,
                                estimated_months: plan.estimated_months,
                                metrics: plan.metrics,
                                gaps: plan.gaps,
                                trajectory_summary: { total_movements: 0, last_movement: 'none', years_in_org: 0 }
                            }"
                        >
                            Ver Análisis Detallado
                        </StButtonGlass>
                    </StCardGlass>
                </div>

                <div v-else class="flex flex-col items-center justify-center py-20 rounded-3xl border border-dashed border-white/10 bg-white/5 text-center">
                    <PhTrendUp :size="48" class="text-white/10 mb-4" />
                    <p class="text-sm font-bold text-white/30">No hay planes formalizados aún.<br>Selecciona un candidato y pulsa "Formalizar".</p>
                </div>
            </div>
    </AppLayout>
</template>

<style scoped>
.v-dialog {
    background: transparent !important;
}

select option {
    background-color: #0f172a;
    color: white;
}

.fade-enter-active, .fade-leave-active {
    transition: opacity 0.5s ease;
}
.fade-enter-from, .fade-leave-to {
    opacity: 0;
}

/* Glassy scrollbar */
::-webkit-scrollbar {
    width: 6px;
}
::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.02);
}
::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 10px;
}
::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.2);
}
</style>
