<script setup lang="ts">
import {
    PhCheckCircle,
    PhWarning,
    PhCrownSimple,
    PhInfo,
    PhX,
} from '@phosphor-icons/vue';
import { ref } from 'vue';

export interface Alert {
    id: string;
    title: string;
    message: string;
    severity: 'critical' | 'high' | 'medium' | 'low' | 'info';
    timestamp: string;
    dismissed?: boolean;
}

interface Props {
    alerts?: Alert[];
}

const props = withDefaults(defineProps<Props>(), {
    alerts: () => [],
});

const dismissedAlerts = ref<Set<string>>(new Set());

const visibleAlerts = () => {
    return props.alerts.filter((a) => !dismissedAlerts.value.has(a.id));
};

const dismissAlert = (id: string) => {
    dismissedAlerts.value.add(id);
};

const getSeverityColor = (severity: string) => {
    switch (severity) {
        case 'critical':
            return {
                bg: 'bg-red-50 dark:bg-red-950',
                border: 'border-red-300 dark:border-red-600',
                icon: 'text-red-600 dark:text-red-400',
                badge: 'bg-red-200 dark:bg-red-900 text-red-800 dark:text-red-200',
            };
        case 'high':
            return {
                bg: 'bg-orange-50 dark:bg-orange-950',
                border: 'border-orange-300 dark:border-orange-600',
                icon: 'text-orange-600 dark:text-orange-400',
                badge: 'bg-orange-200 dark:bg-orange-900 text-orange-800 dark:text-orange-200',
            };
        case 'medium':
            return {
                bg: 'bg-amber-50 dark:bg-amber-950',
                border: 'border-amber-300 dark:border-amber-600',
                icon: 'text-amber-600 dark:text-amber-400',
                badge: 'bg-amber-200 dark:bg-amber-900 text-amber-800 dark:text-amber-200',
            };
        case 'low':
            return {
                bg: 'bg-blue-50 dark:bg-blue-950',
                border: 'border-blue-300 dark:border-blue-600',
                icon: 'text-blue-600 dark:text-blue-400',
                badge: 'bg-blue-200 dark:bg-blue-900 text-blue-800 dark:text-blue-200',
            };
        default:
            return {
                bg: 'bg-cyan-50 dark:bg-cyan-950',
                border: 'border-cyan-300 dark:border-cyan-600',
                icon: 'text-cyan-600 dark:text-cyan-400',
                badge: 'bg-cyan-200 dark:bg-cyan-900 text-cyan-800 dark:text-cyan-200',
            };
    }
};

const getIcon = (severity: string) => {
    switch (severity) {
        case 'critical':
            return 'critical';
        case 'high':
            return 'warning';
        case 'medium':
            return 'warning';
        default:
            return 'info';
    }
};

const formatTime = (dateString: string) => {
    const date = new Date(dateString);
    const now = new Date();
    const diff = (now.getTime() - date.getTime()) / 1000;

    if (diff < 60) return 'Just now';
    if (diff < 3600) return `${Math.round(diff / 60)}m ago`;
    if (diff < 86400) return `${Math.round(diff / 3600)}h ago`;
    return date.toLocaleDateString();
};
</script>

<template>
    <div v-if="visibleAlerts().length === 0" class="text-center py-8">
        <PhCheckCircle
            weight="fill"
            class="mx-auto mb-3 text-green-600 dark:text-green-400"
            :size="32"
        />
        <p class="text-sm text-gray-600 dark:text-gray-400">All systems operating normally</p>
    </div>

    <div v-else class="space-y-3">
        <div
            v-for="alert in visibleAlerts()"
            :key="alert.id"
            :class="[
                'rounded-lg border-l-4 p-4 transition-all',
                getSeverityColor(alert.severity).bg,
                getSeverityColor(alert.severity).border,
            ]"
        >
            <div class="flex items-start justify-between gap-3">
                <!-- Icon -->
                <div class="flex-shrink-0 mt-0.5">
                    <PhCrownSimple
                        v-if="getIcon(alert.severity) === 'critical'"
                        weight="fill"
                        :class="getSeverityColor(alert.severity).icon"
                        :size="24"
                    />
                    <PhWarning
                        v-else-if="getIcon(alert.severity) === 'warning'"
                        weight="fill"
                        :class="getSeverityColor(alert.severity).icon"
                        :size="24"
                    />
                    <PhInfo
                        v-else
                        weight="fill"
                        :class="getSeverityColor(alert.severity).icon"
                        :size="24"
                    />
                </div>

                <!-- Content -->
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <h3 class="font-semibold text-gray-900 dark:text-white">
                            {{ alert.title }}
                        </h3>
                        <span
                            :class="[
                                'inline-block px-2 py-0.5 text-xs font-semibold rounded-full',
                                getSeverityColor(alert.severity).badge,
                            ]"
                        >
                            {{ alert.severity.toUpperCase() }}
                        </span>
                    </div>
                    <p class="mt-1 text-sm text-gray-700 dark:text-gray-300">
                        {{ alert.message }}
                    </p>
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                        {{ formatTime(alert.timestamp) }}
                    </p>
                </div>

                <!-- Dismiss button -->
                <button
                    @click="dismissAlert(alert.id)"
                    class="flex-shrink-0 p-1 rounded hover:bg-black/10 dark:hover:bg-white/10 transition-colors"
                    :aria-label="`Dismiss ${alert.title}`"
                >
                    <PhX :size="20" class="text-gray-500 dark:text-gray-400" />
                </button>
            </div>
        </div>
    </div>
</template>
