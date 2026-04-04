<script setup lang="ts">
import StBadgeGlass from '@/components/StBadgeGlass.vue';
import StButtonGlass from '@/components/StButtonGlass.vue';
import StCardGlass from '@/components/StCardGlass.vue';
import CompletenessIndicator from '@/components/TalentPass/CompletenessIndicator.vue';
import CredentialManager from '@/components/TalentPass/CredentialManager.vue';
import ExperienceManager from '@/components/TalentPass/ExperienceManager.vue';
import ExportMenu from '@/components/TalentPass/ExportMenu.vue';
import ShareDialog from '@/components/TalentPass/ShareDialog.vue';
import SkillsManager from '@/components/TalentPass/SkillsManager.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { useTalentPassStore } from '@/stores/talentPassStore';
import { Head, Link } from '@inertiajs/vue3';
import {
    PhArchive,
    PhArrowLeft,
    PhPencil,
    PhShare,
    PhUpload,
} from '@phosphor-icons/vue';
import { computed, onMounted, ref } from 'vue';

defineOptions({ name: 'TalentPassShow' });

interface Props {
    id: number;
}

const props = defineProps<Props>();
const store = useTalentPassStore();
const isShareDialogOpen = ref(false);
const publishing = ref(false);
const archiving = ref(false);

const tp = computed(() => store.currentTalentPass);

const statusBadge = computed(() => {
    const map: Record<
        string,
        { label: string; variant: 'success' | 'warning' | 'glass' }
    > = {
        draft: { label: 'Borrador', variant: 'warning' },
        published: { label: 'Publicado', variant: 'success' },
        archived: { label: 'Archivado', variant: 'glass' },
    };
    return map[tp.value?.status ?? 'draft'] ?? map.draft;
});

function calculateCompleteness(talentPass: typeof tp.value): number {
    if (!talentPass) return 0;
    let completed = 0;
    let total = 0;

    if (talentPass.title) completed++;
    total++;

    if (talentPass.summary) completed++;
    total++;

    if (talentPass.skills && talentPass.skills.length > 0) completed++;
    total++;

    if (talentPass.experiences && talentPass.experiences.length > 0)
        completed++;
    total++;

    if (talentPass.credentials && talentPass.credentials.length > 0)
        completed++;
    total++;

    return Math.round((completed / total) * 100);
}

async function handlePublish() {
    if (!tp.value) return;
    publishing.value = true;
    try {
        await store.publishTalentPass(tp.value.id);
    } finally {
        publishing.value = false;
    }
}

async function handleArchive() {
    if (!tp.value || !confirm('¿Archivar este Talent Pass?')) return;
    archiving.value = true;
    try {
        await store.archiveTalentPass(tp.value.id);
    } finally {
        archiving.value = false;
    }
}

onMounted(() => {
    store.fetchTalentPass(props.id);
});
</script>

<template>
    <AppLayout>
        <Head>
            <title>{{ tp?.title ?? 'Talent Pass' }}</title>
        </Head>

        <div class="min-h-screen bg-slate-950 p-6 md:p-10">
            <div class="mx-auto max-w-5xl space-y-8">
                <!-- BACK + ACTIONS -->
                <div
                    class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
                >
                    <Link
                        href="/talent-pass"
                        class="inline-flex items-center gap-2 text-sm text-white/50 transition hover:text-white"
                    >
                        <PhArrowLeft :size="16" />
                        Mis Talent Passes
                    </Link>

                    <div v-if="tp" class="flex flex-wrap items-center gap-2">
                        <ExportMenu :talent-pass-id="tp.id" />

                        <StButtonGlass
                            variant="ghost"
                            :icon="PhShare"
                            @click="isShareDialogOpen = true"
                        >
                            Compartir
                        </StButtonGlass>

                        <Link :href="`/talent-pass/${tp.id}/edit`">
                            <StButtonGlass variant="secondary" :icon="PhPencil">
                                Editar
                            </StButtonGlass>
                        </Link>

                        <StButtonGlass
                            v-if="tp.status === 'draft'"
                            variant="primary"
                            :icon="PhUpload"
                            :loading="publishing"
                            @click="handlePublish"
                        >
                            Publicar
                        </StButtonGlass>

                        <StButtonGlass
                            v-if="tp.status !== 'archived'"
                            variant="ghost"
                            :icon="PhArchive"
                            :loading="archiving"
                            @click="handleArchive"
                        >
                            Archivar
                        </StButtonGlass>
                    </div>
                </div>

                <!-- LOADING -->
                <div v-if="store.loading" class="space-y-4">
                    <div class="h-40 animate-pulse rounded-xl bg-white/5" />
                    <div class="h-64 animate-pulse rounded-xl bg-white/5" />
                </div>

                <template v-else-if="tp">
                    <!-- HERO CARD -->
                    <StCardGlass class="p-8">
                        <div
                            class="flex flex-col gap-6 sm:flex-row sm:items-start"
                        >
                            <!-- Avatar placeholder -->
                            <div
                                class="flex h-20 w-20 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 text-3xl font-black text-white"
                            >
                                {{ tp.person?.first_name?.[0] ?? '?'
                                }}{{ tp.person?.last_name?.[0] ?? '' }}
                            </div>

                            <div class="flex-1 space-y-2">
                                <div class="flex flex-wrap items-center gap-3">
                                    <h1 class="text-2xl font-black text-white">
                                        {{ tp.title }}
                                    </h1>
                                    <StBadgeGlass
                                        :variant="statusBadge.variant"
                                    >
                                        {{ statusBadge.label }}
                                    </StBadgeGlass>
                                </div>

                                <p v-if="tp.person" class="text-white/60">
                                    {{ tp.person.first_name }}
                                    {{ tp.person.last_name }}
                                </p>

                                <p
                                    v-if="tp.summary"
                                    class="leading-relaxed text-white/80"
                                >
                                    {{ tp.summary }}
                                </p>

                                <div
                                    class="flex items-center gap-4 pt-2 text-xs text-white/40"
                                >
                                    <span>👁 {{ tp.views_count }} vistas</span>
                                    <span
                                        >📅
                                        {{
                                            new Date(
                                                tp.updated_at,
                                            ).toLocaleDateString('es-CL')
                                        }}</span
                                    >
                                </div>
                            </div>

                            <CompletenessIndicator
                                :completeness="calculateCompleteness(tp)"
                            />
                        </div>
                    </StCardGlass>

                    <!-- CONTENT SECTIONS -->
                    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                        <div class="space-y-6 lg:col-span-2">
                            <SkillsManager
                                :talent-pass-id="tp.id"
                                :skills="tp.skills"
                            />
                            <ExperienceManager
                                :talent-pass-id="tp.id"
                                :experiences="tp.experiences"
                            />
                        </div>

                        <div>
                            <CredentialManager
                                :talent-pass-id="tp.id"
                                :credentials="tp.credentials"
                            />
                        </div>
                    </div>
                </template>

                <!-- ERROR -->
                <div
                    v-else-if="store.error"
                    class="py-20 text-center text-red-400"
                >
                    {{ store.error }}
                </div>
            </div>
        </div>

        <!-- SHARE DIALOG -->
        <ShareDialog
            v-if="tp"
            :is-open="isShareDialogOpen"
            :talent-pass-ulid="String(tp.id)"
            @close="isShareDialogOpen = false"
        />
    </AppLayout>
</template>
