<template>
    <div
        :class="[
            'p-4 rounded-lg border transition-all',
            statusClasses,
        ]"
    >
        <!-- Header -->
        <div class="flex items-start justify-between mb-4">
            <div class="flex-1">
                <h4 class="font-semibold text-gray-900 dark:text-white">
                    {{ approverName }}
                </h4>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    {{ approverRole }}
                </p>
            </div>

            <!-- Status Badge -->
            <span
                :class="[
                    'px-3 py-1 rounded-full text-xs font-medium text-white ml-2 whitespace-nowrap',
                    statusBadgeColor,
                ]"
            >
                {{ formatStatus(status) }}
            </span>
        </div>

        <!-- Notes Section -->
        <div v-if="notes" class="mb-4 p-3 rounded bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700">
            <p class="text-xs text-gray-600 dark:text-gray-400 font-medium mb-1">Notes:</p>
            <p class="text-sm text-gray-700 dark:text-gray-300">{{ notes }}</p>
        </div>

        <!-- Timeline Info -->
        <div class="mb-4 space-y-2 text-sm">
            <div v-if="submittedAt" class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                <span class="text-gray-400 dark:text-gray-500">📅</span>
                <span>Submitted: {{ formatDateTime(submittedAt) }}</span>
            </div>
            <div v-if="respondedAt" class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                <span class="text-gray-400 dark:text-gray-500">⏱️</span>
                <span>{{ status === 'approved' ? 'Approved' : 'Reviewed' }}: {{ formatDateTime(respondedAt) }}</span>
            </div>
        </div>

        <!-- Actions -->
        <div v-if="status === 'pending' && !isViewOnly" class="flex gap-2 pt-4 border-t border-gray-200 dark:border-gray-700">
            <button
                @click="handleApprove"
                :disabled="isLoading"
                class="flex-1 px-3 py-2 bg-green-500 hover:bg-green-600 disabled:bg-gray-400 text-white text-sm font-medium rounded transition-colors"
            >
                {{ isLoading ? 'Processing...' : 'Approve' }}
            </button>
            <button
                @click="showRejectForm = !showRejectForm"
                :disabled="isLoading || showRejectForm"
                class="flex-1 px-3 py-2 bg-red-500 hover:bg-red-600 disabled:bg-gray-400 text-white text-sm font-medium rounded transition-colors"
            >
                {{ showRejectForm ? 'Cancel' : 'Reject' }}
            </button>
        </div>

        <!-- Reject Form -->
        <div v-if="showRejectForm && status === 'pending' && !isViewOnly" class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
            <textarea
                v-model="rejectionReason"
                placeholder="Please provide a reason for rejection (required)"
                class="w-full px-3 py-2 text-sm rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500 dark:focus:ring-red-400"
                rows="3"
            />
            <div class="mt-3 flex gap-2">
                <button
                    @click="handleReject"
                    :disabled="!rejectionReason.trim() || isLoading"
                    class="flex-1 px-3 py-2 bg-red-600 hover:bg-red-700 disabled:bg-gray-400 text-white text-sm font-medium rounded transition-colors"
                >
                    {{ isLoading ? 'Processing...' : 'Confirm Rejection' }}
                </button>
                <button
                    @click="showRejectForm = false"
                    class="flex-1 px-3 py-2 bg-gray-300 hover:bg-gray-400 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-white text-sm font-medium rounded transition-colors"
                >
                    Cancel
                </button>
            </div>
        </div>

        <!-- Error Message -->
        <div v-if="errorMessage" class="mt-4 p-3 rounded bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700">
            <p class="text-sm text-red-700 dark:text-red-300">{{ errorMessage }}</p>
        </div>

        <!-- Success Message -->
        <div v-if="successMessage" class="mt-4 p-3 rounded bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700">
            <p class="text-sm text-green-700 dark:text-green-300">{{ successMessage }}</p>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'

interface ApprovalRequest {
    id: number
    approver_name: string
    approver_role: string
    status: string
    notes?: string
    submitted_at?: string
    responded_at?: string
}

const props = withDefaults(
    defineProps<{
        approvalRequest: ApprovalRequest
        isViewOnly?: boolean
        onApprove?: (requestId: number, notes: string) => Promise<void>
        onReject?: (requestId: number, reason: string) => Promise<void>
    }>(),
    {
        isViewOnly: false,
    }
)

const emit = defineEmits<{
    updated: [status: string]
}>()

const showRejectForm = ref(false)
const rejectionReason = ref('')
const isLoading = ref(false)
const errorMessage = ref('')
const successMessage = ref('')

const approverName = computed(() => props.approvalRequest.approver_name)
const approverRole = computed(() => props.approvalRequest.approver_role)
const status = computed(() => props.approvalRequest.status)
const notes = computed(() => props.approvalRequest.notes)
const submittedAt = computed(() => props.approvalRequest.submitted_at)
const respondedAt = computed(() => props.approvalRequest.responded_at)

const statusClasses = computed(() => {
    const baseClasses = 'transition-all'
    if (status.value === 'approved') {
        return `${baseClasses} bg-green-50 dark:bg-green-900/20 border-green-300 dark:border-green-700`
    }
    if (status.value === 'pending') {
        return `${baseClasses} bg-yellow-50 dark:bg-yellow-900/20 border-yellow-300 dark:border-yellow-700`
    }
    if (status.value === 'rejected') {
        return `${baseClasses} bg-red-50 dark:bg-red-900/20 border-red-300 dark:border-red-700`
    }
    return `${baseClasses} bg-gray-50 dark:bg-gray-800 border-gray-200 dark:border-gray-700`
})

const statusBadgeColor = computed(() => {
    if (status.value === 'approved') return 'bg-green-500'
    if (status.value === 'pending') return 'bg-yellow-500'
    if (status.value === 'rejected') return 'bg-red-500'
    return 'bg-gray-400'
})

const formatStatus = (str: string): string => {
    return str
        .split('_')
        .map(word => word.charAt(0).toUpperCase() + word.slice(1))
        .join(' ')
}

const formatDateTime = (date: string | undefined): string => {
    if (!date) return 'Unknown date'
    return new Date(date).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    })
}

const handleApprove = async (): Promise<void> => {
    if (!props.onApprove) return

    isLoading.value = true
    errorMessage.value = ''
    successMessage.value = ''

    try {
        await props.onApprove(props.approvalRequest.id, '')
        successMessage.value = 'Approval submitted successfully'
        emit('updated', 'approved')
        setTimeout(() => {
            successMessage.value = ''
        }, 3000)
    } catch (error) {
        errorMessage.value = error instanceof Error ? error.message : 'Failed to approve request'
    } finally {
        isLoading.value = false
    }
}

const handleReject = async (): Promise<void> => {
    if (!props.onReject || !rejectionReason.value.trim()) return

    isLoading.value = true
    errorMessage.value = ''
    successMessage.value = ''

    try {
        await props.onReject(props.approvalRequest.id, rejectionReason.value)
        successMessage.value = 'Rejection submitted successfully'
        showRejectForm.value = false
        rejectionReason.value = ''
        emit('updated', 'rejected')
        setTimeout(() => {
            successMessage.value = ''
        }, 3000)
    } catch (error) {
        errorMessage.value = error instanceof Error ? error.message : 'Failed to reject request'
    } finally {
        isLoading.value = false
    }
}
</script>

<style scoped>
textarea:focus {
    outline: none;
}
</style>
