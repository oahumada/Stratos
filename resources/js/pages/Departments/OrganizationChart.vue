<script setup lang="ts">
import * as apiHelper from '@/apiHelper';
import DepartmentTreeNode from '@/components/Departments/DepartmentTreeNode.vue';
import StButtonGlass from '@/components/StButtonGlass.vue';
import { Head } from '@inertiajs/vue3';
import { Layers, Users } from 'lucide-vue-next';
import { onMounted, ref } from 'vue';

interface DepartmentNode {
    id: number;
    name: string;
    description: string | null;
    parent_id: number | null;
    manager: {
        id: number;
        first_name: string;
        last_name: string;
        email: string;
        avatar_url: string | null;
    } | null;
    children: DepartmentNode[];
}

const loading = ref(true);
const treeData = ref<DepartmentNode[]>([]);
const error = ref<string | null>(null);

const fetchTree = async () => {
    loading.value = true;
    error.value = null;
    try {
        const response = await apiHelper.get<
            DepartmentNode[] | { data?: DepartmentNode[] }
        >('/api/departments/tree');

        if (Array.isArray(response)) {
            treeData.value = response;
        } else if (Array.isArray(response?.data)) {
            treeData.value = response.data;
        } else {
            treeData.value = [];
        }
    } catch (e: any) {
        error.value = 'Error al cargar el organigrama: ' + e.message;
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    fetchTree();
});
</script>

<template>
    <Head title="Stratos Map - Organigrama Activo" />

    <div class="mx-auto max-w-7xl space-y-8 px-4 py-8 sm:px-6 lg:px-8">
        <header
            class="flex flex-col justify-between gap-4 md:flex-row md:items-center"
        >
            <div>
                <h1 class="mb-2 text-3xl font-bold tracking-tight text-white">
                    Organigrama Activo
                </h1>
                <p class="text-gray-400">
                    Estructura jerárquica y de liderazgo necesaria para calcular
                    la temperatura en Stratos Map.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <StButtonGlass variant="primary">
                    <Layers class="mr-2 h-4 w-4" />
                    Añadir Departamento
                </StButtonGlass>
            </div>
        </header>

        <div
            class="bg-surface relative min-h-[400px] overflow-hidden rounded-2xl border border-white/5 p-6"
        >
            <div
                v-if="loading"
                class="bg-surface/50 absolute inset-0 z-10 flex items-center justify-center backdrop-blur-sm"
            >
                <div
                    class="h-8 w-8 animate-spin rounded-full border-4 border-emerald-500/30 border-t-emerald-500"
                ></div>
            </div>

            <div v-else-if="error" class="py-12 text-center text-red-400">
                {{ error }}
            </div>

            <div v-else-if="treeData.length === 0" class="py-16 text-center">
                <div
                    class="mb-4 inline-flex h-16 w-16 items-center justify-center rounded-full bg-white/5"
                >
                    <Users class="h-8 w-8 text-white/50" />
                </div>
                <h3 class="mb-2 text-lg font-medium text-white">
                    No hay estructura definida
                </h3>
                <p class="text-gray-400">
                    Haz clic en "Añadir Departamento" para construir la
                    jerarquía raíz (CEO / Empresa).
                </p>
            </div>

            <!-- Árbol dinámico -->
            <div v-else class="space-y-4">
                <DepartmentTreeNode
                    v-for="rootNode in treeData"
                    :key="rootNode.id"
                    :node="rootNode"
                />
            </div>
        </div>
    </div>
</template>
