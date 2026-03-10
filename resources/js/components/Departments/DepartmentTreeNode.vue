<script setup lang="ts">
import { Users } from 'lucide-vue-next';

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

defineProps<{
    node: DepartmentNode;
}>();
</script>

<template>
    <div class="relative rounded-lg border border-white/10 bg-white/3 p-4">
        <h3 class="mb-2 text-lg font-semibold text-white">{{ node.name }}</h3>

        <p v-if="node.description" class="mb-3 text-sm text-gray-400">
            {{ node.description }}
        </p>

        <div class="flex items-center gap-2 text-sm text-gray-300">
            <Users class="h-4 w-4 text-emerald-400" />
            <span>
                Leader:
                <template v-if="node.manager">
                    {{ node.manager.first_name }} {{ node.manager.last_name }}
                </template>
                <em v-else class="text-gray-500">Not assigned</em>
            </span>
        </div>

        <div
            v-if="node.children && node.children.length > 0"
            class="mt-4 space-y-4 border-l-2 border-white/5 pl-4"
        >
            <DepartmentTreeNode
                v-for="child in node.children"
                :key="child.id"
                :node="child"
            />
        </div>
    </div>
</template>
