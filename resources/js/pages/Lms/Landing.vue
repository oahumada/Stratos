<script setup lang="ts">
import LmsLandingContent from '@/components/Lms/LmsLandingContent.vue';

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

defineProps<{
    summary: Summary;
    recentCourses: CourseItem[];
    myEnrollments: EnrollmentItem[];
}>();
</script>

<template>
    <v-container fluid class="pa-6 bg-grey-lighten-4 fill-height align-start">
        <v-row>
            <v-col cols="12">
                <div class="d-flex align-center mb-6">
                    <div>
                        <h1 class="text-h4 font-weight-bold mb-1">
                            LMS — Panel
                        </h1>
                        <p class="text-subtitle-1 text-grey-darken-1">
                            Centro de aprendizaje con progreso, certificados y
                            acceso rápido a las rutas del módulo Growth.
                        </p>
                    </div>
                    <v-spacer></v-spacer>
                </div>
            </v-col>

            <v-col cols="12" md="8">
                <v-card class="pa-6 rounded-xl" elevation="1">
                    <h3 class="text-h6 font-weight-bold mb-4">
                        Resumen del LMS
                    </h3>
                    <LmsLandingContent
                        :summary="summary"
                        :recent-courses="recentCourses"
                        :my-enrollments="myEnrollments"
                    />
                </v-card>
            </v-col>

            <v-col cols="12" md="4">
                <v-card
                    class="pa-6 rounded-xl bg-primary text-white"
                    elevation="2"
                >
                    <v-icon
                        icon="mdi-school-outline"
                        size="40"
                        class="mb-4"
                    ></v-icon>
                    <h3 class="text-h6 font-weight-bold mb-2">
                        Acciones rápidas
                    </h3>
                    <p class="text-body-2 mb-4 opacity-80">
                        Entra directo a los flujos más usados del LMS mientras
                        completamos el hardening funcional de Sprint 1.
                    </p>
                    <div class="d-flex flex-column ga-3">
                        <v-btn
                            color="white"
                            variant="flat"
                            block
                            class="text-none"
                            size="small"
                            href="/learning-paths"
                        >
                            Ver rutas de aprendizaje
                        </v-btn>
                        <v-btn
                            color="white"
                            variant="outlined"
                            block
                            class="text-none"
                            size="small"
                            href="/mentoring"
                        >
                            Ir a mentoring
                        </v-btn>
                        <v-btn
                            color="white"
                            variant="text"
                            block
                            class="text-none"
                            size="small"
                            href="/growth"
                        >
                            Volver a Stratos Growth
                        </v-btn>
                    </div>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>
