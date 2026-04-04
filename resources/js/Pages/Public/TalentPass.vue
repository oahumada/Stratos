<script setup lang="ts">
import StBadgeGlass from '@/components/StBadgeGlass.vue';
import StCardGlass from '@/components/StCardGlass.vue';
import CompletenessIndicator from '@/components/TalentPass/CompletenessIndicator.vue';
import { useTalentPassStore } from '@/stores/talentPassStore';
import { Head } from '@inertiajs/vue3';
import { PhGlobe, PhStar } from '@phosphor-icons/vue';
import { computed, onMounted } from 'vue';

defineOptions({ name: 'TalentPassPublic' });

interface Props {
    ulid: string;
}

const props = defineProps<Props>();
const store = useTalentPassStore();

const tp = computed(() => store.currentTalentPass);

const proficiencyLabel: Record<string, string> = {
    beginner: 'Iniciante',
    intermediate: 'Intermedio',
    expert: 'Experto',
    master: 'Master',
};

const proficiencyColor: Record<string, string> = {
    beginner: 'bg-slate-500/20 text-slate-300',
    intermediate: 'bg-blue-500/20 text-blue-300',
    expert: 'bg-indigo-500/20 text-indigo-300',
    master: 'bg-purple-500/20 text-purple-300',
};

onMounted(() => {
    store.fetchPublicTalentPass(props.ulid);
});
</script>

<template>
    <Head :title="tp ? `${tp.title} — Talent Pass` : 'Talent Pass'" />

    <div class="min-h-screen bg-slate-950">
        <!-- NAV PUBLIC -->
        <nav class="border-b border-white/10 px-6 py-4">
            <div class="mx-auto flex max-w-5xl items-center justify-between">
                <span class="text-lg font-black tracking-tight text-white">
                    Stratos <span class="text-indigo-400">Talent Pass</span>
                </span>
                <div class="flex items-center gap-2 text-xs text-white/40">
                    <PhGlobe :size="14" />
                    Vista pública
                </div>
            </div>
        </nav>

        <div class="mx-auto max-w-5xl space-y-8 p-6 md:p-10">
            <!-- LOADING -->
            <div v-if="store.loading" class="space-y-4">
                <div class="h-40 animate-pulse rounded-xl bg-white/5" />
                <div class="h-64 animate-pulse rounded-xl bg-white/5" />
            </div>

            <template v-else-if="tp">
                <!-- HERO -->
                <StCardGlass class="p-8">
                    <div class="flex flex-col gap-6 sm:flex-row sm:items-start">
                        <div
                            class="flex h-20 w-20 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 text-3xl font-black text-white"
                        >
                            {{ tp.person?.first_name?.[0] ?? '?'
                            }}{{ tp.person?.last_name?.[0] ?? '' }}
                        </div>

                        <div class="flex-1 space-y-2">
                            <h1 class="text-2xl font-black text-white">
                                {{ tp.title }}
                            </h1>
                            <p v-if="tp.person" class="text-white/60">
                                {{ tp.person.first_name }}
                                {{ tp.person.last_name }}
                                <span v-if="tp.person.department">
                                    · {{ tp.person.department }}</span
                                >
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
                            </div>
                        </div>

                        <CompletenessIndicator :value="tp.completeness ?? 0" />
                    </div>
                </StCardGlass>

                <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                    <div class="space-y-6 lg:col-span-2">
                        <!-- SKILLS -->
                        <StCardGlass v-if="tp.skills.length > 0" class="p-6">
                            <h2 class="mb-4 text-lg font-bold text-white">
                                Skills
                            </h2>
                            <div class="flex flex-wrap gap-2">
                                <span
                                    v-for="skill in tp.skills"
                                    :key="skill.id"
                                    :class="[
                                        'inline-flex items-center gap-2 rounded-full px-3 py-1 text-sm',
                                        proficiencyColor[
                                            skill.proficiency_level
                                        ],
                                    ]"
                                >
                                    <PhStar
                                        v-if="
                                            skill.proficiency_level === 'master'
                                        "
                                        :size="12"
                                        weight="fill"
                                    />
                                    {{ skill.skill_name }}
                                    <span class="opacity-60"
                                        >·
                                        {{
                                            proficiencyLabel[
                                                skill.proficiency_level
                                            ]
                                        }}</span
                                    >
                                </span>
                            </div>
                        </StCardGlass>

                        <!-- EXPERIENCIAS -->
                        <StCardGlass
                            v-if="tp.experiences.length > 0"
                            class="p-6"
                        >
                            <h2 class="mb-4 text-lg font-bold text-white">
                                Experiencia
                            </h2>
                            <div class="space-y-5">
                                <div
                                    v-for="exp in tp.experiences"
                                    :key="exp.id"
                                    class="border-l-2 border-indigo-500/40 pl-4"
                                >
                                    <div
                                        class="flex items-start justify-between"
                                    >
                                        <div>
                                            <h3
                                                class="font-semibold text-white"
                                            >
                                                {{ exp.job_title }}
                                            </h3>
                                            <p class="text-sm text-white/60">
                                                {{ exp.company_name }}
                                            </p>
                                        </div>
                                        <StBadgeGlass
                                            v-if="exp.is_current"
                                            variant="success"
                                            class="shrink-0"
                                        >
                                            Actual
                                        </StBadgeGlass>
                                    </div>
                                    <p class="mt-1 text-xs text-white/40">
                                        {{
                                            new Date(
                                                exp.start_date,
                                            ).toLocaleDateString('es-CL', {
                                                month: 'short',
                                                year: 'numeric',
                                            })
                                        }}
                                        —
                                        {{
                                            exp.is_current
                                                ? 'Presente'
                                                : exp.end_date
                                                  ? new Date(
                                                        exp.end_date,
                                                    ).toLocaleDateString(
                                                        'es-CL',
                                                        {
                                                            month: 'short',
                                                            year: 'numeric',
                                                        },
                                                    )
                                                  : ''
                                        }}
                                    </p>
                                    <p
                                        v-if="exp.job_description"
                                        class="mt-2 text-sm text-white/70"
                                    >
                                        {{ exp.job_description }}
                                    </p>
                                </div>
                            </div>
                        </StCardGlass>
                    </div>

                    <!-- CREDENCIALES -->
                    <StCardGlass
                        v-if="tp.credentials.length > 0"
                        class="h-fit p-6"
                    >
                        <h2 class="mb-4 text-lg font-bold text-white">
                            Credenciales
                        </h2>
                        <div class="space-y-4">
                            <div
                                v-for="cred in tp.credentials"
                                :key="cred.id"
                                class="rounded-lg bg-white/5 p-3"
                            >
                                <div
                                    class="flex items-start justify-between gap-2"
                                >
                                    <p class="text-sm font-medium text-white">
                                        {{ cred.credential_name }}
                                    </p>
                                    <PhStar
                                        v-if="cred.is_featured"
                                        :size="14"
                                        weight="fill"
                                        class="shrink-0 text-amber-400"
                                    />
                                </div>
                                <p
                                    v-if="cred.issuer_name"
                                    class="mt-0.5 text-xs text-white/50"
                                >
                                    {{ cred.issuer_name }}
                                </p>
                                <a
                                    v-if="cred.credential_url"
                                    :href="cred.credential_url"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="mt-1 inline-block text-xs text-indigo-400 hover:underline"
                                >
                                    Ver credencial →
                                </a>
                            </div>
                        </div>
                    </StCardGlass>
                </div>
            </template>

            <!-- NOT FOUND -->
            <div v-else class="py-32 text-center">
                <div class="mb-4 text-6xl">🔒</div>
                <h2 class="text-xl font-bold text-white">
                    Talent Pass no encontrado
                </h2>
                <p class="mt-2 text-white/50">
                    Este perfil no existe o ya no es público.
                </p>
            </div>

            <!-- FOOTER -->
            <footer
                class="border-t border-white/10 pt-8 text-center text-xs text-white/30"
            >
                Perfil generado con
                <span class="text-indigo-400">Stratos</span> · Talent Pass CV
                2.0
            </footer>
        </div>
    </div>
</template>
