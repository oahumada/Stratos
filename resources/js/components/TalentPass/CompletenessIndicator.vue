<script setup lang="ts">
import { PhCheckCircle, PhWarning } from '@phosphor-icons/vue';
import { computed } from 'vue';

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
    if (completeness.value < 70)
        tips.push('Include at least 2 work experiences');
    if (completeness.value < 85)
        tips.push('Add professional certifications or credentials');
    if (completeness.value < 95)
        tips.push('Write a compelling professional summary');

    return tips;
});
</script>

<template>
    <div>
        <!-- Compact Mode -->
        <div v-if="compact" class="space-y-2">
            <div
                :class="completenessColor"
                class="inline-flex items-center gap-2 rounded-full px-3 py-1.5 text-xs font-bold"
            >
                <PhCheckCircle :size="14" />
                {{ completeness }}% - {{ completenessLevel }}
            </div>
            <div class="h-2 w-full overflow-hidden rounded-full bg-white/5">
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
                <h3
                    class="flex items-center gap-2 text-lg font-bold text-white"
                >
                    <PhCheckCircle :size="20" class="text-indigo-400" />
                    Profile Completeness
                </h3>
                <div
                    :class="completenessColor"
                    class="rounded-full px-3 py-1.5 text-sm font-bold"
                >
                    {{ completeness }}%
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="space-y-2">
                <div class="h-4 overflow-hidden rounded-full bg-white/5">
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
            <div
                v-if="suggestions.length > 0"
                class="rounded-lg border border-amber-500/30 bg-amber-500/10 p-4"
            >
                <div class="flex items-start gap-3">
                    <PhWarning
                        :size="18"
                        class="mt-0.5 flex-shrink-0 text-amber-400"
                    />
                    <div>
                        <p class="mb-2 text-xs font-semibold text-amber-300">
                            Next Steps:
                        </p>
                        <ul class="space-y-1 text-xs text-amber-300/80">
                            <li
                                v-for="(tip, idx) in suggestions"
                                :key="idx"
                                class="flex items-center gap-1"
                            >
                                <span
                                    class="h-1 w-1 rounded-full bg-amber-400"
                                ></span>
                                {{ tip }}
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Completion Breakdown -->
            <div
                v-if="completeness === 100"
                class="rounded-lg border border-emerald-500/30 bg-emerald-500/10 p-4"
            >
                <div class="flex items-center gap-3">
                    <PhCheckCircle :size="20" class="text-emerald-400" />
                    <div>
                        <p class="text-sm font-bold text-emerald-300">
                            Perfect! Your profile is complete.
                        </p>
                        <p class="text-xs text-emerald-300/80">
                            You're ready to share it with potential employers or
                            collaborators.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
