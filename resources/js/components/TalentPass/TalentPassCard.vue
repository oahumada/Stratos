<script setup lang="ts">
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { PhArrowRight, PhCheckCircle } from '@phosphor-icons/vue';
import StCardGlass from '@/components/StCardGlass.vue';
import StBadgeGlass from '@/components/StBadgeGlass.vue';
import type { TalentPass } from '@/types/talentPass';

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
    <StCardGlass class="group overflow-hidden hover:border-indigo-500/50 transition">
        <Link :href="`/talent-pass/${talentPass.id}`" class="block p-6 hover:bg-white/5 transition">
            <!-- Header -->
            <div class="flex items-start justify-between mb-3">
                <h3 class="text-xl font-bold text-white group-hover:text-indigo-300 transition line-clamp-2">
                    {{ talentPass.title }}
                </h3>
                <StBadgeGlass :class="statusColor" class="ml-2 flex-shrink-0">
                    {{ talentPass.status }}
                </StBadgeGlass>
            </div>

            <!-- Person Name -->
            <p class="text-xs text-slate-400 mb-3">
                {{ talentPass.people?.name || 'N/A' }}
            </p>

            <!-- Summary -->
            <p v-if="!compact && talentPass.summary" class="text-sm text-slate-300 line-clamp-2 mb-4">
                {{ talentPass.summary }}
            </p>

            <!-- Completeness Bar -->
            <div class="mb-4">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-xs text-slate-400 flex items-center gap-1">
                        <PhCheckCircle :size="12" />
                        Completitud
                    </span>
                    <span class="text-xs font-bold text-indigo-400">{{ completeness }}%</span>
                </div>
                <div class="h-2 rounded-full bg-white/5 overflow-hidden">
                    <div
                        class="h-full bg-gradient-to-r from-indigo-500 to-purple-500 transition-all"
                        :style="{ width: `${completeness}%` }"
                    ></div>
                </div>
            </div>

            <!-- Stats -->
            <div v-if="!compact" class="flex gap-4 text-xs text-slate-400 mb-4 pb-4 border-b border-white/10">
                <span>{{ talentPass.skills_count || 0 }} Skills</span>
                <span>{{ talentPass.experiences_count || 0 }} XP</span>
                <span>{{ talentPass.credentials_count || 0 }} Creds</span>
            </div>

            <!-- Footer CTA -->
            <div class="flex items-center gap-2 text-indigo-400 group-hover:text-indigo-300 transition text-sm font-semibold">
                View Profile
                <PhArrowRight :size="14" class="group-hover:translate-x-1 transition" />
            </div>
        </Link>
    </StCardGlass>
</template>
