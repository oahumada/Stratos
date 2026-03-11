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
    PhBroadcast,
    PhDatabase,
    PhGear,
    PhMagnet,
    PhSmiley,
    PhTrendUp,
} from '@phosphor-icons/vue';

defineProps<{
    items: NavItem[];
}>();

const page = usePage();

const platformApps: NavItem[] = [
    { title: 'Stratos Core', href: '/core', icon: PhDatabase },
    { title: 'Stratos Radar', href: '/radar', icon: PhBroadcast },
    { title: 'Stratos PX', href: '/px', icon: PhSmiley },
    { title: 'Stratos Growth', href: '/growth', icon: PhTrendUp },
    { title: 'Stratos Magnet', href: '/magnet', icon: PhMagnet },
    { title: 'Control Center', href: '/controlcenter', icon: PhGear },
];
</script>

<template>
    <SidebarGroup class="px-3 py-2">
        <SidebarGroupLabel> Plataforma </SidebarGroupLabel>
        <SidebarMenu class="gap-1.5">
            <SidebarMenuItem v-for="item in platformApps" :key="item.title">
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

    <!-- Group 2: Modules (Passed from AppSidebar items prop) -->
    <SidebarGroup v-if="items && items.length > 0" class="px-3 py-2">
        <SidebarGroupLabel> Módulos de Sistema </SidebarGroupLabel>
        <SidebarMenu class="gap-1.5">
            <SidebarMenuItem v-for="item in items" :key="item.title">
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
