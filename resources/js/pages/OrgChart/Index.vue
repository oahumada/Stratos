<template>
    <AppLayout title="Organigrama">
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h1
                        class="text-2xl font-bold text-gray-900 dark:text-white"
                    >
                        Organigrama Interactivo
                    </h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Estructura organizacional en tiempo real
                    </p>
                </div>
                <div class="flex gap-2">
                    <v-btn-group density="compact" variant="outlined">
                        <v-btn
                            :color="view === 'people' ? 'indigo' : undefined"
                            :variant="view === 'people' ? 'flat' : 'outlined'"
                            @click="switchView('people')"
                            >👤 Personas</v-btn
                        >
                        <v-btn
                            :color="
                                view === 'departments' ? 'indigo' : undefined
                            "
                            :variant="
                                view === 'departments' ? 'flat' : 'outlined'
                            "
                            @click="switchView('departments')"
                            >🏢 Departamentos</v-btn
                        >
                    </v-btn-group>
                    <v-btn
                        size="small"
                        variant="outlined"
                        prepend-icon="mdi-refresh"
                        :loading="loading"
                        @click="loadData"
                        >Actualizar</v-btn
                    >
                </div>
            </div>

            <!-- Stats Row -->
            <div
                class="grid grid-cols-2 gap-3 sm:grid-cols-4"
                v-if="stats.total_employees"
            >
                <v-card class="pa-3 text-center" elevation="1">
                    <div class="text-xs text-gray-500">Total empleados</div>
                    <div class="text-2xl font-black text-indigo-600">
                        {{ stats.total_employees }}
                    </div>
                </v-card>
                <v-card class="pa-3 text-center" elevation="1">
                    <div class="text-xs text-gray-500">Con reporte directo</div>
                    <div class="text-2xl font-black text-blue-600">
                        {{ stats.total_managers }}
                    </div>
                </v-card>
                <v-card class="pa-3 text-center" elevation="1">
                    <div class="text-xs text-gray-500">Span prom.</div>
                    <div class="text-2xl font-black text-teal-600">
                        {{ stats.avg_span_of_control }}
                    </div>
                </v-card>
                <v-card class="pa-3 text-center" elevation="1">
                    <div class="text-xs text-gray-500">Profundidad máx.</div>
                    <div class="text-2xl font-black text-violet-600">
                        {{ stats.max_depth }}
                    </div>
                </v-card>
            </div>

            <!-- Search / filter -->
            <v-text-field
                v-model="search"
                label="Buscar persona o departamento..."
                prepend-inner-icon="mdi-magnify"
                density="compact"
                clearable
                hide-details
                class="max-w-xs"
            />

            <!-- Tree -->
            <v-card elevation="1" class="pa-4">
                <div v-if="loading" class="flex justify-center py-12">
                    <v-progress-circular
                        indeterminate
                        color="indigo"
                        size="48"
                    />
                </div>
                <div
                    v-else-if="!filteredNodes.length"
                    class="py-12 text-center text-gray-400"
                >
                    Sin datos disponibles.
                </div>
                <div v-else class="overflow-auto">
                    <org-node
                        v-for="node in filteredNodes"
                        :key="node.id"
                        :node="node"
                        :view="view"
                        :depth="0"
                        @select="selectNode"
                    />
                </div>
            </v-card>
        </div>

        <!-- Side panel -->
        <v-navigation-drawer
            v-model="drawer"
            location="right"
            width="340"
            temporary
        >
            <div v-if="selectedNode" class="pa-4 space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-bold">{{ selectedNode.name }}</h2>
                    <v-btn icon size="small" @click="drawer = false"
                        ><v-icon>mdi-close</v-icon></v-btn
                    >
                </div>
                <v-divider />
                <div class="space-y-2 text-sm">
                    <div v-if="view === 'people'">
                        <div class="text-gray-500">Dept ID</div>
                        <div>{{ selectedNode.department_id ?? '–' }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500">Reportes directos</div>
                        <div class="font-semibold">
                            {{
                                selectedNode.direct_reports_count ??
                                selectedNode.children?.length ??
                                0
                            }}
                        </div>
                    </div>
                    <div v-if="selectedNode.is_high_potential">
                        <v-chip size="small" color="amber" variant="flat"
                            >⭐ Alto Potencial</v-chip
                        >
                    </div>
                    <div v-if="selectedNode.depth !== undefined">
                        <div class="text-gray-500">Nivel</div>
                        <div>{{ selectedNode.depth }}</div>
                    </div>
                </div>
                <v-btn
                    v-if="view === 'people'"
                    block
                    variant="outlined"
                    color="indigo"
                    @click="loadSubtree(selectedNode.id)"
                >
                    Ver subárbol
                </v-btn>
            </div>
        </v-navigation-drawer>
    </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { computed, defineComponent, h, onMounted, ref } from 'vue';

interface OrgNode {
    id: number;
    name: string;
    department_id?: number;
    is_high_potential?: boolean;
    photo_url?: string | null;
    depth?: number;
    direct_reports_count?: number;
    manager_id?: number;
    children: OrgNode[];
}

interface OrgStats {
    total_employees?: number;
    total_managers?: number;
    avg_span_of_control?: number;
    max_depth?: number;
}

// ─── Recursive tree node component ────────────────────────────────────────────
const OrgNode = defineComponent({
    name: 'OrgNode',
    props: {
        node: { type: Object as () => OrgNode, required: true },
        view: { type: String, default: 'people' },
        depth: { type: Number, default: 0 },
    },
    emits: ['select'],
    setup(props, { emit }) {
        const expanded = ref(props.depth < 2);

        return () => {
            const n = props.node;
            const hasChildren = n.children?.length > 0;

            return h(
                'div',
                {
                    class: 'pl-4 border-l border-gray-200 dark:border-gray-700 my-1',
                },
                [
                    h(
                        'div',
                        {
                            class: 'flex items-center gap-2 py-1 px-2 rounded cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-800 group',
                            onClick: () => emit('select', n),
                        },
                        [
                            hasChildren
                                ? h(
                                      'v-btn',
                                      {
                                          icon: true,
                                          size: 'x-small',
                                          variant: 'text',
                                          onClick: (e: Event) => {
                                              e.stopPropagation();
                                              expanded.value = !expanded.value;
                                          },
                                      },
                                      [
                                          h('v-icon', { size: 14 }, () =>
                                              expanded.value
                                                  ? 'mdi-chevron-down'
                                                  : 'mdi-chevron-right',
                                          ),
                                      ],
                                  )
                                : h('span', { class: 'w-6' }),
                            props.view === 'people' && n.is_high_potential
                                ? h(
                                      'v-icon',
                                      { size: 14, color: 'amber' },
                                      () => 'mdi-star',
                                  )
                                : null,
                            h(
                                'span',
                                {
                                    class: `text-sm ${n.is_high_potential ? 'font-semibold text-amber-600 dark:text-amber-400' : 'text-gray-700 dark:text-gray-200'}`,
                                },
                                n.name,
                            ),
                            hasChildren
                                ? h(
                                      'v-chip',
                                      {
                                          size: 'x-small',
                                          variant: 'tonal',
                                          color: 'blue',
                                          class: 'ml-auto opacity-0 group-hover:opacity-100',
                                      },
                                      () => `${n.children.length}`,
                                  )
                                : null,
                        ],
                    ),
                    expanded.value && hasChildren
                        ? h(
                              'div',
                              {},
                              n.children.map((child) =>
                                  h(OrgNode, {
                                      node: child,
                                      view: props.view,
                                      depth: props.depth + 1,
                                      onSelect: (node: OrgNode) =>
                                          emit('select', node),
                                  }),
                              ),
                          )
                        : null,
                ],
            );
        };
    },
});

// ─── Page state ───────────────────────────────────────────────────────────────
const view = ref<'people' | 'departments'>('people');
const loading = ref(false);
const nodes = ref<OrgNode[]>([]);
const stats = ref<OrgStats>({});
const search = ref('');
const drawer = ref(false);
const selectedNode = ref<OrgNode | null>(null);

const filteredNodes = computed(() => {
    if (!search.value) return nodes.value;
    const q = search.value.toLowerCase();
    const filterNode = (n: OrgNode): OrgNode | null => {
        const match = n.name.toLowerCase().includes(q);
        const filteredChildren = n.children
            .map(filterNode)
            .filter(Boolean) as OrgNode[];
        if (match || filteredChildren.length)
            return { ...n, children: filteredChildren };
        return null;
    };
    return nodes.value.map(filterNode).filter(Boolean) as OrgNode[];
});

async function loadData() {
    loading.value = true;
    try {
        const [treeRes, statsRes] = await Promise.all([
            fetch(`/api/org-chart/people?view=${view.value}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            }).then((r) => r.json()),
            fetch('/api/org-chart/people/stats', {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            }).then((r) => r.json()),
        ]);
        nodes.value = treeRes.nodes ?? [];
        stats.value = statsRes;
    } catch {
        // silent
    } finally {
        loading.value = false;
    }
}

async function loadSubtree(personId: number) {
    loading.value = true;
    try {
        const res = await fetch(`/api/org-chart/people/${personId}/subtree`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        });
        const json = await res.json();
        nodes.value = json.root ? [json.root] : [];
        drawer.value = false;
    } finally {
        loading.value = false;
    }
}

function switchView(v: 'people' | 'departments') {
    view.value = v;
    loadData();
}

function selectNode(node: OrgNode) {
    selectedNode.value = node;
    drawer.value = true;
}

onMounted(loadData);
</script>
