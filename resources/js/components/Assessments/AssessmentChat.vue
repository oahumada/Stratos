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
    <div class="assessment-chat d-flex flex-column" style="height: 500px">
        <div
            v-if="!session"
            class="d-flex flex-column align-center fill-height justify-center"
        >
            <v-icon size="64" color="primary" class="mb-4">mdi-brain</v-icon>
            <div class="text-h6 mb-2">Evaluación de Potencial AI</div>
            <div class="text-body-2 mb-6 px-10 text-center text-secondary">
                Inicia una entrevista interactiva orquestada por IA para
                descubrir rasgos psicométricos y potencial oculto.
            </div>
            <v-btn
                color="primary"
                prepend-icon="mdi-play"
                @click="startSession"
                :loading="loading"
            >
                Comenzar Entrevista
            </v-btn>
        </div>

        <template v-else>
            <!-- Chat Header -->
            <div
                class="d-flex align-center justify-space-between border-bottom pb-3"
            >
                <div class="d-flex align-center">
                    <v-avatar color="primary" size="32" class="mr-2">
                        <v-icon size="18" color="white">mdi-robot</v-icon>
                    </v-avatar>
                    <div>
                        <div class="text-subtitle-2 font-weight-bold">
                            Stratos AI Interviewer
                        </div>
                        <div
                            class="text-caption text-success d-flex align-center"
                        >
                            <v-icon size="10" class="mr-1">mdi-circle</v-icon>
                            En línea
                        </div>
                    </div>
                </div>
                <v-btn
                    v-if="messages.length >= 3"
                    size="small"
                    color="success"
                    variant="tonal"
                    label="Finalizar y Analizar"
                    @click="finishAndAnalyze"
                    :loading="analyzing"
                >
                    Finalizar y Analizar
                </v-btn>
            </div>

            <!-- Messages Area -->
            <div
                ref="chatContainer"
                class="pa-4 chat-messages-bg my-2 flex-grow-1 overflow-y-auto"
            >
                <div
                    v-for="(msg, i) in messages"
                    :key="i"
                    :class="[
                        'd-flex mb-4',
                        msg.role === 'user' ? 'justify-end' : 'justify-start',
                    ]"
                >
                    <div
                        :class="[
                            'pa-3 text-body-2 rounded-lg',
                            msg.role === 'user'
                                ? 'custom-shadow-user bg-primary text-white'
                                : 'custom-shadow-ai border bg-white',
                        ]"
                        style="max-width: 80%"
                    >
                        {{ msg.content }}
                    </div>
                </div>

                <div v-if="loading" class="d-flex mb-4 justify-start">
                    <div
                        class="pa-3 d-flex align-center rounded-lg border bg-white"
                    >
                        <v-progress-circular
                            indeterminate
                            size="16"
                            width="2"
                            color="primary"
                            class="mr-2"
                        ></v-progress-circular>
                        <span class="text-caption text-secondary"
                            >La IA está pensando...</span
                        >
                    </div>
                </div>
            </div>

            <!-- Input Area -->
            <div class="pt-2">
                <v-text-field
                    v-model="newMessage"
                    placeholder="Escribe tu respuesta aquí..."
                    hide-details
                    full-width
                    density="comfortable"
                    variant="outlined"
                    @keyup.enter="sendMessage"
                    :disabled="loading || analyzing"
                >
                    <template #append-inner>
                        <v-btn
                            icon="mdi-send"
                            size="small"
                            variant="text"
                            color="primary"
                            @click="sendMessage"
                            :disabled="
                                !newMessage.trim() || loading || analyzing
                            "
                        ></v-btn>
                    </template>
                </v-text-field>
                <div class="text-caption mt-1 text-center text-secondary">
                    Tus respuestas son procesadas por Stratos AI para generar un
                    perfil de potencial.
                </div>
            </div>
        </template>
    </div>
</template>

<style scoped>
.chat-messages-bg {
    background-color: #f8fafc;
    border-radius: 8px;
}

.custom-shadow-user {
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.custom-shadow-ai {
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.border-bottom {
    border-bottom: 1px solid #e2e8f0;
}
</style>
