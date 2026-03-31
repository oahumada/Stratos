<template>
    <div class="space-y-6">
        <!-- Summary Stats -->
        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
            <div class="rounded-lg bg-white p-4 shadow dark:bg-gray-800">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Políticas Activas
                        </p>
                        <p
                            class="text-2xl font-bold text-gray-900 dark:text-white"
                        >
                            {{ activePolicies }}
                        </p>
                    </div>
                    <div class="text-3xl">✅</div>
                </div>
            </div>

            <div class="rounded-lg bg-white p-4 shadow dark:bg-gray-800">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Severidades Cubiertas
                        </p>
                        <p
                            class="text-2xl font-bold text-gray-900 dark:text-white"
                        >
                            {{ uniqueSeverities }}
                        </p>
                    </div>
                    <div class="text-3xl">📊</div>
                </div>
            </div>

            <div class="rounded-lg bg-white p-4 shadow dark:bg-gray-800">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Niveles Escalación
                        </p>
                        <p
                            class="text-2xl font-bold text-gray-900 dark:text-white"
                        >
                            {{ maxLevel }}
                        </p>
                    </div>
                    <div class="text-3xl">📈</div>
                </div>
            </div>

            <div class="rounded-lg bg-white p-4 shadow dark:bg-gray-800">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Email/Slack
                        </p>
                        <p
                            class="text-2xl font-bold text-gray-900 dark:text-white"
                        >
                            {{ notificationMethods }}
                        </p>
                    </div>
                    <div class="text-3xl">🔔</div>
                </div>
            </div>
        </div>

        <!-- Escalation Matrix by Severity -->
        <div class="space-y-4">
            <div v-for="severity in severityOrder" :key="severity">
                <div
                    :class="['rounded-lg p-6 shadow', getSeverityBg(severity)]"
                >
                    <!-- Severity Header -->
                    <div class="mb-4 flex items-center gap-3">
                        <span class="text-2xl">{{
                            getSeverityIcon(severity)
                        }}</span>
                        <div>
                            <h3 class="text-lg font-bold text-white">
                                {{ getSeverityLabel(severity) }}
                            </h3>
                            <p class="text-sm opacity-90">
                                Cadena de escalación
                            </p>
                        </div>
                    </div>

                    <!-- Escalation Chain -->
                    <div class="space-y-3">
                        <div
                            v-if="getPoliciesForSeverity(severity).length === 0"
                            class="bg-opacity-20 rounded bg-white py-4 text-center"
                        >
                            <p class="text-white opacity-70">
                                Sin políticas configuradas
                            </p>
                        </div>

                        <template v-else>
                            <div
                                v-for="(
                                    policy, index
                                ) in getPoliciesForSeverity(severity)"
                                :key="policy.id"
                                class="relative"
                            >
                                <!-- Connector Line -->
                                <div
                                    v-if="
                                        index <
                                        getPoliciesForSeverity(severity)
                                            .length -
                                            1
                                    "
                                    class="absolute top-12 left-6 h-16 w-0.5 bg-white opacity-30"
                                ></div>

                                <!-- Policy Card -->
                                <div
                                    class="bg-opacity-10 border-opacity-20 rounded-lg border border-white bg-white p-4 backdrop-blur"
                                >
                                    <div
                                        class="flex items-start justify-between"
                                    >
                                        <!-- Level Badge -->
                                        <div class="flex items-start gap-4">
                                            <div
                                                class="bg-opacity-20 flex h-12 w-12 items-center justify-center rounded-full border-2 border-white bg-white text-sm font-bold text-white"
                                            >
                                                {{ policy.escalation_level }}
                                            </div>
                                            <div>
                                                <!-- Timing -->
                                                <p
                                                    v-if="
                                                        policy.escalation_level ===
                                                        1
                                                    "
                                                    class="text-sm font-medium text-white"
                                                >
                                                    ⚡ Immediatamente
                                                </p>
                                                <p
                                                    v-else
                                                    class="text-sm font-medium text-white"
                                                >
                                                    ⏱️ Después de
                                                    {{ policy.delay_minutes }}
                                                    minutos
                                                </p>

                                                <!-- Recipients -->
                                                <div class="mt-2">
                                                    <p
                                                        class="text-xs font-semibold text-white opacity-70"
                                                    >
                                                        DESTINATARIOS:
                                                    </p>
                                                    <ul
                                                        class="mt-1 space-y-1 text-sm text-white"
                                                    >
                                                        <li
                                                            v-for="recipient in policy.recipients"
                                                            :key="recipient"
                                                            class="flex items-center gap-2"
                                                        >
                                                            <span
                                                                v-if="
                                                                    isEmailValid(
                                                                        recipient,
                                                                    )
                                                                "
                                                                class="text-yellow-200"
                                                                >📧</span
                                                            >
                                                            <span
                                                                v-else
                                                                class="text-blue-200"
                                                                >👤</span
                                                            >
                                                            {{ recipient }}
                                                        </li>
                                                    </ul>
                                                </div>

                                                <!-- Notification Methods -->
                                                <div class="mt-3 flex gap-2">
                                                    <span
                                                        v-if="
                                                            policy.hasEmailNotification
                                                        "
                                                        class="inline-block rounded bg-yellow-400 px-2 py-1 text-xs font-semibold text-yellow-900"
                                                    >
                                                        📧 Email
                                                    </span>
                                                    <span
                                                        v-if="
                                                            policy.hasSlackNotification
                                                        "
                                                        class="inline-block rounded bg-blue-400 px-2 py-1 text-xs font-semibold text-blue-900"
                                                    >
                                                        💬 Slack
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Actions -->
                                        <div class="flex items-center gap-1">
                                            <button
                                                @click="editPolicy(policy)"
                                                class="hover:bg-opacity-20 rounded p-2 text-white hover:bg-white"
                                                title="Editar"
                                            >
                                                ✏️
                                            </button>
                                            <button
                                                @click="deletePolicy(policy)"
                                                class="hover:bg-opacity-50 rounded p-2 text-white hover:bg-red-500"
                                                title="Eliminar"
                                            >
                                                🗑️
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Add Level Button -->
                    <button
                        @click="addLevel(severity)"
                        class="bg-opacity-20 hover:bg-opacity-30 border-opacity-30 mt-4 w-full rounded-lg border border-white bg-white px-4 py-2 font-medium text-white transition-all"
                    >
                        + Agregar Nivel
                    </button>
                </div>
            </div>
        </div>

        <!-- Legend -->
        <div class="rounded-lg bg-white p-4 shadow dark:bg-gray-800">
            <h4 class="mb-3 font-semibold text-gray-900 dark:text-white">
                Leyenda de Severidades
            </h4>
            <div class="grid grid-cols-2 gap-3 md:grid-cols-5">
                <div
                    v-for="sev in severityOrder"
                    :key="sev"
                    class="flex items-center gap-2"
                >
                    <span
                        :class="[
                            'h-3 w-3 rounded-full',
                            getSeverityBgSmall(sev),
                        ]"
                    ></span>
                    <span class="text-sm text-gray-700 dark:text-gray-300">{{
                        getSeverityLabel(sev)
                    }}</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import type { EscalationPolicy } from '@/types';
import { computed } from 'vue';

interface Props {
    policies?: EscalationPolicy[];
}

interface Emits {
    (e: 'edit', policy: EscalationPolicy): void;
    (e: 'delete', policy: EscalationPolicy): void;
    (e: 'addLevel', severity: string): void;
}

const props = withDefaults(defineProps<Props>(), {
    policies: () => [],
});

const emit = defineEmits<Emits>();

const severityOrder = ['critical', 'high', 'medium', 'low', 'info'];

const severityColors = {
    critical: 'bg-gradient-to-r from-red-600 to-red-700',
    high: 'bg-gradient-to-r from-orange-600 to-orange-700',
    medium: 'bg-gradient-to-r from-amber-600 to-amber-700',
    low: 'bg-gradient-to-r from-blue-600 to-blue-700',
    info: 'bg-gradient-to-r from-cyan-600 to-cyan-700',
};

const severityBgsSmall = {
    critical: 'bg-red-500',
    high: 'bg-orange-500',
    medium: 'bg-amber-500',
    low: 'bg-blue-500',
    info: 'bg-cyan-500',
};

const severityIcons = {
    critical: '🚨',
    high: '🔴',
    medium: '🟡',
    low: '🔵',
    info: 'ℹ️',
};

const severityLabels = {
    critical: 'Crítica',
    high: 'Alta',
    medium: 'Media',
    low: 'Baja',
    info: 'Información',
};

const activePolicies = computed(
    () => props.policies.filter((p) => p.is_active).length,
);

const uniqueSeverities = computed(
    () => new Set(props.policies.map((p) => p.severity)).size,
);

const maxLevel = computed(() =>
    props.policies.length > 0
        ? Math.max(...props.policies.map((p) => p.escalation_level))
        : 0,
);

const notificationMethods = computed(() => {
    const methods = new Set<string>();
    props.policies.forEach((p) => {
        if (p.notification_type.includes('email')) methods.add('Email');
        if (p.notification_type.includes('slack')) methods.add('Slack');
    });
    return `${methods.size}/2`;
});

const getSeverityBg = (severity: string) =>
    severityColors[severity as keyof typeof severityColors] || 'bg-gray-600';

const getSeverityBgSmall = (severity: string) =>
    severityBgsSmall[severity as keyof typeof severityBgsSmall] ||
    'bg-gray-500';

const getSeverityIcon = (severity: string) =>
    severityIcons[severity as keyof typeof severityIcons] || '❓';

const getSeverityLabel = (severity: string) =>
    severityLabels[severity as keyof typeof severityLabels] || severity;

const getPoliciesForSeverity = (severity: string) =>
    props.policies
        .filter((p) => p.severity === severity && p.is_active)
        .sort((a, b) => a.escalation_level - b.escalation_level);

const isEmailValid = (email: string) =>
    /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);

const editPolicy = (policy: EscalationPolicy) => {
    emit('edit', policy);
};

const deletePolicy = (policy: EscalationPolicy) => {
    emit('delete', policy);
};

const addLevel = (severity: string) => {
    emit('addLevel', severity);
};
</script>
