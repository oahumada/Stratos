<template>
    <div class="notification-center">
        <!-- Filters -->
        <div class="mb-4 flex gap-3">
            <select
                v-model="filters.type"
                class="rounded-lg border border-border bg-card px-3 py-2 text-sm"
            >
                <option value="">Todos los tipos</option>
                <option value="phase_transition">Transición de fase</option>
                <option value="alert_threshold">Alerta de umbral</option>
                <option value="violation_detected">Violación detectada</option>
            </select>
            <select
                v-model="filters.severity"
                class="rounded-lg border border-border bg-card px-3 py-2 text-sm"
            >
                <option value="">Todas las severidades</option>
                <option value="info">ℹ️ Información</option>
                <option value="warning">⚠️ Advertencia</option>
                <option value="critical">🔴 Crítico</option>
            </select>
            <select
                v-model="filters.read"
                class="rounded-lg border border-border bg-card px-3 py-2 text-sm"
            >
                <option value="">Todos</option>
                <option value="unread">📬 No leídas</option>
                <option value="read">📭 Leídas</option>
            </select>
            <button
                @click="markAllAsRead"
                class="rounded-lg border border-border px-3 py-2 text-sm transition-colors hover:bg-accent"
            >
                ✓ Marcar como leídas
            </button>
        </div>

        <!-- Notifications List -->
        <div class="space-y-2">
            <div
                v-for="notif in notifications"
                :key="notif.id"
                @click="toggleNotification(notif.id)"
                class="cursor-pointer rounded-lg border border-border bg-card p-4 transition-colors hover:border-primary/50"
                :class="{ 'bg-muted/50': notif.read_at }"
            >
                <div class="flex items-start justify-between">
                    <div class="flex flex-1 items-start gap-3">
                        <!-- Severity Icon -->
                        <div class="mt-1 text-xl">
                            <span v-if="notif.severity === 'info'">ℹ️</span>
                            <span v-else-if="notif.severity === 'warning'"
                                >⚠️</span
                            >
                            <span v-else-if="notif.severity === 'critical'"
                                >🔴</span
                            >
                        </div>

                        <!-- Content -->
                        <div class="flex-1">
                            <div class="flex items-center gap-2">
                                <h4 class="font-semibold capitalize">
                                    {{ notif.type.replace(/_/g, ' ') }}
                                </h4>
                                <span
                                    v-if="!notif.read_at"
                                    class="h-2 w-2 rounded-full bg-primary"
                                ></span>
                            </div>
                            <p class="mt-1 text-sm text-muted-foreground">
                                {{ notif.data.message || notif.data.title }}
                            </p>
                            <p class="mt-2 text-xs text-muted-foreground">
                                {{ formatTime(notif.created_at) }}
                            </p>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="text-sm text-muted-foreground">
                        {{ notif.read_at ? '✓' : '●' }}
                    </div>
                </div>

                <!-- Expandable Details -->
                <div
                    v-if="expandedId === notif.id"
                    class="mt-3 border-t border-border pt-3"
                >
                    <pre
                        class="max-h-40 overflow-auto rounded bg-muted/50 p-2 text-xs"
                        >{{ JSON.stringify(notif.data, null, 2) }}</pre
                    >
                </div>
            </div>

            <!-- Empty State -->
            <div
                v-if="!loading && !notifications.length"
                class="py-8 text-center text-muted-foreground"
            >
                <p class="text-sm">No hay notificaciones</p>
            </div>

            <!-- Loading -->
            <div v-if="loading" class="flex justify-center py-4">
                <div class="animate-spin">⏳</div>
            </div>
        </div>

        <!-- Pagination -->
        <div
            v-if="pagination && pagination.total > 0"
            class="mt-4 flex items-center justify-between text-sm text-muted-foreground"
        >
            <p>
                {{ pagination.count }} de {{ pagination.total }} notificaciones
            </p>
            <div class="flex gap-2">
                <button
                    @click="previousPage"
                    :disabled="pagination.current_page === 1"
                    class="rounded border border-border px-3 py-1 hover:bg-accent disabled:cursor-not-allowed disabled:opacity-50"
                >
                    ← Anterior
                </button>
                <button
                    @click="nextPage"
                    :disabled="
                        pagination.current_page * pagination.per_page >=
                        pagination.total
                    "
                    class="rounded border border-border px-3 py-1 hover:bg-accent disabled:cursor-not-allowed disabled:opacity-50"
                >
                    Siguiente →
                </button>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue';

interface Notification {
    id: number;
    type: string;
    severity: string;
    message: string;
    data: Record<string, any>;
    read_at?: string;
    created_at: string;
}

interface Pagination {
    total: number;
    count: number;
    per_page: number;
    current_page: number;
}

const notifications = ref<Notification[]>([]);
const pagination = ref<Pagination | null>(null);
const loading = ref(false);
const expandedId = ref<number | null>(null);
const currentPage = ref(1);

const filters = ref({
    type: '',
    severity: '',
    read: '',
});

const formatTime = (dateStr: string) => {
    const date = new Date(dateStr);
    const now = new Date();
    const diffMinutes = Math.floor((now.getTime() - date.getTime()) / 60000);

    if (diffMinutes < 1) return 'Hace poco';
    if (diffMinutes < 60) return `Hace ${diffMinutes}m`;

    const diffHours = Math.floor(diffMinutes / 60);
    if (diffHours < 24) return `Hace ${diffHours}h`;

    return date.toLocaleString('es-ES', {
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const fetchNotifications = async () => {
    loading.value = true;
    try {
        const params = new URLSearchParams({
            limit: '20',
            page: String(currentPage.value),
            ...(filters.value.type && { type: filters.value.type }),
            ...(filters.value.severity && { severity: filters.value.severity }),
            ...(filters.value.read && { read: filters.value.read }),
        });

        const response = await fetch(
            `/api/deployment/verification/notifications?${params}`,
        );
        const data = await response.json();
        notifications.value = data.data;
        pagination.value = data.pagination;
    } catch (error) {
        console.error('Failed to fetch notifications:', error);
    } finally {
        loading.value = false;
    }
};

const toggleNotification = (id: number) => {
    expandedId.value = expandedId.value === id ? null : id;
};

const markAllAsRead = async () => {
    // This would need a new endpoint
    console.log('Mark all as read (not yet implemented)');
};

const nextPage = () => {
    currentPage.value++;
    fetchNotifications();
};

const previousPage = () => {
    currentPage.value--;
    fetchNotifications();
};

onMounted(() => {
    fetchNotifications();
});

// Watch for filter changes
import { watch } from 'vue';
watch(
    () => [filters.value.type, filters.value.severity, filters.value.read],
    () => {
        currentPage.value = 1;
        fetchNotifications();
    },
);
</script>
