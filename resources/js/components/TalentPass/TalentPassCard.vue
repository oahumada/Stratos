<script setup lang="ts">
import StBadgeGlass from '@/components/StBadgeGlass.vue';
import StCardGlass from '@/components/StCardGlass.vue';
import type { TalentPass } from '@/types/talentPass';
import { Link } from '@inertiajs/vue3';
import { PhArrowRight, PhCheckCircle } from '@phosphor-icons/vue';
import { computed } from 'vue';

interface Props {
    talentPass: TalentPass;
    compact?: boolean;
}

withDefaults(defineProps<Props>(), {
    compact: false,
});

// Computed
const completeness = computed(() => talentPass.completeness || 0);
const statusColor = computed(() => {
    switch (talentPass.status) {
        case 'published':
            return 'bg-green-500/20 text-green-300';
        case 'draft':
            return 'bg-amber-500/20 text-amber-300';
        case 'archived':
            return 'bg-slate-500/20 text-slate-300';
        default:
            return 'bg-slate-500/20 text-slate-300';
    }
});
</script>

<template>
    <StCardGlass
        class="group overflow-hidden transition hover:border-indigo-500/50"
    >
        <Link
            :href="`/talent-pass/${talentPass.id}`"
            class="block p-6 transition hover:bg-white/5"
        >
            <!-- Header -->
            <div class="mb-3 flex items-start justify-between">
                <h3
                    class="line-clamp-2 text-xl font-bold text-white transition group-hover:text-indigo-300"
                >
                    {{ talentPass.title }}
                </h3>
                <StBadgeGlass :class="statusColor" class="ml-2 flex-shrink-0">
                    {{ talentPass.status }}
                </StBadgeGlass>
            </div>

            <!-- Person Name -->
            <p class="mb-3 text-xs text-slate-400">
                {{ talentPass.people?.name || 'N/A' }}
            </p>

            <!-- Summary -->
            <p
                v-if="!compact && talentPass.summary"
                class="mb-4 line-clamp-2 text-sm text-slate-300"
            >
                {{ talentPass.summary }}
            </p>

            <!-- Completeness Bar -->
            <div class="mb-4">
                <div class="mb-2 flex items-center justify-between">
                    <span
                        class="flex items-center gap-1 text-xs text-slate-400"
                    >
                        <PhCheckCircle :size="12" />
                        Completitud
                    </span>
                    <span class="text-xs font-bold text-indigo-400"
                        >{{ completeness }}%</span
                    >
                </div>
                <div class="h-2 overflow-hidden rounded-full bg-white/5">
                    <div
                        class="h-full bg-gradient-to-r from-indigo-500 to-purple-500 transition-all"
                        :style="{ width: `${completeness}%` }"
                    ></div>
                </div>
            </div>

            <!-- Stats -->
            <div
                v-if="!compact"
                class="mb-4 flex gap-4 border-b border-white/10 pb-4 text-xs text-slate-400"
            >
                <span>{{ talentPass.skills_count || 0 }} Skills</span>
                <span>{{ talentPass.experiences_count || 0 }} XP</span>
                <span>{{ talentPass.credentials_count || 0 }} Creds</span>
            </div>

            <!-- Footer CTA -->
            <div
                class="flex items-center gap-2 text-sm font-semibold text-indigo-400 transition group-hover:text-indigo-300"
            >
                View Profile
                <PhArrowRight
                    :size="14"
                    class="transition group-hover:translate-x-1"
                />
            </div>
        </Link>
    </StCardGlass>
</template>
