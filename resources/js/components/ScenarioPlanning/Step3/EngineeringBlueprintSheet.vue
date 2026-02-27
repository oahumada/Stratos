<template>
    <div class="engineering-blueprint-sheet glass-morph">
        <!-- Header: Identidad del Rol -->
        <header class="blueprint-header">
            <div class="role-badge" :class="props.archetype?.toLowerCase()">
                <v-icon color="white" size="24">{{ archetypeIcon }}</v-icon>
                <div class="archetype-info">
                    <span class="label">ARQUETIPO</span>
                    <span class="value">{{ props.archetype }}</span>
                </div>
            </div>

            <div class="header-content">
                <h2 class="role-title">{{ props.roleName }}</h2>
                <div class="competency-meta">
                    <v-icon size="16" class="mr-1">mdi-shield-check</v-icon>
                    <span
                        >Ingeniería de:
                        <strong>{{ props.competencyName }}</strong></span
                    >
                    <v-chip
                        size="x-small"
                        color="indigo-lighten-4"
                        variant="flat"
                        class="ml-3"
                        >SFIA Level {{ props.requiredLevel }}</v-chip
                    >
                </div>
            </div>

            <div class="header-actions">
                <v-btn
                    variant="flat"
                    color="indigo-darken-3"
                    prepend-icon="mdi-auto-fix"
                    class="ai-engine-btn glow-effect"
                    :loading="generatingAi"
                    @click="generateWithAi"
                >
                    Generar Detalle IA
                </v-btn>
                <v-btn
                    icon="mdi-close"
                    variant="text"
                    @click="$emit('close')"
                ></v-btn>
            </div>
        </header>

        <v-divider class="mb-6 opacity-20" />

        <div class="blueprint-body">
            <!-- Left Panel: DNA & Context -->
            <aside class="dna-panel glass-card">
                <h3 class="panel-title">DNA del Puesto</h3>
                <div class="dna-item">
                    <label>Proceso de Negocio</label>
                    <div class="dna-value">
                        {{ props.businessProcess || 'General Operations' }}
                    </div>
                </div>
                <div class="dna-item">
                    <label>Composition Mix</label>
                    <div class="composition-bar">
                        <div
                            class="human"
                            :style="{ width: '70%' }"
                            title="Human 70%"
                        ></div>
                        <div
                            class="ai"
                            :style="{ width: '30%' }"
                            title="AI 30%"
                        ></div>
                    </div>
                    <div
                        class="d-flex justify-space-between mt-1 text-[10px] opacity-70"
                    >
                        <span>HUMAN 70%</span>
                        <span>SINTÉTICO 30%</span>
                    </div>
                </div>
                <div class="dna-item mb-4">
                    <label>Descriptor de Nivel (SFIA)</label>
                    <p class="text-caption text-indigo-100">
                        {{ sfiaDescriptor }}
                    </p>
                </div>

                <div v-if="barsData.agentic_rationale" class="dna-item mb-4">
                    <label>Racional del Agente</label>
                    <p class="text-xs text-indigo-100 italic">
                        "{{ barsData.agentic_rationale }}"
                    </p>
                </div>

                <div v-if="barsData.learning_guidance" class="dna-item">
                    <label>Guía de Aprendizaje</label>
                    <div
                        class="rounded bg-indigo-900/40 p-2 text-xs text-indigo-100"
                    >
                        <v-icon size="12" class="mr-1"
                            >mdi-book-open-page-variant</v-icon
                        >
                        {{ barsData.learning_guidance }}
                    </div>
                </div>
            </aside>

            <!-- Center: BARS Grid -->
            <main class="bars-workspace">
                <div class="bars-grid">
                    <div
                        v-for="section in barsSections"
                        :key="section.key"
                        class="bars-card glass-card"
                        :class="section.key"
                    >
                        <div class="card-header">
                            <v-icon size="18" :color="section.color">{{
                                section.icon
                            }}</v-icon>
                            <h4>{{ section.title }}</h4>
                        </div>
                        <div class="card-content">
                            <div
                                v-if="!barsData[section.key]?.length"
                                class="empty-state"
                            >
                                No definido. Usa "Generar con IA" para un
                                blueprint de detalle.
                            </div>
                            <ul v-else class="bars-list">
                                <li
                                    v-for="(item, idx) in barsData[section.key]"
                                    :key="idx"
                                >
                                    <span
                                        class="bullet"
                                        :style="{
                                            backgroundColor: section.color,
                                        }"
                                    ></span>
                                    <input
                                        v-model="barsData[section.key][idx]"
                                        class="item-input"
                                        placeholder="Escribe conducta observable..."
                                    />
                                    <v-btn
                                        icon="mdi-minus"
                                        size="x-small"
                                        variant="text"
                                        color="red-lighten-3"
                                        @click="
                                            removeItem(section.key, Number(idx))
                                        "
                                    ></v-btn>
                                </li>
                            </ul>
                        </div>
                        <v-btn
                            size="x-small"
                            variant="text"
                            prepend-icon="mdi-plus"
                            @click="addItem(section.key)"
                            class="mt-2"
                            >Agregar Ítem</v-btn
                        >
                    </div>
                </div>
            </main>
        </div>

        <footer class="blueprint-footer">
            <v-btn variant="text" color="white" @click="$emit('close')"
                >Descartar</v-btn
            >
            <v-spacer />
            <v-btn
                color="indigo-accent-2"
                size="large"
                elevation="8"
                class="font-weight-bold px-8"
                @click="saveBlueprint"
                :loading="saving"
                >Confirmar Ingeniería</v-btn
            >
        </footer>
    </div>
</template>

<script setup lang="ts">
import { useTransformStore } from '@/stores/transformStore';
import axios from 'axios';
import { computed, reactive, ref } from 'vue';

interface Props {
    competencyId: number;
    roleName: string;
    competencyName: string;
    archetype: string;
    requiredLevel: number;
    businessProcess?: string;
    initialBars?: any;
}

const props = defineProps<Props>();
const emit = defineEmits(['close', 'transformed']);

const transformStore = useTransformStore();
const generatingAi = ref(false);
const saving = ref(false);

// Normalize initialBars to ensure keys exist
const barsData = reactive<any>({
    behaviour: props.initialBars?.behaviour || [],
    attitude: props.initialBars?.attitude || [],
    responsibility: props.initialBars?.responsibility || [],
    skills: Array.isArray(props.initialBars?.skills)
        ? props.initialBars.skills.map((s: any) =>
              typeof s === 'string' ? { name: s } : s,
          )
        : [],
    agentic_rationale: props.initialBars?.agentic_rationale || '',
    learning_guidance: props.initialBars?.learning_guidance || '',
});

const barsSections = [
    {
        key: 'behaviour',
        title: 'Behaviour',
        icon: 'mdi-account-check',
        color: '#4facfe',
    },
    {
        key: 'attitude',
        title: 'Attitude',
        icon: 'mdi-heart-pulse',
        color: '#f093fb',
    },
    {
        key: 'responsibility',
        title: 'Responsibility',
        icon: 'mdi-shield-crown',
        color: '#ff0844',
    },
    { key: 'skills', title: 'Skills (DNA)', icon: 'mdi-dna', color: '#00f2fe' },
];

const archetypeIcon = computed(() => {
    switch (props.archetype) {
        case 'E':
            return 'mdi-chess-king';
        case 'T':
            return 'mdi-chess-knight';
        default:
            return 'mdi-chess-pawn';
    }
});

const sfiaDescriptor = computed(() => {
    const levels: Record<number, string> = {
        1: 'AYUDA: Realiza tareas guiadas bajo supervisión directa.',
        2: 'APLICA: Trabaja con autonomía en tareas estándar y conocidas.',
        3: 'HABILITA: Soluciona problemas complejos y guía a otros.',
        4: 'ASEGURA: Define estándares y estrategias para el dominio.',
        5: 'MAESTRO: Innova y lidera el dominio a nivel organizacional.',
    };
    return levels[props.requiredLevel] || levels[3];
});

const addItem = (key: string) => {
    if (!barsData[key]) barsData[key] = [];
    if (key === 'skills') {
        barsData[key].push({ name: '' });
    } else {
        barsData[key].push('');
    }
};

const removeItem = (key: string, idx: number) => {
    barsData[key].splice(idx, 1);
};

const generateWithAi = async () => {
    generatingAi.value = true;

    // Extract scenarioId from URL (handles /scenarios/5 or /scenario-planning/5)
    const pathParts = window.location.pathname.split('/');
    let scenarioId = null;

    const idx = pathParts.findIndex(
        (p) =>
            p === 'scenarios' ||
            p === 'scenario-planning' ||
            p === 'strategic-planning',
    );
    if (idx !== -1) {
        scenarioId = pathParts[idx + 1];
    }

    if (!scenarioId) {
        console.error('Scenario ID not found in URL', window.location.pathname);
        alert('No se pudo identificar el escenario desde la URL.');
        generatingAi.value = false;
        return;
    }

    try {
        const response = await axios.post(
            `/api/scenarios/${scenarioId}/step2/engine/generate-bars`,
            {
                role_name: props.roleName,
                competency_name: props.competencyName,
                required_level: props.requiredLevel,
                archetype: props.archetype,
            },
        );

        if (response.data.success && response.data.bars) {
            const generated = response.data.bars;
            barsData.behaviour = generated.behaviour || [];
            barsData.attitude = generated.attitude || [];
            barsData.responsibility = generated.responsibility || [];
            barsData.skills = Array.isArray(generated.skills)
                ? generated.skills.map((s: any) =>
                      typeof s === 'string' ? { name: s } : s,
                  )
                : [];
            barsData.agentic_rationale = generated.agentic_rationale || '';
            barsData.learning_guidance = generated.learning_guidance || '';
        }
    } catch (err) {
        console.error('Error in AI engine generation', err);
        alert(
            'El Ingeniero de Talento IA ha tenido un problema. Por favor reintenta.',
        );
    } finally {
        generatingAi.value = false;
    }
};

const saveBlueprint = async () => {
    saving.value = true;
    try {
        // Build payload for competency transformation
        const payload = {
            name: `${props.roleName} - ${props.competencyName}`,
            description: `Blueprint de ingeniería de detalle para el rol ${props.roleName} en contexto ${props.businessProcess || 'General'}.`,
            metadata: {
                bars: {
                    behaviour: barsData.behaviour,
                    attitude: barsData.attitude,
                    responsibility: barsData.responsibility,
                    skills: barsData.skills,
                    agentic_rationale: barsData.agentic_rationale,
                    learning_guidance: barsData.learning_guidance,
                },
            },
            create_skills_incubated: true,
        };

        const result = await transformStore.transformCompetency(
            props.competencyId,
            payload,
        );
        emit('transformed', result);
        emit('close');
    } catch (err) {
        console.error('Error saving engineering blueprint', err);
        alert(
            'Error al guardar la ingeniería de detalle. Por favor reintenta.',
        );
    } finally {
        saving.value = false;
    }
};
</script>

<style scoped>
.engineering-blueprint-sheet {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 95vw;
    max-width: 1200px;
    height: 85vh;
    border-radius: 24px;
    z-index: 2000;
    padding: 2.5rem;
    display: flex;
    flex-direction: column;
    color: white;
    box-shadow: 0 0 80px rgba(0, 0, 0, 0.5);
    overflow: hidden;
}

.glass-morph {
    background: rgba(15, 23, 42, 0.85);
    backdrop-filter: blur(24px) saturate(180%);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.glass-card {
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.08);
    backdrop-filter: blur(4px);
    border-radius: 16px;
    padding: 1.5rem;
    transition: all 0.3s ease;
}

.blueprint-header {
    display: flex;
    align-items: center;
    gap: 2rem;
    margin-bottom: 2rem;
}

.role-badge {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.75rem 1.25rem;
    border-radius: 12px;
    background: linear-gradient(135deg, #4338ca 0%, #312e81 100%);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
}

.archetype-info {
    display: flex;
    flex-direction: column;
}

.archetype-info .label {
    font-size: 0.6rem;
    font-weight: 900;
    letter-spacing: 1.5px;
    opacity: 0.7;
}

.archetype-info .value {
    font-size: 1.25rem;
    font-weight: 800;
    line-height: 1;
}

.role-title {
    font-size: 1.75rem;
    font-weight: 800;
    letter-spacing: -0.5px;
    margin-bottom: 0.25rem;
}

.competency-meta {
    font-size: 0.9rem;
    opacity: 0.9;
    display: flex;
    align-items: center;
}

.ai-engine-btn {
    position: relative;
    overflow: hidden;
}

.glow-effect {
    box-shadow: 0 0 15px rgba(99, 102, 241, 0.5);
    transition: 0.3s;
}

.glow-effect:hover {
    box-shadow: 0 0 25px rgba(99, 102, 241, 0.8);
}

.blueprint-body {
    flex: 1;
    display: grid;
    grid-template-columns: 280px 1fr;
    gap: 2rem;
    overflow: hidden;
}

.dna-panel {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.panel-title {
    font-size: 0.8rem;
    font-weight: 900;
    letter-spacing: 2px;
    text-transform: uppercase;
    color: #818cf8;
    margin-bottom: 0.5rem;
}

.dna-item label {
    display: block;
    font-size: 0.7rem;
    font-weight: 700;
    opacity: 0.6;
    margin-bottom: 0.5rem;
    text-transform: uppercase;
}

.dna-value {
    font-weight: 600;
    font-size: 1rem;
}

.composition-bar {
    height: 8px;
    border-radius: 4px;
    background: rgba(255, 255, 255, 0.1);
    display: flex;
    overflow: hidden;
}

.composition-bar .human {
    background: #4f46e5;
}
.composition-bar .ai {
    background: #10b981;
}

.bars-workspace {
    overflow-y: auto;
    padding-right: 1rem;
}

.bars-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
}

.bars-card .card-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    padding-bottom: 0.75rem;
}

.bars-card h4 {
    font-weight: 700;
    font-size: 1.1rem;
}

.bars-list {
    list-style: none;
    padding: 0;
}

.bars-list li {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 0.75rem;
    background: rgba(255, 255, 255, 0.02);
    padding: 0.25rem 0.5rem;
    border-radius: 8px;
}

.bullet {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    flex-shrink: 0;
}

.item-input {
    flex: 1;
    background: transparent;
    border: none;
    color: white;
    font-size: 0.9rem;
    padding: 0.5rem 0;
}

.item-input:focus {
    outline: none;
}

.empty-state {
    padding: 2rem;
    text-align: center;
    opacity: 0.4;
    font-size: 0.85rem;
    font-style: italic;
    border: 1px dashed rgba(255, 255, 255, 0.1);
    border-radius: 12px;
}

.blueprint-footer {
    padding-top: 2rem;
    display: flex;
    align-items: center;
}
</style>
