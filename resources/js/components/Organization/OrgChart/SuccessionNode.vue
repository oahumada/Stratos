<script setup lang="ts">
import { Handle, Position } from '@vue-flow/core';
import { Briefcase, Star, UserCheck } from 'lucide-vue-next';

defineProps<{
    data: {
        label: string;
        type: 'person' | 'vacancy';
        fit?: number;
    };
}>();
</script>

<template>
    <div
        class="succession-node glass-card"
        :class="
            data.type === 'vacancy'
                ? 'border-amber-500/40'
                : 'border-indigo-500/40'
        "
    >
        <Handle type="target" :position="Position.Left" class="handle-flow" />

        <div class="node-content">
            <div
                class="icon-avatar"
                :class="
                    data.type === 'vacancy'
                        ? 'bg-amber-500/20'
                        : 'bg-indigo-500/20'
                "
            >
                <Briefcase
                    v-if="data.type === 'vacancy'"
                    class="h-4 w-4 text-amber-400"
                />
                <UserCheck v-else class="h-4 w-4 text-indigo-400" />
            </div>

            <div class="text-info">
                <div class="node-label">{{ data.label }}</div>
                <div v-if="data.fit !== undefined" class="fit-badge">
                    <Star class="mr-1 h-3 w-3" />
                    {{ (data.fit * 100).toFixed(0) }}% Fit
                </div>
            </div>
        </div>

        <Handle type="source" :position="Position.Right" class="handle-flow" />
    </div>
</template>

<style scoped>
.succession-node {
    padding: 12px 16px;
    border-radius: 14px;
    min-width: 180px;
    background: rgba(15, 23, 42, 0.7);
    backdrop-filter: blur(12px);
    border-width: 1px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
    color: white;
}

.node-content {
    display: flex;
    align-items: center;
    gap: 12px;
}

.icon-avatar {
    padding: 8px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.text-info {
    display: flex;
    flex-direction: column;
}

.node-label {
    font-size: 13px;
    font-weight: 600;
}

.fit-badge {
    display: flex;
    align-items: center;
    font-size: 10px;
    color: #10b981;
    margin-top: 2px;
}

.handle-flow {
    background-color: #6366f1;
    width: 6px;
    height: 6px;
}
</style>
