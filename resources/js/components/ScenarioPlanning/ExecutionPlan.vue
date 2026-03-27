<template>
    <div class="space-y-6">
        <!-- Header -->
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                Execution Plan
            </h2>
            <p class="mt-1 text-gray-600 dark:text-gray-400">
                Structured phases and milestones for scenario implementation
            </p>
        </div>

        <!-- Progress Overview -->
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div class="p-4 rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700">
                <p class="text-xs text-blue-600 dark:text-blue-300 font-medium">PHASES</p>
                <p class="text-2xl font-bold text-blue-900 dark:text-blue-100 mt-1">
                    {{ phases.length }}
                </p>
            </div>
            <div class="p-4 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700">
                <p class="text-xs text-green-600 dark:text-green-300 font-medium">MILESTONES</p>
                <p class="text-2xl font-bold text-green-900 dark:text-green-100 mt-1">
                    {{ totalMilestones }}
                </p>
            </div>
            <div class="p-4 rounded-lg bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-700">
                <p class="text-xs text-purple-600 dark:text-purple-300 font-medium">TASKS</p>
                <p class="text-2xl font-bold text-purple-900 dark:text-purple-100 mt-1">
                    {{ totalTasks }}
                </p>
            </div>
            <div class="p-4 rounded-lg bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-700">
                <p class="text-xs text-orange-600 dark:text-orange-300 font-medium">TOTAL DURATION</p>
                <p class="text-2xl font-bold text-orange-900 dark:text-orange-100 mt-1">
                    {{ totalDuration }} days
                </p>
            </div>
        </div>

        <!-- Phases & Details -->
        <div class="space-y-4">
            <div
                v-for="(phase, phaseIndex) in phases"
                :key="phaseIndex"
                class="rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden"
            >
                <!-- Phase Header -->
                <button
                    @click="togglePhase(phaseIndex)"
                    class="w-full px-4 py-3 flex items-center justify-between bg-gray-50 hover:bg-gray-100 dark:bg-gray-800/50 dark:hover:bg-gray-800 transition-colors"
                >
                    <div class="flex items-center gap-3 flex-1">
                        <span
                            :class="[
                                'flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center font-bold text-white',
                                getPhaseColor(phaseIndex),
                            ]"
                        >
                            {{ phaseIndex + 1 }}
                        </span>
                        <div class="text-left">
                            <h3 class="font-semibold text-gray-900 dark:text-white">
                                {{ phase.name }}
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ phase.milestones.length }} milestones · {{ phase.duration }} days
                            </p>
                        </div>
                    </div>
                    <svg
                        :class="[
                            'w-5 h-5 text-gray-600 dark:text-gray-400 transition-transform',
                            expandedPhases.includes(phaseIndex) ? 'transform rotate-180' : '',
                        ]"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                    </svg>
                </button>

                <!-- Phase Content -->
                <div v-if="expandedPhases.includes(phaseIndex)" class="px-4 py-4 bg-gray-50 dark:bg-gray-900/20 space-y-4">
                    <!-- Phase Description -->
                    <p class="text-sm text-gray-700 dark:text-gray-300">
                        {{ phase.description }}
                    </p>

                    <!-- Milestones -->
                    <div class="space-y-3">
                        <h4 class="font-medium text-gray-900 dark:text-white text-sm">Milestones</h4>
                        <div
                            v-for="(milestone, milestoneIndex) in phase.milestones"
                            :key="milestoneIndex"
                            class="pl-6 pb-3 border-l-2 border-gray-300 dark:border-gray-600 last:pb-0"
                        >
                            <div class="flex items-start gap-3">
                                <span class="text-green-500 font-bold mt-0.5">✓</span>
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900 dark:text-white text-sm">
                                        {{ milestone.name }}
                                    </p>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                        {{ milestone.description }}
                                    </p>
                                    <p v-if="milestone.dueDate" class="text-xs text-gray-500 dark:text-gray-500 mt-1.5">
                                        Due: {{ formatDate(milestone.dueDate) }}
                                    </p>
                                </div>
                            </div>

                            <!-- Tasks under Milestone -->
                            <div v-if="milestone.tasks && milestone.tasks.length > 0" class="mt-3 ml-6 space-y-2">
                                <div
                                    v-for="(task, taskIndex) in milestone.tasks"
                                    :key="taskIndex"
                                    class="flex items-start gap-2 text-sm"
                                >
                                    <input
                                        type="checkbox"
                                        :checked="task.completed"
                                        @change="toggleTask(phaseIndex, milestoneIndex, taskIndex)"
                                        class="mt-0.5 w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-blue-500 focus:ring-blue-500 dark:focus:ring-blue-400 cursor-pointer"
                                    />
                                    <label class="flex-1 cursor-pointer">
                                        <span
                                            :class="[
                                                'text-gray-700 dark:text-gray-300',
                                                task.completed ? 'line-through text-gray-500 dark:text-gray-500' : '',
                                            ]"
                                        >
                                            {{ task.name }}
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Timeline Chart -->
        <div class="rounded-lg bg-gray-50 dark:bg-gray-900/20 border border-gray-200 dark:border-gray-700 p-4">
            <h4 class="font-semibold text-gray-900 dark:text-white mb-4">Timeline</h4>
            <div class="space-y-3">
                <div
                    v-for="(phase, index) in phases"
                    :key="index"
                    class="flex items-center gap-3"
                >
                    <div class="w-24 text-sm font-medium text-gray-700 dark:text-gray-300 truncate">
                        {{ phase.name }}
                    </div>
                    <div class="flex-1 h-2 rounded border border-gray-200 dark:border-gray-600 overflow-hidden">
                        <div
                            :style="{ width: `${(phase.duration / totalDuration) * 100}%` }"
                            :class="[
                                'h-full transition-all',
                                getPhaseBarColor(index),
                            ]"
                        />
                    </div>
                    <div class="w-12 text-right text-xs text-gray-600 dark:text-gray-400">
                        {{ phase.duration }}d
                    </div>
                </div>
            </div>
        </div>

        <!-- Legend -->
        <div class="rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 p-4">
            <h4 class="font-semibold text-blue-900 dark:text-blue-100 mb-3">Tips</h4>
            <ul class="space-y-2 text-sm text-blue-800 dark:text-blue-200">
                <li class="flex items-start gap-2">
                    <span class="flex-shrink-0">→</span>
                    <span>Click on phases to expand and view milestones and tasks</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="flex-shrink-0">→</span>
                    <span>Check off tasks as you complete them to track progress</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="flex-shrink-0">→</span>
                    <span>Timeline bar shows relative duration of each phase</span>
                </li>
            </ul>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'

interface Task {
    id: string
    name: string
    completed: boolean
}

interface Milestone {
    name: string
    description: string
    dueDate?: string
    tasks?: Task[]
}

interface Phase {
    name: string
    description: string
    duration: number
    milestones: Milestone[]
}

const props = defineProps<{
    executionPlan?: {
        phases?: Phase[]
    }
}>()

const expandedPhases = ref<number[]>([0])
const taskCompletion = ref<Map<string, boolean>>(new Map())

const phases = computed<Phase[]>(() => {
    if (props.executionPlan?.phases) {
        return props.executionPlan.phases
    }

    // Default execution plan structure
    return [
        {
            name: 'Phase 1: Planning & Assessment',
            description:
                'Conduct comprehensive analysis, identify stakeholders, and develop detailed implementation strategy.',
            duration: 14,
            milestones: [
                {
                    name: 'Stakeholder Identification',
                    description: 'Map all stakeholders and define their roles',
                    dueDate: '2026-04-10',
                    tasks: [
                        { id: '1-1', name: 'Create stakeholder matrix', completed: false },
                        { id: '1-2', name: 'Schedule kickoff meeting', completed: false },
                        { id: '1-3', name: 'Gather requirements', completed: false },
                    ],
                },
                {
                    name: 'Risk Assessment',
                    description: 'Identify potential risks and mitigations',
                    dueDate: '2026-04-14',
                    tasks: [
                        { id: '1-4', name: 'Document risk register', completed: false },
                        { id: '1-5', name: 'Define contingencies', completed: false },
                    ],
                },
            ],
        },
        {
            name: 'Phase 2: Resource Allocation',
            description: 'Allocate talent, budget, and tools required for execution.',
            duration: 21,
            milestones: [
                {
                    name: 'Team Assembly',
                    description: 'Recruit and onboard core team members',
                    dueDate: '2026-04-21',
                    tasks: [
                        { id: '2-1', name: 'Post job descriptions', completed: false },
                        { id: '2-2', name: 'Conduct interviews', completed: false },
                        { id: '2-3', name: 'Complete onboarding', completed: false },
                    ],
                },
                {
                    name: 'Budget Approval',
                    description: 'Secure financial resources',
                    dueDate: '2026-04-28',
                    tasks: [
                        { id: '2-4', name: 'Prepare budget proposal', completed: false },
                        { id: '2-5', name: 'Get stakeholder sign-off', completed: false },
                    ],
                },
            ],
        },
        {
            name: 'Phase 3: Implementation',
            description: 'Execute core activities and deliverables according to plan.',
            duration: 30,
            milestones: [
                {
                    name: 'Systems Setup',
                    description: 'Configure tools and infrastructure',
                    dueDate: '2026-05-18',
                    tasks: [
                        { id: '3-1', name: 'Install systems', completed: false },
                        { id: '3-2', name: 'Configure environments', completed: false },
                        { id: '3-3', name: 'Run integration tests', completed: false },
                    ],
                },
                {
                    name: 'Training & Enablement',
                    description: 'Prepare teams for new processes',
                    dueDate: '2026-05-25',
                    tasks: [
                        { id: '3-4', name: 'Develop training materials', completed: false },
                        { id: '3-5', name: 'Conduct training sessions', completed: false },
                    ],
                },
            ],
        },
        {
            name: 'Phase 4: Monitoring & Optimization',
            description: 'Track performance metrics and optimize based on feedback.',
            duration: 14,
            milestones: [
                {
                    name: 'Metrics Collection',
                    description: 'Establish KPI tracking',
                    dueDate: '2026-06-08',
                    tasks: [
                        { id: '4-1', name: 'Setup dashboards', completed: false },
                        { id: '4-2', name: 'Define reporting cadence', completed: false },
                    ],
                },
            ],
        },
    ]
})

const totalMilestones = computed(() => {
    return phases.value.reduce((sum, phase) => sum + phase.milestones.length, 0)
})

const totalTasks = computed(() => {
    return phases.value.reduce((sum, phase) => {
        return (
            sum +
            phase.milestones.reduce((milestoneSum, milestone) => {
                return milestoneSum + (milestone.tasks?.length || 0)
            }, 0)
        )
    }, 0)
})

const totalDuration = computed(() => {
    return phases.value.reduce((sum, phase) => sum + phase.duration, 0)
})

const togglePhase = (index: number): void => {
    const idx = expandedPhases.value.indexOf(index)
    if (idx > -1) {
        expandedPhases.value.splice(idx, 1)
    } else {
        expandedPhases.value.push(index)
    }
}

const toggleTask = (phaseIndex: number, milestoneIndex: number, taskIndex: number): void => {
    const task = phases.value[phaseIndex].milestones[milestoneIndex].tasks?.[taskIndex]
    if (task) {
        task.completed = !task.completed
    }
}

const getPhaseColor = (index: number): string => {
    const colors = ['bg-blue-500', 'bg-green-500', 'bg-purple-500', 'bg-orange-500']
    return colors[index % colors.length]
}

const getPhaseBarColor = (index: number): string => {
    const colors = ['bg-blue-300', 'bg-green-300', 'bg-purple-300', 'bg-orange-300']
    return colors[index % colors.length]
}

const formatDate = (date: string): string => {
    return new Date(date).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    })
}
</script>

<style scoped>
input[type='checkbox']:focus {
    outline: none;
}
</style>
