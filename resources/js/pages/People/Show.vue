<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import axios from 'axios';
import { onMounted, ref } from 'vue';

// Components
import AssessmentChat from '@/components/Assessments/AssessmentChat.vue';
import DevelopmentTab from '@/components/Talent/DevelopmentTab.vue';

const props = defineProps<{
    id: string | number;
}>();

const loading = ref(true);
const personData = ref<any>(null);
const activeTab = ref('profile');

const fetchProfile = async () => {
    try {
        loading.value = true;
        const response = await axios.get(`/api/people/profile/${props.id}`);
        personData.value = response.data.data;
    } catch (error) {
        console.error('Error fetching profile:', error);
    } finally {
        loading.value = false;
    }
};

const formatDate = (dateString: string) => {
    if (!dateString) return 'N/A';
    return new Date(dateString).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};

const getStatusColor = (status: string) => {
    const colors: any = {
        ready: 'success',
        potencial: 'info',
        'Gap significativo': 'warning',
        'no recomendado': 'error',
    };
    return colors[status] || 'grey';
};

onMounted(() => {
    fetchProfile();
});

defineOptions({ layout: AppLayout });
</script>

<template>
    <div class="people-profile-container">
        <!-- Loading State -->
        <v-overlay :model-value="loading" class="align-center justify-center">
            <v-progress-circular indeterminate color="primary" size="64" />
        </v-overlay>

        <div v-if="personData" class="profile-content animate-fade-in">
            <!-- Header Section: Premium Look -->
            <v-card class="profile-header mb-6 overflow-hidden" elevation="2">
                <div
                    class="header-gradient pa-6 d-flex align-center flex-wrap gap-6"
                >
                    <v-avatar size="120" border color="white">
                        <v-img
                            :src="
                                personData.person.photo_url ||
                                '/placeholder-avatar.png'
                            "
                            cover
                        />
                    </v-avatar>

                    <div class="flex-grow-1 text-white">
                        <div class="d-flex align-center mb-1 gap-3">
                            <h1 class="text-h3 font-weight-black">
                                {{ personData.person.first_name }}
                                {{ personData.person.last_name }}
                            </h1>
                            <v-chip
                                v-if="personData.person.is_high_potential"
                                color="amber-lighten-4"
                                class="text-amber-darken-4 font-weight-bold"
                                size="small"
                            >
                                <v-icon start icon="mdi-star" /> HIGH POTENTIAL
                            </v-chip>
                        </div>
                        <div class="text-h5 font-weight-light mb-2 opacity-90">
                            {{
                                personData.person.role?.name ||
                                'Cargo no asignado'
                            }}
                        </div>
                        <div class="d-flex text-subtitle-2 gap-4 opacity-80">
                            <div class="d-flex align-center gap-1">
                                <v-icon size="16">mdi-domain</v-icon>
                                {{
                                    personData.person.department?.name || 'N/A'
                                }}
                            </div>
                            <div class="d-flex align-center gap-1">
                                <v-icon size="16">mdi-email</v-icon>
                                {{ personData.person.email }}
                            </div>
                            <div class="d-flex align-center gap-1">
                                <v-icon size="16">mdi-calendar</v-icon>
                                Hired:
                                {{ formatDate(personData.person.hire_date) }}
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-column gap-2 text-right">
                        <div
                            class="text-caption tracking-tighter text-white uppercase opacity-70"
                        >
                            Match Actual
                        </div>
                        <div class="text-h2 font-weight-black text-white">
                            {{
                                personData.gap_analysis?.match_percentage || 0
                            }}%
                        </div>
                        <v-chip
                            :color="
                                getStatusColor(
                                    personData.gap_analysis?.summary.category,
                                )
                            "
                            size="small"
                            class="font-weight-bold"
                        >
                            {{
                                personData.gap_analysis?.summary.category ||
                                'Sin calificar'
                            }}
                        </v-chip>
                    </div>
                </div>

                <v-tabs
                    v-model="activeTab"
                    bg-color="white"
                    color="primary"
                    grow
                >
                    <v-tab value="profile" prepend-icon="mdi-account-circle"
                        >Perfil Potencial</v-tab
                    >
                    <v-tab value="gaps" prepend-icon="mdi-chart-radar"
                        >Competencias & Gaps</v-tab
                    >
                    <v-tab value="learning" prepend-icon="mdi-school"
                        >Ruta de Aprendizaje</v-tab
                    >
                    <v-tab value="strategic" prepend-icon="mdi-clover"
                        >Valor Estratégico</v-tab
                    >
                </v-tabs>
            </v-card>

            <!-- Main Content Area -->
            <v-window v-model="activeTab">
                <!-- Potential Tab -->
                <v-window-item value="profile">
                    <v-row>
                        <v-col cols="12" md="8">
                            <v-card border flat class="pa-6 h-100">
                                <div
                                    class="d-flex justify-space-between align-center mb-6"
                                >
                                    <h3 class="text-h5 font-weight-bold">
                                        Análisis de Potencial IA
                                    </h3>
                                    <v-btn
                                        icon="mdi-refresh"
                                        variant="text"
                                        size="small"
                                        color="primary"
                                    ></v-btn>
                                </div>

                                <div
                                    v-if="
                                        personData.person.psychometric_profiles
                                            ?.length
                                    "
                                >
                                    <v-alert
                                        type="info"
                                        variant="tonal"
                                        border="start"
                                        class="mb-6"
                                    >
                                        <div class="font-weight-bold mb-1">
                                            Reporte de Síntesis
                                        </div>
                                        {{
                                            personData.person.metadata
                                                ?.summary_report ||
                                            'Análisis consolidado de rasgos y aptitudes basado en interacciones 360 y evaluaciones.'
                                        }}
                                    </v-alert>

                                    <v-row>
                                        <v-col
                                            v-for="trait in personData.person
                                                .psychometric_profiles"
                                            :key="trait.id"
                                            cols="12"
                                            sm="6"
                                        >
                                            <div class="mb-4">
                                                <div
                                                    class="d-flex justify-space-between mb-1"
                                                >
                                                    <span
                                                        class="text-caption font-weight-bold text-uppercase"
                                                        >{{
                                                            trait.trait_name
                                                        }}</span
                                                    >
                                                    <span
                                                        class="text-caption font-weight-black"
                                                        >{{
                                                            (
                                                                trait.score *
                                                                100
                                                            ).toFixed(0)
                                                        }}%</span
                                                    >
                                                </div>
                                                <v-progress-linear
                                                    :model-value="
                                                        trait.score * 100
                                                    "
                                                    height="8"
                                                    rounded
                                                    color="primary"
                                                />
                                                <div
                                                    class="text-caption text-grey-darken-1 mt-1"
                                                >
                                                    {{ trait.rationale }}
                                                </div>
                                            </div>
                                        </v-col>
                                    </v-row>
                                </div>
                                <div v-else class="py-10 text-center">
                                    <v-icon
                                        size="64"
                                        color="grey-lighten-2"
                                        class="mb-4"
                                        >mdi-brain</v-icon
                                    >
                                    <p class="text-grey mb-4">
                                        No hay perfil psicométrico disponible.
                                        Comienza una evaluación interactiva.
                                    </p>
                                    <AssessmentChat
                                        :person-id="personData.person.id"
                                        @completed="fetchProfile"
                                    />
                                </div>
                            </v-card>
                        </v-col>

                        <v-col cols="12" md="4">
                            <v-card
                                border
                                flat
                                class="pa-6 mb-6"
                                v-if="
                                    personData.person.metadata?.blind_spots
                                        ?.length
                                "
                            >
                                <h4
                                    class="text-subtitle-1 font-weight-bold d-flex align-center mb-4 gap-2"
                                >
                                    <v-icon color="warning">mdi-eye-off</v-icon>
                                    Puntos Ciegos
                                </h4>
                                <ul class="pl-4">
                                    <li
                                        v-for="(spot, i) in personData.person
                                            .metadata.blind_spots"
                                        :key="i"
                                        class="text-body-2 mb-2"
                                    >
                                        {{ spot }}
                                    </li>
                                </ul>
                            </v-card>

                            <v-card border flat class="pa-6">
                                <h4
                                    class="text-subtitle-1 font-weight-bold mb-4"
                                >
                                    Relaciones 360
                                </h4>
                                <div class="d-flex flex-column gap-3">
                                    <div
                                        v-for="rel in personData.person
                                            .relations"
                                        :key="rel.id"
                                        class="d-flex align-center gap-3"
                                    >
                                        <v-avatar
                                            size="32"
                                            color="grey-lighten-3"
                                        >
                                            <v-img
                                                :src="
                                                    rel.related_person.photo_url
                                                "
                                            />
                                        </v-avatar>
                                        <div>
                                            <div
                                                class="text-body-2 font-weight-bold"
                                            >
                                                {{
                                                    rel.related_person
                                                        .first_name
                                                }}
                                                {{
                                                    rel.related_person.last_name
                                                }}
                                            </div>
                                            <div
                                                class="text-caption text-grey text-capitalize"
                                            >
                                                {{ rel.relationship_type }}
                                            </div>
                                        </div>
                                    </div>
                                    <v-btn
                                        block
                                        variant="tonal"
                                        size="small"
                                        color="primary"
                                        class="mt-2"
                                        >Configurar Red</v-btn
                                    >
                                </div>
                            </v-card>
                        </v-col>
                    </v-row>
                </v-window-item>

                <!-- Gaps Tab -->
                <v-window-item value="gaps">
                    <v-row>
                        <v-col cols="12" md="8">
                            <v-card border flat class="pa-6">
                                <h3 class="text-h5 font-weight-bold mb-6">
                                    Matriz de Competencias & Skills
                                </h3>

                                <div v-if="personData.competencies?.length">
                                    <div
                                        v-for="comp in personData.competencies"
                                        :key="comp.id"
                                        class="mb-8"
                                    >
                                        <div
                                            class="text-h6 font-weight-bold d-flex align-center mb-4 gap-2 text-primary"
                                        >
                                            <v-icon
                                                icon="mdi-hexagon-multiple"
                                            />
                                            {{ comp.name }}
                                        </div>

                                        <v-row>
                                            <v-col
                                                v-for="skill in comp.skills"
                                                :key="skill.id"
                                                cols="10"
                                                offset="1"
                                                class="py-1"
                                            >
                                                <div
                                                    class="d-flex justify-space-between align-center mb-1"
                                                >
                                                    <div
                                                        class="d-flex align-center gap-2"
                                                    >
                                                        <v-icon
                                                            v-if="
                                                                skill.is_critical
                                                            "
                                                            color="error"
                                                            size="14"
                                                            >mdi-alert-circle</v-icon
                                                        >
                                                        <span
                                                            class="text-body-2"
                                                            >{{
                                                                skill.name
                                                            }}</span
                                                        >
                                                    </div>
                                                    <span
                                                        class="text-caption font-weight-bold"
                                                        :class="
                                                            skill.current_level >=
                                                            skill.required_level
                                                                ? 'text-success'
                                                                : 'text-warning'
                                                        "
                                                    >
                                                        {{
                                                            skill.current_level
                                                        }}
                                                        /
                                                        {{
                                                            skill.required_level
                                                        }}
                                                    </span>
                                                </div>
                                                <v-progress-linear
                                                    :model-value="
                                                        (skill.current_level /
                                                            skill.required_level) *
                                                        100
                                                    "
                                                    :color="
                                                        skill.current_level >=
                                                        skill.required_level
                                                            ? 'success'
                                                            : skill.current_level >
                                                                0
                                                              ? 'warning'
                                                              : 'grey-lighten-2'
                                                    "
                                                    height="6"
                                                    rounded
                                                />
                                            </v-col>
                                        </v-row>
                                    </div>
                                </div>
                            </v-card>
                        </v-col>

                        <v-col cols="12" md="5">
                            <v-card
                                border
                                flat
                                class="pa-6 bg-grey-lighten-5 mb-6 text-center"
                            >
                                <h4 class="text-overline mb-2">
                                    Resumen de Brechas
                                </h4>
                                <div class="text-h2 font-weight-black mb-1">
                                    {{
                                        personData.gap_analysis?.summary
                                            .skills_ok || 0
                                    }}
                                </div>
                                <div class="text-caption mb-4">
                                    de
                                    {{
                                        personData.gap_analysis?.summary
                                            .total_skills || 0
                                    }}
                                    skills cumplen el requerimiento
                                </div>

                                <v-sheet
                                    class="pa-4 rounded border bg-white text-left"
                                >
                                    <div
                                        class="text-subtitle-2 font-weight-bold mb-2"
                                    >
                                        Recomendación de Movilidad
                                    </div>
                                    <p class="text-body-2">
                                        {{
                                            personData.gap_analysis
                                                ?.match_percentage > 85
                                                ? 'El colaborador está sobre-calificado o listo para una promoción inmediata.'
                                                : personData.gap_analysis
                                                        ?.match_percentage > 60
                                                  ? 'El colaborador tiene el potencial pero requiere focalizar desarrollo en skills críticas.'
                                                  : 'Se recomienda un plan intensivo de capacitación previo a cualquier movimiento estratégico.'
                                        }}
                                    </p>
                                </v-sheet>
                            </v-card>
                        </v-col>
                    </v-row>
                </v-window-item>

                <!-- Learning Tab -->
                <v-window-item value="learning">
                    <DevelopmentTab
                        :person-id="personData.person.id"
                        :skills="personData.person.skills"
                    />
                </v-window-item>

                <!-- Strategic Tab -->
                <v-window-item value="strategic">
                    <v-row>
                        <v-col cols="12" md="6">
                            <v-card border flat class="pa-6 h-100">
                                <h3
                                    class="text-h5 font-weight-bold d-flex align-center mb-4 gap-2"
                                >
                                    <v-icon color="deep-purple"
                                        >mdi-axis-z-arrow</v-icon
                                    >
                                    Escenarios de Transformación
                                </h3>
                                <div
                                    v-if="personData.scenarios?.length"
                                    class="d-flex flex-column gap-4"
                                >
                                    <v-card
                                        v-for="scn in personData.scenarios"
                                        :key="scn.id"
                                        border
                                        flat
                                        class="pa-4 bg-deep-purple-lighten-5"
                                    >
                                        <div
                                            class="d-flex justify-space-between align-center mb-2"
                                        >
                                            <div
                                                class="font-weight-black text-deep-purple-darken-3"
                                            >
                                                {{ scn.name }}
                                            </div>
                                            <v-chip
                                                size="x-small"
                                                color="deep-purple"
                                                variant="flat"
                                                >{{ scn.status }}</v-chip
                                            >
                                        </div>
                                        <div class="text-body-2 mb-2">
                                            Impacto en rol:
                                            <span
                                                class="font-weight-bold text-capitalize"
                                                >{{ scn.impact_level }}</span
                                            >
                                        </div>
                                        <div class="text-caption">
                                            Evolución prevista:
                                            <span class="text-capitalize">{{
                                                scn.evolution_type.replace(
                                                    '_',
                                                    ' ',
                                                )
                                            }}</span>
                                        </div>
                                    </v-card>
                                </div>
                                <div
                                    v-else
                                    class="py-10 text-center opacity-50"
                                >
                                    <v-icon size="48">mdi-molecule</v-icon>
                                    <p class="mt-2">
                                        No se detecta participación activa en
                                        escenarios estratégicos.
                                    </p>
                                </div>
                            </v-card>
                        </v-col>

                        <v-col cols="12" md="6">
                            <v-card border flat class="pa-6 h-100">
                                <h3
                                    class="text-h5 font-weight-bold d-flex align-center mb-4 gap-2"
                                >
                                    <v-icon color="primary"
                                        >mdi-account-star</v-icon
                                    >
                                    Plan de Sucesión
                                </h3>
                                <!-- This could be more detailed if the data was fully available -->
                                <div
                                    class="pa-4 dashed rounded border py-8 text-center"
                                >
                                    <v-icon size="48" color="grey"
                                        >mdi-shield-account</v-icon
                                    >
                                    <div
                                        class="text-subtitle-1 font-weight-bold mt-3"
                                    >
                                        Estado de Sucesión
                                    </div>
                                    <p class="text-caption text-grey">
                                        Actualmente no está listado como sucesor
                                        primario en planes críticos.
                                    </p>
                                    <v-btn
                                        variant="text"
                                        size="small"
                                        color="primary"
                                        class="mt-2"
                                        >Explorar oportunidades</v-btn
                                    >
                                </div>
                            </v-card>
                        </v-col>
                    </v-row>
                </v-window-item>
            </v-window>
        </div>
    </div>
</template>

<style scoped>
.people-profile-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
    min-height: 100vh;
}

.profile-header {
    border-radius: 16px;
    background: white;
}

.header-gradient {
    background: linear-gradient(135deg, #1867c0 0%, #5cbbf6 100%);
    min-height: 200px;
}

.animate-fade-in {
    animation: fadeIn 0.6s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.gap-item {
    transition: transform 0.2s;
}

.gap-item:hover {
    transform: translateX(4px);
}

.no-wrap {
    white-space: nowrap;
}

.opacity-90 {
    opacity: 0.9;
}
.opacity-80 {
    opacity: 0.8;
}
.opacity-70 {
    opacity: 0.7;
}
</style>
