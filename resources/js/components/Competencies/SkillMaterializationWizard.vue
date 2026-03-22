<script setup lang="ts">
import StBadgeGlass from '@/components/StBadgeGlass.vue';
import StButtonCyber from '@/components/StButtonCyber.vue';
import StButtonGlass from '@/components/StButtonGlass.vue';
import StCardGlass from '@/components/StCardGlass.vue';
import {
    PhArrowLeft,
    PhBrain,
    PhLightning,
    PhLock,
    PhMagicWand,
    PhNotebook,
    PhPulse,
    PhRobot,
    PhSealCheck,
    PhShieldCheck,
    PhTarget,
    PhUser,
} from '@phosphor-icons/vue';
import axios from 'axios';
import { ref } from 'vue';

const props = defineProps<{
    competency: any;
}>();

const emit = defineEmits(['close', 'success']);

const currentStep = ref(1);
const loading = ref(false);
const saving = ref(false);
const blueprint = ref<any>(null);
const certificationComplete = ref(false);

// ─── Step 1: Generate AI Blueprint ───────────────────────────
const generateBlueprint = async () => {
    loading.value = true;
    try {
        const response = await axios.post(
            `/api/competencies/${props.competency.id}/generate-blueprint`,
        );
        blueprint.value = response.data.blueprint;
        blueprint.value.skills = blueprint.value.skills.map((s: any) => ({
            ...s,
            activeTab: 1,
        }));
        currentStep.value = 2;
    } catch (error) {
        console.error('Error generating blueprint:', error);
    } finally {
        loading.value = false;
    }
};

// ─── Step 3: Final Materialize & Seal ────────────────────────
const materialize = async () => {
    saving.value = true;
    try {
        await axios.post(
            `/api/competencies/${props.competency.id}/materialize`,
            {
                blueprint: blueprint.value,
            },
        );

        certificationComplete.value = true;

        // Brief delay for success animation before closing
        setTimeout(() => {
            emit('success');
        }, 2000);
    } catch (error) {
        console.error('Error materializing competency:', error);
    } finally {
        saving.value = false;
    }
};

const prevStep = () => {
    if (currentStep.value > 1) currentStep.value--;
};
</script>

<template>
    <div class="skill-materialization-wizard">
        <!-- Step 1: Initial State / Generation -->
        <div v-if="currentStep === 1" class="step-content py-8 text-center">
            <div class="icon-orb mx-auto mb-6">
                <component
                    :is="PhBrain"
                    :size="48"
                    class="text-indigo-accent-1"
                />
            </div>

            <h2 class="text-h5 font-weight-black font-premium mb-2 text-white">
                Materializar Arquitectura de Habilidades
            </h2>
            <p class="mx-auto mb-8 max-w-md text-slate-400">
                Esta competencia no tiene habilidades definidas. <br />
                Podemos usar el <strong>Cerebro Stratos</strong> para desglosar
                esta capacidad en habilidades técnicas, niveles de maestría y
                criterios de evaluación.
            </p>

            <StCardGlass
                class="mx-auto mb-8 max-w-lg border-indigo-500/20 bg-indigo-500/5 text-left"
            >
                <div class="pa-4">
                    <div
                        class="mb-2 text-[10px] font-black tracking-widest text-indigo-300 uppercase opacity-60"
                    >
                        Competencia de Origen
                    </div>
                    <div
                        class="text-subtitle-1 font-weight-bold mb-1 text-white"
                    >
                        {{ competency.name }}
                    </div>
                    <div class="text-caption text-slate-400">
                        {{ competency.description }}
                    </div>
                </div>
            </StCardGlass>

            <div class="d-flex justify-center gap-4">
                <StButtonGlass variant="ghost" @click="emit('close')"
                    >Cancelar</StButtonGlass
                >
                <StButtonGlass
                    variant="primary"
                    :icon="PhMagicWand"
                    :loading="loading"
                    @click="generateBlueprint"
                >
                    Generar Desglose IA
                </StButtonGlass>
            </div>
        </div>

        <!-- Step 2: Review & Edit Blueprint -->
        <div v-if="currentStep === 2 && blueprint" class="step-content">
            <div class="d-flex align-center justify-space-between mb-6">
                <div>
                    <h2
                        class="text-h6 font-weight-black font-premium mb-1 text-white"
                    >
                        Revisión del Blueprint Estratégico
                    </h2>
                    <p class="text-caption text-slate-400">
                        Ajusta las habilidades y sus niveles de maestría antes
                        de confirmar.
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <StButtonGlass
                        variant="ghost"
                        size="sm"
                        :icon="PhArrowLeft"
                        @click="prevStep"
                        >Atrás</StButtonGlass
                    >
                    <StButtonGlass
                        variant="primary"
                        size="sm"
                        :icon="PhShieldCheck"
                        @click="currentStep = 3"
                        >Continuar a Firma</StButtonGlass
                    >
                </div>
            </div>

            <div
                class="skills-review-container custom-scrollbar pr-2"
                style="max-height: 500px; overflow-y: auto"
            >
                <v-expansion-panels variant="accordion" class="glass-panels">
                    <v-expansion-panel
                        v-for="(skill, sIdx) in blueprint.skills"
                        :key="sIdx"
                        class="mb-4 rounded-xl border border-white/5 bg-white/2"
                    >
                        <v-expansion-panel-title>
                            <div class="d-flex align-center w-full gap-3">
                                <StBadgeGlass variant="primary" size="sm">{{
                                    Number(sIdx) + 1
                                }}</StBadgeGlass>
                                <span
                                    class="text-subtitle-2 font-weight-bold text-white"
                                    >{{ skill.name }}</span
                                >
                                <v-spacer />
                                <div class="d-flex align-center gap-2 pr-4">
                                    <v-chip
                                        size="x-small"
                                        variant="flat"
                                        :color="
                                            skill.talent_mode ===
                                            'synthetic_autonomous'
                                                ? 'deep-purple-accent-2'
                                                : skill.talent_mode ===
                                                    'hybrid_augmented'
                                                  ? 'indigo-accent-1'
                                                  : 'slate-500'
                                        "
                                        class="h-5 px-2 text-[9px] font-black"
                                    >
                                        <component
                                            :is="
                                                skill.talent_mode ===
                                                'synthetic_autonomous'
                                                    ? PhRobot
                                                    : skill.talent_mode ===
                                                        'hybrid_augmented'
                                                      ? PhPulse
                                                      : PhUser
                                            "
                                            :size="10"
                                            class="mr-1"
                                        />
                                        {{
                                            skill.talent_mode ===
                                            'synthetic_autonomous'
                                                ? 'SINTÉTICO'
                                                : skill.talent_mode ===
                                                    'hybrid_augmented'
                                                  ? 'HÍBRIDO'
                                                  : 'HUMANO'
                                        }}
                                    </v-chip>
                                    <div
                                        class="d-flex items-center gap-1 text-[10px] font-bold text-slate-500"
                                        v-if="skill.ai_fluency"
                                    >
                                        <component
                                            :is="PhLightning"
                                            :size="10"
                                        />
                                        {{
                                            Math.round(
                                                (Number(
                                                    skill.ai_fluency
                                                        ?.delegation || 0,
                                                ) +
                                                    Number(
                                                        skill.ai_fluency
                                                            ?.description || 0,
                                                    )) /
                                                    2,
                                            )
                                        }}/5
                                    </div>
                                </div>
                            </div>
                        </v-expansion-panel-title>
                        <v-expansion-panel-text>
                            <div class="pa-2">
                                <!-- Editable Hybrid DNA Section -->
                                <div
                                    class="pa-4 mb-6 rounded-xl border border-indigo-500/10 bg-indigo-500/5"
                                >
                                    <div
                                        class="d-flex align-center justify-space-between mb-4"
                                    >
                                        <div>
                                            <div
                                                class="mb-1 text-[11px] font-black tracking-widest text-indigo-300 uppercase opacity-60"
                                            >
                                                Genética del Talento
                                            </div>
                                            <div
                                                class="text-caption text-slate-400"
                                            >
                                                Define quién y cómo ejecuta esta
                                                habilidad
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div
                                                class="text-[10px] font-bold text-slate-500 uppercase"
                                            >
                                                Automatización
                                            </div>
                                            <div
                                                class="text-h6 font-black text-indigo-300"
                                            >
                                                {{
                                                    Math.round(
                                                        (Number(
                                                            skill.ai_fluency
                                                                ?.delegation ||
                                                                0,
                                                        ) +
                                                            Number(
                                                                skill.ai_fluency
                                                                    ?.description ||
                                                                    0,
                                                            )) /
                                                            2,
                                                    )
                                                }}/5
                                            </div>
                                        </div>
                                    </div>

                                    <div
                                        class="grid grid-cols-1 gap-6 md:grid-cols-2"
                                    >
                                        <!-- Talent Mode Selector -->
                                        <div>
                                            <label
                                                :for="'talent-mode-' + sIdx"
                                                class="d-block mb-2 text-[10px] font-black text-slate-500 uppercase"
                                                >Arquetipo de Ejecución</label
                                            >
                                            <v-select
                                                :id="'talent-mode-' + sIdx"
                                                v-model="skill.talent_mode"
                                                :items="[
                                                    {
                                                        title: 'Humano (Centric)',
                                                        value: 'human_centric',
                                                    },
                                                    {
                                                        title: 'Híbrido (Augmented)',
                                                        value: 'hybrid_augmented',
                                                    },
                                                    {
                                                        title: 'Sintético (Autonomous)',
                                                        value: 'synthetic_autonomous',
                                                    },
                                                ]"
                                                variant="outlined"
                                                density="compact"
                                                hide-details
                                                class="glass-field"
                                            >
                                                <template v-slot:prepend-inner>
                                                    <component
                                                        :is="
                                                            skill.talent_mode ===
                                                            'synthetic_autonomous'
                                                                ? PhRobot
                                                                : skill.talent_mode ===
                                                                    'hybrid_augmented'
                                                                  ? PhPulse
                                                                  : PhUser
                                                        "
                                                        :size="16"
                                                        class="text-indigo-300"
                                                    />
                                                </template>
                                            </v-select>
                                        </div>

                                        <!-- 4D Fluency Inputs (Explicit) -->
                                        <div class="space-y-3">
                                            <label
                                                :for="'d-delegation-' + sIdx"
                                                class="d-block mb-1 text-[10px] font-black text-slate-500 uppercase"
                                                >Fluidez de Automatización
                                                (Framework 4D)</label
                                            >
                                            <div class="d-flex gap-2">
                                                <!-- Each D gets its own labeled input for clarity -->
                                                <div class="grow">
                                                    <div
                                                        class="mb-1 text-[8px] font-black text-indigo-300 uppercase"
                                                    >
                                                        Delegación
                                                    </div>
                                                    <input
                                                        :id="
                                                            'd-delegation-' +
                                                            sIdx
                                                        "
                                                        type="number"
                                                        v-model="
                                                            skill.ai_fluency
                                                                .delegation
                                                        "
                                                        min="1"
                                                        max="5"
                                                        class="dna-input"
                                                    />
                                                </div>
                                                <div class="grow">
                                                    <div
                                                        class="mb-1 text-[8px] font-black text-indigo-300 uppercase"
                                                    >
                                                        Descripción
                                                    </div>
                                                    <input
                                                        type="number"
                                                        v-model="
                                                            skill.ai_fluency
                                                                .description
                                                        "
                                                        min="1"
                                                        max="5"
                                                        class="dna-input"
                                                    />
                                                </div>
                                                <div class="grow">
                                                    <div
                                                        class="mb-1 text-[8px] font-black text-indigo-300 uppercase"
                                                    >
                                                        Discernimiento
                                                    </div>
                                                    <input
                                                        type="number"
                                                        v-model="
                                                            skill.ai_fluency
                                                                .discernment
                                                        "
                                                        min="1"
                                                        max="5"
                                                        class="dna-input"
                                                    />
                                                </div>
                                                <div class="grow">
                                                    <div
                                                        class="mb-1 text-[8px] font-black text-indigo-300 uppercase"
                                                    >
                                                        Diligencia
                                                    </div>
                                                    <input
                                                        type="number"
                                                        v-model="
                                                            skill.ai_fluency
                                                                .diligence
                                                        "
                                                        min="1"
                                                        max="5"
                                                        class="dna-input"
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Automation Hub for Synthetic Skills -->
                                        <div
                                            v-if="
                                                skill.talent_mode ===
                                                'synthetic_autonomous'
                                            "
                                            class="col-span-1 mt-2 border-t border-indigo-500/10 pt-4 md:col-span-2"
                                        >
                                            <div
                                                class="d-flex align-center justify-space-between pa-3 rounded-lg border border-indigo-500/20 bg-indigo-500/10"
                                            >
                                                <div
                                                    class="d-flex align-center gap-3"
                                                >
                                                    <div
                                                        class="d-flex align-center h-8 w-8 justify-center rounded-full bg-indigo-500/20"
                                                    >
                                                        <component
                                                            :is="PhRobot"
                                                            :size="16"
                                                            class="text-indigo-300"
                                                        />
                                                    </div>
                                                    <div>
                                                        <div
                                                            class="text-[11px] font-black tracking-wider text-white uppercase"
                                                        >
                                                            Despliegue Externo
                                                        </div>
                                                        <div
                                                            class="text-[10px] text-slate-400"
                                                        >
                                                            Status en Wand AI /
                                                            n8n
                                                        </div>
                                                    </div>
                                                </div>
                                                <v-switch
                                                    v-model="
                                                        skill.provision_external
                                                    "
                                                    color="indigo-accent-1"
                                                    hide-details
                                                    density="compact"
                                                ></v-switch>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <v-textarea
                                    v-model="skill.description"
                                    label="Descripción de la Habilidad"
                                    variant="outlined"
                                    density="comfortable"
                                    class="glass-field mb-6"
                                    rows="2"
                                    hide-details
                                ></v-textarea>

                                <div
                                    class="mb-4 ml-1 text-[10px] font-black tracking-widest text-indigo-300 uppercase"
                                >
                                    Niveles de Maestría (1-5)
                                </div>

                                <v-tabs
                                    v-model="skill.activeTab"
                                    density="compact"
                                    class="mb-4"
                                >
                                    <v-tab
                                        v-for="l in 5"
                                        :key="l"
                                        :value="l"
                                        class="text-caption"
                                    >
                                        Nivel {{ l }}
                                    </v-tab>
                                </v-tabs>

                                <v-window v-model="skill.activeTab">
                                    <v-window-item
                                        v-for="(level, lIdx) in skill.levels"
                                        :key="lIdx"
                                        :value="Number(lIdx) + 1"
                                    >
                                        <div class="space-y-4 pt-2">
                                            <div
                                                class="grid grid-cols-1 gap-4 md:grid-cols-2"
                                            >
                                                <div class="space-y-1">
                                                    <label
                                                        :for="
                                                            'behavior-' +
                                                            sIdx +
                                                            '-' +
                                                            lIdx
                                                        "
                                                        class="d-flex align-center ml-1 gap-1 text-[10px] font-black text-slate-500 uppercase"
                                                    >
                                                        <component
                                                            :is="PhLightning"
                                                            :size="12"
                                                        />
                                                        Comportamiento Esperado
                                                    </label>
                                                    <textarea
                                                        :id="
                                                            'behavior-' +
                                                            sIdx +
                                                            '-' +
                                                            lIdx
                                                        "
                                                        v-model="
                                                            level.behavioral_description
                                                        "
                                                        class="glass-textarea w-full"
                                                        rows="3"
                                                        placeholder="¿Qué hace la persona en este nivel?"
                                                    ></textarea>
                                                </div>
                                                <div class="space-y-4">
                                                    <div class="space-y-1">
                                                        <label
                                                            :for="
                                                                'learning-' +
                                                                sIdx +
                                                                '-' +
                                                                lIdx
                                                            "
                                                            class="d-flex align-center ml-1 gap-1 text-[10px] font-black text-slate-500 uppercase"
                                                        >
                                                            <component
                                                                :is="PhNotebook"
                                                                :size="12"
                                                            />
                                                            Unidad de
                                                            Aprendizaje
                                                        </label>
                                                        <input
                                                            :id="
                                                                'learning-' +
                                                                sIdx +
                                                                '-' +
                                                                lIdx
                                                            "
                                                            v-model="
                                                                level.learning_content
                                                            "
                                                            type="text"
                                                            class="glass-field-input w-full"
                                                            placeholder="¿Qué debe estudiar?"
                                                        />
                                                    </div>
                                                    <div class="space-y-1">
                                                        <label
                                                            :for="
                                                                'performance-' +
                                                                sIdx +
                                                                '-' +
                                                                lIdx
                                                            "
                                                            class="d-flex align-center ml-1 gap-1 text-[10px] font-black text-slate-500 uppercase"
                                                        >
                                                            <component
                                                                :is="PhTarget"
                                                                :size="12"
                                                            />
                                                            Criterio de
                                                            Desempeño
                                                        </label>
                                                        <input
                                                            :id="
                                                                'performance-' +
                                                                sIdx +
                                                                '-' +
                                                                lIdx
                                                            "
                                                            v-model="
                                                                level.performance_indicator
                                                            "
                                                            type="text"
                                                            class="glass-field-input w-full"
                                                            placeholder="¿Cómo se mide el éxito?"
                                                        />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </v-window-item>
                                </v-window>
                            </div>
                        </v-expansion-panel-text>
                    </v-expansion-panel>
                </v-expansion-panels>
            </div>
        </div>

        <!-- Step 3: Certification & Signature -->
        <div v-if="currentStep === 3" class="step-content">
            <!-- Normal Signature State -->
            <div v-if="!certificationComplete" class="py-8 text-center">
                <div
                    class="icon-orb mx-auto mb-6 scale-125 border-indigo-500/30 bg-indigo-500/5"
                >
                    <component
                        :is="PhShieldCheck"
                        :size="56"
                        class="text-indigo-400"
                    />
                </div>

                <h2
                    class="text-h5 font-weight-black font-premium mb-2 text-white"
                >
                    Sello de Arquitectura de Talento
                </h2>
                <p class="mx-auto mb-10 max-w-sm text-slate-400">
                    Al firmar, certificas la combinación de arquetipos humanos y
                    sintéticos para esta competencia.
                </p>

                <div
                    class="pa-6 mx-auto mb-10 max-w-lg rounded-2xl border border-white/5 bg-white/3 text-left"
                >
                    <div class="grid grid-cols-2 gap-4">
                        <div
                            class="pa-3 rounded-xl border border-white/5 bg-white/2"
                        >
                            <div
                                class="text-[9px] font-bold text-slate-500 uppercase"
                            >
                                Capacidades
                            </div>
                            <div class="text-h6 font-black text-white">
                                {{ blueprint.skills.length }} Habilidades
                            </div>
                        </div>
                        <div
                            class="pa-3 rounded-xl border border-white/5 bg-white/2"
                        >
                            <div
                                class="text-[9px] font-bold text-slate-500 uppercase"
                            >
                                Gobernanza
                            </div>
                            <div class="text-h6 font-black text-indigo-300">
                                Sello Sentinel
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-center gap-4">
                    <StButtonGlass variant="ghost" @click="currentStep = 2"
                        >Volver</StButtonGlass
                    >
                    <StButtonCyber
                        variant="primary"
                        size="lg"
                        :icon="PhLock"
                        :loading="saving"
                        tag="SENTINEL"
                        cyberId="AUTH-V1"
                        @click="materialize"
                    >
                        Firmar y Activar
                    </StButtonCyber>
                </div>
                <div
                    class="mt-6 text-[10px] font-black tracking-[0.2em] text-slate-500 uppercase"
                >
                    Authenticated Sentinel Signature v1.0
                </div>
            </div>

            <!-- Success State -->
            <div
                v-else
                class="animate-in py-12 text-center duration-500 fade-in zoom-in"
            >
                <div
                    class="success-orb d-flex align-center mx-auto mb-8 justify-center border-emerald-500/30 bg-emerald-500/10"
                >
                    <component
                        :is="PhSealCheck"
                        :size="64"
                        class="text-emerald-400"
                    />
                </div>
                <h2 class="text-h4 mb-2 font-black text-white">
                    ¡Sello Aplicado!
                </h2>
                <p class="text-slate-400">
                    La arquitectura ha sido certificada con éxito.
                </p>
            </div>
        </div>
    </div>
</template>

<style scoped>
.skill-materialization-wizard {
    min-height: 400px;
}

.icon-orb {
    width: 96px;
    height: 96px;
    background: radial-gradient(
        circle at center,
        rgba(99, 102, 241, 0.15) 0%,
        transparent 70%
    );
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid rgba(255, 255, 255, 0.05);
}

.success-orb {
    width: 120px;
    height: 120px;
    background: radial-gradient(
        circle at center,
        rgba(16, 185, 129, 0.15) 0%,
        transparent 70%
    );
    border-radius: 50%;
    border: 1px solid rgba(16, 185, 129, 0.2);
}

.font-premium {
    letter-spacing: -0.02em;
}

@keyframes seal-pop {
    0% {
        transform: scale(0.5);
        opacity: 0;
    }
    70% {
        transform: scale(1.1);
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

.animate-seal {
    animation: seal-pop 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
}

.glass-field-input,
.glass-textarea {
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.07);
    border-radius: 8px;
    padding: 10px 14px;
    color: rgba(255, 255, 255, 1);
    font-size: 13px;
    transition: all 0.2s ease;
}

.glass-field-input:focus,
.glass-textarea:focus {
    outline: none;
    border-color: rgba(99, 102, 241, 0.4);
    background: rgba(99, 102, 241, 0.03);
}

.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.05);
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(99, 102, 241, 0.5);
    border-radius: 4px;
}

.glass-panels :deep(.v-expansion-panel) {
    background: transparent !important;
    color: white !important;
}

.glass-panels :deep(.v-expansion-panel-title) {
    min-height: 64px;
}

.glass-panels :deep(.v-expansion-panel-text__wrapper) {
    border-top: 1px solid rgba(255, 255, 255, 0.03);
}
</style>
