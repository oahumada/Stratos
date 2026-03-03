<script setup lang="ts">
import axios from 'axios';
import { onMounted, ref, watch } from 'vue';

interface Suggestion {
    icon: string;
    title: string;
    description: string;
    action: string;
}

interface ProactiveTip {
    type: string;
    message: string;
    action: string;
}

interface OnboardingStep {
    step: string;
    title: string;
    order: number;
}

interface GuideData {
    module: string;
    suggestions: Suggestion[];
    proactive_tips: ProactiveTip[];
    quick_actions: Array<{ label: string; route: string; icon: string }>;
    onboarding_step: OnboardingStep | null;
}

const props = defineProps<{
    currentModule?: string;
}>();

const isOpen = ref(false);
const loading = ref(false);
const loadingAnswer = ref(false);
const guideData = ref<GuideData | null>(null);
const question = ref('');
const answer = ref<{ answer: string; next_action?: string | null } | null>(
    null,
);

const pulseAnimation = ref(true);

async function loadSuggestions() {
    const mod = props.currentModule || 'dashboard';
    loading.value = true;
    try {
        const { data } = await axios.get('/api/guide/suggestions', {
            params: { module: mod },
        });
        guideData.value = data.data ?? data;
    } catch {
        // Fail silently for guide
    } finally {
        loading.value = false;
    }
}

async function askQuestion() {
    if (!question.value.trim()) return;
    loadingAnswer.value = true;
    answer.value = null;
    try {
        const { data } = await axios.post('/api/guide/ask', {
            question: question.value,
            module: props.currentModule || 'dashboard',
        });
        answer.value = data.data ?? data;
    } catch {
        answer.value = {
            answer: 'No pude procesar tu pregunta. Intenta reformularla.',
        };
    } finally {
        loadingAnswer.value = false;
    }
}

async function completeOnboarding(step: string) {
    try {
        await axios.post('/api/guide/onboarding/complete', {
            module: props.currentModule || 'dashboard',
            step,
        });
        await loadSuggestions(); // Refresh
    } catch {
        // Ignore
    }
}

function toggle() {
    isOpen.value = !isOpen.value;
    pulseAnimation.value = false;
    if (isOpen.value && !guideData.value) {
        loadSuggestions();
    }
}

watch(
    () => props.currentModule,
    () => {
        if (isOpen.value) loadSuggestions();
    },
);

onMounted(() => {
    // Show pulse after 5 seconds to draw attention
    setTimeout(() => {
        if (!isOpen.value) pulseAnimation.value = true;
    }, 5000);
});
</script>

<template>
    <!-- Floating Action Button -->
    <div class="fixed right-6 bottom-6 z-50">
        <!-- Guide Panel -->
        <Transition name="guide-panel">
            <div
                v-if="isOpen"
                class="absolute right-0 bottom-16 max-h-[70vh] w-[380px] overflow-y-auto rounded-2xl border border-white/10 bg-slate-900/95 shadow-2xl shadow-black/40 backdrop-blur-2xl"
            >
                <!-- Panel Header -->
                <div
                    class="sticky top-0 z-10 flex items-center justify-between rounded-t-2xl border-b border-white/10 bg-slate-900/90 p-4 backdrop-blur-xl"
                >
                    <div class="flex items-center gap-2">
                        <div
                            class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 text-sm"
                        >
                            🧭
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-white">
                                Stratos Guide
                            </h3>
                            <p class="text-[0.6rem] text-white/40">
                                Tu asistente contextual
                            </p>
                        </div>
                    </div>
                    <button
                        @click="isOpen = false"
                        class="text-lg text-white/30 hover:text-white/60"
                    >
                        ✕
                    </button>
                </div>

                <div class="space-y-4 p-4">
                    <!-- Onboarding Step -->
                    <div
                        v-if="guideData?.onboarding_step"
                        class="rounded-xl border border-indigo-500/30 bg-indigo-500/10 p-3"
                    >
                        <div class="mb-1 flex items-center gap-2">
                            <span class="text-xs">📍</span>
                            <p
                                class="text-[0.6rem] font-bold tracking-widest text-indigo-300 uppercase"
                            >
                                Paso {{ guideData.onboarding_step.order }}
                            </p>
                        </div>
                        <p class="text-sm text-white/80">
                            {{ guideData.onboarding_step.title }}
                        </p>
                        <button
                            @click="
                                completeOnboarding(
                                    guideData!.onboarding_step!.step,
                                )
                            "
                            class="mt-2 text-xs text-indigo-300 underline underline-offset-2 hover:text-indigo-200"
                        >
                            Marcar como completado ✓
                        </button>
                    </div>

                    <!-- Proactive Tips -->
                    <div
                        v-if="guideData?.proactive_tips?.length"
                        class="space-y-2"
                    >
                        <div
                            v-for="(tip, i) in guideData.proactive_tips"
                            :key="i"
                            class="rounded-xl border border-amber-500/20 bg-amber-500/5 p-3"
                        >
                            <div class="flex items-start gap-2">
                                <span class="mt-0.5 text-sm">{{
                                    tip.type === 'nudge' ? '💡' : '✨'
                                }}</span>
                                <p class="text-xs text-white/70">
                                    {{ tip.message }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Suggestions -->
                    <div v-if="guideData?.suggestions?.length">
                        <p
                            class="mb-2 text-[0.6rem] font-bold tracking-widest text-white/30 uppercase"
                        >
                            Sugerencias
                        </p>
                        <div class="space-y-2">
                            <button
                                v-for="(sug, i) in guideData.suggestions"
                                :key="i"
                                class="w-full rounded-xl border border-white/8 bg-white/3 p-3 text-left transition-all duration-200 hover:border-white/15 hover:bg-white/6"
                            >
                                <div class="flex items-start gap-3">
                                    <span class="mt-0.5 text-base opacity-60">{{
                                        sug.icon.startsWith('ph-')
                                            ? '📌'
                                            : sug.icon
                                    }}</span>
                                    <div>
                                        <p
                                            class="text-sm font-semibold text-white/80"
                                        >
                                            {{ sug.title }}
                                        </p>
                                        <p
                                            class="mt-0.5 text-[0.65rem] text-white/40"
                                        >
                                            {{ sug.description }}
                                        </p>
                                    </div>
                                </div>
                            </button>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div v-if="guideData?.quick_actions?.length">
                        <p
                            class="mb-2 text-[0.6rem] font-bold tracking-widest text-white/30 uppercase"
                        >
                            Acciones Rápidas
                        </p>
                        <div class="flex flex-wrap gap-2">
                            <a
                                v-for="(action, i) in guideData.quick_actions"
                                :key="i"
                                :href="action.route"
                                class="inline-flex items-center gap-1.5 rounded-lg border border-indigo-500/20 bg-indigo-500/10 px-3 py-1.5 text-xs text-indigo-300 transition-all hover:bg-indigo-500/20"
                            >
                                {{ action.label }}
                            </a>
                        </div>
                    </div>

                    <!-- Divider -->
                    <div class="border-t border-white/5"></div>

                    <!-- Ask Section -->
                    <div>
                        <p
                            class="mb-2 text-[0.6rem] font-bold tracking-widest text-white/30 uppercase"
                        >
                            Pregunta al Guide
                        </p>
                        <div class="flex gap-2">
                            <input
                                v-model="question"
                                @keyup.enter="askQuestion"
                                placeholder="¿Cómo creo un escenario?"
                                class="flex-1 rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-sm text-white placeholder-white/20 focus:border-indigo-500/40 focus:outline-none"
                            />
                            <button
                                @click="askQuestion"
                                :disabled="loadingAnswer || !question.trim()"
                                class="rounded-lg bg-indigo-600 px-3 py-2 text-sm font-bold text-white transition-all hover:bg-indigo-500 disabled:opacity-40"
                            >
                                {{ loadingAnswer ? '...' : '→' }}
                            </button>
                        </div>

                        <Transition name="fade-in">
                            <div
                                v-if="answer"
                                class="mt-3 rounded-xl border border-white/10 bg-white/5 p-3"
                            >
                                <p
                                    class="text-sm whitespace-pre-line text-white/80"
                                >
                                    {{ answer.answer }}
                                </p>
                                <p
                                    v-if="answer.next_action"
                                    class="mt-2 text-xs text-indigo-300"
                                >
                                    → {{ answer.next_action }}
                                </p>
                            </div>
                        </Transition>
                    </div>
                </div>
            </div>
        </Transition>

        <!-- FAB Button -->
        <button
            @click="toggle"
            class="relative flex h-14 w-14 items-center justify-center rounded-full bg-gradient-to-br from-indigo-600 to-purple-600 text-white shadow-2xl shadow-indigo-500/30 transition-all duration-300 hover:scale-110 hover:shadow-indigo-500/50 active:scale-95"
            :class="isOpen ? 'ring-2 ring-indigo-400/40' : ''"
        >
            <span class="text-xl">{{ isOpen ? '✕' : '🧭' }}</span>

            <!-- Pulse ring -->
            <span
                v-if="pulseAnimation && !isOpen"
                class="absolute inset-0 animate-ping rounded-full bg-indigo-500 opacity-20"
            ></span>

            <!-- Notification dot -->
            <span
                v-if="guideData?.proactive_tips?.length && !isOpen"
                class="absolute -top-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full border-2 border-slate-900 bg-amber-500 text-[0.5rem] font-bold"
            >
                {{ guideData.proactive_tips.length }}
            </span>
        </button>
    </div>
</template>

<style scoped>
.guide-panel-enter-active,
.guide-panel-leave-active {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.guide-panel-enter-from,
.guide-panel-leave-to {
    opacity: 0;
    transform: translateY(12px) scale(0.95);
}
.fade-in-enter-active,
.fade-in-leave-active {
    transition: all 0.2s ease;
}
.fade-in-enter-from,
.fade-in-leave-to {
    opacity: 0;
}
</style>
