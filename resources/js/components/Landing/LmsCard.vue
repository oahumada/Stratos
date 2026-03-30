<template>
    <v-card
        class="fill-height d-flex flex-column overflow-hidden rounded-xl"
        elevation="1"
    >
        <div class="pa-6" style="position: relative">
            <div class="d-flex align-center mb-4">
                <v-avatar
                    color="deep-purple-darken-2"
                    size="56"
                    class="elevation-3 mr-4 rounded-xl"
                >
                    <v-icon size="32" color="white">mdi-school</v-icon>
                </v-avatar>
                <div>
                    <h3 class="text-h6 font-weight-bold">Operador LMS</h3>
                    <div class="text-body-2">
                        Accede al módulo LMS para inscripciones, certificados y
                        seguimiento.
                    </div>
                </div>
                <v-spacer></v-spacer>
            </div>
        </div>

        <v-card-text class="pa-6 grow"></v-card-text>

        <v-divider></v-divider>

        <v-card-actions class="pa-4 bg-grey-lighten-5">
            <v-btn
                :to="{ path: '/lms' }"
                color="primary"
                variant="tonal"
                size="small"
            >
                Ir al LMS
            </v-btn>
            <v-spacer></v-spacer>
            <v-btn variant="text" size="small" color="primary">
                Configurar
            </v-btn>
        </v-card-actions>
    </v-card>
</template>

<script setup lang="ts">
// Componente simple de tarjeta para acceso al LMS desde el Agents Hub
</script>

<style scoped></style>
