<template>
    <AppLayout title="Skill Intelligence">
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1
                        class="text-2xl font-bold text-gray-900 dark:text-white"
                    >
                        Skill Intelligence
                        <span
                            class="ml-2 text-xs font-normal tracking-widest text-indigo-500 uppercase"
                            >v2</span
                        >
                    </h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Análisis de brechas de habilidades · Heatmap por
                        departamento · Upskilling
                    </p>
                </div>
                <v-btn-group density="compact" variant="outlined">
                    <v-btn
                        v-for="tab in tabs"
                        :key="tab.key"
                        :color="activeTab === tab.key ? 'indigo' : undefined"
                        :variant="activeTab === tab.key ? 'flat' : 'outlined'"
                        @click="activeTab = tab.key"
                    >
                        {{ tab.label }}
                    </v-btn>
                </v-btn-group>
            </div>

            <!-- KPI Cards -->
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                <v-card class="pa-4 text-center" elevation="1">
                    <div class="mb-1 text-xs text-gray-500">Cobertura Org.</div>
                    <div
                        class="text-3xl font-black"
                        :class="coverageColor(summary.org_avg_coverage_pct)"
                    >
                        {{ summary.org_avg_coverage_pct?.toFixed(1) ?? '–' }}%
                    </div>
                </v-card>
                <v-card class="pa-4 text-center" elevation="1">
                    <div class="mb-1 text-xs text-gray-500">
                        Skills con brecha
                    </div>
                    <div class="text-3xl font-black text-red-500">
                        {{ topGaps.length }}
                    </div>
                </v-card>
                <v-card class="pa-4 text-center" elevation="1">
                    <div class="mb-1 text-xs text-gray-500">
                        Personas afectadas
                    </div>
                    <div class="text-3xl font-black text-amber-500">
                        {{ totalAffected }}
                    </div>
                </v-card>
                <v-card class="pa-4 text-center" elevation="1">
                    <div class="mb-1 text-xs text-gray-500">Críticos</div>
                    <div class="text-3xl font-black text-red-700">
                        {{ totalCritical }}
                    </div>
                </v-card>
            </div>

            <!-- Tab: Top Gaps -->
            <div v-if="activeTab === 'gaps'">
                <v-card elevation="1">
                    <v-card-title
                        >Top Brechas — Toda la Organización</v-card-title
                    >
                    <v-data-table
                        :headers="gapHeaders"
                        :items="topGaps"
                        :loading="loadingGaps"
                        item-value="skill_id"
                        density="compact"
                    >
                        <template #item.skill_name="{ item }">
                            <div class="flex items-center gap-2">
                                <v-chip
                                    size="x-small"
                                    :color="categoryColor(item.category)"
                                    variant="tonal"
                                >
                                    {{ item.category }}
                                </v-chip>
                                <span
                                    :class="{ 'font-bold': item.is_critical }"
                                    >{{ item.skill_name }}</span
                                >
                                <v-icon
                                    v-if="item.is_critical"
                                    size="14"
                                    color="red"
                                    >mdi-alert</v-icon
                                >
                            </div>
                        </template>
                        <template #item.avg_gap="{ item }">
                            <div class="flex items-center gap-2">
                                <v-progress-linear
                                    :model-value="(item.avg_gap / 3) * 100"
                                    :color="
                                        item.avg_gap >= 2
                                            ? 'red'
                                            : item.avg_gap >= 1
                                              ? 'amber'
                                              : 'green'
                                    "
                                    height="8"
                                    rounded
                                    class="w-24"
                                />
                                <span class="text-xs">{{ item.avg_gap }}</span>
                            </div>
                        </template>
                        <template #item.critical_count="{ item }">
                            <v-chip
                                size="x-small"
                                :color="
                                    item.critical_count >= 3 ? 'red' : 'orange'
                                "
                                variant="tonal"
                            >
                                {{ item.critical_count }}
                            </v-chip>
                        </template>
                    </v-data-table>
                </v-card>
            </div>

            <!-- Tab: Heatmap -->
            <div v-if="activeTab === 'heatmap'">
                <v-card elevation="1">
                    <v-card-title class="flex items-center gap-2">
                        Heatmap Departamento × Skill
                        <v-select
                            v-model="categoryFilter"
                            :items="categoryOptions"
                            label="Categoría"
                            density="compact"
                            clearable
                            class="ml-auto"
                            style="max-width: 200px"
                            @update:model-value="loadHeatmap"
                        />
                    </v-card-title>
                    <v-card-text>
                        <div
                            v-if="loadingHeatmap"
                            class="flex justify-center py-8"
                        >
                            <v-progress-circular indeterminate color="indigo" />
                        </div>
                        <div
                            v-else-if="heatmap.departments?.length"
                            class="overflow-x-auto"
                        >
                            <table class="min-w-full text-xs">
                                <thead>
                                    <tr>
                                        <th
                                            class="px-3 py-2 text-left font-semibold text-gray-600 dark:text-gray-300"
                                        >
                                            Departamento
                                        </th>
                                        <th
                                            v-for="skill in heatmap.skills"
                                            :key="skill"
                                            class="px-2 py-2 text-center font-medium text-gray-500 dark:text-gray-400"
                                            :title="skill"
                                        >
                                            {{
                                                skill.length > 10
                                                    ? skill.slice(0, 10) + '…'
                                                    : skill
                                            }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="dept in heatmap.departments"
                                        :key="dept"
                                        class="border-t border-gray-100 dark:border-gray-700"
                                    >
                                        <td
                                            class="px-3 py-2 font-medium whitespace-nowrap text-gray-700 dark:text-gray-300"
                                        >
                                            {{ dept }}
                                        </td>
                                        <td
                                            v-for="skill in heatmap.skills"
                                            :key="skill"
                                            class="px-2 py-2 text-center"
                                        >
                                            <span
                                                v-if="
                                                    heatmap.matrix[dept]?.[
                                                        skill
                                                    ] !== undefined
                                                "
                                                class="inline-block rounded px-2 py-1 text-xs font-bold text-white"
                                                :style="{
                                                    backgroundColor: heatColor(
                                                        heatmap.matrix[dept][
                                                            skill
                                                        ],
                                                    ),
                                                }"
                                            >
                                                {{
                                                    heatmap.matrix[dept][skill]
                                                }}
                                            </span>
                                            <span v-else class="text-gray-300"
                                                >–</span
                                            >
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div
                                class="mt-3 flex items-center gap-3 text-xs text-gray-500"
                            >
                                <span
                                    >Brecha promedio: 0 (verde) → 3+ (rojo
                                    oscuro)</span
                                >
                            </div>
                        </div>
                        <div v-else class="py-8 text-center text-gray-400">
                            Sin datos de habilidades registrados.
                        </div>
                    </v-card-text>
                </v-card>
            </div>

            <!-- Tab: Upskilling -->
            <div v-if="activeTab === 'upskilling'">
                <div v-if="loadingUpskilling" class="flex justify-center py-8">
                    <v-progress-circular indeterminate color="indigo" />
                </div>
                <div v-else class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <v-card
                        v-for="rec in recommendations"
                        :key="rec.skill_id"
                        elevation="2"
                        class="border-l-4"
                        :class="priorityBorder(rec.priority)"
                    >
                        <v-card-title class="flex items-center gap-2 text-base">
                            {{ rec.skill_name }}
                            <v-chip
                                size="x-small"
                                :color="priorityColor(rec.priority)"
                                variant="flat"
                                class="ml-auto"
                            >
                                {{ rec.priority }}
                            </v-chip>
                        </v-card-title>
                        <v-card-text class="space-y-2">
                            <div class="text-xs text-gray-500">
                                {{ rec.affected_people }} personas afectadas ·
                                brecha prom. {{ rec.avg_gap }}
                            </div>
                            <v-alert
                                density="compact"
                                type="info"
                                variant="tonal"
                                class="text-xs"
                            >
                                <strong>Acción sugerida:</strong>
                                {{ rec.suggested_action }}
                            </v-alert>
                            <div v-if="rec.urgent_people?.length">
                                <div
                                    class="mb-1 text-xs font-semibold text-gray-600 dark:text-gray-300"
                                >
                                    Prioritarios:
                                </div>
                                <div
                                    v-for="p in rec.urgent_people"
                                    :key="p.people_id"
                                    class="flex justify-between py-0.5 text-xs"
                                >
                                    <span>{{
                                        p.name || `ID ${p.people_id}`
                                    }}</span>
                                    <span class="font-medium text-red-500"
                                        >brecha {{ p.gap }}</span
                                    >
                                </div>
                            </div>
                        </v-card-text>
                    </v-card>
                </div>
                <div
                    v-if="!loadingUpskilling && !recommendations.length"
                    class="py-8 text-center text-gray-400"
                >
                    Sin brechas detectadas. ¡Excelente cobertura!
                </div>
            </div>

            <!-- Tab: Coverage -->
            <div v-if="activeTab === 'coverage'">
                <v-card elevation="1">
                    <v-card-title>Cobertura por Skill</v-card-title>
                    <v-data-table
                        :headers="coverageHeaders"
                        :items="coverageSkills"
                        :loading="loadingCoverage"
                        item-value="skill_id"
                        density="compact"
                        :sort-by="[{ key: 'coverage_pct', order: 'asc' }]"
                    >
                        <template #item.coverage_pct="{ item }">
                            <div class="flex items-center gap-2">
                                <v-progress-linear
                                    :model-value="item.coverage_pct"
                                    :color="coverageBarColor(item.coverage_pct)"
                                    height="8"
                                    rounded
                                    class="w-24"
                                />
                                <span class="text-xs"
                                    >{{ item.coverage_pct }}%</span
                                >
                            </div>
                        </template>
                        <template #item.is_critical="{ item }">
                            <v-icon
                                v-if="item.is_critical"
                                size="16"
                                color="red"
                                >mdi-alert-circle</v-icon
                            >
                        </template>
                    </v-data-table>
                </v-card>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { computed, onMounted, ref } from 'vue';

interface HeatmapData {
    departments: string[];
    skills: string[];
    matrix: Record<string, Record<string, number>>;
    meta: { total_records: number; category_filter: string | null };
}

interface GapItem {
    skill_id: number;
    skill_name: string;
    category: string;
    avg_gap: number;
    affected_people: number;
    critical_count: number;
    is_critical: boolean;
}

interface Recommendation {
    skill_id: number;
    skill_name: string;
    category: string;
    avg_gap: number;
    affected_people: number;
    priority: 'alta' | 'media' | 'baja';
    suggested_action: string;
    urgent_people: { people_id: number; name: string; gap: number }[];
}

interface CoverageSkill {
    skill_id: number;
    skill_name: string;
    category: string;
    is_critical: boolean;
    coverage_pct: number;
    people_meeting: number;
    total_people: number;
}

interface CoverageSummary {
    org_avg_coverage_pct?: number;
    skills: CoverageSkill[];
}

const activeTab = ref<'heatmap' | 'gaps' | 'upskilling' | 'coverage'>('gaps');

const tabs = [
    { key: 'gaps', label: 'Top Brechas' },
    { key: 'heatmap', label: 'Heatmap' },
    { key: 'upskilling', label: 'Upskilling' },
    { key: 'coverage', label: 'Cobertura' },
];

const categoryFilter = ref<string | null>(null);
const categoryOptions = [
    'technical',
    'leadership',
    'soft',
    'domain',
    'general',
];

const heatmap = ref<HeatmapData>({
    departments: [],
    skills: [],
    matrix: {},
    meta: { total_records: 0, category_filter: null },
});
const topGaps = ref<GapItem[]>([]);
const recommendations = ref<Recommendation[]>([]);
const coverageSkills = ref<CoverageSkill[]>([]);
const summary = ref<CoverageSummary>({
    org_avg_coverage_pct: undefined,
    skills: [],
});

const loadingHeatmap = ref(false);
const loadingGaps = ref(false);
const loadingUpskilling = ref(false);
const loadingCoverage = ref(false);

const gapHeaders = [
    { title: 'Skill', key: 'skill_name' },
    { title: 'Brecha prom.', key: 'avg_gap' },
    { title: 'Afectados', key: 'affected_people' },
    { title: 'Críticos', key: 'critical_count' },
];

const coverageHeaders = [
    { title: 'Skill', key: 'skill_name' },
    { title: 'Crítico', key: 'is_critical' },
    { title: 'Cobertura', key: 'coverage_pct' },
    { title: 'Cumplen / Total', key: 'people_meeting' },
];

const totalAffected = computed(() =>
    topGaps.value.reduce((s, g) => s + g.affected_people, 0),
);
const totalCritical = computed(() =>
    topGaps.value.reduce((s, g) => s + g.critical_count, 0),
);

function heatColor(gap: number): string {
    if (gap === 0) return '#22c55e';
    if (gap < 1) return '#86efac';
    if (gap < 2) return '#f59e0b';
    if (gap < 3) return '#ef4444';
    return '#7f1d1d';
}

function coverageColor(pct?: number): string {
    if (!pct) return 'text-gray-400';
    if (pct >= 80) return 'text-green-500';
    if (pct >= 60) return 'text-amber-500';
    return 'text-red-500';
}

function coverageBarColor(pct: number): string {
    if (pct >= 80) return 'green';
    if (pct >= 60) return 'amber';
    return 'red';
}

function categoryColor(cat: string): string {
    const map: Record<string, string> = {
        technical: 'blue',
        leadership: 'purple',
        soft: 'teal',
        domain: 'orange',
        general: 'gray',
    };
    return map[cat] ?? 'default';
}

function priorityColor(p: string): string {
    return p === 'alta' ? 'red' : p === 'media' ? 'amber' : 'green';
}

function priorityBorder(p: string): string {
    return p === 'alta'
        ? 'border-red-500'
        : p === 'media'
          ? 'border-amber-400'
          : 'border-green-400';
}

async function loadHeatmap() {
    loadingHeatmap.value = true;
    try {
        const params = categoryFilter.value
            ? `?category=${categoryFilter.value}`
            : '';
        const res = await fetch(`/api/skill-intelligence/heatmap${params}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        });
        heatmap.value = await res.json();
    } finally {
        loadingHeatmap.value = false;
    }
}

async function loadTopGaps() {
    loadingGaps.value = true;
    try {
        const res = await fetch('/api/skill-intelligence/top-gaps?limit=20', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        });
        const json = await res.json();
        topGaps.value = json.top_gaps ?? [];
    } finally {
        loadingGaps.value = false;
    }
}

async function loadUpskilling() {
    loadingUpskilling.value = true;
    try {
        const res = await fetch('/api/skill-intelligence/upskilling?limit=10', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        });
        const json = await res.json();
        recommendations.value = json.recommendations ?? [];
    } finally {
        loadingUpskilling.value = false;
    }
}

async function loadCoverage() {
    loadingCoverage.value = true;
    try {
        const res = await fetch('/api/skill-intelligence/coverage', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        });
        const json = await res.json();
        summary.value = json;
        coverageSkills.value = json.skills ?? [];
    } finally {
        loadingCoverage.value = false;
    }
}

onMounted(() => {
    loadTopGaps();
    loadCoverage();
    loadHeatmap();
    loadUpskilling();
});
</script>
