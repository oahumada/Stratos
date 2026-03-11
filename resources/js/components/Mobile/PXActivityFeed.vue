<script setup lang="ts">
import { formatDistanceToNow } from 'date-fns';
import { es } from 'date-fns/locale';
import { computed } from 'vue';

type ActivityType = 'badge' | 'skill' | 'quest' | 'pulse' | 'mentor';

interface ActivityItem {
    id: number;
    type: ActivityType;
    title: string;
    description: string;
    user_name: string;
    user_photo?: string;
    timestamp: string;
    metadata?: any;
}

defineProps<{
    currentUserId: number;
}>();

// Mock data representing the "Social/Activity" side of PX
const activities = computed<ActivityItem[]>(() => [
    {
        id: 1,
        type: 'badge',
        title: '¡Nueva Insignia!',
        description:
            'ha obtenido la insignia "Visionario de Datos" tras completar el reto trimestral.',
        user_name: 'Ana García',
        user_photo: 'https://i.pravatar.cc/150?u=ana',
        timestamp: new Date(Date.now() - 1000 * 60 * 45).toISOString(),
        metadata: { badge_icon: 'mdi-chart-bubble', badge_color: 'purple' },
    },
    {
        id: 2,
        type: 'quest',
        title: 'Misión Cumplida',
        description:
            'finalizó la misión "Onboarding Cortex" y ganó 500 puntos de maestría.',
        user_name: 'Tú',
        user_photo: 'https://i.pravatar.cc/150?u=me',
        timestamp: new Date(Date.now() - 1000 * 60 * 120).toISOString(),
    },
    {
        id: 3,
        type: 'skill',
        title: 'Skill Mastered',
        description: 'alcanzó Nivel 4 en "Liderazgo Adaptativo".',
        user_name: 'Carlos Ruiz',
        user_photo: 'https://i.pravatar.cc/150?u=carlos',
        timestamp: new Date(Date.now() - 1000 * 60 * 60 * 5).toISOString(),
        metadata: { skill_level: 4 },
    },
    {
        id: 4,
        type: 'mentor',
        title: 'Nueva Mentoría',
        description:
            'comenzó un viaje de aprendizaje con la IA Mentor en "Estrategia de Producto".',
        user_name: 'Elena Soto',
        user_photo: 'https://i.pravatar.cc/150?u=elena',
        timestamp: new Date(Date.now() - 1000 * 60 * 60 * 24).toISOString(),
    },
]);

const getIcon = (type: ActivityType) => {
    const icons: Record<ActivityType, string> = {
        badge: 'mdi-trophy-variant',
        skill: 'mdi-star-circle',
        quest: 'mdi-lightning-bolt',
        pulse: 'mdi-heart-pulse',
        mentor: 'mdi-account-star',
    };
    return icons[type] || 'mdi-bell';
};

const getIconColor = (type: ActivityType) => {
    const colors: Record<ActivityType, string> = {
        badge: 'amber-accent-3',
        skill: 'cyan-accent-2',
        quest: 'purple-accent-2',
        pulse: 'pink-accent-2',
        mentor: 'indigo-accent-2',
    };
    return colors[type] || 'white';
};

const formatTime = (ts: string) => {
    return formatDistanceToNow(new Date(ts), { addSuffix: true, locale: es });
};
</script>

<template>
    <div class="px-activity-feed">
        <div class="feed-header d-flex align-center justify-space-between mb-4">
            <h3 class="text-h6 font-weight-bold text-white">
                Actividad de la Comunidad
            </h3>
            <v-btn
                icon="mdi-tune-variant"
                variant="text"
                color="grey"
                size="small"
            />
        </div>

        <div class="feed-list">
            <div
                v-for="item in activities"
                :key="item.id"
                class="feed-item glass-card mb-4"
                :class="{ mine: item.user_name === 'Tú' }"
            >
                <div class="d-flex gap-4">
                    <v-avatar size="48" class="shrink-0">
                        <v-img :src="item.user_photo" cover />
                        <div
                            class="type-indicator"
                            :style="{
                                backgroundColor: `var(--v-${getIconColor(item.type)})`,
                            }"
                        >
                            <v-icon
                                :icon="getIcon(item.type)"
                                size="10"
                                color="black"
                            />
                        </div>
                    </v-avatar>

                    <div class="grow">
                        <div class="d-flex justify-space-between align-start">
                            <span class="activity-title">{{ item.title }}</span>
                            <span class="activity-time">{{
                                formatTime(item.timestamp)
                            }}</span>
                        </div>
                        <p class="activity-desc">
                            <span class="user-name">{{ item.user_name }}</span>
                            {{ item.description }}
                        </p>

                        <!-- Metadata Visuals -->
                        <div v-if="item.metadata?.badge_icon" class="mt-3">
                            <v-chip
                                size="small"
                                variant="tonal"
                                :color="item.metadata.badge_color"
                                prepend-icon="mdi-medal"
                            >
                                Achievement Unlocked
                            </v-chip>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.px-activity-feed {
    padding-bottom: 24px;
}

.feed-item {
    padding: 16px;
    border-radius: 20px;
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.05);
    transition: transform 0.2s;
}

.feed-item:active {
    transform: scale(0.98);
}

.feed-item.mine {
    background: rgba(99, 102, 241, 0.05);
    border-color: rgba(99, 102, 241, 0.1);
}

.type-indicator {
    position: absolute;
    bottom: 0;
    right: 0;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid #020617;
}

.activity-title {
    font-size: 11px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: rgba(255, 255, 255, 0.5);
}

.activity-time {
    font-size: 10px;
    color: rgba(255, 255, 255, 0.3);
}

.activity-desc {
    font-size: 13px;
    line-height: 1.5;
    color: rgba(255, 255, 255, 0.8);
    margin-top: 4px;
}

.user-name {
    font-weight: 700;
    color: white;
}
</style>
