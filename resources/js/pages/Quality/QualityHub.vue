<script setup lang="ts">
import StBadgeGlass from '@/components/StBadgeGlass.vue';
import StButtonGlass from '@/components/StButtonGlass.vue';
import StCardGlass from '@/components/StCardGlass.vue';
//import AppLayout from '@/layouts/AppLayout.vue';
import axios from 'axios';
import {
    BarChart3,
    Bug,
    ChevronRight,
    Clock,
    Filter,
    Search,
    ShieldCheck,
    Target,
    User,
    Zap,
} from 'lucide-vue-next';
import { onMounted, ref } from 'vue';

interface Ticket {
    id: number | string;
    title: string;
    type: string;
    status: string;
    priority?: string;
    reporter?: {
        name: string;
    };
    created_at: string;
    context?: {
        url?: string;
    };
    assignee?: {
        name: string;
    };
}

interface Metrics {
    critical_bugs: number;
    by_status: Record<string, number>;
    by_type: Record<string, number>;
    total: number;
}

const tickets = ref<Ticket[]>([]);
const metrics = ref<Metrics | null>(null);
const loading = ref(true);

const fetchTickets = async () => {
    loading.value = true;
    try {
        const [ticketsRes, metricsRes] = await Promise.all([
            axios.get('/api/support-tickets'),
            axios.get('/api/support-tickets/metrics'),
        ]);
        tickets.value = ticketsRes.data.data;
        metrics.value = metricsRes.data.data;
    } catch (e) {
        console.error('Failed to fetch QA data', e);
    } finally {
        loading.value = false;
    }
};

onMounted(fetchTickets);

const getStatusVariant = (status: string) => {
    switch (status) {
        case 'open':
            return 'warning';
        case 'in_progress':
            return 'primary';
        case 'resolved':
            return 'success';
        case 'closed':
            return 'glass';
        default:
            return 'glass';
    }
};

const getTypeIcon = (type: string) => {
    switch (type) {
        case 'bug':
            return Bug;
        case 'improvement':
            return Zap;
        case 'code_quality':
            return ShieldCheck;
        case 'ux':
            return Target;
        default:
            return ShieldCheck;
    }
};
/* const layoutProps = {
    title: 'Quality Hub',
    breadcrumbs: [
        {
            title: 'Calidad',
            href: '/quality-hub',
        },
    ],
}; */
</script>

<template>
        <template>
            <div
                class="flex flex-col justify-between gap-4 md:flex-row md:items-center"
            >
                <div>
                    <h1 class="text-3xl font-bold tracking-tight text-white">
                        Quality Hub
                    </h1>
                    <p class="text-white/60">
                        Garantía de Calidad y Mejora Continua de Stratos.
                    </p>
                </div>
                <div class="flex gap-2">
                    <StButtonGlass variant="glass">
                        <Search class="mr-2 h-4 w-4" /> Buscar Hallazgos
                    </StButtonGlass>
                    <StButtonGlass variant="primary">
                        Exportar Reporte QA
                    </StButtonGlass>
                </div>
            </div>
        </template>

        <div class="space-y-8 py-6">
            <!-- Metrics Bar -->
            <div
                v-if="metrics"
                class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4"
            >
                <StCardGlass class="border-l-4 border-l-red-500 p-4">
                    <div class="flex items-start justify-between">
                        <div>
                            <p
                                class="text-xs font-medium tracking-wider text-white/40 uppercase"
                            >
                                Errores Críticos
                            </p>
                            <h3 class="mt-1 text-2xl font-bold text-white">
                                {{ metrics.critical_bugs || 0 }}
                            </h3>
                        </div>
                        <div class="rounded-lg bg-red-500/10 p-2">
                            <Bug class="h-5 w-5 text-red-500" />
                        </div>
                    </div>
                </StCardGlass>

                <StCardGlass class="border-l-4 border-l-amber-500 p-4">
                    <div class="flex items-start justify-between">
                        <div>
                            <p
                                class="text-xs font-medium tracking-wider text-white/40 uppercase"
                            >
                                Tickets Abiertos
                            </p>
                            <h3 class="mt-1 text-2xl font-bold text-white">
                                {{ metrics.by_status?.open || 0 }}
                            </h3>
                        </div>
                        <div class="rounded-lg bg-amber-500/10 p-2">
                            <Clock class="h-5 w-5 text-amber-500" />
                        </div>
                    </div>
                </StCardGlass>

                <StCardGlass class="border-l-4 border-l-blue-500 p-4">
                    <div class="flex items-start justify-between">
                        <div>
                            <p
                                class="text-xs font-medium tracking-wider text-white/40 uppercase"
                            >
                                Mejoras de Calidad
                            </p>
                            <h3 class="mt-1 text-2xl font-bold text-white">
                                {{ metrics.by_type?.code_quality || 0 }}
                            </h3>
                        </div>
                        <div class="rounded-lg bg-blue-500/10 p-2">
                            <ShieldCheck class="h-5 w-5 text-blue-500" />
                        </div>
                    </div>
                </StCardGlass>

                <StCardGlass class="border-l-4 border-l-primary/50 p-4">
                    <div class="flex items-start justify-between">
                        <div>
                            <p
                                class="text-xs font-medium tracking-wider text-white/40 uppercase"
                            >
                                Total Histórico
                            </p>
                            <h3 class="mt-1 text-2xl font-bold text-white">
                                {{ metrics.total || 0 }}
                            </h3>
                        </div>
                        <div class="rounded-lg bg-primary/10 p-2">
                            <BarChart3 class="h-5 w-5 text-primary" />
                        </div>
                    </div>
                </StCardGlass>
            </div>

            <!-- Tickets List -->
            <StCardGlass>
                <div
                    class="flex items-center justify-between border-b border-white/10 p-6"
                >
                    <h2 class="text-lg font-bold text-white">
                        Listado de Hallazgos
                    </h2>
                    <div class="flex gap-2">
                        <button
                            class="flex items-center gap-1 rounded-lg border border-white/10 bg-white/5 px-3 py-1 text-xs text-white/60 hover:bg-white/10"
                        >
                            <Filter class="h-3 w-3" /> Filtrar
                        </button>
                    </div>
                </div>

                <div v-if="loading" class="flex justify-center p-12">
                    <div
                        class="h-8 w-8 animate-spin rounded-full border-b-2 border-primary"
                    ></div>
                </div>

                <div v-else-if="tickets.length === 0" class="p-12 text-center">
                    <ShieldCheck class="mx-auto mb-4 h-12 w-12 text-white/10" />
                    <p class="text-white/40">
                        No se han detectado hallazgos hasta el momento. ¡Calidad
                        impecable!
                    </p>
                </div>

                <div v-else class="divide-y divide-white/10">
                    <div
                        v-for="ticket in tickets"
                        :key="ticket.id"
                        class="group cursor-pointer p-4 transition-colors hover:bg-white/5"
                    >
                        <div class="flex items-center justify-between">
                            <div class="flex flex-1 items-center gap-4">
                                <div
                                    class="rounded-lg border border-white/10 bg-white/5 p-2"
                                >
                                    <component
                                        :is="getTypeIcon(ticket.type)"
                                        class="h-5 w-5 text-primary"
                                    />
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <h4 class="font-medium text-white">
                                            {{ ticket.title }}
                                        </h4>
                                        <StBadgeGlass
                                            :variant="
                                                getStatusVariant(ticket.status)
                                            "
                                            size="sm"
                                        >
                                            {{ ticket.status }}
                                        </StBadgeGlass>
                                        <span
                                            v-if="
                                                ticket.priority === 'critical'
                                            "
                                            class="rounded bg-red-500/20 px-1.5 py-0.5 text-[10px] font-bold text-red-500 uppercase"
                                        >
                                            Crítico
                                        </span>
                                    </div>
                                    <div
                                        class="mt-1 flex items-center gap-4 text-xs text-white/40"
                                    >
                                        <span class="flex items-center gap-1">
                                            <User class="h-3 w-3" />
                                            {{
                                                ticket.reporter?.name ||
                                                'Sistema'
                                            }}
                                        </span>
                                        <span class="flex items-center gap-1">
                                            <Clock class="h-3 w-3" />
                                            {{
                                                new Date(
                                                    ticket.created_at,
                                                ).toLocaleDateString()
                                            }}
                                        </span>
                                        <span
                                            v-if="ticket.context?.url"
                                            class="hidden max-w-xs truncate md:inline"
                                        >
                                            {{ ticket.context.url }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <div
                                    v-if="ticket.assignee"
                                    class="flex flex-col items-end"
                                >
                                    <span
                                        class="text-[10px] text-white/30 uppercase"
                                        >Asignado a</span
                                    >
                                    <span class="text-xs text-white/70">{{
                                        ticket.assignee.name
                                    }}</span>
                                </div>
                                <ChevronRight
                                    class="h-5 w-5 text-white/20 transition-colors group-hover:text-primary"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </StCardGlass>
        </div>
</template>
