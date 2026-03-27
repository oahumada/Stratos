<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { useRoute } from 'vue-router';
import { useTalentPassStore } from '@/stores/talentPassStore';
import { useNotification } from '@kyvg/vue3-notification';
import {
    PhArrowLeft,
    PhDownload,
    PhLink,
    PhCheckCircle,
    PhShare,
} from '@phosphor-icons/vue';
import StCardGlass from '@/components/StCardGlass.vue';
import StBadgeGlass from '@/components/StBadgeGlass.vue';
import { computed, onMounted, ref } from 'vue';

// Setup
const route = useRoute();
const store = useTalentPassStore();
const { notify } = useNotification();
const loading = ref(true);
const ulid = ref<string>('');

// Computed
const talentPass = computed(() => store.currentTalentPass);
const completinessPercentage = computed(
    () => talentPass.value?.completeness || 0
);

// Methods
async function loadPublicTalentPass() {
    try {
        if (!ulid.value) return;

        // Fetch public talent pass by ULID
        const response = await fetch(`/api/talent-pass/${ulid.value}`);

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
        <div class="mx-auto max-w-4xl mb-6">
            <div class="p-4 rounded-lg bg-indigo-500/10 border border-indigo-500/30 flex items-center gap-3">
                <PhLink :size="20" class="text-indigo-400 flex-shrink-0" />
                <div>
                    <p class="text-sm font-semibold text-indigo-300">Public View</p>
                    <p class="text-xs text-indigo-400/80">
                        This is a publicly shared Talent Pass profile
                    </p>
                </div>
            </div>
        </div>

        <!-- Loading State -->
        <div
            v-if="loading"
            class="flex items-center justify-center py-20"
        >
            <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-indigo-500"></div>
        </div>

        <!-- Not Found State -->
        <div
            v-else-if="!talentPass"
            class="mx-auto max-w-4xl text-center py-20"
        >
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-white/5 mb-4">
                <PhArrowLeft :size="32} weight="thin" class="text-slate-600" />
            </div>
            <h2 class="text-2xl font-bold text-white mb-2">Talent Pass not found</h2>
            <p class="text-slate-400 mb-6">
                This profile is no longer available or could not be found.
            </p>
            <Link
                href="/"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-indigo-500/20 text-indigo-300 hover:bg-indigo-500/30 border border-indigo-500/30 font-semibold transition"
            >
                <PhArrowLeft :size="16" />
                Back to Home
            </Link>
        </div>

        <!-- Content -->
        <div v-else class="mx-auto max-w-4xl">
            <Head :title="talentPass.title || 'Talent Pass'" />

            <!-- Main Card -->
            <StCardGlass class="p-8 mb-8">
                <!-- Header Section -->
                <div class="flex flex-col md:flex-row justify-between items-start gap-6 mb-8 pb-8 border-b border-white/10">
                    <div class="flex-1">
                        <h1 class="text-4xl font-black text-white mb-2">
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
                            class="px-4 py-2 rounded-lg bg-indigo-500/20 hover:bg-indigo-500/30 border border-indigo-500/30 text-indigo-300 font-semibold transition flex items-center gap-2"
                        >
                            <PhShare :size="16" />
                            Share
                        </button>
                        <button
                            @click="handleExport"
                            class="px-4 py-2 rounded-lg bg-blue-500/20 hover:bg-blue-500/30 border border-blue-500/30 text-blue-300 font-semibold transition flex items-center gap-2"
                        >
                            <PhDownload :size="16" />
                            Download
                        </button>
                    </div>
                </div>

                <!-- Summary -->
                <div v-if="talentPass.summary" class="mb-8">
                    <h3 class="text-sm font-bold text-slate-400 mb-2">PROFESSIONAL SUMMARY</h3>
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

                <!-- Stats Grid -->
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    <div class="p-3 rounded-lg bg-white/5 border border-white/10">
                        <p class="text-xs text-slate-400 mb-1">Skills</p>
                        <span class="font-bold text-white text-xl">
                            {{ talentPass.skills_count || 0 }}
                        </span>
                    </div>
                    <div class="p-3 rounded-lg bg-white/5 border border-white/10">
                        <p class="text-xs text-slate-400 mb-1">Experience</p>
                        <span class="font-bold text-white text-xl">
                            {{ talentPass.experiences_count || 0 }}
                        </span>
                    </div>
                    <div class="p-3 rounded-lg bg-white/5 border border-white/10">
                        <p class="text-xs text-slate-400 mb-1">Credentials</p>
                        <span class="font-bold text-white text-xl">
                            {{ talentPass.credentials_count || 0 }}
                        </span>
                    </div>
                </div>
            </StCardGlass>

            <!-- Section Cards (Placeholder) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <StCardGlass class="p-6">
                    <h3 class="text-lg font-bold text-white mb-4">Technical Skills</h3>
                    <div class="space-y-2">
                        <p class="text-slate-400 text-sm">
                            {{ talentPass.skills_count || 0 }} skills on this profile
                        </p>
                    </div>
                </StCardGlass>

                <StCardGlass class="p-6">
                    <h3 class="text-lg font-bold text-white mb-4">Professional Experience</h3>
                    <div class="space-y-2">
                        <p class="text-slate-400 text-sm">
                            {{ talentPass.experiences_count || 0 }} experiences listed
                        </p>
                    </div>
                </StCardGlass>

                <StCardGlass class="p-6">
                    <h3 class="text-lg font-bold text-white mb-4">Verified Credentials</h3>
                    <div class="space-y-2">
                        <p class="text-slate-400 text-sm">
                            {{ talentPass.credentials_count || 0 }} credentials verified
                        </p>
                    </div>
                </StCardGlass>

                <StCardGlass class="p-6">
                    <h3 class="text-lg font-bold text-white mb-4">About</h3>
                    <div class="space-y-3 text-sm text-slate-400">
                        <div>
                            <p class="font-semibold text-white mb-1">Privacy</p>
                            <p>This profile is shared via public link</p>
                        </div>
                    </div>
                </StCardGlass>
            </div>

            <!-- Footer CTA -->
            <div class="mt-12 p-8 rounded-xl bg-gradient-to-r from-indigo-500/10 to-purple-500/10 border border-indigo-500/30 text-center">
                <h3 class="text-xl font-bold text-white mb-2">Interested in connecting?</h3>
                <p class="text-slate-400 mb-6 max-w-2xl mx-auto">
                    This profile is shared publicly for portfolio purposes. Contact via email or social media.
                </p>
                <Link
                    href="/"
                    class="inline-flex items-center gap-2 px-6 py-3 rounded-lg bg-indigo-500 hover:bg-indigo-600 text-white font-semibold transition"
                >
                    Back to Stratos
                </Link>
            </div>
        </div>
    </div>
</template>
