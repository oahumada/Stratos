<script setup lang="ts">
import { computed, toRefs } from 'vue';

interface Props {
    currentStep: number;
    totalSteps?: number;
    scenarioStatus?: string;
    decisionStatus?: string;
}

interface Step {
    id: number;
    title: string;
    description: string;
    icon: string;
    guardRules: string[];
    requiredFor: string[];
}

const props = withDefaults(defineProps<Props>(), {
    totalSteps: 7,
    scenarioStatus: 'active',
    decisionStatus: 'draft',
});

const { currentStep, totalSteps, scenarioStatus, decisionStatus } =
    toRefs(props);

const emit = defineEmits<{
    (e: 'update:currentStep', step: number): void;
    (e: 'stepClick', step: number): void;
}>();

const steps: Step[] = [
    {
        id: 1,
        title: 'Define Scenario',
        description: 'Name, scope, time horizon and strategic objectives',
        icon: 'mdi-target',
        guardRules: [
            'Must have a name',
            'Defined scope (org/dept/role)',
            'Time horizon > 0 weeks',
        ],
        requiredFor: ['All subsequent steps depend on this foundation'],
    },
    {
        id: 2,
        title: 'Estimate Demand',
        description: 'Add roles and required skills with expected levels',
        icon: 'mdi-chart-line',
        guardRules: [
            'At least 1 skill demand added',
            'Competency levels defined',
        ],
        requiredFor: ['Calculating gaps (step 4)'],
    },
    {
        id: 3,
        title: 'Calculate Supply',
        description: 'Assess current availability of talent and capabilities',
        icon: 'mdi-account-group',
        guardRules: [
            'Demand defined (step 2)',
            'Supply is automatically calculated',
        ],
        requiredFor: ['Identifying gaps (step 4)'],
    },
    {
        id: 4,
        title: 'Identify Gaps',
        description: 'Detect headcount breaches and competency level gaps',
        icon: 'mdi-alert-circle-outline',
        guardRules: [
            'Supply calculated',
            'At least 1 gap detected to continue',
        ],
        requiredFor: ['Generating strategies (step 5)'],
    },
    {
        id: 5,
        title: 'Generate Strategies',
        description: 'Recommend actions: Buy, Build, Borrow, Bridge, Bind, Bot',
        icon: 'mdi-lightbulb-on-outline',
        guardRules: ['Gaps identified', 'Strategy preferences configured'],
        requiredFor: ['Approval and execution'],
    },
    {
        id: 6,
        title: 'Cross-Benchmark',
        description: 'Compare with previous versions and historical data',
        icon: 'mdi-compare-horizontal',
        guardRules: ['Strategies defined'],
        requiredFor: ['Final approval'],
    },
    {
        id: 7,
        title: 'Approve & Finalize',
        description: 'Transition status and begin neural implementation',
        icon: 'mdi-check-decagram-outline',
        guardRules: ['Execution status = planned before starting'],
        requiredFor: ['Completing scenario architecture'],
    },
];

const canNavigateToStep = (stepId: number): boolean => {
    if (stepId <= props.currentStep) return true;
    if (stepId > props.currentStep + 1) return false;

    // Custom logic per step if needed
    return true;
};

const handleStepClick = (stepId: number) => {
    if (canNavigateToStep(stepId)) {
        emit('stepClick', stepId);
        emit('update:currentStep', stepId);
    }
};

const activeStep = computed(() =>
    steps.find((s) => s.id === props.currentStep),
);
</script>

<template>
    <div class="scenario-stepper-container space-y-10">
        <!-- New Premium Glass Stepper -->
        <div class="relative">
            <!-- Progress Line -->
            <div class="absolute top-6 left-0 h-0.5 w-full bg-white/5">
                <div
                    class="h-full bg-gradient-to-r from-indigo-500 to-emerald-500 shadow-[0_0_15px_rgba(99,102,241,0.5)] transition-all duration-700"
                    :style="{
                        width:
                            ((currentStep - 1) / (steps.length - 1)) * 100 +
                            '%',
                    }"
                ></div>
            </div>

            <div class="relative flex justify-between">
                <div
                    v-for="step in steps"
                    :key="step.id"
                    class="group flex flex-col items-center gap-3"
                    :class="[
                        canNavigateToStep(step.id)
                            ? 'cursor-pointer'
                            : 'cursor-not-allowed opacity-40',
                        step.id <= currentStep ? 'is-active' : '',
                    ]"
                    @click="handleStepClick(step.id)"
                >
                    <!-- Step Node -->
                    <div
                        class="relative flex h-12 w-12 items-center justify-center rounded-2xl border-2 transition-all duration-500"
                        :class="[
                            step.id < currentStep
                                ? 'border-emerald-400 bg-emerald-500 shadow-[0_0_20px_rgba(16,185,129,0.4)]'
                                : step.id === currentStep
                                  ? 'scale-110 border-indigo-400 bg-indigo-600 shadow-[0_0_25px_rgba(99,102,241,0.6)]'
                                  : 'border-white/10 bg-black/40 hover:border-white/30',
                        ]"
                    >
                        <v-icon
                            :icon="
                                step.id < currentStep ? 'mdi-check' : step.icon
                            "
                            :color="
                                step.id <= currentStep ? 'white' : 'white/20'
                            "
                            size="20"
                            class="transition-transform duration-500 group-hover:scale-110"
                        />
                    </div>

                    <!-- Label -->
                    <div class="text-center">
                        <div
                            class="text-[10px] font-black tracking-widest uppercase transition-colors"
                            :class="
                                step.id === currentStep
                                    ? 'text-indigo-400'
                                    : 'text-white/20'
                            "
                        >
                            Step {{ step.id }}
                        </div>
                        <div
                            class="mt-1 text-xs font-black tracking-tight whitespace-nowrap transition-colors"
                            :class="
                                step.id <= currentStep
                                    ? 'text-white'
                                    : 'text-white/10'
                            "
                        >
                            {{ step.title }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step Specific Content & Controls -->
        <div class="step-window-animate">
            <transition name="fade-slide" mode="out-in">
                <div :key="currentStep" class="space-y-8">
                    <!-- Step Guardrails (Collapsible or subtle) -->
                    <div
                        v-if="activeStep"
                        class="grid grid-cols-1 gap-4 md:grid-cols-2"
                    >
                        <div
                            class="flex items-start gap-3 rounded-2xl border border-indigo-500/20 bg-indigo-500/5 p-4"
                        >
                            <v-icon
                                icon="mdi-shield-check-outline"
                                color="indigo-400"
                                size="20"
                                class="mt-1"
                            />
                            <div>
                                <div
                                    class="mb-1 text-[10px] font-black tracking-widest text-indigo-400 uppercase"
                                >
                                    Architecture Guardrails
                                </div>
                                <ul class="space-y-1">
                                    <li
                                        v-for="(
                                            rule, idx
                                        ) in activeStep.guardRules"
                                        :key="idx"
                                        class="flex items-center gap-2 text-[11px] font-medium text-white/50"
                                    >
                                        <div
                                            class="h-1 w-1 rounded-full bg-indigo-500"
                                        ></div>
                                        {{ rule }}
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div
                            class="flex items-start gap-3 rounded-2xl border border-amber-500/20 bg-amber-500/5 p-4"
                        >
                            <v-icon
                                icon="mdi-link-variant"
                                color="amber-400"
                                size="20"
                                class="mt-1"
                            />
                            <div>
                                <div
                                    class="mb-1 text-[10px] font-black tracking-widest text-amber-400 uppercase"
                                >
                                    Downstream Dependencies
                                </div>
                                <ul class="space-y-1">
                                    <li
                                        v-for="(
                                            req, idx
                                        ) in activeStep.requiredFor"
                                        :key="idx"
                                        class="flex items-center gap-2 text-[11px] font-medium text-white/50"
                                    >
                                        <div
                                            class="h-1 w-1 rounded-full bg-amber-500"
                                        ></div>
                                        {{ req }}
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Step Content Slot -->
                    <div class="step-content">
                        <slot
                            :name="`step-${currentStep}`"
                            :step="activeStep"
                        />
                    </div>

                    <!-- Navigation Footer -->
                    <div
                        class="flex items-center justify-between border-t border-white/10 pt-8"
                    >
                        <StButtonGlass
                            v-if="currentStep > 1"
                            variant="ghost"
                            icon="mdi-arrow-left"
                            @click="handleStepClick(currentStep - 1)"
                        >
                            Back to Step {{ currentStep - 1 }}
                        </StButtonGlass>
                        <div v-else></div>

                        <StButtonGlass
                            v-if="currentStep < totalSteps"
                            variant="primary"
                            icon="mdi-arrow-right"
                            icon-position="right"
                            :disabled="!canNavigateToStep(currentStep + 1)"
                            @click="handleStepClick(currentStep + 1)"
                        >
                            Proceed to {{ steps[currentStep].title }}
                        </StButtonGlass>
                        <StButtonGlass
                            v-else
                            variant="primary"
                            icon="mdi-check-decagram"
                            :disabled="
                                decisionStatus !== 'approved' &&
                                scenarioStatus !== 'completed'
                            "
                        >
                            Finalize Architecture
                        </StButtonGlass>
                    </div>
                </div>
            </transition>
        </div>
    </div>
</template>

<style scoped>
.scenario-stepper-container {
    width: 100%;
}

.fade-slide-enter-active,
.fade-slide-leave-active {
    transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
}
.fade-slide-enter-from {
    opacity: 0;
    transform: translateX(30px);
}
.fade-slide-leave-to {
    opacity: 0;
    transform: translateX(-30px);
}

.step-window-animate {
    perspective: 1000px;
}
</style>
