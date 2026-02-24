<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { ref } from 'vue';

const props = defineProps<{
    token: string;
    request: any;
}>();

const submitted = ref(false);
const loading = ref(false);
const error = ref<string | null>(null);

// Initialize form with existing feedback structure
const form = ref({
    token: props.token,
    answers: props.request.feedback.map((f: any) => ({
        id: f.id,
        skill_id: f.skill_id,
        question: f.question,
        score: null,
        answer: '',
        skill_name: f.skill?.name || 'General',
    })),
});

const submitFeedback = async () => {
    loading.value = true;
    error.value = null;
    try {
        await axios.post('/api/assessments/feedback/submit-guest', {
            token: form.value.token,
            answers: form.value.answers,
        });
        submitted.value = true;
    } catch (e: any) {
        error.value =
            e.response?.data?.message ||
            'Ocurrió un error al enviar el feedback.';
    } finally {
        loading.value = false;
    }
};

const getRelationLabel = (relation: string) => {
    const labels: any = {
        manager: 'Jefe / Supervisor',
        peer: 'Colega / Par',
        subordinate: 'Reporte Directo',
        self: 'Autoevaluación',
    };
    return labels[relation] || relation;
};
</script>

<template>
    <div class="feedback-page d-flex align-center fill-height justify-center">
        <Head :title="'Feedback para ' + request.subject.first_name" />

        <v-container
            fluid
            class="pa-0 fill-height d-flex align-center justify-center"
        >
            <div class="glass-container pa-6 pa-md-10">
                <v-fade-transition hide-on-leave>
                    <div v-if="!submitted" key="form">
                        <div class="header mb-8 text-center">
                            <v-avatar
                                size="80"
                                class="elevation-4 mb-4 border-white"
                            >
                                <v-img
                                    :src="request.subject.photo_url"
                                    cover
                                ></v-img>
                            </v-avatar>
                            <h1
                                class="text-h4 font-weight-bold white--text mb-1"
                            >
                                Evaluación de {{ request.subject.first_name }}
                                {{ request.subject.last_name }}
                            </h1>
                            <p class="text-subtitle-1 opacity-70">
                                Tu perspectiva como
                                <span class="font-weight-bold text-accent">{{
                                    getRelationLabel(request.relationship)
                                }}</span>
                                es fundamental para su desarrollo.
                            </p>
                        </div>

                        <v-form @submit.prevent="submitFeedback">
                            <div
                                v-for="(item, index) in form.answers"
                                :key="index"
                                class="question-card pa-5 mb-6"
                            >
                                <div class="d-flex align-center mb-3">
                                    <v-chip
                                        size="small"
                                        color="primary"
                                        variant="flat"
                                        class="mr-2 px-3"
                                    >
                                        {{ item.skill_name }}
                                    </v-chip>
                                </div>
                                <p class="text-h6 font-weight-medium mb-4">
                                    {{ item.question }}
                                </p>

                                <div class="rating-container mb-4">
                                    <div class="text-caption op-6 mb-1">
                                        Nivel de Competencia Observado
                                    </div>
                                    <v-rating
                                        v-model="item.score"
                                        color="amber-darken-2"
                                        active-color="amber"
                                        density="comfortable"
                                        size="40"
                                        hover
                                        half-increments
                                        clearable
                                    ></v-rating>
                                    <div
                                        class="d-flex justify-space-between text-caption op-4 mt-1 px-1"
                                    >
                                        <span>Aprendiz</span>
                                        <span>Experto</span>
                                    </div>
                                </div>

                                <v-textarea
                                    v-model="item.answer"
                                    label="Evidencia o Justificación (Opcional)"
                                    placeholder="¿Por qué asignas este puntaje? Algún ejemplo concreto ayuda mucho..."
                                    variant="outlined"
                                    rows="2"
                                    auto-grow
                                    bg-color="rgba(255,255,255,0.05)"
                                    hide-details
                                ></v-textarea>
                            </div>

                            <v-alert
                                v-if="error"
                                type="error"
                                variant="tonal"
                                class="animate-shake mb-6"
                            >
                                {{ error }}
                            </v-alert>

                            <v-btn
                                block
                                size="x-large"
                                color="primary"
                                height="64"
                                rounded="lg"
                                :loading="loading"
                                type="submit"
                                class="submit-btn elevation-8 mt-4"
                            >
                                Enviar Evaluación
                                <v-icon end icon="mdi-send"></v-icon>
                            </v-btn>
                        </v-form>
                    </div>

                    <div
                        v-else
                        key="success"
                        class="success-state pa-10 text-center"
                    >
                        <v-icon
                            size="120"
                            color="success"
                            class="mb-6 animate-bounce"
                            >mdi-check-decagram</v-icon
                        >
                        <h2 class="text-h3 font-weight-bold mb-4">
                            ¡Gracias por tu aporte!
                        </h2>
                        <p class="text-h6 mb-8 opacity-70">
                            Tus respuestas han sido procesadas por la IA de
                            Stratos y serán integradas en el perfil de
                            desarrollo de {{ request.subject.first_name }}.
                        </p>
                        <v-btn
                            color="white"
                            variant="outlined"
                            rounded="pill"
                            size="large"
                            @click="submitted = false"
                            v-if="false"
                        >
                            Cerrar
                        </v-btn>
                    </div>
                </v-fade-transition>
            </div>
        </v-container>

        <!-- Background Decorations -->
        <div class="bg-blob blob-1"></div>
        <div class="bg-blob blob-2"></div>
        <div class="bg-blob blob-3"></div>
    </div>
</template>

<style scoped>
.feedback-page {
    background-color: #0f172a;
    min-height: 100vh;
    color: white;
    overflow-x: hidden;
    position: relative;
    font-family: 'Inter', sans-serif;
}

.glass-container {
    background: rgba(30, 41, 59, 0.7);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 32px;
    width: 100%;
    max-width: 800px;
    z-index: 10;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
    margin: 20px;
}

.question-card {
    background: rgba(255, 255, 255, 0.03);
    border-radius: 20px;
    border: 1px solid rgba(255, 255, 255, 0.05);
    transition: all 0.3s ease;
}

.question-card:hover {
    background: rgba(255, 255, 255, 0.05);
    transform: translateY(-2px);
    border-color: rgba(255, 255, 255, 0.1);
}

.submit-btn {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%) !important;
    text-transform: none;
    letter-spacing: 0.5px;
    font-size: 1.1rem;
    font-weight: 600;
}

.text-accent {
    color: #60a5fa;
}

.op-6 {
    opacity: 0.6;
}
.op-4 {
    opacity: 0.4;
}

.animate-bounce {
    animation: bounce 2s infinite;
}

@keyframes bounce {
    0%,
    100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-10px);
    }
}

.animate-shake {
    animation: shake 0.5s cubic-bezier(0.36, 0.07, 0.19, 0.97) both;
}

@keyframes shake {
    10%,
    90% {
        transform: translate3d(-1px, 0, 0);
    }
    20%,
    80% {
        transform: translate3d(2px, 0, 0);
    }
    30%,
    50%,
    70% {
        transform: translate3d(-4px, 0, 0);
    }
    40%,
    60% {
        transform: translate3d(4px, 0, 0);
    }
}

/* Background Blobs */
.bg-blob {
    position: fixed;
    border-radius: 50%;
    filter: blur(100px);
    z-index: 1;
    opacity: 0.15;
}

.blob-1 {
    width: 500px;
    height: 500px;
    background: #3b82f6;
    top: -100px;
    left: -100px;
    animation: move 20s infinite alternate;
}

.blob-2 {
    width: 600px;
    height: 600px;
    background: #8b5cf6;
    bottom: -200px;
    right: -100px;
    animation: move 25s infinite alternate-reverse;
}

.blob-3 {
    width: 300px;
    height: 300px;
    background: #ec4899;
    top: 40%;
    right: 10%;
    opacity: 0.1;
    animation: move 30s infinite alternate;
}

@keyframes move {
    from {
        transform: translate(0, 0) scale(1);
    }
    to {
        transform: translate(50px, 50px) scale(1.1);
    }
}

@media (max-width: 600px) {
    .glass-container {
        padding: 24px 16px;
        border-radius: 20px;
    }
}
</style>
