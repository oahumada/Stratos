<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { useNotification } from '@/composables/useNotification';
import { useApi } from '@/composables/useApi';
import { AlertCircle, Check, X } from 'lucide-vue-next';
import StButtonGlass from '@/components/StButtonGlass.vue';
import StCardGlass from '@/components/StCardGlass.vue';

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

const { notify } = useNotification();
const { put } = useApi();
const loading = ref(false);
const selectedParentId = ref<number | null>(props.department.parent_id);

// Filtrar departamentos: no puede ser padre de sí mismo ni de sus descendientes
const availableParents = computed(() => {
    const getDescendantIds = (dept: DepartmentNode): number[] => {
        const ids = [dept.id];
        if (dept.children) {
            dept.children.forEach(child => {
                ids.push(...getDescendantIds(child));
            });
        }
        return ids;
    };

    const invalidIds = getDescendantIds(props.department);

    return props.allDepartments.filter(
        dept => !invalidIds.includes(dept.id)
    );
});

const currentParentName = computed(() => {
    if (!props.department.parent_id) return 'Sin padre (Nivel superior)';
    return props.allDepartments.find(d => d.id === props.department.parent_id)?.name || 'Desconocido';
});

const selectedParentName = computed(() => {
    if (!selectedParentId.value) return 'Sin padre (Nivel superior)';
    return props.allDepartments.find(d => d.id === selectedParentId.value)?.name || 'Desconocido';
});

const hasChanges = computed(() => {
    return selectedParentId.value !== props.department.parent_id;
});

const save = async () => {
    loading.value = true;
    try {
        const response = await apiHelper.put(
            `/api/departments/${props.department.id}/hierarchy`,
            { parent_id: selectedParentId.value }
        );

        notify.success(`Jerarquía actualizada: ${props.department.name}`);
        emit('updated', response);
        emit('close');
    } catch (error: any) {
        notify.error(error.response?.data?.message || 'Error al actualizar jerarquía');
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
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="$emit('close')"></div>

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
                    class="rounded-lg p-2 hover:bg-white/10 transition-colors text-gray-400 hover:text-white"
                >
                    <X class="h-5 w-5" />
                </button>
            </div>

            <!-- Current Parent Info -->
            <div class="rounded-lg bg-white/5 border border-white/10 p-4">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">Padre Actual</p>
                <p class="text-sm font-medium text-white">
                    {{ currentParentName }}
                </p>
            </div>

            <!-- Parent Selector -->
            <div class="space-y-3">
                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wide">
                    Nuevo Padre
                </label>
                
                <!-- Option: Sin padre -->
                <label class="flex items-center gap-3 p-3 rounded-lg border border-white/10 bg-white/5 cursor-pointer hover:bg-white/10 transition-all"
                    :class="selectedParentId === null ? 'border-indigo-500/50 bg-indigo-500/10' : ''"
                >
                    <input
                        type="radio"
                        :value="null"
                        v-model="selectedParentId"
                        class="w-4 h-4"
                    />
                    <div>
                        <p class="text-sm font-medium text-white">Sin padre</p>
                        <p class="text-xs text-gray-500">Nivel superior (raíz)</p>
                    </div>
                </label>

                <!-- Option: Departamentos disponibles -->
                <div class="max-h-44 overflow-y-auto space-y-2 pr-2">
                    <label 
                        v-for="dept in availableParents.filter(d => d.id !== props.department.id)"
                        :key="dept.id"
                        class="flex items-center gap-3 p-3 rounded-lg border border-white/10 bg-white/5 cursor-pointer hover:bg-white/10 transition-all"
                        :class="selectedParentId === dept.id ? 'border-indigo-500/50 bg-indigo-500/10' : ''"
                    >
                        <input
                            type="radio"
                            :value="dept.id"
                            v-model="selectedParentId"
                            class="w-4 h-4"
                        />
                        <div>
                            <p class="text-sm font-medium text-white">{{ dept.name }}</p>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Selected Parent Preview -->
            <div v-if="selectedParentId !== props.department.parent_id" class="rounded-lg bg-indigo-500/10 border border-indigo-500/30 p-4">
                <p class="text-xs font-semibold text-indigo-400 uppercase tracking-wide mb-2 flex items-center gap-2">
                    <AlertCircle class="h-4 w-4" />
                    Cambio Detectado
                </p>
                <p class="text-sm text-indigo-100">
                    {{ props.department.name }} pasará a depender de: <strong>{{ selectedParentName }}</strong>
                </p>
            </div>

            <!-- Actions -->
            <div class="flex gap-3 pt-4">
                <button
                    @click="revert"
                    class="flex-1 px-4 py-2 rounded-lg bg-white/10 hover:bg-white/20 text-white transition-colors text-sm font-medium"
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
                    <Check class="h-4 w-4 mr-2" v-if="!loading" />
                    {{ loading ? 'Guardando...' : 'Guardar' }}
                </StButtonGlass>
            </div>

            <!-- Info -->
            <p class="text-xs text-gray-500 text-center">
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
