<script setup lang="ts">
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { urlIsActive } from '@/lib/utils';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';

import {
    Cog,
    Database,
    Magnet,
    Radar,
    Smile,
    TrendingUp,
} from 'lucide-vue-next';

defineProps<{
    items: NavItem[];
}>();

const page = usePage();

const mainNavItems: NavItem[] = [
    { title: 'Stratos Core', href: '/core', icon: Database },
    { title: 'Stratos Radar', href: '/radar', icon: Radar },
    { title: 'Stratos PX', href: '/px', icon: Smile },
    { title: 'Stratos Growth', href: '/growth', icon: TrendingUp },
    { title: 'Stratos Magnet', href: '/magnet', icon: Magnet },
    { title: 'Control Center', href: '/controlcenter', icon: Cog },
];
</script>

<template>
    <SidebarGroup class="px-2 py-0">
        <SidebarGroupLabel>Plataforma</SidebarGroupLabel>
        <SidebarMenu>
            <SidebarMenuItem v-for="item in mainNavItems" :key="item.title">
                <SidebarMenuButton
                    as-child
                    :is-active="urlIsActive(item.href, page.url)"
                    :tooltip="item.title"
                >
                    <Link :href="item.href">
                        <component :is="item.icon" />
                        <span>{{ item.title }}</span>
                    </Link>
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
</template>
