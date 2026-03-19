<script setup lang="ts">
import { Handle, Position, type NodeProps } from '@vue-flow/core';
import { Users, Briefcase, TrendingUp, Edit3 } from 'lucide-vue-next';

interface DepartmentData {
    label: string;
    description?: string;
    managerName?: string;
    headcount?: number;
    payroll?: string;
    departmentId?: number;
}

const props = defineProps<NodeProps<DepartmentData>>();
const emit = defineEmits<{
    editHierarchy: [departmentId: number];
}>();

const handleEditClick = (e: Event) => {
    e.stopPropagation();
    if (props.data.departmentId) {
        emit('editHierarchy', props.data.departmentId);
    }
};
</script>

<template>
    <div class="dept-node glass-card group">
        <Handle type="target" :position="Position.Top" class="handle-top" />
        
        <div class="flex items-start justify-between gap-2 mb-2">
            <div class="node-header flex-1">
                <div class="node-icon">
                    <Briefcase class="h-4 w-4 text-indigo-400" />
                </div>
                <div class="node-title">{{ data.label }}</div>
            </div>
            <button
                @click="handleEditClick"
                class="opacity-0 group-hover:opacity-100 transition-opacity p-1.5 rounded-lg hover:bg-indigo-500/20 text-indigo-400 hover:text-indigo-300"
                title="Editar jerarquía"
            >
                <Edit3 class="h-4 w-4" />
            </button>
        </div>

        <div v-if="data.description" class="node-desc">
            {{ data.description }}
        </div>

        <div class="node-footer">
            <div v-if="data.managerName" class="footer-item">
                <Users class="h-3 w-3 text-emerald-400" />
                <span>{{ data.managerName }}</span>
            </div>
            <div v-if="data.headcount !== undefined" class="footer-item">
                <TrendingUp class="h-3 w-3 text-blue-400" />
                <span>{{ data.headcount }} p.</span>
            </div>
        </div>

        <Handle type="source" :position="Position.Bottom" class="handle-bottom" />
    </div>
</template>

<style scoped>
.dept-node {
    padding: 16px;
    border-radius: 16px;
    min-width: 220px;
    background: rgba(15, 23, 42, 0.6);
    backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 255, 255, 0.08);
    box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3);
    color: white;
    transition: all 0.3s ease;
}

.dept-node:hover {
    border-color: rgba(99, 102, 241, 0.4);
    box-shadow: 0 8px 32px 0 rgba(99, 102, 241, 0.15);
    transform: translateY(-2px);
}

.node-header {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 8px;
}

.node-icon {
    background: rgba(99, 102, 241, 0.1);
    padding: 6px;
    border-radius: 8px;
}

.node-title {
    font-weight: 600;
    font-size: 14px;
}

.node-desc {
    font-size: 11px;
    color: #9ca3af;
    margin-bottom: 12px;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.node-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-top: 1px solid rgba(255, 255, 255, 0.05);
    padding-top: 8px;
    gap: 8px;
}

.footer-item {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 10px;
    color: #d1d5db;
}

.handle-top, .handle-bottom {
    background-color: #6366f1;
    width: 8px;
    height: 8px;
    border: 2px solid white;
}
</style>
