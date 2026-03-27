<script setup lang="ts">
import StBadgeGlass from '@/components/StBadgeGlass.vue';
import StCardGlass from '@/components/StCardGlass.vue';
import { useApi } from '@/composables/useApi';
import AppLayout from '@/layouts/AppLayout.vue';
import { useTalentPassStore } from '@/stores/talentPassStore';
import { Head, Link } from '@inertiajs/vue3';
import { useNotification } from '@kyvg/vue3-notification';
import {
    PhArchiveBox,
    PhDownload,
    PhEye,
    PhMagnifyingGlass,
    PhPencil,
    PhPlus,
    PhShare,
    PhTrash,
} from '@phosphor-icons/vue';
import { computed, onMounted, ref } from 'vue';

defineOptions({ layout: AppLayout });

// Setup
const store = useTalentPassStore();
const { notify } = useNotification();
const { post } = useApi();

const searchQuery = ref('');
const selectedStatus = ref('');
const selectedVisibility = ref('');

// Computed
const filteredPasses = computed(() => {
    let filtered = store.talentPasses;

    if (searchQuery.value) {
        const q = searchQuery.value.toLowerCase();
        filtered = filtered.filter(
            (tp) =>
                tp.title.toLowerCase().includes(q) ||
                tp.summary?.toLowerCase().includes(q),
        );
    }

    if (selectedStatus.value) {
        filtered = filtered.filter((tp) => tp.status === selectedStatus.value);
    }

    if (selectedVisibility.value) {
        filtered = filtered.filter(
            (tp) => tp.visibility === selectedVisibility.value,
        );
    }

    return filtered;
});

const completenessStats = computed(() => {
    if (filteredPasses.value.length === 0) return { avg: 0, count: 0 };

    const total = filteredPasses.value.reduce(
        (sum, tp) => sum + (tp.completeness || 0),
        0,
    );
    return {
        avg: Math.round(total / filteredPasses.value.length),
        count: filteredPasses.value.length,
    };
});

// Methods
async function fetchTalentPasses() {
    try {
        await store.fetchTalentPasses();
    } catch (err) {
        notify({
            type: 'error',
            text: 'Error loading Talent Passes',
        });
    }
}

async function handleDelete(id: number) {
    if (!window.confirm('Delete this Talent Pass?')) return;

    try {
        await store.deleteTalentPass(id);
        notify({
            type: 'success',
            text: 'Talent Pass deleted',
        });
    } catch (err) {
        notify({
            type: 'error',
            text: 'Error deleting Talent Pass',
        });
    }
}

async function handleArchive(id: number) {
    try {
        await store.archiveTalentPass(id);
        notify({
            type: 'success',
            text: 'Talent Pass archived',
        });
    } catch (err) {
        notify({
            type: 'error',
            text: 'Error archiving Talent Pass',
        });
    }
}

async function handlePublish(id: number) {
    try {
        await store.publishTalentPass(id);
        notify({
            type: 'success',
            text: 'Talent Pass published',
        });
    } catch (err) {
        notify({
            type: 'error',
            text: 'Error publishing Talent Pass',
        });
    }
}

// Lifecycle
onMounted(() => {
    fetchTalentPasses();
});
</script>

<template>
    <Head title="Talent Pass — Mi Portafolio Profesional" />

    <div class="mb-8 min-h-screen bg-[#020617] p-6">
        <!-- Header -->
        <div class="mx-auto mb-8 max-w-7xl">
            <div class="mb-6 flex flex-col gap-2">
                <div class="flex items-center gap-2">
                    <div
                        class="h-1 w-8 rounded-full bg-gradient-to-r from-indigo-500 to-purple-500"
                    ></div>
                    <span
                        class="text-xs font-black tracking-[0.2em] text-indigo-400 uppercase"
                    >
                        Talent Pass CV 2.0
                    </span>
                </div>
                <h1 class="text-4xl font-black text-white">
                    Mi Portafolio Profesional
                </h1>
                <p class="max-w-2xl text-sm text-slate-400">
                    Crea y comparte tu perfil profesional avanzado con
                    componentes interactivos, credenciales verificables y
                    análisis de completitud.
                </p>
            </div>

            <!-- Action Bar -->
            <div class="mb-8 flex gap-3">
                <Link
                    href="/talent-pass/create"
                    as="button"
                    class="flex items-center gap-2 rounded-xl bg-gradient-to-r from-indigo-500 to-purple-500 px-4 py-2 font-semibold text-white transition hover:from-indigo-600 hover:to-purple-600"
                >
                    <PhPlus :size="18" weight="bold" />
                    Crear Talent Pass
                </Link>
            </div>

            <!-- Filters -->
            <div class="mb-8 grid grid-cols-1 gap-4 md:grid-cols-3">
                <!-- Search -->
                <div class="relative">
                    <PhMagnifyingGlass
                        :size="18"
                        weight="bold"
                        class="absolute top-1/2 left-3 -translate-y-1/2 text-slate-500"
                    />
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Buscar por título o resumen..."
                        class="w-full rounded-lg border border-white/10 bg-white/5 py-2 pr-4 pl-10 text-white placeholder-slate-500 focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                    />
                </div>

                <!-- Status Filter -->
                <select
                    v-model="selectedStatus"
                    class="rounded-lg border border-white/10 bg-white/5 px-4 py-2 text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                >
                    <option value="">Todos los estados</option>
                    <option value="draft">Borrador</option>
                    <option value="published">Publicado</option>
                    <option value="archived">Archivado</option>
                </select>

                <!-- Visibility Filter -->
                <select
                    v-model="selectedVisibility"
                    class="rounded-lg border border-white/10 bg-white/5 px-4 py-2 text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                >
                    <option value="">Todas las visibilidades</option>
                    <option value="private">Privado</option>
                    <option value="link">A través de enlace</option>
                    <option value="public">Público</option>
                </select>
            </div>

            <!-- Stats Bar -->
            <div class="mb-8 grid grid-cols-2 gap-4 md:grid-cols-4">
                <StCardGlass class="p-4">
                    <div class="mb-1 text-sm text-slate-400">Total</div>
                    <div class="text-2xl font-bold text-white">
                        {{ store.talentPasses.length }}
                    </div>
                </StCardGlass>
                <StCardGlass class="p-4">
                    <div class="mb-1 text-sm text-slate-400">
                        Completitud Prom.
                    </div>
                    <div class="text-2xl font-bold text-white">
                        {{ completenessStats.avg }}%
                    </div>
                </StCardGlass>
                <StCardGlass class="p-4">
                    <div class="mb-1 text-sm text-slate-400">Publicados</div>
                    <div class="text-2xl font-bold text-green-400">
                        {{
                            store.talentPasses.filter(
                                (tp) => tp.status === 'published',
                            ).length
                        }}
                    </div>
                </StCardGlass>
                <StCardGlass class="p-4">
                    <div class="mb-1 text-sm text-slate-400">Borradores</div>
                    <div class="text-2xl font-bold text-amber-400">
                        {{
                            store.talentPasses.filter(
                                (tp) => tp.status === 'draft',
                            ).length
                        }}
                    </div>
                </StCardGlass>
            </div>
        </div>

        <!-- Content Area -->
        <div class="mx-auto max-w-7xl">
            <!-- Empty State -->
            <div v-if="filteredPasses.length === 0" class="py-20 text-center">
                <div
                    class="mb-4 inline-flex h-16 w-16 items-center justify-center rounded-full bg-white/5"
                >
                    <PhDownload
                        :size="32"
                        weight="thin"
                        class="text-slate-600"
                    />
                </div>
                <h3 class="mb-2 text-xl font-bold text-white">
                    No hay Talent Passes
                </h3>
                <p class="mb-6 text-sm text-slate-400">
                    Crea tu primer Talent Pass para comenzar a compartir tu
                    perfil profesional.
                </p>
                <Link
                    href="/talent-pass/create"
                    as="button"
                    class="inline-flex items-center gap-2 rounded-lg border border-indigo-500/30 bg-indigo-500/20 px-4 py-2 font-semibold text-indigo-300 transition hover:bg-indigo-500/30"
                >
                    <PhPlus :size="16" />
                    Crear Talent Pass
                </Link>
            </div>

            <!-- Grid View -->
            <div
                v-else
                class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3"
            >
                <div
                    v-for="talentPass in filteredPasses"
                    :key="talentPass.id"
                    class="group"
                >
                    <StCardGlass
                        class="flex h-full flex-col gap-4 p-6 transition hover:border-indigo-500/50"
                    >
                        <!-- Header -->
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h3
                                    class="text-lg font-bold text-white transition group-hover:text-indigo-300"
                                >
                                    {{ talentPass.title }}
                                </h3>
                                <p class="mt-1 text-xs text-slate-400">
                                    {{ talentPass.people?.name || 'N/A' }}
                                </p>
                            </div>
                            <StBadgeGlass
                                :class="{
                                    'bg-green-500/20 text-green-300':
                                        talentPass.status === 'published',
                                    'bg-amber-500/20 text-amber-300':
                                        talentPass.status === 'draft',
                                    'bg-slate-500/20 text-slate-300':
                                        talentPass.status === 'archived',
                                }"
                            >
                                {{ talentPass.status }}
                            </StBadgeGlass>
                        </div>

                        <!-- Summary -->
                        <p class="line-clamp-3 text-sm text-slate-300">
                            {{ talentPass.summary || 'Sin descripción' }}
                        </p>

                        <!-- Completeness Bar -->
                        <div>
                            <div class="mb-2 flex justify-between">
                                <span class="text-xs text-slate-400"
                                    >Completitud</span
                                >
                                <span class="text-xs font-bold text-indigo-400">
                                    {{ talentPass.completeness || 0 }}%
                                </span>
                            </div>
                            <div
                                class="h-2 overflow-hidden rounded-full bg-white/5"
                            >
                                <div
                                    class="h-full bg-gradient-to-r from-indigo-500 to-purple-500 transition-all"
                                    :style="{
                                        width: `${talentPass.completeness || 0}%`,
                                    }"
                                ></div>
                            </div>
                        </div>

                        <!-- Metadata -->
                        <div class="flex gap-2 text-xs text-slate-400">
                            <span
                                >Skills:
                                {{ talentPass.skills_count || 0 }}</span
                            >
                            <span>•</span>
                            <span
                                >Experiencia:
                                {{ talentPass.experiences_count || 0 }}</span
                            >
                        </div>

                        <!-- Actions -->
                        <div class="flex gap-2 border-t border-white/10 pt-4">
                            <Link
                                :href="`/talent-pass/${talentPass.id}`"
                                class="flex flex-1 items-center justify-center gap-2 rounded-lg bg-white/5 px-3 py-2 text-sm font-semibold text-slate-300 transition hover:bg-indigo-500/20 hover:text-indigo-300"
                            >
                                <PhEye :size="16" />
                            </Link>
                            <Link
                                :href="`/talent-pass/${talentPass.id}/edit`"
                                class="flex flex-1 items-center justify-center gap-2 rounded-lg bg-white/5 px-3 py-2 text-sm font-semibold text-slate-300 transition hover:bg-blue-500/20 hover:text-blue-300"
                            >
                                <PhPencil :size="16" />
                            </Link>
                            <button
                                v-if="talentPass.status === 'draft'"
                                @click="handlePublish(talentPass.id)"
                                class="flex flex-1 items-center justify-center gap-2 rounded-lg bg-white/5 px-3 py-2 text-sm font-semibold text-slate-300 transition hover:bg-green-500/20 hover:text-green-300"
                                title="Publicar"
                            >
                                <PhShare :size="16" />
                            </button>
                            <button
                                v-else
                                @click="handleArchive(talentPass.id)"
                                class="flex flex-1 items-center justify-center gap-2 rounded-lg bg-white/5 px-3 py-2 text-sm font-semibold text-slate-300 transition hover:bg-amber-500/20 hover:text-amber-300"
                                title="Archivar"
                            >
                                <PhArchiveBox :size="16" />
                            </button>
                            <button
                                @click="handleDelete(talentPass.id)"
                                class="flex flex-1 items-center justify-center gap-2 rounded-lg bg-white/5 px-3 py-2 text-sm font-semibold text-slate-300 transition hover:bg-red-500/20 hover:text-red-300"
                                title="Eliminar"
                            >
                                <PhTrash :size="16" />
                            </button>
                        </div>
                    </StCardGlass>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.st-glass-container {
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
</style>
