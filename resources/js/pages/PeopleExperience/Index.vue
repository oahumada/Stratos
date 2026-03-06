<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import {
    PhHeart,
    PhRocketLaunch,
    PhSmiley,
    PhSmileyAngry,
    PhWaves,
} from '@phosphor-icons/vue';
import { ref } from 'vue';
import StButtonGlass from '../../components/StButtonGlass.vue';

const props = defineProps<{
    user_id: number;
}>();

const submitted = ref(false);
const currentStep = ref(1);

const form = useForm({
    people_id: props.user_id || 1, // Mock para pruebas si no viene el ID
    e_nps: null as number | null,
    stress_level: 3,
    engagement_level: 3,
    comments: '',
});

const setNps = (val: number) => {
    form.e_nps = val;
};

const submitPulse = () => {
    form.post('/api/people-experience/employee-pulses', {
        onSuccess: () => {
            submitted.value = true;
        },
    });
};

const next = () => {
    if (currentStep.value < 4) currentStep.value++;
};
</script>

<template>
    <div
        class="relative flex min-h-screen flex-col items-center justify-center overflow-hidden bg-slate-950 px-4 py-12"
    >
        <Head title="Stratos Px - Tu Pulso Hoy" />

        <!-- Background Orbs -->
        <div
            class="pointer-events-none absolute -top-24 -right-24 h-96 w-96 animate-pulse rounded-full bg-indigo-500/10 blur-[120px]"
        ></div>
        <div
            class="pointer-events-none absolute -bottom-24 -left-24 h-96 w-96 rounded-full bg-emerald-500/10 blur-[120px]"
            style="animation-delay: 2s"
        ></div>

        <div
            v-if="!submitted"
            class="relative z-10 w-full max-w-md transition-all duration-500"
        >
            <div class="mb-10 text-center">
                <div
                    class="mb-6 inline-flex h-16 w-16 items-center justify-center rounded-2xl border border-indigo-500/20 bg-indigo-500/10 text-indigo-400 shadow-lg shadow-indigo-500/10"
                >
                    <PhWaves :size="32" weight="duotone" />
                </div>
                <h1 class="mb-2 text-3xl font-black tracking-tight text-white">
                    ¡Hola!
                </h1>
                <p class="font-medium text-white/40">
                    Solo nos toma 10 segundos saber cómo estás.
                </p>
            </div>

            <!-- Wizard Content -->
            <div
                class="relative rounded-[32px] border border-white/10 bg-white/5 p-8 shadow-2xl backdrop-blur-3xl"
            >
                <!-- Step 1: eNPS -->
                <div v-if="currentStep === 1" class="animate-fade-in">
                    <h2 class="mb-8 text-center text-xl font-bold text-white">
                        ¿Qué tan probable es que recomiendes trabajar aquí hoy?
                    </h2>

                    <div class="mb-10 grid grid-cols-5 gap-3">
                        <button
                            v-for="n in 10"
                            :key="n - 1"
                            @click="setNps(n - 1)"
                            class="flex aspect-square items-center justify-center rounded-xl border font-bold transition-all duration-300"
                            :class="[
                                form.e_nps === n - 1
                                    ? 'scale-110 border-indigo-400 bg-indigo-500 text-white shadow-lg shadow-indigo-500/30'
                                    : 'border-white/5 bg-white/5 text-white/40 hover:bg-white/10',
                            ]"
                        >
                            {{ n - 1 }}
                        </button>
                    </div>
                </div>

                <!-- Step 2: Stress -->
                <div v-if="currentStep === 2" class="animate-fade-in">
                    <h2 class="mb-8 text-center text-xl font-bold text-white">
                        ¿Cuál es tu nivel de estrés esta semana?
                    </h2>
                    <div
                        class="mb-8 flex items-center justify-between rounded-2xl bg-white/5 p-6"
                    >
                        <div class="flex flex-col items-center gap-2">
                            <PhSmiley
                                :size="40"
                                :weight="
                                    form.stress_level === 1 ? 'fill' : 'light'
                                "
                                class="text-emerald-400"
                                @click="form.stress_level = 1"
                            />
                            <span
                                class="text-[10px] font-black text-white/30 uppercase"
                                >Relax</span
                            >
                        </div>
                        <div class="mx-2 h-px flex-1 bg-white/10"></div>
                        <div class="flex flex-col items-center gap-2">
                            <PhSmileyAngry
                                :size="40"
                                :weight="
                                    form.stress_level === 5 ? 'fill' : 'light'
                                "
                                class="text-rose-400"
                                @click="form.stress_level = 5"
                            />
                            <span
                                class="text-[10px] font-black text-white/30 uppercase"
                                >Burnout</span
                            >
                        </div>
                    </div>
                    <v-slider
                        v-model="form.stress_level"
                        min="1"
                        max="5"
                        step="1"
                        color="indigo"
                        thumb-label
                        hide-details
                    ></v-slider>
                </div>

                <!-- Step 3: Engagement -->
                <div v-if="currentStep === 3" class="animate-fade-in">
                    <h2 class="mb-8 text-center text-xl font-bold text-white">
                        ¿Cómo sientes tu conexión con el propósito?
                    </h2>
                    <div class="mb-8 flex flex-col gap-4">
                        <button
                            v-for="i in 5"
                            :key="i"
                            @click="form.engagement_level = i"
                            class="flex items-center gap-4 rounded-2xl border p-4 transition-all duration-300"
                            :class="[
                                form.engagement_level === i
                                    ? 'border-emerald-500/50 bg-emerald-500/10 text-emerald-400'
                                    : 'border-white/5 bg-white/5 text-white/40',
                            ]"
                        >
                            <PhHeart
                                :size="24"
                                :weight="
                                    form.engagement_level >= i
                                        ? 'fill'
                                        : 'light'
                                "
                            />
                            <span class="font-bold">Nivel {{ i }}</span>
                        </button>
                    </div>
                </div>

                <!-- Step 4: Comments -->
                <div v-if="currentStep === 4" class="animate-fade-in">
                    <h2 class="mb-4 text-center text-xl font-bold text-white">
                        ¿Algo más que nos quieras decir?
                    </h2>
                    <p class="mb-6 text-center text-sm text-white/40">
                        (Opcional y confidencial)
                    </p>
                    <textarea
                        v-model="form.comments"
                        class="mb-6 h-32 w-full resize-none rounded-2xl border border-white/10 bg-white/5 p-4 text-white placeholder-white/20 transition-all focus:border-indigo-500/50 focus:outline-none"
                        placeholder="Cualquier idea, queja o sugerencia..."
                    ></textarea>
                </div>

                <!-- Actions -->
                <div class="flex gap-4">
                    <StButtonGlass
                        v-if="currentStep > 1"
                        variant="ghost"
                        block
                        @click="currentStep--"
                    >
                        Atrás
                    </StButtonGlass>

                    <StButtonGlass
                        v-if="currentStep < 4"
                        variant="primary"
                        block
                        :disabled="currentStep === 1 && form.e_nps === null"
                        @click="next"
                    >
                        Siguiente
                    </StButtonGlass>

                    <StButtonGlass
                        v-else
                        variant="primary"
                        block
                        @click="submitPulse"
                        :loading="form.processing"
                        :icon="PhRocketLaunch"
                    >
                        Enviar Pulso
                    </StButtonGlass>
                </div>

                <!-- Progress dots -->
                <div class="mt-8 flex justify-center gap-2">
                    <div
                        v-for="i in 4"
                        :key="i"
                        class="h-1 rounded-full transition-all duration-300"
                        :class="[
                            i === currentStep
                                ? 'w-8 bg-indigo-500'
                                : 'w-2 bg-white/10',
                        ]"
                    ></div>
                </div>
            </div>
        </div>

        <!-- Success Message -->
        <div v-else class="animate-fade-in max-w-md text-center">
            <div
                class="mb-8 inline-flex h-24 w-24 items-center justify-center rounded-full border border-emerald-500/30 bg-emerald-500/20 text-emerald-400 shadow-[0_0_50px_rgba(16,185,129,0.2)]"
            >
                <PhCheckCircle :size="56" weight="fill" />
            </div>
            <h1 class="mb-4 text-4xl font-black text-white">¡Gracias!</h1>
            <p class="mb-10 text-xl leading-relaxed text-white/50">
                Tu aporte ya está siendo analizado por el Córtex de Talento para
                mejorar tu experiencia.
            </p>
            <StButtonGlass
                variant="ghost"
                @click="
                    submitted = false;
                    currentStep = 1;
                    form.reset();
                "
            >
                Enviar otro pulso
            </StButtonGlass>
        </div>
    </div>
</template>

<style scoped>
.animate-fade-in {
    animation: fadeIn 0.4s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
