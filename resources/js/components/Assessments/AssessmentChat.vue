<script setup lang="ts">
import { post } from '@/apiHelper';
import { nextTick, onMounted, ref } from 'vue';

const props = defineProps<{
    personId: number;
    scenarioId?: number;
    type?: string;
}>();

const emit = defineEmits(['completed']);

interface Message {
    id?: number;
    role: 'user' | 'assistant' | 'system';
    content: string;
}

const session = ref<any>(null);
const messages = ref<Message[]>([]);
const newMessage = ref('');
const loading = ref(false);
const analyzing = ref(false);
const chatContainer = ref<HTMLElement | null>(null);

const startSession = async () => {
    loading.value = true;
    try {
        const response = await post(
            '/api/strategic-planning/assessments/sessions',
            {
                people_id: props.personId,
                scenario_id: props.scenarioId,
                type: props.type || 'psychometric',
            },
        );
        session.value = response;
        // Enviar un mensaje vacío o inicial para que la IA salude
        await sendSystemInitial();
    } catch (e) {
        console.error('Error starting session', e);
    } finally {
        loading.value = false;
    }
};

const sendSystemInitial = async () => {
    if (!session.value) return;
    loading.value = true;
    try {
        const response = await post(
            `/api/strategic-planning/assessments/sessions/${session.value.id}/messages`,
            {
                content: 'Hola, por favor inicia la entrevista.',
            },
        );
        messages.value.push(response);
        scrollToBottom();
    } catch (e) {
        console.error('Error in initial greeting', e);
    } finally {
        loading.value = false;
    }
};

const sendMessage = async () => {
    if (!newMessage.value.trim() || loading.value || analyzing.value) return;

    const userMsg = newMessage.value;
    newMessage.value = '';
    messages.value.push({ role: 'user', content: userMsg });
    scrollToBottom();

    loading.value = true;
    try {
        const response = await post(
            `/api/strategic-planning/assessments/sessions/${session.value.id}/messages`,
            {
                content: userMsg,
            },
        );
        messages.value.push(response);
        scrollToBottom();
    } catch (e) {
        console.error('Error sending message', e);
    } finally {
        loading.value = false;
    }
};

const finishAndAnalyze = async () => {
    if (analyzing.value || messages.value.length < 3) return;

    analyzing.value = true;
    try {
        const response = await post(
            `/api/strategic-planning/assessments/sessions/${session.value.id}/analyze`,
        );
        session.value = response.session;
        emit('completed', session.value);
    } catch (e) {
        console.error('Error analyzing session', e);
    } finally {
        analyzing.value = false;
    }
};

const scrollToBottom = async () => {
    await nextTick();
    if (chatContainer.value) {
        chatContainer.value.scrollTop = chatContainer.value.scrollHeight;
    }
};

onMounted(() => {
    // Si queremos empezar automáticamente al montar
    // startSession();
});

defineExpose({ startSession });
</script>

<template>
    <div class="assessment-container flex h-full min-h-[500px] flex-col">
        <div
            v-if="!session"
            class="d-flex flex-column align-center flex-grow justify-center py-12"
        >
            <div class="icon-pulse mb-8">
                <v-icon size="80" color="indigo-400">mdi-brain</v-icon>
            </div>
            <h2
                class="text-h4 font-weight-black mb-2 tracking-tighter text-white"
            >
                AI Potential <span class="text-indigo-400">Assessment</span>
            </h2>
            <p
                class="text-h6 font-weight-regular mb-10 max-w-2xl px-12 text-center leading-relaxed text-white/50"
            >
                Enter an interactive session orchestrated by Stratos AI to
                uncover psychometric traits and hidden talent potential.
            </p>
            <v-btn
                color="indigo-700"
                size="x-large"
                rounded="xl"
                class="font-weight-bold px-10"
                prepend-icon="mdi-auto-fix"
                @click="startSession"
                :loading="loading"
                elevation="12"
            >
                Launch Interview
            </v-btn>
        </div>

        <template v-else>
            <!-- Chat Header -->
            <div
                class="flex items-center justify-between border-b border-white/10 pb-6"
            >
                <div class="flex items-center gap-4">
                    <div class="relative">
                        <v-avatar
                            color="indigo-700"
                            size="48"
                            class="border border-indigo-400/30"
                        >
                            <v-icon size="24" color="white"
                                >mdi-robot-glow</v-icon
                            >
                        </v-avatar>
                        <div
                            class="absolute right-0 bottom-0 h-3 w-3 rounded-full border-2 border-slate-900 bg-emerald-500"
                        ></div>
                    </div>
                    <div>
                        <div
                            class="text-h6 font-weight-black leading-tight text-white"
                        >
                            Cerbero AI
                        </div>
                        <div
                            class="text-caption flex items-center gap-1 font-bold tracking-widest text-emerald-400 uppercase"
                        >
                            Active Session
                        </div>
                    </div>
                </div>
                <v-btn
                    v-if="messages.length >= 3"
                    size="large"
                    color="emerald-600"
                    variant="flat"
                    rounded="xl"
                    class="font-weight-bold px-6"
                    @click="finishAndAnalyze"
                    :loading="analyzing"
                    elevation="8"
                >
                    Complete & Analyze
                </v-btn>
            </div>

            <!-- Messages Area -->
            <div
                ref="chatContainer"
                class="scrollbar-hide my-4 flex-grow-1 overflow-y-auto px-2 py-8"
            >
                <div
                    v-for="(msg, i) in messages"
                    :key="i"
                    :class="[
                        'mb-6 flex animate-in duration-500 fade-in slide-in-from-bottom-4',
                        msg.role === 'user' ? 'justify-end' : 'justify-start',
                    ]"
                >
                    <div
                        :class="[
                            'text-body-1 max-w-[85%] rounded-2xl px-6 py-4 leading-relaxed',
                            msg.role === 'user'
                                ? 'rounded-tr-none border border-indigo-500 bg-indigo-600 font-medium text-white shadow-lg shadow-indigo-900/20'
                                : 'rounded-tl-none border border-white/10 bg-white/5 text-white/90 backdrop-blur-md',
                        ]"
                    >
                        {{ msg.content }}
                    </div>
                </div>

                <div
                    v-if="loading"
                    class="mb-6 flex animate-pulse justify-start"
                >
                    <div
                        class="flex items-center gap-3 rounded-2xl rounded-tl-none border border-white/10 bg-white/5 px-6 py-4 backdrop-blur-md"
                    >
                        <div class="flex gap-1">
                            <div
                                class="h-1.5 w-1.5 animate-bounce rounded-full bg-indigo-400 [animation-delay:-0.3s]"
                            ></div>
                            <div
                                class="h-1.5 w-1.5 animate-bounce rounded-full bg-indigo-400 [animation-delay:-0.15s]"
                            ></div>
                            <div
                                class="h-1.5 w-1.5 animate-bounce rounded-full bg-indigo-400"
                            ></div>
                        </div>
                        <span
                            class="text-caption font-bold tracking-widest text-white/40 uppercase"
                            >Cerbero is thinking</span
                        >
                    </div>
                </div>
            </div>

            <!-- Input Area -->
            <div class="mt-auto pt-4">
                <div class="group relative">
                    <v-text-field
                        v-model="newMessage"
                        placeholder="Type your response here..."
                        hide-details
                        full-width
                        density="comfortable"
                        variant="outlined"
                        class="glass-input-premium"
                        @keyup.enter="sendMessage"
                        :disabled="loading || analyzing"
                        persistent-placeholder
                    >
                        <template #append-inner>
                            <v-btn
                                icon="mdi-send"
                                size="small"
                                variant="flat"
                                color="indigo-600"
                                class="mr-[-4px] rounded-lg"
                                @click="sendMessage"
                                :disabled="
                                    !newMessage.trim() || loading || analyzing
                                "
                                elevation="4"
                            ></v-btn>
                        </template>
                    </v-text-field>
                </div>
                <div
                    class="text-caption mt-4 text-center font-medium text-white/30 italic"
                >
                    Conversations are analyzed in real-time by Stratos AI to map
                    functional and cognitive patterns.
                </div>
            </div>
        </template>
    </div>
</template>

<style scoped>
.assessment-container {
    background: transparent;
}

.scrollbar-hide::-webkit-scrollbar {
    display: none;
}

.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

.icon-pulse {
    animation: icon-float 3s ease-in-out infinite;
}

@keyframes icon-float {
    0%,
    100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-10px);
    }
}

.glass-input-premium :deep(.v-field) {
    background: rgba(255, 255, 255, 0.03) !important;
    border-radius: 20px !important;
    border: 1px solid rgba(255, 255, 255, 0.08) !important;
    padding-left: 12px !important;
    padding-right: 12px !important;
    transition: all 0.3s ease;
}

.glass-input-premium :deep(.v-field--focused) {
    border-color: rgba(99, 102, 241, 0.4) !important;
    background: rgba(255, 255, 255, 0.05) !important;
    box-shadow: 0 0 20px rgba(99, 102, 241, 0.1);
}

.glass-input-premium :deep(.v-field__input) {
    color: white !important;
    font-size: 1rem !important;
}

.glass-input-premium :deep(.v-field__placeholder) {
    color: rgba(255, 255, 255, 0.2) !important;
}
</style>
