<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <v-container class="h-full d-flex flex-column pa-4 gap-4">
            <!-- Cards Grid (3 columns on desktop) -->
      <!--       <v-row no-gutters class="gap-4">
                <v-col cols="12" sm="6" md="4">
                    <v-card
                        class="h-100 rounded-lg"
                        elevation="0"
                        border
                    >
                        <div class="aspect-video position-relative overflow-hidden">
                            <PlaceholderPattern />
                        </div>
                    </v-card>
                </v-col>
                <v-col cols="12" sm="6" md="4">
                    <v-card
                        class="h-100 rounded-lg"
                        elevation="0"
                        border
                    >
                        <div class="aspect-video position-relative overflow-hidden">
                            <PlaceholderPattern />
                        </div>
                    </v-card>
                </v-col>
                <v-col cols="12" sm="6" md="4">
                    <v-card
                        class="h-100 rounded-lg"
                        elevation="0"
                        border
                    >
                        <div class="aspect-video position-relative overflow-hidden">
                            <PlaceholderPattern />
                        </div>
                    </v-card>
                </v-col>
            </v-row> -->
asasa
            <!-- Main Content Area -->
            <v-row no-gutters class="flex-grow-1">
                <!-- <v-col cols="12">
                    <v-card
                        class="h-100 rounded-lg min-h-screen"
                        elevation="0"
                        border
                    >
                        <div class="position-relative h-100 overflow-hidden">
                            <PlaceholderPattern />
                        </div>
                    </v-card>
                </v-col> -->
                aaa
            </v-row>
        </v-container>
    </AppLayout>
</template>

<style scoped>
.aspect-video {
    aspect-ratio: 16 / 9;
}

.h-100 {
    height: 100%;
}

.h-full {
    height: 100%;
}

.min-h-screen {
    min-height: 100vh;
}

.d-flex {
    display: flex;
}

.flex-column {
    flex-direction: column;
}

.flex-grow-1 {
    flex-grow: 1;
}

.gap-4 {
    gap: 1rem;
}

.pa-4 {
    padding: 1rem;
}

.rounded-lg {
    border-radius: 0.5rem;
}

.position-relative {
    position: relative;
}

.overflow-hidden {
    overflow: hidden;
}

.no-gutters {
    margin: 0 !important;
}

.no-gutters > * {
    padding: 0 !important;
}
</style>