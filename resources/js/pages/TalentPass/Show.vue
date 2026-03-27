<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { useTalentPassStore } from '@/stores/talentPassStore';
import { useNotification } from '@kyvg/vue3-notification';
import {
    PhArrowLeft,
    PhShare,
    PhDownload,
    PhPencil,
    PhTrash,
    PhArchiveBox,
    PhLink,
    PhGlobe,
    PhLock,
    PhCheckCircle,
} from '@phosphor-icons/vue';
import StCardGlass from '@/components/StCardGlass.vue';
import StBadgeGlass from '@/components/StBadgeGlass.vue';
import StButtonGlass from '@/components/StButtonGlass.vue';
import { computed, onMounted, ref } from 'vue';

defineOptions({ layout: AppLayout });

// Props Interface
interface Props {
    id: number | string;
}

const props = defineProps<Props>();

// Setup
const page = usePage();
const store = useTalentPassStore();
const { notify } = useNotification();
const loading = ref(true);
const talentPassId = ref<number | null>(null);

// Computed
const talentPass = computed(() => store.currentTalentPass);
const completinessPercentage = computed(
    () => talentPass.value?.completeness || 0
);

const visibilityIcon = computed(() => {
    switch (talentPass.value?.visibility) {
        case 'public':
            return PhGlobe;
        case 'link':
            return PhLink;
        default:
            return PhLock;
    }
});

const visibilityLabel = computed(() => {
    switch (talentPass.value?.visibility) {
        case 'public':
            return 'Público';
        case 'link':
            return 'En enlace';
        default:
            return 'Privado';
    }
});

// Methods
async function loadTalentPass() {
    try {
        if (!talentPassId.value) return;
        await store.fetchTalentPass(talentPassId.value);
    } catch (err) {
        notify({
            type: 'error',
            text: 'Error loading Talent Pass',
        });
    } finally {
        loading.value = false;
    }
}

async function handleDelete() {
    if (!window.confirm('Delete this Talent Pass?')) return;
    if (!talentPassId.value) return;

    try {
        await store.deleteTalentPass(talentPassId.value);
        notify({
            type: 'success',
            text: 'Talent Pass deleted',
        });
        window.location.href = '/talent-pass';
    } catch (err) {
        notify({
            type: 'error',
            text: 'Error deleting Talent Pass',
        });
    }
}

async function handleArchive() {
    if (!talentPassId.value) return;

    try {
        await store.archiveTalentPass(talentPassId.value);
        notify({
            type: 'success',
            text: 'Talent Pass archived',
        });
        await loadTalentPass();
    } catch (err) {
        notify({
            type: 'error',
            text: 'Error archiving Talent Pass',
        });
    }
}

async function handleExport() {
    if (!talentPassId.value) return;

    try {
        await store.exportTalentPass(talentPassId.value, 'pdf');
        notify({
            type: 'success',
            text: 'Talent Pass exported to PDF',
        });
    } catch (err) {
        notify({
            type: 'error',
            text: 'Error exporting Talent Pass',
        });
    }
}

// Lifecycle
onMounted(() => {
    const id = Array.isArray(props.id)
        ? parseInt(Array.isArray(props.id) ? props.id[0].toString() : props.id.toString())
        : parseInt(props.id.toString());

    if (id) {
        talentPassId.value = id;
        loadTalentPass();
    }
});
</script>

<template>
    <Head :title="`${talentPass?.title || 'Talent Pass'}`" />

    <div v-if="loading" class="flex items-center justify-center min-h-screen bg-[#020617]">
        <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-indigo-500"></div>
    </div>

    <div v-else-if="!talentPass" class="flex items-center justify-center min-h-screen bg-[#020617]">
        <div class="text-center">
            <h2 class="text-2xl font-bold text-white mb-4">Talent Pass not found</h2>
            <Link
                href="/talent-pass"
                class="text-indigo-400 hover:text-indigo-300 font-semibold"
            >
                Back to list
            </Link>
        </div>
    </div>

    <div v-else class="min-h-screen bg-[#020617] p-6 pb-20">
        <div class="mx-auto max-w-4xl">
            <!-- Navigation -->
            <Link
                href="/talent-pass"
                class="inline-flex items-center gap-2 text-indigo-400 hover:text-indigo-300 font-semibold mb-6 transition"
            >
                <PhArrowLeft :size="18" />
                Back
            </Link>

            <!-- Main Card -->
            <StCardGlass class="p-8 mb-8">
                <!-- Header Section -->
                <div class="flex flex-col md:flex-row justify-between items-start gap-6 mb-8 pb-8 border-b border-white/10">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <h1 class="text-4xl font-black text-white">
                                {{ talentPass.title }}
                            </h1>
                            <StBadgeGlass
                                :class="{
                                    'bg-green-500/20 text-green-300': talentPass.status === 'published',
                                    'bg-amber-500/20 text-amber-300': talentPass.status === 'draft',
                                    'bg-slate-500/20 text-slate-300': talentPass.status === 'archived',
                                }"
                            >
                                {{ talentPass.status }}
                            </StBadgeGlass>
                        </div>
                        <p class="text-sm text-slate-400">
                            {{ talentPass.people?.name || 'N/A' }}
                        </p>
                    </div>

                    <!-- Actions Dropdown -->
                    <div class="flex gap-2">
                        <Link
                            :href="`/talent-pass/${talentPass.id}/edit`"
                            class="px-4 py-2 rounded-lg bg-blue-500/20 hover:bg-blue-500/30 border border-blue-500/30 text-blue-300 font-semibold transition flex items-center gap-2"
                        >
                            <PhPencil :size="16" />
                            Edit
                        </Link>
                        <button
                            @click="handleExport"
                            class="px-4 py-2 rounded-lg bg-indigo-500/20 hover:bg-indigo-500/30 border border-indigo-500/30 text-indigo-300 font-semibold transition flex items-center gap-2"
                        >
                            <PhDownload :size="16" />
                            Export
                        </button>
                        <button
                            @click="handleArchive"
                            class="px-4 py-2 rounded-lg bg-amber-500/20 hover:bg-amber-500/30 border border-amber-500/30 text-amber-300 font-semibold transition flex items-center gap-2"
                        >
                            <PhArchiveBox :size="16" />
                        </button>
                        <button
                            @click="handleDelete"
                            class="px-4 py-2 rounded-lg bg-red-500/20 hover:bg-red-500/30 border border-red-500/30 text-red-300 font-semibold transition flex items-center gap-2"
                        >
                            <PhTrash :size="16" />
                        </button>
                    </div>
                </div>

                <!-- Summary -->
                <div v-if="talentPass.summary" class="mb-8">
                    <h3 class="text-sm font-bold text-slate-400 mb-2">SUMMARY</h3>
                    <p class="text-white leading-relaxed">{{ talentPass.summary }}</p>
                </div>

                <!-- Completeness Bar -->
                <div class="mb-8">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-semibold text-white flex items-center gap-2">
                            <PhCheckCircle :size="16" class="text-indigo-400" />
                            Profile Completeness
                        </span>
                        <span class="text-lg font-bold text-indigo-400">
                            {{ completinessPercentage }}%
                        </span>
                    </div>
                    <div class="h-3 rounded-full bg-white/5 overflow-hidden">
                        <div
                            class="h-full bg-gradient-to-r from-indigo-500 to-purple-500 transition-all duration-500"
                            :style="{ width: `${completinessPercentage}%` }"
                        ></div>
                    </div>
                </div>

                <!-- Visibility & Meta -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="p-3 rounded-lg bg-white/5 border border-white/10">
                        <p class="text-xs text-slate-400 mb-1">Visibility</p>
                        <div class="flex items-center gap-2">
                            <component :is="visibilityIcon" :size="16" class="text-indigo-400" />
                            <span class="font-semibold text-white">{{ visibilityLabel }}</span>
                        </div>
                    </div>
                    <div class="p-3 rounded-lg bg-white/5 border border-white/10">
                        <p class="text-xs text-slate-400 mb-1">Skills</p>
                        <span class="font-bold text-white">{{ talentPass.skills_count || 0 }}</span>
                    </div>
                    <div class="p-3 rounded-lg bg-white/5 border border-white/10">
                        <p class="text-xs text-slate-400 mb-1">Experience</p>
                        <span class="font-bold text-white">
                            {{ talentPass.experiences_count || 0 }}
                        </span>
                    </div>
                    <div class="p-3 rounded-lg bg-white/5 border border-white/10">
                        <p class="text-xs text-slate-400 mb-1">Credentials</p>
                        <span class="font-bold text-white">
                            {{ talentPass.credentials_count || 0 }}
                        </span>
                    </div>
                </div>
            </StCardGlass>

            <!-- Sections (Placeholder for components) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <StCardGlass class="p-6">
                    <h3 class="text-lg font-bold text-white mb-4">Skills</h3>
                    <p class="text-slate-400 text-sm">
                        {{ talentPass.skills_count || 0 }} skills added
                    </p>
                </StCardGlass>
                <StCardGlass class="p-6">
                    <h3 class="text-lg font-bold text-white mb-4">Experience</h3>
                    <p class="text-slate-400 text-sm">
                        {{ talentPass.experiences_count || 0 }} experiences added
                    </p>
                </StCardGlass>
                <StCardGlass class="p-6">
                    <h3 class="text-lg font-bold text-white mb-4">Credentials</h3>
                    <p class="text-slate-400 text-sm">
                        {{ talentPass.credentials_count || 0 }} credentials added
                    </p>
                </StCardGlass>
                <StCardGlass class="p-6">
                    <h3 class="text-lg font-bold text-white mb-4">Public View</h3>
                    <p class="text-slate-400 text-sm mb-3">
                        View this Talent Pass as other users see it.
                    </p>
                    <Link
                        :href="`/public/talent-pass/${talentPass.ulid}`"
                        target="_blank"
                        class="inline-flex items-center gap-2 px-3 py-2 rounded text-sm bg-indigo-500/20 text-indigo-300 hover:bg-indigo-500/30 transition"
                    >
                        <PhGlobe :size="14" />
                        Open
                    </Link>
                </StCardGlass>
            </div>
        </div>
    </div>
</template>
