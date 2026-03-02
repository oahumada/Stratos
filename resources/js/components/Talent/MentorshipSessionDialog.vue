<template>
    <v-dialog v-model="dialog" max-width="700" persistent class="glass-dialog">
        <div
            class="overflow-hidden rounded-[2rem] border border-white/10 bg-slate-900/80 p-1 shadow-2xl shadow-black/50 backdrop-blur-2xl"
        >
            <div
                class="relative overflow-hidden rounded-[1.8rem] bg-gradient-to-br from-white/5 to-transparent p-6"
            >
                <!-- Header -->
                <div class="mb-8 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-2xl border border-indigo-500/30 bg-indigo-500/10 text-indigo-400"
                        >
                            <PhUsers :size="24" />
                        </div>
                        <div>
                            <h2
                                class="text-xl font-black tracking-tight text-white"
                            >
                                {{
                                    t(
                                        'talent_development.mentorship_sessions.title',
                                    )
                                }}
                            </h2>
                            <p
                                class="text-[10px] font-bold tracking-widest text-white/30 uppercase"
                            >
                                {{ actionTitle }}
                            </p>
                        </div>
                    </div>
                    <StButtonGlass
                        variant="ghost"
                        :icon="PhX"
                        circle
                        size="sm"
                        @click="dialog = false"
                    />
                </div>

                <!-- Tabs -->
                <div class="mb-6 flex gap-2">
                    <button
                        v-for="tab in [
                            {
                                id: 'list',
                                label: t(
                                    'talent_development.mentorship_sessions.tabs.history',
                                ),
                                icon: PhChatTeardropText,
                            },
                            {
                                id: 'add',
                                label: t(
                                    'talent_development.mentorship_sessions.tabs.new',
                                ),
                                icon: PhPlus,
                            },
                        ]"
                        :key="tab.id"
                        @click="activeTab = tab.id"
                        :class="[
                            'flex flex-1 items-center justify-center gap-2 rounded-xl py-3 text-xs font-bold transition-all duration-300',
                            activeTab === tab.id
                                ? 'border border-indigo-500/30 bg-indigo-500/10 text-indigo-300'
                                : 'text-white/40 hover:bg-white/5 hover:text-white',
                        ]"
                    >
                        <component :is="tab.icon" :size="14" />
                        {{ tab.label }}
                    </button>
                </div>

                <!-- Content Area -->
                <div class="min-h-[300px]">
                    <!-- List View -->
                    <div
                        v-if="activeTab === 'list'"
                        class="animate-in space-y-4 duration-300 fade-in slide-in-from-bottom-2"
                    >
                        <div v-if="loading" class="flex justify-center py-12">
                            <div
                                class="h-8 w-8 animate-spin rounded-full border-2 border-indigo-500/20 border-t-indigo-500"
                            />
                        </div>

                        <div
                            v-else-if="sessions.length === 0"
                            class="flex flex-col items-center justify-center py-12 text-center"
                        >
                            <div class="mb-4 text-white/10">
                                <PhChatTeardropText :size="48" weight="thin" />
                            </div>
                            <p class="text-sm font-medium text-white/30">
                                {{
                                    t(
                                        'talent_development.mentorship_sessions.empty',
                                    )
                                }}
                            </p>
                            <StButtonGlass
                                variant="ghost"
                                size="sm"
                                class="mt-4"
                                @click="activeTab = 'add'"
                            >
                                {{
                                    t(
                                        'talent_development.mentorship_sessions.tabs.new',
                                    )
                                }}
                            </StButtonGlass>
                        </div>

                        <div
                            v-else
                            class="custom-scrollbar max-h-[400px] space-y-4 overflow-y-auto pr-2"
                        >
                            <div
                                v-for="session in sessions"
                                :key="session.id"
                                class="rounded-2xl border border-white/5 bg-white/5 p-4 transition-colors hover:border-white/10"
                            >
                                <div
                                    class="mb-3 flex items-center justify-between"
                                >
                                    <div
                                        class="flex items-center gap-2 text-xs font-bold text-white/50"
                                    >
                                        <PhCalendar :size="14" />
                                        {{ formatDate(session.session_date) }}
                                    </div>
                                    <StBadgeGlass
                                        :variant="
                                            getStatusVariant(session.status)
                                        "
                                    >
                                        {{ session.status }}
                                    </StBadgeGlass>
                                </div>

                                <blockquote
                                    class="mb-4 text-sm leading-relaxed text-white/70 italic before:content-['“'] after:content-['”']"
                                >
                                    {{ session.summary }}
                                </blockquote>

                                <div
                                    v-if="session.next_steps"
                                    class="rounded-xl border border-white/5 bg-white/5 p-3"
                                >
                                    <div
                                        class="mb-1 text-[10px] font-black tracking-widest text-indigo-400 uppercase"
                                    >
                                        {{
                                            t(
                                                'talent_development.mentorship_sessions.next_steps',
                                            )
                                        }}
                                    </div>
                                    <p class="text-xs text-white/50">
                                        {{ session.next_steps }}
                                    </p>
                                </div>

                                <div
                                    class="mt-3 flex items-center gap-2 text-[10px] font-bold tracking-widest text-white/20 uppercase"
                                >
                                    <PhClock :size="12" />
                                    {{ session.duration_minutes }} min
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Add View -->
                    <div
                        v-else
                        class="animate-in duration-300 fade-in slide-in-from-bottom-2"
                    >
                        <form @submit.prevent="saveSession" class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <label
                                        class="px-1 text-[10px] font-black tracking-widest text-white/30 uppercase"
                                    >
                                        {{
                                            t(
                                                'talent_development.mentorship_sessions.form.date',
                                            )
                                        }}
                                    </label>
                                    <input
                                        v-model="newSession.session_date"
                                        type="date"
                                        required
                                        class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm text-white transition-all focus:border-indigo-500/50 focus:outline-none"
                                    />
                                </div>
                                <div class="space-y-1.5">
                                    <label
                                        class="px-1 text-[10px] font-black tracking-widest text-white/30 uppercase"
                                    >
                                        {{
                                            t(
                                                'talent_development.mentorship_sessions.form.duration',
                                            )
                                        }}
                                    </label>
                                    <input
                                        v-model="newSession.duration_minutes"
                                        type="number"
                                        class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm text-white transition-all focus:border-indigo-500/50 focus:outline-none"
                                    />
                                </div>
                            </div>

                            <div class="space-y-1.5">
                                <label
                                    class="px-1 text-[10px] font-black tracking-widest text-white/30 uppercase"
                                >
                                    {{
                                        t(
                                            'talent_development.mentorship_sessions.form.summary',
                                        )
                                    }}
                                </label>
                                <textarea
                                    v-model="newSession.summary"
                                    required
                                    rows="4"
                                    class="w-full resize-none rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white transition-all focus:border-indigo-500/50 focus:outline-none"
                                    :placeholder="
                                        t(
                                            'talent_development.mentorship_sessions.form.summary_placeholder',
                                        )
                                    "
                                ></textarea>
                            </div>

                            <div class="space-y-1.5">
                                <label
                                    class="px-1 text-[10px] font-black tracking-widest text-white/30 uppercase"
                                >
                                    {{
                                        t(
                                            'talent_development.mentorship_sessions.form.next_steps',
                                        )
                                    }}
                                </label>
                                <textarea
                                    v-model="newSession.next_steps"
                                    rows="2"
                                    class="w-full resize-none rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white transition-all focus:border-indigo-500/50 focus:outline-none"
                                    :placeholder="
                                        t(
                                            'talent_development.mentorship_sessions.form.next_steps_placeholder',
                                        )
                                    "
                                ></textarea>
                            </div>

                            <div class="flex justify-end pt-4">
                                <StButtonGlass
                                    variant="primary"
                                    :loading="saving"
                                    type="submit"
                                >
                                    {{
                                        t(
                                            'talent_development.mentorship_sessions.form.save',
                                        )
                                    }}
                                </StButtonGlass>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </v-dialog>
</template>

<script setup lang="ts">
import StBadgeGlass from '@/components/StBadgeGlass.vue';
import StButtonGlass from '@/components/StButtonGlass.vue';
import {
    PhCalendar,
    PhChatTeardropText,
    PhClock,
    PhPlus,
    PhUsers,
    PhX,
} from '@phosphor-icons/vue';
import axios from 'axios';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const props = defineProps({
    actionId: {
        type: Number,
        required: true,
    },
    actionTitle: {
        type: String,
        default: 'Sesion de Mentoría',
    },
});

const dialog = ref(false);
const activeTab = ref('list');
interface MentorshipSession {
    id: number;
    session_date: string;
    summary: string;
    next_steps: string;
    duration_minutes: number;
    status: string;
}

const sessions = ref<MentorshipSession[]>([]);
const loading = ref(false);
const saving = ref(false);

const newSession = ref({
    session_date: new Date().toISOString().substr(0, 10),
    summary: '',
    next_steps: '',
    duration_minutes: 60,
    status: 'completed',
});

const open = () => {
    dialog.value = true;
    fetchSessions();
};

const fetchSessions = async () => {
    if (!props.actionId) return;
    loading.value = true;
    try {
        const response = await axios.get('/api/mentorship-sessions', {
            params: { development_action_id: props.actionId },
        });
        sessions.value = response.data.data;
    } catch (e) {
        console.error('Error fetching sessions', e);
    } finally {
        loading.value = false;
    }
};

const saveSession = async () => {
    saving.value = true;
    try {
        await axios.post('/api/mentorship-sessions', {
            ...newSession.value,
            development_action_id: props.actionId,
        });

        // Reset form and go to list
        newSession.value = {
            session_date: new Date().toISOString().substr(0, 10),
            summary: '',
            next_steps: '',
            duration_minutes: 60,
            status: 'completed',
        };
        activeTab.value = 'list';
        await fetchSessions();
    } catch (e) {
        console.error('Error saving session', e);
    } finally {
        saving.value = false;
    }
};

const formatDate = (dateStr: string) => {
    if (!dateStr) return '';
    const date = new Date(dateStr);
    return date.toLocaleDateString();
};

const getStatusVariant = (status: string) => {
    switch (status) {
        case 'completed':
            return 'success';
        case 'scheduled':
            return 'glass';
        case 'cancelled':
            return 'warning';
        default:
            return 'glass';
    }
};

defineExpose({ open });
</script>

<style scoped>
.glass-dialog {
    backdrop-filter: blur(10px);
}
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 10px;
}
</style>
