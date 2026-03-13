<script setup lang="ts">
import { 
    PhChatTeardropText, 
    PhIdentificationBadge, 
    PhRobot, 
    PhTarget,
    PhUser,
    PhClock,
    PhArrowRight
} from '@phosphor-icons/vue';
import StCardGlass from '@/components/StCardGlass.vue';

interface Props {
    conversations: Array<any>;
    formatDate: (date: string) => string;
}

defineProps<Props>();

const getConversationIcon = (type: string) => {
    switch (type) {
        case 'psychometric': return PhIdentificationBadge;
        case 'mentor': return PhRobot;
        case 'gap_feedback': return PhTarget;
        default: return PhChatTeardropText;
    }
};

const getConversationLabel = (type: string) => {
    switch (type) {
        case 'psychometric': return 'Perfil ADN';
        case 'mentor': return 'Mentor AI';
        case 'gap_feedback': return 'Feedback de Brecha';
        default: return 'Conversación';
    }
};

const getStatusColor = (status: string) => {
    if (status.toLowerCase() === 'completado') return 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20';
    return 'bg-amber-500/10 text-amber-400 border-amber-500/20';
};
</script>

<template>
    <div class="animate-in fade-in slide-in-from-left-4 duration-700">
        <h2 class="text-2xl font-black text-white mb-6 flex items-center gap-3">
            <PhChatTeardropText :size="28" weight="duotone" class="text-indigo-400" />
            Mis Conversaciones
        </h2>

        <div v-if="conversations.length === 0" class="py-20 flex flex-col items-center justify-center bg-white/5 rounded-3xl border border-white/5 border-dashed">
            <PhChatTeardropText :size="64" weight="thin" class="text-white/20 mb-6" />
            <h3 class="text-xl font-bold text-white/60 mb-2">Sin actividad reciente</h3>
            <p class="text-sm text-white/40">Tus interacciones con Cerbero AI aparecerán aquí.</p>
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <StCardGlass 
                v-for="conv in conversations" 
                :key="conv.id"
                class="hover:border-white/20 group cursor-pointer transition-all active:scale-[0.98]"
            >
                <div class="p-6">
                    <div class="flex items-start justify-between mb-6">
                        <div class="w-12 h-12 rounded-2xl bg-white/5 flex items-center justify-center text-indigo-400 group-hover:bg-indigo-500/10 transition-colors">
                            <component :is="getConversationIcon(conv.type)" :size="24" weight="duotone" />
                        </div>
                        <div :class="['px-2 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest border', getStatusColor(conv.status)]">
                            {{ conv.status }}
                        </div>
                    </div>

                    <h4 class="text-lg font-bold text-white mb-1 group-hover:text-indigo-300 transition-colors">
                        {{ getConversationLabel(conv.type) }}
                    </h4>
                    
                    <div class="flex items-center gap-2 text-white/30 text-[10px] font-bold uppercase tracking-widest mb-6">
                        <PhClock :size="12" />
                        {{ formatDate(conv.created_at) }}
                    </div>

                    <div class="flex items-center justify-between pt-4 border-t border-white/5">
                        <div class="flex items-center gap-2 text-white/40 text-[10px] font-black uppercase tracking-widest">
                            <PhUser :size="14" />
                            Cerbero AI
                        </div>
                        <PhArrowRight :size="18" weight="bold" class="text-white/0 group-hover:text-white/40 transition-all transform group-hover:translate-x-1" />
                    </div>
                </div>
            </StCardGlass>
        </div>
    </div>
</template>
