<template>
    <div class="space-y-6">
        <!-- Header -->
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                Approval Matrix
            </h2>
            <p class="mt-1 text-gray-600 dark:text-gray-400">
                Stakeholders required to approve this scenario
            </p>
        </div>

        <!-- Status Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="rounded-lg bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/30 dark:to-blue-800/30 p-4 border border-blue-200 dark:border-blue-700">
                <div class="text-sm font-medium text-blue-600 dark:text-blue-300">Required</div>
                <div class="mt-2 text-3xl font-bold text-blue-900 dark:text-blue-100">
                    {{ matrix.required_approvals }}
                </div>
            </div>

            <div class="rounded-lg bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/30 dark:to-green-800/30 p-4 border border-green-200 dark:border-green-700">
                <div class="text-sm font-medium text-green-600 dark:text-green-300">Approved ✓</div>
                <div class="mt-2 text-3xl font-bold text-green-900 dark:text-green-100">
                    {{ matrix.approvals_complete }}
                </div>
            </div>

            <div class="rounded-lg bg-gradient-to-br from-amber-50 to-amber-100 dark:from-amber-900/30 dark:to-amber-800/30 p-4 border border-amber-200 dark:border-amber-700">
                <div class="text-sm font-medium text-amber-600 dark:text-amber-300">Pending</div>
                <div class="mt-2 text-3xl font-bold text-amber-900 dark:text-amber-100">
                    {{ matrix.approvals_pending }}
                </div>
            </div>

            <div class="rounded-lg bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900/30 dark:to-red-800/30 p-4 border border-red-200 dark:border-red-700">
                <div class="text-sm font-medium text-red-600 dark:text-red-300">Rejected ✗</div>
                <div class="mt-2 text-3xl font-bold text-red-900 dark:text-red-100">
                    {{ matrix.approvals_rejected }}
                </div>
            </div>
        </div>

        <!-- Approval Progress Bar -->
        <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-6 bg-white dark:bg-gray-900">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-900 dark:text-white">Overall Progress</h3>
                <span class="text-sm font-medium text-gray-600 dark:text-gray-400">
                    {{ matrix.approvals_complete }}/{{ matrix.required_approvals }} Approved
                </span>
            </div>
            
            <div class="relative w-full h-3 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                <div
                    class="h-full bg-gradient-to-r from-emerald-400 to-emerald-600 transition-all duration-500"
                    :style="{ width: `${progressPercentage}%` }"
                />
            </div>

            <div class="mt-4 text-center">
                <span class="text-lg font-bold text-emerald-600 dark:text-emerald-400">
                    {{ progressPercentage }}%
                </span>
                <span class="text-sm text-gray-600 dark:text-gray-400 ml-2">complete</span>
            </div>
        </div>

        <!-- Approvers List -->
        <div class="space-y-3">
            <h3 class="font-semibold text-gray-900 dark:text-white">Approvers</h3>
            
            <div v-if="matrix.approvers.length === 0" class="text-center py-8">
                <div class="text-gray-500 dark:text-gray-400">No approvers found</div>
            </div>

            <div
                v-for="approver in matrix.approvers"
                :key="approver.id"
                class="rounded-lg border border-gray-200 dark:border-gray-700 p-4 bg-white dark:bg-gray-900 hover:shadow-md transition-shadow"
            >
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <!-- Status Badge -->
                            <span
                                :class="[
                                    'px-3 py-1 rounded-full text-xs font-medium text-white',
                                    approver.status === 'approved'
                                        ? 'bg-green-500'
                                        : approver.status === 'rejected'
                                        ? 'bg-red-500'
                                        : 'bg-amber-500'
                                ]"
                            >
                                {{
                                    approver.status === 'approved'
                                        ? '✓ Approved'
                                        : approver.status === 'rejected'
                                        ? '✗ Rejected'
                                        : '⏳ Pending'
                                }}
                            </span>
                            
                            <h4 class="font-semibold text-gray-900 dark:text-white">
                                {{ approver.approver_name }}
                            </h4>
                        </div>

                        <!-- Approver Details -->
                        <div class="text-sm text-gray-600 dark:text-gray-400 space-y-1 ml-10">
                            <p>
                                <span class="font-medium">Approver ID:</span>
                                {{ approver.approver_id }}
                            </p>
                            <p v-if="approver.approved_at">
                                <span class="font-medium">Approved:</span>
                                {{ formatDate(approver.approved_at) }}
                            </p>
                            <p v-if="approver.status === 'pending'">
                                <span class="font-medium">Awaiting Action...</span>
                            </p>
                        </div>
                    </div>

                    <!-- Action Button for Pending -->
                    <div v-if="approver.status === 'pending' && isCurrentUserApprover(approver)" class="ml-4">
                        <button
                            @click="copyApprovalLink(approver)"
                            class="text-sm px-3 py-2 rounded-lg bg-blue-100 hover:bg-blue-200 text-blue-700 dark:bg-blue-900/30 dark:hover:bg-blue-900/50 dark:text-blue-400 transition font-medium"
                        >
                            Copy Link
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Decision Status -->
        <div class="rounded-lg border-l-4 border-emerald-500 bg-emerald-50 dark:bg-emerald-900/20 p-4">
            <p class="text-sm text-emerald-800 dark:text-emerald-200">
                <span class="font-semibold">Status:</span>
                {{ matrix.decision_status }}
                <span v-if="allApproved" class="ml-2 text-emerald-600 dark:text-emerald-400">
                    — Ready to Activate
                </span>
            </p>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'

interface Approver {
    id: number
    approver_id: number
    approver_name: string
    status: 'pending' | 'approved' | 'rejected'
    approved_at?: string
    token?: string
}

interface Matrix {
    scenario_id: number
    decision_status: string
    approvers: Approver[]
    required_approvals: number
    approvals_complete: number
    approvals_pending: number
    approvals_rejected: number
}

const props = defineProps<{
    scenarioId: number
}>()

const matrix = ref<Matrix>({
    scenario_id: props.scenarioId,
    decision_status: 'draft',
    approvers: [],
    required_approvals: 0,
    approvals_complete: 0,
    approvals_pending: 0,
    approvals_rejected: 0,
})

const progressPercentage = computed(() => {
    if (matrix.value.required_approvals === 0) return 0
    return Math.round((matrix.value.approvals_complete / matrix.value.required_approvals) * 100)
})

const allApproved = computed(() => {
    return matrix.value.approvals_pending === 0 && matrix.value.approvals_complete > 0
})

onMounted(async () => {
    try {
        const response = await fetch(`/api/scenarios/${props.scenarioId}/approval-matrix`)
        const data = await response.json()
        if (data.status === 'success') {
            matrix.value = data
        }
    } catch (error) {
        console.error('Error loading approval matrix:', error)
    }
})

const formatDate = (date: string): string => {
    return new Date(date).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    })
}

const isCurrentUserApprover = (approver: Approver): boolean => {
    // In real implementation, compare with auth user ID
    return false
}

const copyApprovalLink = (approver: Approver) => {
    if (approver.token) {
        const link = `${window.location.origin}/approve/scenario/${approver.token}`
        navigator.clipboard.writeText(link)
        // Show toast notification
        console.log('Link copied to clipboard')
    }
}
</script>

<style scoped>
/* Smooth transitions for status badges */
span[class*='bg-green'], span[class*='bg-red'], span[class*='bg-amber'] {
    transition: all 0.2s ease;
}
</style>
