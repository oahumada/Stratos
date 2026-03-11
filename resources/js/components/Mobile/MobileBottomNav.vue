<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();

const navItems = [
    {
        title: 'Inicio',
        icon: 'mdi-home-variant-outline',
        activeIcon: 'mdi-home-variant',
        href: '/mi-stratos',
    },
    {
        title: 'Explorar',
        icon: 'mdi-compass-outline',
        activeIcon: 'mdi-compass',
        href: '/dashboard/analytics',
    },
    {
        title: 'Pulse',
        icon: 'mdi-heart-pulse',
        activeIcon: 'mdi-heart-pulse',
        href: '/mi-stratos?tab=pulse',
        isAction: true,
    },
    {
        title: 'Crecimiento',
        icon: 'mdi-school-outline',
        activeIcon: 'mdi-school',
        href: '/learning-paths',
    },
    {
        title: 'Perfil',
        icon: 'mdi-account-circle-outline',
        activeIcon: 'mdi-account-circle',
        href: '/mi-stratos?tab=profile',
    },
];

const currentPath = computed(() => page.url);

const isActive = (href: string) => {
    return currentPath.value.startsWith(href.split('?')[0]);
};
</script>

<template>
    <div class="mobile-bottom-nav md:hidden">
        <div class="nav-container">
            <template v-for="item in navItems" :key="item.href">
                <Link
                    :href="item.href"
                    class="nav-item"
                    :class="{
                        active: isActive(item.href),
                        'action-item': item.isAction,
                    }"
                >
                    <div class="icon-container">
                        <v-icon
                            :icon="
                                isActive(item.href)
                                    ? item.activeIcon
                                    : item.icon
                            "
                            :size="item.isAction ? 32 : 24"
                        />
                        <div v-if="item.isAction" class="pulse-ring"></div>
                    </div>
                    <span class="nav-label">{{ item.title }}</span>
                </Link>
            </template>
        </div>
    </div>
</template>

<style scoped>
.mobile-bottom-nav {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    z-index: 1000;
    padding: 12px 16px 24px 16px;
    background: linear-gradient(
        to top,
        rgba(2, 6, 23, 0.9) 0%,
        rgba(2, 6, 23, 0.4) 100%
    );
    backdrop-filter: blur(20px);
    border-top: 1px solid rgba(255, 255, 255, 0.08);
}

.nav-container {
    display: flex;
    justify-content: space-around;
    align-items: flex-end;
    max-width: 500px;
    margin: 0 auto;
}

.nav-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-decoration: none;
    color: rgba(255, 255, 255, 0.5);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    padding: 4px;
    min-width: 64px;
}

.nav-label {
    font-size: 10px;
    font-weight: 500;
    margin-top: 4px;
    opacity: 0.8;
}

.icon-container {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 32px;
}

.nav-item.active {
    color: #6366f1; /* Indigo 500 */
}

.nav-item.active .nav-label {
    opacity: 1;
}

/* Central Active Action Item (Pulse) */
.action-item {
    top: -16px;
}

.action-item .icon-container {
    background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
    color: white;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    box-shadow: 0 8px 16px -4px rgba(99, 102, 241, 0.5);
    border: 4px solid rgba(2, 6, 23, 0.8);
}

.action-item .nav-label {
    margin-top: 8px;
}

.pulse-ring {
    position: absolute;
    width: 100%;
    height: 100%;
    border-radius: 50%;
    border: 2px solid rgba(99, 102, 241, 0.3);
    animation: ring-pulse 2s infinite;
}

@keyframes ring-pulse {
    0% {
        transform: scale(1);
        opacity: 0.5;
    }
    100% {
        transform: scale(1.6);
        opacity: 0;
    }
}

.nav-item:active {
    transform: scale(0.9);
}
</style>
