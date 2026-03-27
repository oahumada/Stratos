<script setup lang="ts">
import { computed } from 'vue';
import { PhCheckCircle, PhWarning } from '@phosphor-icons/vue';

interface Props {
    completeness: number;
    compact?: boolean;
}

withDefaults(defineProps<Props>(), {
    compact: false,
});

const completenessLevel = computed(() => {
    if (completeness.value >= 90) return 'Excellent';
    if (completeness.value >= 70) return 'Good';
    if (completeness.value >= 50) return 'Fair';
    if (completeness.value >= 30) return 'Incomplete';
    return 'Just Started';
});

const completenessColor = computed(() => {
    if (completeness.value >= 90) return 'text-emerald-400 bg-emerald-500/10';
    if (completeness.value >= 70) return 'text-blue-400 bg-blue-500/10';
    if (completeness.value >= 50) return 'text-indigo-400 bg-indigo-500/10';
    if (completeness.value >= 30) return 'text-amber-400 bg-amber-500/10';
    return 'text-slate-400 bg-slate-500/10';
});

const barColor = computed(() => {
    if (completeness.value >= 90) return 'from-emerald-500 to-green-500';
    if (completeness.value >= 70) return 'from-blue-500 to-cyan-500';
    if (completeness.value >= 50) return 'from-indigo-500 to-purple-500';
    if (completeness.value >= 30) return 'from-amber-500 to-orange-500';
    return 'from-slate-500 to-slate-400';
});

const suggestions = computed(() => {
    const tips: string[] = [];

    if (completeness.value < 50) tips.push('Add your top 5 key skills');
    if (completeness.value < 70) tips.push('Include at least 2 work experiences');
    if (completeness.value < 85) tips.push('Add professional certifications or credentials');
    if (completeness.value < 95) tips.push('Write a compelling professional summary');

    return tips;
});
</script>

<template>
    <div>
        <!-- Compact Mode -->
        <div v-if="compact" class="space-y-2">
            <div :class="completenessColor" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-bold">
                <PhCheckCircle :size="14" />
                {{ completeness }}% - {{ completenessLevel }}
            </div>
            <div class="h-2 w-full rounded-full bg-white/5 overflow-hidden">
                <div
                    :class="`bg-gradient-to-r ${barColor}`"
                    class="h-full transition-all duration-500"
                    :style="{ width: `${completeness}%` }"
                ></div>
            </div>
        </div>

        <!-- Full Mode -->
        <div v-else class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <PhCheckCircle :size="20" class="text-indigo-400" />
                    Profile Completeness
                </h3>
                <div :class="completenessColor" class="px-3 py-1.5 rounded-full text-sm font-bold">
                    {{ completeness }}%
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="space-y-2">
                <div class="h-4 rounded-full bg-white/5 overflow-hidden">
                    <div
                        :class="`bg-gradient-to-r ${barColor} shadow-lg`"
                        class="h-full transition-all duration-500"
                        :style="{ width: `${completeness}%` }"
                    ></div>
                </div>
                <p class="text-xs text-slate-400">
                    {{ completenessLevel }} - Keep adding content to reach 100%
                </p>
            </div>

            <!-- Suggestions -->
            <div v-if="suggestions.length > 0" class="p-4 rounded-lg bg-amber-500/10 border border-amber-500/30">
                <div class="flex items-start gap-3">
                    <PhWarning :size="18} class="text-amber-400 flex-shrink-0 mt-0.5" />
                    <div>
                        <p class="text-xs font-semibold text-amber-300 mb-2">Next Steps:</p>
                        <ul class="text-xs text-amber-300/80 space-y-1">
                            <li v-for="(tip, idx) in suggestions" :key="idx" class="flex items-center gap-1">
                                <span class="w-1 h-1 rounded-full bg-amber-400"></span>
                                {{ tip }}
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Completion Breakdown -->
            <div v-if="completeness === 100" class="p-4 rounded-lg bg-emerald-500/10 border border-emerald-500/30">
                <div class="flex items-center gap-3">
                    <PhCheckCircle :size="20} class="text-emerald-400" />
                    <div>
                        <p class="text-sm font-bold text-emerald-300">Perfect! Your profile is complete.</p>
                        <p class="text-xs text-emerald-300/80">
                            You're ready to share it with potential employers or collaborators.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
