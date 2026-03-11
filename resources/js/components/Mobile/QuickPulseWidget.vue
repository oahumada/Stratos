<script setup lang="ts">
import { useNotification } from '@kyvg/vue3-notification';
import axios from 'axios';
import { ref } from 'vue';

const props = defineProps<{
    peopleId: number;
}>();

const emit = defineEmits(['pulse-submitted']);
const { notify } = useNotification();

const step = ref(1);
const loading = ref(false);
const show = ref(true);

const pulse = ref({
    people_id: props.peopleId,
    e_nps: 10,
    stress_level: 1,
    engagement_level: 5,
    comments: '',
});

const submitPulse = async () => {
    loading.value = true;
    try {
        await axios.post('/api/people-experience/employee-pulses', pulse.value);
        notify({
            title: '¡Pulso Registrado!',
            text: 'Tu feedback ayuda a mejorar la cultura de Stratos.',
            type: 'success',
        });
        show.value = false;
        emit('pulse-submitted');
    } catch (error) {
        console.error('Error submitting pulse:', error);
        notify({
            title: 'Error',
            text: 'No pudimos registrar tu pulso. Reintenta pronto.',
            type: 'error',
        });
    } finally {
        loading.value = false;
    }
};

const nextStep = () => {
    if (step.value < 4) step.value++;
};

const prevStep = () => {
    if (step.value > 1) step.value--;
};
</script>

<template>
    <div v-if="show" class="quick-pulse-widget">
        <v-card class="pulse-card glass-panel" flat>
            <div class="card-glow"></div>

            <v-btn
                icon="mdi-close"
                variant="text"
                size="small"
                class="close-btn"
                @click="show = false"
            />

            <div class="pulse-header">
                <v-icon icon="mdi-heart-pulse" color="primary" class="mr-2" />
                <span class="text-overline">Check-in Diario</span>
            </div>

            <div class="pulse-content">
                <!-- Step 1: Mood/eNPS -->
                <div
                    v-if="step === 1"
                    class="step-view animate__animated animate__fadeIn"
                >
                    <h3 class="step-title">
                        ¿Qué tan probable es que recomiendes Stratos hoy?
                    </h3>
                    <div class="rating-scale">
                        <v-slider
                            v-model="pulse.e_nps"
                            :min="0"
                            :max="10"
                            :step="1"
                            thumb-label="always"
                            color="indigo-accent-2"
                            track-color="rgba(255,255,255,0.1)"
                        >
                            <template #prepend>
                                <v-icon
                                    icon="mdi-emoticon-sad-outline"
                                    color="red-lighten-2"
                                />
                            </template>
                            <template #append>
                                <v-icon
                                    icon="mdi-emoticon-excited-outline"
                                    color="green-lighten-2"
                                />
                            </template>
                        </v-slider>
                    </div>
                </div>

                <!-- Step 2: Stress -->
                <div
                    v-if="step === 2"
                    class="step-view animate__animated animate__fadeIn"
                >
                    <h3 class="step-title">
                        ¿Cuál es tu nivel de estrés actual?
                    </h3>
                    <div class="emoji-selector">
                        <div
                            v-for="i in 5"
                            :key="i"
                            class="emoji-item"
                            :class="{ selected: pulse.stress_level === i }"
                            @click="pulse.stress_level = i"
                        >
                            <span class="emoji-icon">{{
                                ['🧘', '😊', '😐', '😟', '😫'][i - 1]
                            }}</span>
                            <span class="emoji-label">{{
                                [
                                    'Bajo',
                                    'Calmado',
                                    'Normal',
                                    'Alto',
                                    'Crítico',
                                ][i - 1]
                            }}</span>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Engagement -->
                <div
                    v-if="step === 3"
                    class="step-view animate__animated animate__fadeIn"
                >
                    <h3 class="step-title">
                        ¿Cómo te sientes con tus misiones actuales?
                    </h3>
                    <v-rating
                        v-model="pulse.engagement_level"
                        length="5"
                        color="amber"
                        active-color="amber"
                        half-increments
                        hover
                        size="56"
                        class="d-flex justify-center"
                    />
                    <p class="text-caption text-grey mt-2 text-center">
                        ¡Tu energía impacta a todo el equipo!
                    </p>
                </div>

                <!-- Step 4: Comments -->
                <div
                    v-if="step === 4"
                    class="step-view animate__animated animate__fadeIn"
                >
                    <h3 class="step-title">
                        ¿Algo que quieras compartir (anónimo)?
                    </h3>
                    <v-textarea
                        v-model="pulse.comments"
                        placeholder="Escribe aquí..."
                        variant="solo"
                        bg-color="rgba(255,255,255,0.05)"
                        class="custom-textarea mt-4"
                        rows="3"
                        hide-details
                    />
                </div>
            </div>

            <div class="pulse-footer">
                <v-btn
                    v-if="step > 1"
                    variant="text"
                    color="white"
                    @click="prevStep"
                    :disabled="loading"
                    >Atrás</v-btn
                >
                <v-spacer />
                <v-btn
                    v-if="step < 4"
                    color="indigo-accent-2"
                    variant="flat"
                    class="rounded-pill px-6"
                    @click="nextStep"
                >
                    Siguiente
                </v-btn>
                <v-btn
                    v-else
                    color="primary"
                    variant="flat"
                    class="rounded-pill px-8"
                    @click="submitPulse"
                    :loading="loading"
                >
                    Enviar Pulso
                </v-btn>
            </div>

            <div class="step-indicator">
                <div
                    v-for="i in 4"
                    :key="i"
                    class="dot"
                    :class="{ active: step === i }"
                ></div>
            </div>
        </v-card>
    </div>
</template>

<style scoped>
.quick-pulse-widget {
    margin: 16px 0;
}

.pulse-card {
    background: rgba(30, 41, 59, 0.4) !important;
    backdrop-filter: blur(24px);
    border: 1px solid rgba(255, 255, 255, 0.1) !important;
    border-radius: 24px !important;
    overflow: hidden;
    position: relative;
    padding: 24px;
}

.card-glow {
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(
        circle at center,
        rgba(99, 102, 241, 0.05) 0%,
        transparent 50%
    );
    pointer-events: none;
}

.close-btn {
    position: absolute;
    top: 8px;
    right: 8px;
    color: rgba(255, 255, 255, 0.3);
}

.pulse-header {
    display: flex;
    align-items: center;
    color: rgba(255, 255, 255, 0.6);
    margin-bottom: 24px;
}

.step-title {
    font-size: 1.25rem;
    font-weight: 700;
    line-height: 1.4;
    color: white;
    margin-bottom: 32px;
    text-align: center;
}

.step-view {
    min-height: 220px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.rating-scale {
    padding: 0 16px;
}

.emoji-selector {
    display: flex;
    justify-content: space-between;
    gap: 8px;
}

.emoji-item {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 12px 8px;
    border-radius: 16px;
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid transparent;
    transition: all 0.2s;
    cursor: pointer;
}

.emoji-item.selected {
    background: rgba(99, 102, 241, 0.1);
    border-color: rgba(99, 102, 241, 0.3);
    transform: translateY(-4px);
}

.emoji-icon {
    font-size: 28px;
    margin-bottom: 8px;
}

.emoji-label {
    font-size: 10px;
    font-weight: 600;
    color: rgba(255, 255, 255, 0.5);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.emoji-item.selected .emoji-label {
    color: #6366f1;
}

.pulse-footer {
    margin-top: 32px;
    display: flex;
    align-items: center;
}

.step-indicator {
    display: flex;
    justify-content: center;
    gap: 8px;
    margin-top: 24px;
}

.dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.1);
    transition: all 0.3s;
}

.dot.active {
    width: 24px;
    border-radius: 4px;
    background: #6366f1;
}

.custom-textarea :deep(.v-field__input) {
    color: white !important;
}
</style>
