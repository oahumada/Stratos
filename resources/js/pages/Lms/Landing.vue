<script setup lang="ts">
import LmsLandingContent from '@/components/Lms/LmsLandingContent.vue';
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
                            Página de aterrizaje del LMS: inscripciones,
                            certificados y contenido.
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
                    <p class="text-body-2 mb-4">
                        Aquí irán métricas, cursos recientes y acciones rápidas
                        para administradores.
                    </p>

                    <LmsLandingContent />
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
                        CMS / Contenido
                    </h3>
                    <p class="text-body-2 mb-4 opacity-80">
                        Área reservada para contenido administrable del LMS
                        (plantillas, textos, banners). Será administrable desde
                        el CMS del producto.
                    </p>
                    <v-btn
                        color="white"
                        variant="flat"
                        block
                        class="text-none mt-auto"
                        size="small"
                    >
                        Editar en CMS
                    </v-btn>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<style scoped></style>
