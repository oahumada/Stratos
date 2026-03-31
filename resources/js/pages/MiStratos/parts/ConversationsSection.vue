<script setup lang="ts">
import StCardGlass from '@/components/StCardGlass.vue';
import {
    PhArrowRight,
    PhChatTeardropText,
    PhClock,
    PhIdentificationBadge,
    PhRobot,
    PhTarget,
    PhUser,
} from '@phosphor-icons/vue';

interface Props {
    conversations: Array<any>;
    formatDate: (date: string) => string;
}

defineProps<Props>();

const getConversationIcon = (type: string) => {
    switch (type) {
        case 'psychometric':
            return PhIdentificationBadge;
        case 'mentor':
            return PhRobot;
        case 'gap_feedback':
            return PhTarget;
        default:
            return PhChatTeardropText;
    }
};

const getConversationLabel = (type: string) => {
    switch (type) {
        case 'psychometric':
            return 'Perfil ADN';
        case 'mentor':
            return 'Mentor AI';
        case 'gap_feedback':
            return 'Feedback de Brecha';
        default:
            return 'Conversación';
    }
};

const getStatusColor = (status: string) => {
    if (status.toLowerCase() === 'completado')
        return 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20';
    return 'bg-amber-500/10 text-amber-400 border-amber-500/20';
};
</script>

<template>
    <div class="animate-in duration-700 fade-in slide-in-from-left-4">
        <h2 class="mb-6 flex items-center gap-3 text-2xl font-black text-white">
            <PhChatTeardropText
                :size="28"
                weight="duotone"
                class="text-indigo-400"
            />
            Mis Conversaciones
        </h2>

        <div
            v-if="conversations.length === 0"
            class="flex flex-col items-center justify-center rounded-3xl border border-dashed border-white/5 bg-white/5 py-20"
        >
            <PhChatTeardropText
                :size="64"
                weight="thin"
                class="mb-6 text-white/20"
            />
            <h3 class="mb-2 text-xl font-bold text-white/60">
                Sin actividad reciente
            </h3>
            <p class="text-sm text-white/40">
                Tus interacciones con Cerbero AI aparecerán aquí.
            </p>
        </div>

        <div
            v-else
            class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3"
        >
            <StCardGlass
                v-for="conv in conversations"
                :key="conv.id"
                class="group cursor-pointer p-12! transition-all hover:border-white/20 active:scale-[0.98]"
            >
                <div class="relative">
                    <div class="mb-6 flex items-start justify-between">
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/5 text-indigo-400 transition-colors group-hover:bg-indigo-500/10"
                        >
                            <component
                                :is="getConversationIcon(conv.type)"
                                :size="24"
                                weight="duotone"
                            />
                        </div>
                        <div
                            :class="[
                                'rounded-lg border px-2 py-1 text-[9px] font-black tracking-widest uppercase',
                                getStatusColor(conv.status),
                            ]"
                        >
                            {{ conv.status }}
                        </div>
                    </div>

                    <h4
                        class="mb-1 text-lg font-bold text-white transition-colors group-hover:text-indigo-300"
                    >
                        {{ getConversationLabel(conv.type) }}
                    </h4>

                    <div
                        class="mb-6 flex items-center gap-2 text-[10px] font-bold tracking-widest text-white/30 uppercase"
                    >
                        <PhClock :size="12" />
                        {{ formatDate(conv.created_at) }}
                    </div>

                    <div
                        class="flex items-center justify-between border-t border-white/5 pt-4"
                    >
                        <div
                            class="flex items-center gap-2 text-[10px] font-black tracking-widest text-white/40 uppercase"
                        >
                            <PhUser :size="14" />
                            Cerbero AI
                        </div>
                        <PhArrowRight
                            :size="18"
                            weight="bold"
                            class="transform text-white/0 transition-all group-hover:translate-x-1 group-hover:text-white/40"
                        />
                    </div>
                </div>
            </StCardGlass>
        </div>
    </div>
</template>
