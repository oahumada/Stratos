<template>
    <v-dialog
        v-model="internalVisible"
        fullscreen
        transition="dialog-bottom-transition"
        persistent
    >
        <v-card class="cube-wizard-card">
            <v-toolbar flat color="transparent" class="px-4">
                <v-btn icon @click="close">
                    <v-icon>mdi-close</v-icon>
                </v-btn>
                <v-toolbar-title class="text-h5 font-weight-bold gradient-text">
                    Role Designer Explorer
                </v-toolbar-title>
                <v-spacer></v-spacer>
                <v-chip color="secondary" variant="flat" class="mr-4">
                    <v-icon start size="small">mdi-robot-glow</v-icon>
                    Cerbero AI Orchestrator
                </v-chip>
            </v-toolbar>

            <v-card-text class="pa-0 overflow-hidden">
                <v-row no-gutters class="fill-height">
                    <!-- Left Sidebar: Progress -->
                    <v-col
                        cols="12"
                        md="3"
                        class="wizard-sidebar pa-6 text-white"
                    >
                        <div class="sidebar-content">
                            <div class="text-overline mb-6 opacity-70">
                                Design Journey
                            </div>

                            <div class="steps-container">
                                <div
                                    v-for="(step, i) in steps"
                                    :key="i"
                                    :class="[
                                        'step-item',
                                        {
                                            active: currentStep === i + 1,
                                            completed: currentStep > i + 1,
                                        },
                                    ]"
                                >
                                    <div class="step-icon">
                                        <v-icon v-if="currentStep > i + 1"
                                            >mdi-check</v-icon
                                        >
                                        <span v-else>{{ i + 1 }}</span>
                                    </div>
                                    <div class="step-info">
                                        <div class="step-title">
                                            {{ step.title }}
                                        </div>
                                        <div class="step-desc">
                                            {{ step.desc }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <v-spacer></v-spacer>

                            <div class="sidebar-footer">
                                <v-card
                                    variant="tonal"
                                    class="pa-4 bg-white-transparent border-white-op"
                                >
                                    <div
                                        class="text-caption font-weight-bold mb-1"
                                    >
                                        CUBE METHODOLOGY v2
                                    </div>
                                    <div class="text-body-2 opacity-80">
                                        Define roles based on multidimensional
                                        impact and organizational clarity.
                                    </div>
                                </v-card>
                            </div>
                        </div>
                    </v-col>

                    <!-- Main Content area -->
                    <v-col cols="12" md="9" class="wizard-main pa-8">
                        <transition name="fade-slide" mode="out-in">
                            <!-- Step 1: Definition -->
                            <div
                                v-if="currentStep === 1"
                                :key="1"
                                class="step-content"
                            >
                                <div class="text-h4 font-weight-bold mb-2">
                                    Let's define the new role
                                </div>
                                <div class="text-body-1 mb-8 text-secondary">
                                    Tell Cerbero about the purpose and
                                    responsibilities. AI will suggest the cube
                                    coordinates.
                                </div>

                                <v-text-field
                                    v-model="form.name"
                                    label="Role Name"
                                    placeholder="e.g., Strategic Growth Lead"
                                    variant="outlined"
                                    class="mb-6"
                                    prepend-inner-icon="mdi-account-tie"
                                    clearable
                                ></v-text-field>

                                <v-textarea
                                    v-model="form.description"
                                    label="Role Purpose / Mission"
                                    placeholder="What does this role achieve? What are the top 3 priorities?"
                                    variant="outlined"
                                    rows="6"
                                    prepend-inner-icon="mdi-text-box-outline"
                                    counter
                                ></v-textarea>

                                <div class="d-flex mt-8 justify-end">
                                    <v-btn
                                        color="primary"
                                        size="x-large"
                                        rounded="xl"
                                        :disabled="
                                            !form.name || !form.description
                                        "
                                        :loading="analyzing"
                                        @click="analyzeRole"
                                        append-icon="mdi-auto-fix"
                                        elevation="4"
                                    >
                                        Analyze with AI
                                    </v-btn>
                                </div>
                            </div>

                            <!-- Step 2: Cube Mapping -->
                            <div
                                v-else-if="currentStep === 2"
                                :key="2"
                                class="step-content"
                            >
                                <div class="d-flex align-center mb-6">
                                    <div>
                                        <div class="text-h4 font-weight-bold">
                                            Cube Dimensions Mapping
                                        </div>
                                        <div class="text-body-1 text-secondary">
                                            Cerbero suggested these coordinates.
                                            You can fine-tune them.
                                        </div>
                                    </div>
                                    <v-spacer></v-spacer>
                                    <v-chip
                                        color="info"
                                        variant="tonal"
                                        prepend-icon="mdi-robot"
                                        >AI Suggestions Ready</v-chip
                                    >
                                </div>

                                <v-row>
                                    <v-col cols="12" md="8">
                                        <v-row>
                                            <!-- Eje X -->
                                            <v-col cols="12">
                                                <v-label
                                                    class="font-weight-bold mb-4"
                                                    >Axis X: Archetype (Impact
                                                    Scope)</v-label
                                                >
                                                <v-btn-toggle
                                                    v-model="
                                                        form.cube.x_archetype
                                                    "
                                                    mandatory
                                                    color="primary"
                                                    variant="outlined"
                                                    class="d-flex w-100"
                                                >
                                                    <v-btn
                                                        value="Strategic"
                                                        class="h-auto flex-grow-1 py-4"
                                                    >
                                                        <div
                                                            class="d-flex flex-column align-center"
                                                        >
                                                            <v-icon
                                                                size="32"
                                                                class="mb-1"
                                                                >mdi-chess-king</v-icon
                                                            >
                                                            <span
                                                                >Strategic</span
                                                            >
                                                            <div
                                                                class="text-caption opacity-60"
                                                            >
                                                                Long-term /
                                                                Direction
                                                            </div>
                                                        </div>
                                                    </v-btn>
                                                    <v-btn
                                                        value="Tactical"
                                                        class="h-auto flex-grow-1 py-4"
                                                    >
                                                        <div
                                                            class="d-flex flex-column align-center"
                                                        >
                                                            <v-icon
                                                                size="32"
                                                                class="mb-1"
                                                                >mdi-chess-rook</v-icon
                                                            >
                                                            <span
                                                                >Tactical</span
                                                            >
                                                            <div
                                                                class="text-caption opacity-60"
                                                            >
                                                                Medium-term /
                                                                Org
                                                            </div>
                                                        </div>
                                                    </v-btn>
                                                    <v-btn
                                                        value="Operational"
                                                        class="h-auto flex-grow-1 py-4"
                                                    >
                                                        <div
                                                            class="d-flex flex-column align-center"
                                                        >
                                                            <v-icon
                                                                size="32"
                                                                class="mb-1"
                                                                >mdi-chess-pawn</v-icon
                                                            >
                                                            <span
                                                                >Operational</span
                                                            >
                                                            <div
                                                                class="text-caption opacity-60"
                                                            >
                                                                Action /
                                                                Efficiency
                                                            </div>
                                                        </div>
                                                    </v-btn>
                                                </v-btn-toggle>
                                            </v-col>

                                            <!-- Eje Y -->
                                            <v-col cols="12" class="mt-4">
                                                <div
                                                    class="d-flex justify-space-between align-center mb-4"
                                                >
                                                    <v-label
                                                        class="font-weight-bold"
                                                        >Axis Y: Mastery Level
                                                        (Expertise
                                                        Required)</v-label
                                                    >
                                                    <v-chip
                                                        size="small"
                                                        color="primary"
                                                        >Level
                                                        {{
                                                            form.cube
                                                                .y_mastery_level
                                                        }}</v-chip
                                                    >
                                                </div>
                                                <v-slider
                                                    v-model="
                                                        form.cube
                                                            .y_mastery_level
                                                    "
                                                    :min="1"
                                                    :max="5"
                                                    :step="1"
                                                    show-ticks="always"
                                                    thumb-label="always"
                                                    color="secondary"
                                                >
                                                    <template
                                                        #tick-label="{ tick }"
                                                    >
                                                        {{ tick.value }}
                                                    </template>
                                                </v-slider>
                                            </v-col>

                                            <!-- Eje Z -->
                                            <v-col cols="12" class="mt-4">
                                                <v-label
                                                    class="font-weight-bold mb-2"
                                                    >Axis Z: Business Process
                                                    (Value Stream)</v-label
                                                >
                                                <v-text-field
                                                    v-model="
                                                        form.cube
                                                            .z_business_process
                                                    "
                                                    variant="underlined"
                                                    prepend-icon="mdi-sitemap-outline"
                                                    placeholder="Identify the main business process"
                                                ></v-text-field>
                                            </v-col>
                                        </v-row>
                                    </v-col>

                                    <!-- Sidebar details -->
                                    <v-col cols="12" md="4">
                                        <v-card
                                            variant="flat"
                                            border
                                            class="pa-4 bg-surface-variant-op h-100"
                                        >
                                            <div
                                                class="text-subtitle-2 font-weight-bold mb-2"
                                            >
                                                AI Rationale
                                            </div>
                                            <p class="text-body-2 mb-4 italic">
                                                "{{ form.cube.justification }}"
                                            </p>

                                            <v-divider class="mb-4"></v-divider>

                                            <div
                                                class="text-subtitle-2 font-weight-bold mb-2"
                                            >
                                                Nitidez Suggestions
                                            </div>
                                            <v-alert
                                                type="info"
                                                variant="tonal"
                                                density="compact"
                                                class="text-caption"
                                            >
                                                {{ form.suggestions }}
                                            </v-alert>
                                        </v-card>
                                    </v-col>
                                </v-row>

                                <div class="d-flex mt-8 justify-end gap-4">
                                    <v-btn variant="text" @click="currentStep--"
                                        >Back</v-btn
                                    >
                                    <v-btn
                                        color="primary"
                                        size="large"
                                        rounded="lg"
                                        @click="currentStep++"
                                    >
                                        Confirm Coordinates
                                    </v-btn>
                                </div>
                            </div>

                            <!-- Step 3: Skills -->
                            <div
                                v-else-if="currentStep === 3"
                                :key="3"
                                class="step-content"
                            >
                                <div
                                    class="text-h4 font-weight-bold text-gradient mb-6"
                                >
                                    Competency Blueprint
                                </div>

                                <v-card
                                    variant="outlined"
                                    border
                                    class="pa-0 mb-6 overflow-hidden rounded-xl"
                                >
                                    <v-table density="comfortable">
                                        <thead class="bg-surface-variant">
                                            <tr>
                                                <th
                                                    scope="col"
                                                    class="text-overline"
                                                >
                                                    Competency / Skill
                                                </th>
                                                <th
                                                    scope="col"
                                                    class="text-overline text-center"
                                                >
                                                    Required Level
                                                </th>
                                                <th
                                                    scope="col"
                                                    class="text-overline"
                                                >
                                                    Rationale
                                                </th>
                                                <th
                                                    scope="col"
                                                    class="text-overline"
                                                >
                                                    Action
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr
                                                v-for="(
                                                    skill, idx
                                                ) in form.competencies"
                                                :key="idx"
                                            >
                                                <td class="font-weight-bold">
                                                    {{ skill.name }}
                                                </td>
                                                <td class="text-center">
                                                    <v-rating
                                                        v-model="skill.level"
                                                        density="compact"
                                                        color="amber-darken-2"
                                                        active-color="amber-darken-2"
                                                        size="small"
                                                    ></v-rating>
                                                </td>
                                                <td
                                                    class="text-caption py-2 text-secondary"
                                                >
                                                    {{ skill.rationale }}
                                                </td>
                                                <td>
                                                    <v-btn
                                                        icon="mdi-delete-outline"
                                                        variant="text"
                                                        size="small"
                                                        color="error"
                                                        @click="
                                                            removeSkill(idx)
                                                        "
                                                    ></v-btn>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </v-table>
                                </v-card>

                                <div class="d-flex align-center mt-4 gap-4">
                                    <v-btn
                                        prepend-icon="mdi-plus"
                                        variant="tonal"
                                        color="secondary"
                                        @click="addSkill"
                                    >
                                        Add Competency
                                    </v-btn>
                                    <v-spacer></v-spacer>
                                    <v-btn variant="text" @click="currentStep--"
                                        >Back</v-btn
                                    >
                                    <v-btn
                                        color="success"
                                        size="large"
                                        rounded="lg"
                                        :loading="saving"
                                        @click="saveRole"
                                    >
                                        Finalize & Create Role
                                    </v-btn>
                                </div>
                            </div>
                        </transition>
                    </v-col>
                </v-row>
            </v-card-text>
        </v-card>
    </v-dialog>
</template>

<script setup lang="ts">
import axios from 'axios';
import { ref, watch } from 'vue';

const props = defineProps<{
    visible: boolean;
    scenarioId?: number | null;
}>();

const emit = defineEmits(['close', 'created']);

const internalVisible = ref(props.visible);
const currentStep = ref(1);
const analyzing = ref(false);
const saving = ref(false);

const steps = [
    { title: 'Goal Definition', desc: 'Purpose and Mission' },
    { title: 'Cube Mapping', desc: 'Structural Coords' },
    { title: 'Competencies', desc: 'Skill Blueprint' },
];

const form = ref({
    name: '',
    description: '',
    cube: {
        x_archetype: 'Tactical',
        y_mastery_level: 3,
        z_business_process: '',
        justification: '',
    },
    competencies: [] as any[],
    suggestions: '',
});

watch(
    () => props.visible,
    (val) => {
        internalVisible.value = val;
    },
);

const close = () => {
    internalVisible.value = false;
    emit('close');
};

const analyzeRole = async () => {
    analyzing.value = true;
    try {
        // En lugar de usar un ID, mandamos la data para análisis previo
        const response = await axios.post(
            '/api/strategic-planning/roles/analyze-preview',
            {
                name: form.value.name,
                description: form.value.description,
            },
        );

        const data = response.data.analysis;
        form.value.cube.x_archetype = data.cube_coordinates.x_archetype;
        form.value.cube.y_mastery_level = data.cube_coordinates.y_mastery_level;
        form.value.cube.z_business_process =
            data.cube_coordinates.z_business_process;
        form.value.cube.justification = data.cube_coordinates.justification;
        form.value.competencies = data.core_competencies;
        form.value.suggestions = data.organizational_suggestions;

        currentStep.value = 2;
    } catch (error) {
        console.error('Error analyzing role:', error);
    } finally {
        analyzing.value = false;
    }
};

const removeSkill = (index: number) => {
    form.value.competencies.splice(index, 1);
};

const addSkill = () => {
    form.value.competencies.push({
        name: 'New Competency',
        level: 3,
        rationale: 'Added manually by user.',
    });
};

const saveRole = async () => {
    saving.value = true;
    try {
        const payload = {
            name: form.value.name,
            role_name: form.value.name, // compatibility
            role_description: form.value.description, // compatibility
            description: form.value.description,
            cube_dimensions: form.value.cube,
            competencies: form.value.competencies,
            ai_archetype_config: {
                cube_coordinates: form.value.cube,
                core_competencies: form.value.competencies,
                organizational_suggestions: form.value.suggestions,
            },
            // Scenario specific fields defaults
            fte: 1,
            role_change: 'create',
            evolution_type: 'new_role',
            impact_level: 'medium',
        };

        const url = props.scenarioId
            ? `/api/scenarios/${props.scenarioId}/step2/roles`
            : '/api/roles';

        await axios.post(url, payload);

        emit('created');
        close();
    } catch (error) {
        console.error('Error saving role:', error);
    } finally {
        saving.value = false;
    }
};
</script>

<style scoped>
.cube-wizard-card {
    background: #0f172a !important; /* Slate 900 */
    color: white !important;
}

.wizard-sidebar {
    background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
    border-right: 1px solid rgba(255, 255, 255, 0.05);
    height: 100%;
}

.sidebar-content {
    display: flex;
    flex-direction: column;
    height: 100%;
}

.step-item {
    display: flex;
    align-items: flex-start;
    gap: 16px;
    margin-bottom: 24px;
    opacity: 0.4;
    transition: all 0.3s ease;
}

.step-item.active {
    opacity: 1;
    transform: translateX(10px);
}

.step-item.completed {
    opacity: 0.8;
}

.step-icon {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 14px;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.step-item.active .step-icon {
    background: var(--v-primary-base);
    border-color: white;
}

.step-item.completed .step-icon {
    background: #10b981; /* Emerald 500 */
    border-color: #34d399;
}

.step-title {
    font-weight: bold;
    font-size: 16px;
}

.step-desc {
    font-size: 12px;
    opacity: 0.7;
}

.wizard-main {
    background: #f8fafc; /* Slate 50 */
    color: #0f172a;
    border-top-left-radius: 24px;
    border-bottom-left-radius: 24px;
}

.gradient-text {
    background: linear-gradient(45deg, #6366f1, #a855f7);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
}

.fade-slide-enter-active,
.fade-slide-leave-active {
    transition: all 0.4s ease;
}

.fade-slide-enter-from {
    opacity: 0;
    transform: translateX(30px);
}

.fade-slide-leave-to {
    opacity: 0;
    transform: translateX(-30px);
}

.bg-white-transparent {
    background: rgba(255, 255, 255, 0.05) !important;
}

.border-white-op {
    border: 1px solid rgba(255, 255, 255, 0.1) !important;
}

.bg-surface-variant-op {
    background: rgba(var(--v-theme-surface-variant), 0.05) !important;
}

.italic {
    font-style: italic;
}

.text-gradient {
    background: linear-gradient(120deg, #1e293b, #6366f1);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
}
</style>
