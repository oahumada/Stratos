<script setup lang="ts">
import StCardGlass from '@/components/StCardGlass.vue';
import { router } from '@inertiajs/vue3';
import {
    AlertCircle,
    BarChart3,
    ChevronRight,
    Gauge,
    TrendingUp,
} from 'lucide-vue-next';

interface MonitoringModule {
    title: string;
    description: string;
    icon: any;
    href: string;
    iconColor: string;
    badge?: string;
}

const modules: MonitoringModule[] = [
    {
        title: 'Quality Dashboard',
        description:
            'Evaluación de calidad LLM con métricas RAGAS en tiempo real',
        icon: BarChart3,
        href: '/intelligence/quality-dashboard',
        iconColor: 'text-cyan-400',
        badge: 'Nuevas métricas',
    },
    {
        title: 'Agent Metrics',
        description: 'Monitoreo de latencia, éxito y performance de agentes IA',
        icon: TrendingUp,
        href: '/intelligence/agent-metrics',
        iconColor: 'text-indigo-400',
        badge: 'En vivo',
    },
    {
        title: 'LLM Quality (RAGAS)',
        description: 'Evaluación detallada de fidelidad y hallucinations',
        icon: AlertCircle,
        href: '/quality/ragas-metrics',
        iconColor: 'text-amber-400',
    },
    {
        title: 'Compliance Audit',
        description: 'Auditoría de cumplimiento ISO 27001 y GDPR',
        icon: Gauge,
        href: '/quality/compliance-audit',
        iconColor: 'text-emerald-400',
    },
];

const navigate = (href: string) => {
    router.visit(href);
};
</script>

<template>
    <div class="mx-auto max-w-5xl px-4 py-8">
        <!-- Header -->
        <div class="mb-12">
            <div class="mb-4 flex items-center gap-2">
                <div
                    class="h-1 w-12 rounded-full bg-gradient-to-r from-indigo-500 to-cyan-500"
                ></div>
            </div>
            <h1 class="text-4xl font-bold tracking-tight text-white">
                Intelligence & Monitoring Hub
            </h1>
            <p class="mt-2 text-lg text-white/60">
                Observabilidad completa de agentes IA, calidad LLM y
                cumplimiento normativo
            </p>
        </div>

        <!-- Modules Grid -->
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
            <div v-for="module in modules" :key="module.title">
                <StCardGlass
                    class="group relative flex h-full cursor-pointer flex-col gap-4 p-6 transition-all duration-300 hover:border-white/50 hover:shadow-lg hover:shadow-indigo-500/20"
                    @click="navigate(module.href)"
                >
                    <!-- Badge -->
                    <div v-if="module.badge" class="absolute top-4 right-4">
                        <span
                            class="inline-flex items-center rounded-full bg-indigo-500/20 px-2.5 py-0.5 text-xs font-medium tracking-wide text-indigo-200"
                        >
                            {{ module.badge }}
                        </span>
                    </div>

                    <!-- Icon -->
                    <div
                        :class="[
                            module.iconColor,
                            'w-fit rounded-lg bg-white/5 p-3',
                        ]"
                    >
                        <component :is="module.icon" class="h-6 w-6" />
                    </div>

                    <!-- Content -->
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-white">
                            {{ module.title }}
                        </h3>
                        <p class="mt-2 text-sm leading-relaxed text-white/60">
                            {{ module.description }}
                        </p>
                    </div>

                    <!-- Arrow -->
                    <div
                        class="inline-flex items-center gap-2 text-sm font-medium text-white/60 transition-colors group-hover:text-white/80"
                    >
                        Ver Dashboard
                        <ChevronRight
                            class="h-4 w-4 transition-transform group-hover:translate-x-1"
                        />
                    </div>
                </StCardGlass>
            </div>
        </div>

        <!-- Quick Stats Row -->
        <div class="mt-12 grid gap-4 md:grid-cols-3">
            <StCardGlass class="border-l-4 border-l-indigo-500 p-4">
                <p
                    class="text-xs font-medium tracking-wider text-white/40 uppercase"
                >
                    Métrica en Tiempo Real
                </p>
                <h3 class="mt-2 text-2xl font-bold text-white">3 Dashboards</h3>
                <p class="mt-1 text-sm text-white/60">Monitoreo activo</p>
            </StCardGlass>

            <StCardGlass class="border-l-4 border-l-cyan-500 p-4">
                <p
                    class="text-xs font-medium tracking-wider text-white/40 uppercase"
                >
                    Cobertura
                </p>
                <h3 class="mt-2 text-2xl font-bold text-white">Multi-tenant</h3>
                <p class="mt-1 text-sm text-white/60">
                    Isolamiento seguro por org
                </p>
            </StCardGlass>

            <StCardGlass class="border-l-4 border-l-emerald-500 p-4">
                <p
                    class="text-xs font-medium tracking-wider text-white/40 uppercase"
                >
                    Actualización
                </p>
                <h3 class="mt-2 text-2xl font-bold text-white">
                    30s / Auto-poll
                </h3>
                <p class="mt-1 text-sm text-white/60">Polling configurable</p>
            </StCardGlass>
        </div>

        <!-- Documentation Link -->
        <div class="mt-8 rounded-lg border border-white/10 bg-white/5 p-6">
            <h4 class="mb-2 font-semibold text-white">📚 Documentación</h4>
            <p class="mb-4 text-sm text-white/60">
                Aprende cómo usar estos dashboards y configurar alertas
                personalizadas.
            </p>
            <a
                href="/docs/QW5_AGENT_METRICS_GUIDE.md"
                class="inline-flex items-center gap-2 text-sm font-medium text-indigo-400 transition-colors hover:text-indigo-300"
            >
                Ver Guía Completa
                <ChevronRight class="h-4 w-4" />
            </a>
        </div>
    </div>
</template>
