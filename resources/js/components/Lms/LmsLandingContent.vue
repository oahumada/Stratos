<script setup lang="ts">
interface Summary {
    active_courses: number;
    my_enrollments: number;
    in_progress_enrollments: number;
    completed_enrollments: number;
    my_certificates: number;
}

interface CourseItem {
    id: number;
    title: string;
    category: string | null;
    level: string;
    estimated_duration_minutes: number;
    xp_points: number;
}

interface EnrollmentItem {
    id: number;
    course_title: string | null;
    category: string | null;
    level: string | null;
    progress_percentage: number;
    status: string;
    started_at: string | null;
    completed_at: string | null;
}

const props = defineProps<{
    summary: Summary;
    recentCourses: CourseItem[];
    myEnrollments: EnrollmentItem[];
}>();

const enrollmentStatusColor = (status: string): string => {
    const colors: Record<string, string> = {
        enrolled: 'grey',
        in_progress: 'primary',
        completed: 'success',
    };

    return colors[status] ?? 'grey';
};

const formatDuration = (minutes: number): string => {
    if (minutes < 60) {
        return `${minutes} min`;
    }

    const hours = Math.floor(minutes / 60);
    const remainingMinutes = minutes % 60;

    if (remainingMinutes === 0) {
        return `${hours} h`;
    }

    return `${hours} h ${remainingMinutes} min`;
};
</script>

<template>
    <div>
        <v-row class="mb-2">
            <v-col cols="12" md="4">
                <v-card
                    class="pa-4 rounded-lg"
                    elevation="0"
                    color="indigo-lighten-5"
                >
                    <div class="text-caption text-indigo-darken-1 mb-1">
                        Cursos activos
                    </div>
                    <div class="text-h5 font-weight-bold">
                        {{ props.summary.active_courses }}
                    </div>
                </v-card>
            </v-col>
            <v-col cols="12" md="4">
                <v-card
                    class="pa-4 rounded-lg"
                    elevation="0"
                    color="cyan-lighten-5"
                >
                    <div class="text-caption text-cyan-darken-2 mb-1">
                        Inscripciones activas
                    </div>
                    <div class="text-h5 font-weight-bold">
                        {{ props.summary.in_progress_enrollments }}
                    </div>
                </v-card>
            </v-col>
            <v-col cols="12" md="4">
                <v-card
                    class="pa-4 rounded-lg"
                    elevation="0"
                    color="emerald-lighten-5"
                >
                    <div class="text-caption text-emerald-darken-2 mb-1">
                        Certificados emitidos
                    </div>
                    <div class="text-h5 font-weight-bold">
                        {{ props.summary.my_certificates }}
                    </div>
                </v-card>
            </v-col>
        </v-row>

        <v-row>
            <v-col cols="12" md="6">
                <v-card class="pa-4 rounded-lg" elevation="0">
                    <div class="text-subtitle-1 font-weight-medium mb-3">
                        Cursos disponibles
                    </div>
                    <div
                        v-if="props.recentCourses.length === 0"
                        class="text-body-2 text-grey-darken-1"
                    >
                        Aún no hay cursos activos cargados para tu organización.
                    </div>
                    <div v-else class="d-flex flex-column ga-3">
                        <div
                            v-for="course in props.recentCourses"
                            :key="course.id"
                            class="pa-3 rounded-lg border"
                        >
                            <div
                                class="d-flex align-center justify-space-between ga-2 mb-2"
                            >
                                <div class="font-weight-bold">
                                    {{ course.title }}
                                </div>
                                <v-chip
                                    size="small"
                                    color="primary"
                                    variant="tonal"
                                >
                                    {{ course.level }}
                                </v-chip>
                            </div>
                            <div class="text-body-2 text-grey-darken-1 mb-2">
                                {{ course.category || 'Categoría general' }} ·
                                {{
                                    formatDuration(
                                        course.estimated_duration_minutes,
                                    )
                                }}
                            </div>
                            <div class="text-caption text-grey-darken-1">
                                {{ course.xp_points }} XP disponibles
                            </div>
                        </div>
                    </div>
                </v-card>
            </v-col>

            <v-col cols="12" md="6">
                <v-card class="pa-4 rounded-lg" elevation="0">
                    <div class="text-subtitle-1 font-weight-medium mb-3">
                        Mi progreso
                    </div>
                    <div
                        v-if="props.myEnrollments.length === 0"
                        class="text-body-2 text-grey-darken-1"
                    >
                        Todavía no tienes inscripciones activas. Usa Stratos
                        Growth para asignar tu primera ruta.
                    </div>
                    <div v-else class="d-flex flex-column ga-3">
                        <div
                            v-for="enrollment in props.myEnrollments"
                            :key="enrollment.id"
                            class="pa-3 rounded-lg border"
                        >
                            <div
                                class="d-flex align-center justify-space-between ga-2 mb-2"
                            >
                                <div class="font-weight-bold">
                                    {{
                                        enrollment.course_title ||
                                        'Curso sin título'
                                    }}
                                </div>
                                <v-chip
                                    size="small"
                                    :color="
                                        enrollmentStatusColor(enrollment.status)
                                    "
                                    variant="tonal"
                                >
                                    {{ enrollment.status }}
                                </v-chip>
                            </div>
                            <div class="text-caption text-grey-darken-1 mb-2">
                                {{ enrollment.category || 'Sin categoría' }}
                            </div>
                            <v-progress-linear
                                :model-value="enrollment.progress_percentage"
                                color="primary"
                                height="8"
                                rounded
                            />
                            <div
                                class="d-flex justify-space-between text-caption text-grey-darken-1 mt-2"
                            >
                                <span
                                    >{{ enrollment.progress_percentage }}%
                                    completado</span
                                >
                                <span v-if="enrollment.completed_at"
                                    >Finalizado
                                    {{ enrollment.completed_at }}</span
                                >
                                <span v-else-if="enrollment.started_at"
                                    >Iniciado {{ enrollment.started_at }}</span
                                >
                            </div>
                        </div>
                    </div>
                </v-card>
            </v-col>
        </v-row>

        <v-alert class="mt-4" type="info" variant="tonal">
            Sprint 1 hardening iniciado: esta landing ya muestra datos reales de
            cursos e inscripciones del LMS por organización.
        </v-alert>
    </div>
</template>
