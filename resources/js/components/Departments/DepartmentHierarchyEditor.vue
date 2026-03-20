<script setup lang="ts">
import StButtonGlass from '@/components/StButtonGlass.vue';
import StCardGlass from '@/components/StCardGlass.vue';
import { useApi } from '@/composables/useApi';
import { useNotification } from '@/composables/useNotification';
import { AlertCircle, Check, X } from 'lucide-vue-next';
import { computed, ref } from 'vue';

interface DepartmentNode {
    id: number;
    name: string;
    parent_id: number | null;
    children?: DepartmentNode[];
}

interface Props {
    department: DepartmentNode;
    allDepartments: DepartmentNode[];
    open: boolean;
}

const props = defineProps<Props>();
const emit = defineEmits<{
    close: [];
    updated: [Department: DepartmentNode];
}>();

const { showSuccess, showError } = useNotification();
const { put } = useApi();
const loading = ref(false);
const selectedParentId = ref<number | null>(props.department.parent_id);

// Filtrar departamentos: no puede ser padre de sí mismo ni de sus descendientes
const availableParents = computed(() => {
    const getDescendantIds = (dept: DepartmentNode): number[] => {
        const ids = [dept.id];
        if (dept.children) {
            dept.children.forEach((child) => {
                ids.push(...getDescendantIds(child));
            });
        }
        return ids;
    };

    const invalidIds = getDescendantIds(props.department);

    return props.allDepartments.filter((dept) => !invalidIds.includes(dept.id));
});

const currentParentName = computed(() => {
    if (!props.department.parent_id) return 'Sin padre (Nivel superior)';
    return (
        props.allDepartments.find((d) => d.id === props.department.parent_id)
            ?.name || 'Desconocido'
    );
});

const selectedParentName = computed(() => {
    if (!selectedParentId.value) return 'Sin padre (Nivel superior)';
    return (
        props.allDepartments.find((d) => d.id === selectedParentId.value)
            ?.name || 'Desconocido'
    );
});

const hasChanges = computed(() => {
    return selectedParentId.value !== props.department.parent_id;
});

const save = async () => {
    loading.value = true;
    try {
        const response = await put(
            `/api/departments/${props.department.id}/hierarchy`,
            { parent_id: selectedParentId.value },
        );

        showSuccess(`Jerarquía actualizada: ${props.department.name}`);
        emit('updated', response);
        emit('close');
    } catch (error: any) {
        showError(
            error.response?.data?.message || 'Error al actualizar jerarquía',
        );
    } finally {
        loading.value = false;
    }
};

const revert = () => {
    selectedParentId.value = props.department.parent_id;
};
</script>

<template>
    <div
        v-if="open"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        @click.self="$emit('close')"
    >
        <!-- Backdrop -->
        <div
            class="absolute inset-0 bg-black/60 backdrop-blur-sm"
            @click="$emit('close')"
        ></div>

        <!-- Modal -->
        <StCardGlass class="relative w-full max-w-md space-y-6 p-6">
            <!-- Header -->
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-xl font-bold text-white">
                        Editar Jerarquía
                    </h2>
                    <p class="mt-1 text-sm text-gray-400">
                        {{ props.department.name }}
                    </p>
                </div>
                <button
                    @click="$emit('close')"
                    class="rounded-lg p-2 text-gray-400 transition-colors hover:bg-white/10 hover:text-white"
                >
                    <X class="h-5 w-5" />
                </button>
            </div>

            <!-- Current Parent Info -->
            <div class="rounded-lg border border-white/10 bg-white/5 p-4">
                <p
                    class="mb-2 text-xs font-semibold tracking-wide text-gray-400 uppercase"
                >
                    Padre Actual
                </p>
                <p class="text-sm font-medium text-white">
                    {{ currentParentName }}
                </p>
            </div>

            <!-- Parent Selector -->
            <div class="space-y-3">
                <label
                    class="text-xs font-semibold tracking-wide text-gray-400 uppercase"
                >
                    Nuevo Padre
                </label>

                <!-- Option: Sin padre -->
                <label
                    class="flex cursor-pointer items-center gap-3 rounded-lg border border-white/10 bg-white/5 p-3 transition-all hover:bg-white/10"
                    :class="
                        selectedParentId === null
                            ? 'border-indigo-500/50 bg-indigo-500/10'
                            : ''
                    "
                >
                    <input
                        type="radio"
                        :value="null"
                        v-model="selectedParentId"
                        class="h-4 w-4"
                    />
                    <div>
                        <p class="text-sm font-medium text-white">Sin padre</p>
                        <p class="text-xs text-gray-500">
                            Nivel superior (raíz)
                        </p>
                    </div>
                </label>

                <!-- Option: Departamentos disponibles -->
                <div class="max-h-44 space-y-2 overflow-y-auto pr-2">
                    <label
                        v-for="dept in availableParents.filter(
                            (d) => d.id !== props.department.id,
                        )"
                        :key="dept.id"
                        class="flex cursor-pointer items-center gap-3 rounded-lg border border-white/10 bg-white/5 p-3 transition-all hover:bg-white/10"
                        :class="
                            selectedParentId === dept.id
                                ? 'border-indigo-500/50 bg-indigo-500/10'
                                : ''
                        "
                    >
                        <input
                            type="radio"
                            :value="dept.id"
                            v-model="selectedParentId"
                            class="h-4 w-4"
                        />
                        <div>
                            <p class="text-sm font-medium text-white">
                                {{ dept.name }}
                            </p>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Selected Parent Preview -->
            <div
                v-if="selectedParentId !== props.department.parent_id"
                class="rounded-lg border border-indigo-500/30 bg-indigo-500/10 p-4"
            >
                <p
                    class="mb-2 flex items-center gap-2 text-xs font-semibold tracking-wide text-indigo-400 uppercase"
                >
                    <AlertCircle class="h-4 w-4" />
                    Cambio Detectado
                </p>
                <p class="text-sm text-indigo-100">
                    {{ props.department.name }} pasará a depender de:
                    <strong>{{ selectedParentName }}</strong>
                </p>
            </div>

            <!-- Actions -->
            <div class="flex gap-3 pt-4">
                <button
                    @click="revert"
                    class="flex-1 rounded-lg bg-white/10 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-white/20"
                    :disabled="!hasChanges || loading"
                >
                    Revertir
                </button>
                <StButtonGlass
                    @click="save"
                    :disabled="!hasChanges || loading"
                    :loading="loading"
                    class="flex-1"
                >
                    <Check class="mr-2 h-4 w-4" v-if="!loading" />
                    {{ loading ? 'Guardando...' : 'Guardar' }}
                </StButtonGlass>
            </div>

            <!-- Info -->
            <p class="text-center text-xs text-gray-500">
                Los cambios se guardarán inmediatamente en la base de datos
            </p>
        </StCardGlass>
    </div>
</template>

<style scoped>
::-webkit-scrollbar {
    width: 6px;
}

::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 3px;
}

::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.3);
}
</style>
