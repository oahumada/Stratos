<script setup lang="ts">
import StButtonGlass from '@/components/StButtonGlass.vue';
import StCardGlass from '@/components/StCardGlass.vue';
import TalentPassCard from '@/components/TalentPass/TalentPassCard.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { useTalentPassStore } from '@/stores/talentPassStore';
import type { TalentPassStatus } from '@/types/talentPass';
import { Head, Link } from '@inertiajs/vue3';
import { PhFunnelSimple, PhMagnifyingGlass, PhPlus } from '@phosphor-icons/vue';
import { computed, onMounted, ref } from 'vue';

const route = (name: string, params?: Record<string, any>) => {
    return (globalThis as any).route(name, params);
};

defineOptions({ name: 'TalentPassIndex' });

const store = useTalentPassStore();
const searchQuery = ref('');
const statusFilter = ref<string>('all');

const filteredPasses = computed(() => {
    let passes = store.talentPasses;
    if (searchQuery.value) {
        const q = searchQuery.value.toLowerCase();
        passes = passes.filter(
            (tp) =>
                tp.title.toLowerCase().includes(q) ||
                tp.summary?.toLowerCase().includes(q),
        );
    }
    if (statusFilter.value !== 'all') {
        passes = passes.filter((tp) => tp.status === statusFilter.value);
    }
    return passes;
});

const statusOptions: Array<{ value: 'all' | TalentPassStatus; label: string }> =
    [
        { value: 'all', label: 'Todos' },
        { value: 'draft', label: 'Borrador' },
        { value: 'active', label: 'Publicado' },
        { value: 'archived', label: 'Archivado' },
    ];

onMounted(() => {
    store.fetchTalentPasses();
});
</script>

<template>
    <AppLayout>
        <Head>
            <title>Talent Pass</title>
        </Head>

        <div class="min-h-screen bg-slate-950 p-6 md:p-10">
            <div class="mx-auto max-w-7xl space-y-8">
                <!-- HEADER -->
                <div
                    class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
                >
                    <div>
                        <h1
                            class="text-3xl font-black tracking-tight text-white"
                        >
                            Talent Pass
                        </h1>
                        <p class="mt-1 text-white/60">
                            Tu CV 2.0 — skills, experiencias y credenciales
                            verificables
                        </p>
                    </div>
                    <Link href="/talent-pass/create">
                        <StButtonGlass variant="primary" :icon="PhPlus">
                            Nuevo Talent Pass
                        </StButtonGlass>
                    </Link>
                </div>

                <!-- FILTERS -->
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                    <div class="relative flex-1">
                        <PhMagnifyingGlass
                            class="absolute top-1/2 left-3 -translate-y-1/2 text-white/40"
                            :size="18"
                        />
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Buscar por título o descripción..."
                            class="w-full rounded-lg border border-white/10 bg-white/5 py-2 pr-4 pl-9 text-sm text-white placeholder-white/30 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none"
                        />
                    </div>

                    <div class="flex items-center gap-2">
                        <PhFunnelSimple :size="16" class="text-white/40" />
                        <select
                            v-model="statusFilter"
                            class="rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-sm text-white focus:border-indigo-500 focus:outline-none"
                        >
                            <option
                                v-for="opt in statusOptions"
                                :key="opt.value"
                                :value="opt.value"
                                class="bg-slate-900"
                            >
                                {{ opt.label }}
                            </option>
                        </select>
                    </div>
                </div>

                <!-- LOADING -->
                <div
                    v-if="store.loading"
                    class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3"
                >
                    <div
                        v-for="i in 6"
                        :key="i"
                        class="h-48 animate-pulse rounded-xl bg-white/5"
                    />
                </div>

                <!-- EMPTY STATE -->
                <div
                    v-else-if="filteredPasses.length === 0"
                    class="py-20 text-center"
                >
                    <StCardGlass class="mx-auto max-w-sm p-10">
                        <div class="mb-4 text-5xl">🎯</div>
                        <h3 class="mb-2 text-xl font-bold text-white">
                            Sin Talent Passes
                        </h3>
                        <p class="mb-6 text-sm text-white/60">
                            Crea tu primer Talent Pass y comparte tu perfil
                            profesional con el mundo.
                        </p>
                        <Link :href="route('talent-pass.create')">
                            <StButtonGlass
                                variant="primary"
                                :icon="PhPlus"
                                class="w-full"
                            >
                                Crear Ahora
                            </StButtonGlass>
                        </Link>
                    </StCardGlass>
                </div>

                <!-- GRID -->
                <div
                    v-else
                    class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3"
                >
                    <TalentPassCard
                        v-for="tp in filteredPasses"
                        :key="tp.id"
                        :talent-pass="tp"
                    />
                </div>

                <!-- STATS FOOTER -->
                <div
                    v-if="store.talentPasses.length > 0"
                    class="flex gap-6 border-t border-white/10 pt-4 text-sm text-white/40"
                >
                    <span>{{ store.talentPasses.length }} total</span>
                    <span>
                        {{
                            store.talentPasses.filter(
                                (tp) =>
                                    tp.status ===
                                    ('published' as TalentPassStatus),
                            ).length
                        }}
                        publicados
                    </span>
                    <span>
                        {{
                            store.talentPasses.filter(
                                (tp) =>
                                    tp.status === ('draft' as TalentPassStatus),
                            ).length
                        }}
                        borradores
                    </span>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
