<script setup lang="ts">
import StBadgeGlass from '@/components/StBadgeGlass.vue';
import StCardGlass from '@/components/StCardGlass.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { useTalentPassStore } from '@/stores/talentPassStore';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { useNotification } from '@kyvg/vue3-notification';
import {
    PhArchiveBox,
    PhArrowLeft,
    PhCheckCircle,
    PhDownload,
    PhGlobe,
    PhLink,
    PhLock,
    PhPencil,
    PhTrash,
} from '@phosphor-icons/vue';
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
    () => talentPass.value?.completeness || 0,
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
        ? parseInt(
              Array.isArray(props.id)
                  ? props.id[0].toString()
                  : props.id.toString(),
          )
        : parseInt(props.id.toString());

    if (id) {
        talentPassId.value = id;
        loadTalentPass();
    }
});
</script>

<template>
    <Head :title="`${talentPass?.title || 'Talent Pass'}`" />

    <div
        v-if="loading"
        class="flex min-h-screen items-center justify-center bg-[#020617]"
    >
        <div
            class="h-12 w-12 animate-spin rounded-full border-t-2 border-b-2 border-indigo-500"
        ></div>
    </div>

    <div
        v-else-if="!talentPass"
        class="flex min-h-screen items-center justify-center bg-[#020617]"
    >
        <div class="text-center">
            <h2 class="mb-4 text-2xl font-bold text-white">
                Talent Pass not found
            </h2>
            <Link
                href="/talent-pass"
                class="font-semibold text-indigo-400 hover:text-indigo-300"
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
                class="mb-6 inline-flex items-center gap-2 font-semibold text-indigo-400 transition hover:text-indigo-300"
            >
                <PhArrowLeft :size="18" />
                Back
            </Link>

            <!-- Main Card -->
            <StCardGlass class="mb-8 p-8">
                <!-- Header Section -->
                <div
                    class="mb-8 flex flex-col items-start justify-between gap-6 border-b border-white/10 pb-8 md:flex-row"
                >
                    <div class="flex-1">
                        <div class="mb-2 flex items-center gap-3">
                            <h1 class="text-4xl font-black text-white">
                                {{ talentPass.title }}
                            </h1>
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
                        <p class="text-sm text-slate-400">
                            {{ talentPass.people?.name || 'N/A' }}
                        </p>
                    </div>

                    <!-- Actions Dropdown -->
                    <div class="flex gap-2">
                        <Link
                            :href="`/talent-pass/${talentPass.id}/edit`"
                            class="flex items-center gap-2 rounded-lg border border-blue-500/30 bg-blue-500/20 px-4 py-2 font-semibold text-blue-300 transition hover:bg-blue-500/30"
                        >
                            <PhPencil :size="16" />
                            Edit
                        </Link>
                        <button
                            @click="handleExport"
                            class="flex items-center gap-2 rounded-lg border border-indigo-500/30 bg-indigo-500/20 px-4 py-2 font-semibold text-indigo-300 transition hover:bg-indigo-500/30"
                        >
                            <PhDownload :size="16" />
                            Export
                        </button>
                        <button
                            @click="handleArchive"
                            class="flex items-center gap-2 rounded-lg border border-amber-500/30 bg-amber-500/20 px-4 py-2 font-semibold text-amber-300 transition hover:bg-amber-500/30"
                        >
                            <PhArchiveBox :size="16" />
                        </button>
                        <button
                            @click="handleDelete"
                            class="flex items-center gap-2 rounded-lg border border-red-500/30 bg-red-500/20 px-4 py-2 font-semibold text-red-300 transition hover:bg-red-500/30"
                        >
                            <PhTrash :size="16" />
                        </button>
                    </div>
                </div>

                <!-- Summary -->
                <div v-if="talentPass.summary" class="mb-8">
                    <h3 class="mb-2 text-sm font-bold text-slate-400">
                        SUMMARY
                    </h3>
                    <p class="leading-relaxed text-white">
                        {{ talentPass.summary }}
                    </p>
                </div>

                <!-- Completeness Bar -->
                <div class="mb-8">
                    <div class="mb-2 flex items-center justify-between">
                        <span
                            class="flex items-center gap-2 text-sm font-semibold text-white"
                        >
                            <PhCheckCircle :size="16" class="text-indigo-400" />
                            Profile Completeness
                        </span>
                        <span class="text-lg font-bold text-indigo-400">
                            {{ completinessPercentage }}%
                        </span>
                    </div>
                    <div class="h-3 overflow-hidden rounded-full bg-white/5">
                        <div
                            class="h-full bg-gradient-to-r from-indigo-500 to-purple-500 transition-all duration-500"
                            :style="{ width: `${completinessPercentage}%` }"
                        ></div>
                    </div>
                </div>

                <!-- Visibility & Meta -->
                <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
                    <div
                        class="rounded-lg border border-white/10 bg-white/5 p-3"
                    >
                        <p class="mb-1 text-xs text-slate-400">Visibility</p>
                        <div class="flex items-center gap-2">
                            <component
                                :is="visibilityIcon"
                                :size="16"
                                class="text-indigo-400"
                            />
                            <span class="font-semibold text-white">{{
                                visibilityLabel
                            }}</span>
                        </div>
                    </div>
                    <div
                        class="rounded-lg border border-white/10 bg-white/5 p-3"
                    >
                        <p class="mb-1 text-xs text-slate-400">Skills</p>
                        <span class="font-bold text-white">{{
                            talentPass.skills_count || 0
                        }}</span>
                    </div>
                    <div
                        class="rounded-lg border border-white/10 bg-white/5 p-3"
                    >
                        <p class="mb-1 text-xs text-slate-400">Experience</p>
                        <span class="font-bold text-white">
                            {{ talentPass.experiences_count || 0 }}
                        </span>
                    </div>
                    <div
                        class="rounded-lg border border-white/10 bg-white/5 p-3"
                    >
                        <p class="mb-1 text-xs text-slate-400">Credentials</p>
                        <span class="font-bold text-white">
                            {{ talentPass.credentials_count || 0 }}
                        </span>
                    </div>
                </div>
            </StCardGlass>

            <!-- Sections (Placeholder for components) -->
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <StCardGlass class="p-6">
                    <h3 class="mb-4 text-lg font-bold text-white">Skills</h3>
                    <p class="text-sm text-slate-400">
                        {{ talentPass.skills_count || 0 }} skills added
                    </p>
                </StCardGlass>
                <StCardGlass class="p-6">
                    <h3 class="mb-4 text-lg font-bold text-white">
                        Experience
                    </h3>
                    <p class="text-sm text-slate-400">
                        {{ talentPass.experiences_count || 0 }} experiences
                        added
                    </p>
                </StCardGlass>
                <StCardGlass class="p-6">
                    <h3 class="mb-4 text-lg font-bold text-white">
                        Credentials
                    </h3>
                    <p class="text-sm text-slate-400">
                        {{ talentPass.credentials_count || 0 }} credentials
                        added
                    </p>
                </StCardGlass>
                <StCardGlass class="p-6">
                    <h3 class="mb-4 text-lg font-bold text-white">
                        Public View
                    </h3>
                    <p class="mb-3 text-sm text-slate-400">
                        View this Talent Pass as other users see it.
                    </p>
                    <Link
                        :href="`/public/talent-pass/${talentPass.ulid}`"
                        target="_blank"
                        class="inline-flex items-center gap-2 rounded bg-indigo-500/20 px-3 py-2 text-sm text-indigo-300 transition hover:bg-indigo-500/30"
                    >
                        <PhGlobe :size="14" />
                        Open
                    </Link>
                </StCardGlass>
            </div>
        </div>
    </div>
</template>
