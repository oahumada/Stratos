<script setup lang="ts">
import StCardGlass from '@/components/StCardGlass.vue';
import { useTalentPassStore } from '@/stores/talentPassStore';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { useNotification } from '@kyvg/vue3-notification';
import {
    PhArrowLeft,
    PhCheckCircle,
    PhDownload,
    PhLink,
    PhShare,
} from '@phosphor-icons/vue';
import { computed, onMounted, ref } from 'vue';

// Props
interface Props {
    ulid: string;
}

const props = defineProps<Props>();

// Setup
const page = usePage();
const store = useTalentPassStore();
const { notify } = useNotification();
const loading = ref(true);

// Computed
const talentPass = computed(() => store.currentTalentPass);
const completinessPercentage = computed(
    () => talentPass.value?.completeness || 0,
);

// Methods
async function loadPublicTalentPass() {
    try {
        if (!props.ulid) return;

        // Fetch public talent pass by ULID
        const response = await fetch(`/api/talent-pass/${props.ulid}`);

        if (!response.ok) {
            throw new Error('Talent Pass not found or not public');
        }

        const data = await response.json();
        store.currentTalentPass = data.data;
    } catch (err) {
        notify({
            type: 'error',
            text: 'Error loading Talent Pass',
        });
    } finally {
        loading.value = false;
    }
}

async function handleExport() {
    if (!talentPass.value?.id) return;

    try {
        await store.exportTalentPass(talentPass.value.id, 'pdf');
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

function copyShareLink() {
    if (!talentPass.value?.ulid) return;

    const link = `${window.location.origin}/public/talent-pass/${talentPass.value.ulid}`;
    navigator.clipboard.writeText(link);

    notify({
        type: 'success',
        text: 'Link copied to clipboard',
    });
}

// Lifecycle
onMounted(() => {
    const id = Array.isArray(route.params.ulid)
        ? route.params.ulid[0]
        : route.params.ulid;

    if (id) {
        ulid.value = id;
        loadPublicTalentPass();
    }
});
</script>

<template>
    <div class="min-h-screen bg-[#020617] p-6 pb-20">
        <!-- Header Info Banner -->
        <div class="mx-auto mb-6 max-w-4xl">
            <div
                class="flex items-center gap-3 rounded-lg border border-indigo-500/30 bg-indigo-500/10 p-4"
            >
                <PhLink :size="20" class="flex-shrink-0 text-indigo-400" />
                <div>
                    <p class="text-sm font-semibold text-indigo-300">
                        Public View
                    </p>
                    <p class="text-xs text-indigo-400/80">
                        This is a publicly shared Talent Pass profile
                    </p>
                </div>
            </div>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="flex items-center justify-center py-20">
            <div
                class="h-12 w-12 animate-spin rounded-full border-t-2 border-b-2 border-indigo-500"
            ></div>
        </div>

        <!-- Not Found State -->
        <div
            v-else-if="!talentPass"
            class="mx-auto max-w-4xl py-20 text-center"
        >
            <div
                class="mb-4 inline-flex h-16 w-16 items-center justify-center rounded-full bg-white/5"
            >
                <PhArrowLeft :size="32" weight="thin" class="text-slate-600" />
            </div>
            <h2 class="mb-2 text-2xl font-bold text-white">
                Talent Pass not found
            </h2>
            <p class="mb-6 text-slate-400">
                This profile is no longer available or could not be found.
            </p>
            <Link
                href="/"
                class="inline-flex items-center gap-2 rounded-lg border border-indigo-500/30 bg-indigo-500/20 px-4 py-2 font-semibold text-indigo-300 transition hover:bg-indigo-500/30"
            >
                <PhArrowLeft :size="16" />
                Back to Home
            </Link>
        </div>

        <!-- Content -->
        <div v-else class="mx-auto max-w-4xl">
            <Head :title="talentPass.title || 'Talent Pass'" />

            <!-- Main Card -->
            <StCardGlass class="mb-8 p-8">
                <!-- Header Section -->
                <div
                    class="mb-8 flex flex-col items-start justify-between gap-6 border-b border-white/10 pb-8 md:flex-row"
                >
                    <div class="flex-1">
                        <h1 class="mb-2 text-4xl font-black text-white">
                            {{ talentPass.title }}
                        </h1>
                        <p class="text-sm text-slate-400">
                            {{ talentPass.people?.name || 'Professional' }}
                        </p>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-2">
                        <button
                            @click="copyShareLink"
                            class="flex items-center gap-2 rounded-lg border border-indigo-500/30 bg-indigo-500/20 px-4 py-2 font-semibold text-indigo-300 transition hover:bg-indigo-500/30"
                        >
                            <PhShare :size="16" />
                            Share
                        </button>
                        <button
                            @click="handleExport"
                            class="flex items-center gap-2 rounded-lg border border-blue-500/30 bg-blue-500/20 px-4 py-2 font-semibold text-blue-300 transition hover:bg-blue-500/30"
                        >
                            <PhDownload :size="16" />
                            Download
                        </button>
                    </div>
                </div>

                <!-- Summary -->
                <div v-if="talentPass.summary" class="mb-8">
                    <h3 class="mb-2 text-sm font-bold text-slate-400">
                        PROFESSIONAL SUMMARY
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

                <!-- Stats Grid -->
                <div class="grid grid-cols-2 gap-4 md:grid-cols-3">
                    <div
                        class="rounded-lg border border-white/10 bg-white/5 p-3"
                    >
                        <p class="mb-1 text-xs text-slate-400">Skills</p>
                        <span class="text-xl font-bold text-white">
                            {{ talentPass.skills_count || 0 }}
                        </span>
                    </div>
                    <div
                        class="rounded-lg border border-white/10 bg-white/5 p-3"
                    >
                        <p class="mb-1 text-xs text-slate-400">Experience</p>
                        <span class="text-xl font-bold text-white">
                            {{ talentPass.experiences_count || 0 }}
                        </span>
                    </div>
                    <div
                        class="rounded-lg border border-white/10 bg-white/5 p-3"
                    >
                        <p class="mb-1 text-xs text-slate-400">Credentials</p>
                        <span class="text-xl font-bold text-white">
                            {{ talentPass.credentials_count || 0 }}
                        </span>
                    </div>
                </div>
            </StCardGlass>

            <!-- Section Cards (Placeholder) -->
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <StCardGlass class="p-6">
                    <h3 class="mb-4 text-lg font-bold text-white">
                        Technical Skills
                    </h3>
                    <div class="space-y-2">
                        <p class="text-sm text-slate-400">
                            {{ talentPass.skills_count || 0 }} skills on this
                            profile
                        </p>
                    </div>
                </StCardGlass>

                <StCardGlass class="p-6">
                    <h3 class="mb-4 text-lg font-bold text-white">
                        Professional Experience
                    </h3>
                    <div class="space-y-2">
                        <p class="text-sm text-slate-400">
                            {{ talentPass.experiences_count || 0 }} experiences
                            listed
                        </p>
                    </div>
                </StCardGlass>

                <StCardGlass class="p-6">
                    <h3 class="mb-4 text-lg font-bold text-white">
                        Verified Credentials
                    </h3>
                    <div class="space-y-2">
                        <p class="text-sm text-slate-400">
                            {{ talentPass.credentials_count || 0 }} credentials
                            verified
                        </p>
                    </div>
                </StCardGlass>

                <StCardGlass class="p-6">
                    <h3 class="mb-4 text-lg font-bold text-white">About</h3>
                    <div class="space-y-3 text-sm text-slate-400">
                        <div>
                            <p class="mb-1 font-semibold text-white">Privacy</p>
                            <p>This profile is shared via public link</p>
                        </div>
                    </div>
                </StCardGlass>
            </div>

            <!-- Footer CTA -->
            <div
                class="mt-12 rounded-xl border border-indigo-500/30 bg-gradient-to-r from-indigo-500/10 to-purple-500/10 p-8 text-center"
            >
                <h3 class="mb-2 text-xl font-bold text-white">
                    Interested in connecting?
                </h3>
                <p class="mx-auto mb-6 max-w-2xl text-slate-400">
                    This profile is shared publicly for portfolio purposes.
                    Contact via email or social media.
                </p>
                <Link
                    href="/"
                    class="inline-flex items-center gap-2 rounded-lg bg-indigo-500 px-6 py-3 font-semibold text-white transition hover:bg-indigo-600"
                >
                    Back to Stratos
                </Link>
            </div>
        </div>
    </div>
</template>
