<script setup lang="ts">
import { Handle, Position, type NodeProps } from '@vue-flow/core';
import { Briefcase, TrendingUp, Users } from 'lucide-vue-next';

interface DepartmentData {
    label: string;
    description?: string;
    managerName?: string;
    headcount?: number;
    payroll?: string;
}

defineProps<NodeProps<DepartmentData>>();
</script>

<template>
    <div class="dept-node glass-card">
        <Handle type="target" :position="Position.Top" class="handle-top" />

        <div class="node-header">
            <div class="node-icon">
                <Briefcase class="h-4 w-4 text-indigo-400" />
            </div>
            <div class="node-title">{{ data.label }}</div>
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

        <Handle
            type="source"
            :position="Position.Bottom"
            class="handle-bottom"
        />
    </div>
</template>

<style scoped>
.dept-node {
    padding: 16px;
    border-radius: 16px;
    min-width: 220px;
    background: rgba(255, 255, 255, 0.08);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid rgba(255, 255, 255, 0.15);
    box-shadow:
        0 8px 32px 0 rgba(0, 0, 0, 0.3),
        inset 0 1px 2px 0 rgba(255, 255, 255, 0.1);
    color: white;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.dept-node::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(
        135deg,
        rgba(255, 255, 255, 0.05),
        transparent 40%,
        transparent 60%,
        rgba(255, 255, 255, 0.02)
    );
    pointer-events: none;
    border-radius: 16px;
}

.dept-node:hover {
    background: rgba(255, 255, 255, 0.12);
    border-color: rgba(99, 102, 241, 0.5);
    box-shadow:
        0 12px 40px 0 rgba(99, 102, 241, 0.2),
        inset 0 1px 2px 0 rgba(255, 255, 255, 0.15);
    transform: translateY(-2px);
}

.node-header {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 8px;
}

.node-icon {
    background: linear-gradient(
        135deg,
        rgba(99, 102, 241, 0.25),
        rgba(99, 102, 241, 0.1)
    );
    padding: 6px;
    border-radius: 8px;
    border: 1px solid rgba(99, 102, 241, 0.3);
    backdrop-filter: blur(8px);
}

.node-title {
    font-weight: 700;
    font-size: 14px;
    color: #f1f5f9;
    letter-spacing: -0.01em;
}

.node-desc {
    font-size: 11px;
    color: #cbd5e1;
    margin-bottom: 12px;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    padding-bottom: 8px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
}

.node-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 8px;
    gap: 8px;
}

.footer-item {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 10px;
    color: #e2e8f0;
    font-weight: 500;
}

.handle-top,
.handle-bottom {
    background-color: #6366f1;
    width: 8px;
    height: 8px;
    border: 2px solid white;
}
</style>
