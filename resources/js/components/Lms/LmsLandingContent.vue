<template>
    <div>
        <h4 class="text-subtitle-1 font-weight-medium mb-3">
            Contenido del LMS (placeholder)
        </h4>
        <div class="text-body-2 mb-4">
            - Módulo de inscripciones
            <br />- Gestión de certificados <br />- Seguimiento de progreso
        </div>

        <v-row>
            <v-col cols="12" md="6">
                <v-card class="pa-4 rounded-lg" elevation="0">
                    <div class="text-caption text-grey mb-1">
                        Cursos Recientes
                    </div>
                    <div class="text-body-2 font-weight-bold">
                        No hay cursos cargados aún
                    </div>
                </v-card>
            </v-col>

            <v-col cols="12" md="6">
                <v-card class="pa-4 rounded-lg" elevation="0">
                    <div class="text-caption text-grey mb-1">
                        Acciones Rápidas
                    </div>
                    <v-btn size="small" color="primary" class="mr-2"
                        >Crear Inscripción</v-btn
                    >
                    <v-btn size="small" variant="text"
                        >Emitir Certificado</v-btn
                    >
                </v-card>
            </v-col>
        </v-row>
    </div>
</template>

<script setup lang="ts">
// Placeholder component for LMS landing content. Replace with CMS-driven data.
</script>

<style scoped></style>
