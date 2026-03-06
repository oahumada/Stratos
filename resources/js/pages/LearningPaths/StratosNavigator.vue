<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import {
    PhCheckCircle,
    PhClock,
    PhGraduationCap,
    PhLightning,
    PhTarget,
} from '@phosphor-icons/vue';
import axios from 'axios';
import { onMounted, ref } from 'vue';
import StCardGlass from '../../components/StCardGlass.vue';

const paths = ref<any[]>([]);
const loading = ref(true);

const fetchPaths = async () => {
    loading.value = true;
    try {
        const { data } = await axios.get('/api/development-paths');
        paths.value = data.data;
    } catch (e) {
        console.error('Error fetching paths:', e);
    } finally {
        loading.value = false;
    }
};

const getStrategyColor = (strategy: string) => {
    switch (strategy) {
        case 'build':
            return 'emerald';
        case 'borrow':
            return 'indigo';
        case 'buy':
            return 'amber';
        case 'bot':
            return 'cyan';
        default:
            return 'slate';
    }
};

onMounted(() => {
    fetchPaths();
});
</script>

<template>
    <div class="min-h-screen bg-slate-950 p-8 text-white">
        <Head>
            <title>Stratos Navigator - IA Learning</title>
        </Head>

        <div class="mx-auto max-w-7xl">
            <!-- Header -->
            <div class="mb-12">
                <div class="mb-2 flex items-center gap-3">
                    <div class="h-2 w-12 rounded-full bg-indigo-500"></div>
                    <span
                        class="text-xs font-black tracking-[0.3em] text-indigo-400 uppercase"
                        >Phase 3: Actionable Growth</span
                    >
                </div>
                <h1 class="mb-4 text-5xl font-black tracking-tight text-white">
                    Stratos
                    <span
                        class="bg-linear-to-r from-indigo-400 via-purple-400 to-cyan-400 bg-clip-text text-transparent"
                        >Navigator</span
                    >
                </h1>
                <p class="max-w-2xl text-lg font-medium text-white/40">
                    Rutas de desarrollo generadas por IA basadas en la
                    triangulación de tu desempeño y tus aspiraciones de carrera.
                </p>
            </div>

            <div v-if="loading" class="grid grid-cols-1 gap-8 md:grid-cols-2">
                <div
                    v-for="i in 2"
                    :key="i"
                    class="h-64 animate-pulse rounded-3xl bg-white/5"
                ></div>
            </div>

            <div v-else-if="paths.length === 0" class="py-24 text-center">
                <div
                    class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-3xl bg-white/5 text-white/20"
                >
                    <PhLightning :size="40" weight="duotone" />
                </div>
                <h3 class="mb-2 text-xl font-bold">No hay rutas activas</h3>
                <p class="text-white/40">
                    Genera una ruta desde el análisis de brechas de tu perfil.
                </p>
            </div>

            <div v-else class="grid grid-cols-1 gap-12">
                <div
                    v-for="path in paths"
                    :key="path.id"
                    class="group relative"
                >
                    <StCardGlass
                        class="border-white/10 p-8 transition-all duration-500 hover:border-indigo-500/30"
                    >
                        <!-- Path Header -->
                        <div
                            class="mb-12 flex flex-col justify-between gap-6 lg:flex-row lg:items-center"
                        >
                            <div class="flex items-center gap-6">
                                <div
                                    class="flex h-16 w-16 items-center justify-center rounded-2xl bg-linear-to-br from-indigo-600 to-purple-600 text-white shadow-xl shadow-indigo-500/20"
                                >
                                    <PhGraduationCap
                                        :size="32"
                                        weight="duotone"
                                    />
                                </div>
                                <div>
                                    <h2
                                        class="mb-1 text-2xl font-black capitalize"
                                    >
                                        {{ path.action_title }}
                                    </h2>
                                    <div
                                        class="flex items-center gap-3 text-sm font-bold text-white/40"
                                    >
                                        <div class="flex items-center gap-1">
                                            <PhTarget
                                                :size="16"
                                                class="text-rose-400"
                                            />
                                            <span>{{
                                                path.target_role_name
                                            }}</span>
                                        </div>
                                        <span class="text-white/10">•</span>
                                        <div class="flex items-center gap-1">
                                            <PhClock
                                                :size="16"
                                                class="text-cyan-400"
                                            />
                                            <span
                                                >~{{
                                                    path.estimated_duration_months
                                                }}
                                                meses</span
                                            >
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-4">
                                <div class="hidden text-right sm:block">
                                    <div
                                        class="mb-1 text-[10px] font-black tracking-widest text-white/30 uppercase"
                                    >
                                        Progreso General
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="h-2 w-32 overflow-hidden rounded-full bg-white/5"
                                        >
                                            <div
                                                class="h-full bg-indigo-500 shadow-[0_0_10px_rgba(99,102,241,0.5)]"
                                                :style="{
                                                    width:
                                                        (path.progress || 0) +
                                                        '%',
                                                }"
                                            ></div>
                                        </div>
                                        <span class="font-black text-white"
                                            >{{ path.progress || 0 }}%</span
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 70-20-10 Timeline -->
                        <div
                            class="relative grid grid-cols-1 gap-8 md:grid-cols-3"
                        >
                            <!-- Desktop Connectors -->
                            <div
                                class="absolute top-1/2 right-0 left-0 z-0 hidden h-px -translate-y-1/2 bg-linear-to-r from-transparent via-white/10 to-transparent md:block"
                            ></div>

                            <div
                                v-for="(action, index) in path.actions"
                                :key="action.id"
                                class="relative z-10 flex flex-col items-center"
                            >
                                <!-- Node -->
                                <div class="relative mb-6">
                                    <div
                                        :class="`flex h-12 w-12 items-center justify-center rounded-full border-2 bg-slate-900 shadow-xl transition-all duration-300 ${action.status === 'completed' ? 'border-emerald-500 text-emerald-500' : 'border-white/10 text-white/40'}`"
                                    >
                                        <PhCheckCircle
                                            v-if="action.status === 'completed'"
                                            :size="24"
                                            weight="fill"
                                        />
                                        <span
                                            v-else
                                            class="text-sm font-black"
                                            >{{ index + 1 }}</span
                                        >
                                    </div>
                                    <div
                                        v-if="action.impact_weight > 0.5"
                                        class="absolute -top-1 -right-1"
                                    >
                                        <div
                                            class="absolute h-4 w-4 animate-ping rounded-full bg-rose-500"
                                        ></div>
                                        <div
                                            class="relative flex h-4 w-4 items-center justify-center rounded-full bg-rose-500 text-[8px] font-black"
                                        >
                                            !
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Content -->
                                <div class="w-full text-center">
                                    <div class="mb-3 inline-flex">
                                        <div
                                            :class="
                                                `rounded-full border border-white/10 bg-white/5 px-3 py-1 text-[10px] font-black tracking-widest uppercase` +
                                                ` text-${getStrategyColor(action.strategy)}-400`
                                            "
                                        >
                                            {{ action.type }} •
                                            {{ action.strategy }}
                                        </div>
                                    </div>
                                    <h4
                                        class="mb-2 text-sm font-bold transition-colors group-hover:text-indigo-300"
                                    >
                                        {{ action.title }}
                                    </h4>
                                    <p
                                        class="mb-4 px-4 text-xs leading-relaxed text-white/30"
                                    >
                                        {{ action.description }}
                                    </p>

                                    <div
                                        v-if="
                                            action.mentor &&
                                            action.type === 'mentoring'
                                        "
                                        class="mt-auto flex items-center justify-center gap-2 rounded-2xl bg-white/2 p-3"
                                    >
                                        <div
                                            class="flex h-6 w-6 items-center justify-center rounded-full bg-indigo-500/20 text-[10px] font-bold"
                                        >
                                            {{ action.mentor.first_name?.[0] }}
                                        </div>
                                        <span
                                            class="text-[10px] font-medium text-white/50"
                                            >Mentor:
                                            {{ action.mentor.first_name }}</span
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>
                    </StCardGlass>
                </div>
            </div>
        </div>
    </div>
</template>
