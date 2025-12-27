<script setup lang="ts">
import { dashboard, login, register } from '@/routes';
import { Head, Link } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const auth = page.props.auth as any;

const features = [
    'Tailwind CSS included',
    'Database migrations',
    'Authentication',
    'RESTful routing',
    'Cache for enhanced performance',
    'API ready',
    'Deploy now'
];

withDefaults(
    defineProps<{
        canRegister: boolean;
    }>(),
    {
        canRegister: true,
    },
);
</script>

<template>
    <Head title="Welcome">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    
    <v-app>
        <!-- Main Container -->
        <v-container class="h-screen d-flex flex-column pa-6 pa-md-8">
            <!-- Header Navigation -->
            <v-row no-gutters class="mb-8">
                <v-col cols="12">
                    <v-row align="center" justify="space-between" no-gutters class="w-100">
                        <!-- Logo -->
                        <v-col cols="auto">
                            <Link href="/">
                                <h1 class="text-h3 font-weight-bold text-decoration-none text-black dark:text-white">
                                    Talentia
                                </h1>
                            </Link>
                        </v-col>

                        <!-- Navigation Links -->
                        <v-col cols="auto">
                            <v-button-group>
                                <Link
                                    v-if="!auth.user"
                                    :href="login()"
                                    class="text-decoration-none"
                                >
                                    <v-btn
                                        variant="outlined"
                                        size="small"
                                        class="rounded-sm"
                                    >
                                        Ingresar
                                    </v-btn>
                                </Link>
                                <Link
                                    v-if="!auth.user"
                                    :href="register()"
                                    class="text-decoration-none"
                                >
                                    <v-btn
                                        variant="flat"
                                        color="surface-variant"
                                        size="small"
                                        class="rounded-sm"
                                    >
                                        Registrarse
                                    </v-btn>
                                </Link>
                                <Link
                                    v-else
                                    :href="dashboard()"
                                    class="text-decoration-none"
                                >
                                    <v-btn
                                        variant="flat"
                                        color="surface-variant"
                                        size="small"
                                        class="rounded-sm"
                                    >
                                        Dashboard
                                    </v-btn>
                                </Link>
                            </v-button-group>
                        </v-col>
                    </v-row>
                </v-col>
            </v-row>

            <!-- Main Content Row -->
            <v-row class="flex-grow-1 align-center gap-12" no-gutters>
                <!-- Left Column: Heading and Description -->
                <v-col cols="12" lg="6" class="d-flex flex-column justify-center">
                    <h2 class="text-h2 font-weight-bold mb-6 text-black dark:text-white">
                        Build faster with
                        <span
                            class="d-block font-weight-bold text-transparent"
                            style="background: linear-gradient(to right, #F53003, #FDB022);
                                   -webkit-background-clip: text;
                                   -webkit-text-fill-color: transparent;
                                   background-clip: text;"
                        >
                            Talentia
                        </span>
                    </h2>
                    <p class="text-h6 text-grey-darken-1 dark:text-grey-lighten-2 mb-4">
                        Discover how Talentia helps product teams do their
                        <br />
                        best work with less effort.
                    </p>
                </v-col>

                <!-- Right Column: Cards and Features -->
                <v-col cols="12" lg="6">
                    <!-- Documentation Card -->
                    <a
                        href="https://laravel.com"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="text-decoration-none d-block mb-2"
                    >
                        <v-card
                            class="hover:elevation-4 transition-all cursor-pointer rounded-md"
                            elevation="0"
                            border
                        >
                            <v-card-text class="d-flex align-center gap-2 pa-3">
                                <v-icon size="small" icon="mdi-file-document" />
                                <span class="text-black dark:text-white">Documentation</span>
                                <v-spacer />
                                <v-icon size="small" icon="mdi-arrow-right" />
                            </v-card-text>
                        </v-card>
                    </a>

                    <!-- Interactive Tutorials Card -->
                    <a
                        href="https://laracasts.com"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="text-decoration-none d-block mb-6"
                    >
                        <v-card
                            class="hover:elevation-4 transition-all cursor-pointer rounded-md"
                            elevation="0"
                            border
                        >
                            <v-card-text class="d-flex align-center gap-2 pa-3">
                                <v-icon size="small" icon="mdi-play-circle" />
                                <span class="text-black dark:text-white">Interactive tutorials</span>
                                <v-spacer />
                                <v-icon size="small" icon="mdi-arrow-right" />
                            </v-card-text>
                        </v-card>
                    </a>

                    <!-- Features Chips -->
                    <div class="space-y-2">
                        <div class="mb-2 text-overline text-grey-darken-1 dark:text-grey-lighten-2">
                            Included Features
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <v-chip
                                v-for="feature in features"
                                :key="feature"
                                variant="outlined"
                                prepend-icon="mdi-check"
                                size="small"
                                class="rounded-sm"
                            >
                                {{ feature }}
                            </v-chip>
                        </div>
                    </div>
                </v-col>

                <!-- Right Column: SVG Graphics -->
                <v-col cols="12" lg="6" class="d-none d-lg-flex align-center justify-center">
                    <svg
                        class="w-100"
                        style="max-width: 438px; aspect-ratio: 438 / 376;"
                        viewBox="0 0 438 104"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <!-- SVG Logo/Graphic (simplified) -->
                        <path
                            d="M17.2036 -3H0V102.197H49.5189V86.7187H17.2036V-3Z"
                            fill="currentColor"
                            class="text-[#F53003] dark:text-[#F61500]"
                        />
                        <path
                            d="M110.256 41.6337C108.061 38.1275 104.945 35.3731 100.905 33.3681C96.8667 31.3647 92.8016 30.3618 88.7131 30.3618C83.4247 30.3618 78.5885 31.3389 74.201 33.2923C69.8111 35.2456 66.0474 37.928 62.9059 41.3333C59.7643 44.7401 57.3198 48.6726 55.5754 53.1293C53.8287 57.589 52.9572 62.274 52.9572 67.1813C52.9572 72.1925 53.8287 76.8995 55.5754 81.3069C57.3191 85.7173 59.7636 89.6241 62.9059 93.0293C66.0474 96.4361 69.8119 99.1155 74.201 101.069C78.5885 103.022 83.4247 103.999 88.7131 103.999C92.8016 103.999 96.8667 102.997 100.905 100.994C104.945 98.9911 108.061 96.2359 110.256 92.7282V102.195H126.563V32.1642H110.256V41.6337ZM108.76 75.7472C107.762 78.4531 106.366 80.8078 104.572 82.8112C102.776 84.8161 100.606 86.4183 98.0637 87.6206C95.5202 88.823 92.7004 89.4238 89.6103 89.4238C86.5178 89.4238 83.7252 88.823 81.2324 87.6206C78.7388 86.4183 76.5949 84.8161 74.7998 82.8112C73.004 80.8078 71.6319 78.4531 70.6856 75.7472C69.7356 73.0421 69.2644 70.1868 69.2644 67.1821C69.2644 64.1758 69.7356 61.3205 70.6856 58.6154C71.6319 55.9102 73.004 53.5571 74.7998 51.5522C76.5949 49.5495 78.738 47.9451 81.2324 46.7427C83.7252 45.5404 86.5178 44.9396 89.6103 44.9396C92.7012 44.9396 95.5202 45.5404 98.0637 46.7427C100.606 47.9451 102.776 49.5487 104.572 51.5522C106.367 53.5571 107.762 55.9102 108.76 58.6154C109.756 61.3205 110.256 64.1758 110.256 67.1821C110.256 70.1868 109.756 73.0421 108.76 75.7472Z"
                            fill="currentColor"
                            class="text-[#F53003] dark:text-[#F61500]"
                        />
                    </svg>
                </v-col>
            </v-row>
        </v-container>
    </v-app>
</template>

<style scoped>
.rounded-sm {
    border-radius: 0.125rem !important;
}

.hover\:elevation-4:hover {
    box-shadow: 0px 3px 6px -1px rgba(0, 0, 0, 0.2),
                0px 6px 16px 0px rgba(0, 0, 0, 0.14),
                0px 9px 28px 8px rgba(0, 0, 0, 0.12) !important;
}

.cursor-pointer {
    cursor: pointer;
}

.transition-all {
    transition: all 0.3s ease;
}

.space-y-2 > * + * {
    margin-top: 0.5rem;
}

.gap-2 {
    gap: 0.5rem;
}

.gap-12 {
    gap: 3rem;
}

.text-decoration-none {
    text-decoration: none !important;
}

.w-100 {
    width: 100%;
}

.h-screen {
    height: 100vh;
}

.d-flex {
    display: flex;
}

.flex-column {
    flex-direction: column;
}

.flex-wrap {
    flex-wrap: wrap;
}

.flex-grow-1 {
    flex-grow: 1;
}

.d-none {
    display: none;
}

.d-lg-flex {
    @media (min-width: 1024px) {
        display: flex !important;
    }
}

.align-center {
    align-items: center;
}

.justify-center {
    justify-content: center;
}

.gap-12 {
    gap: 3rem;
}

/* Dark mode utilities */
.dark\:text-white {
    color: white;
}

.dark\:text-grey-lighten-2 {
    color: #a0a09e;
}

.text-black {
    color: #1b1b18;
}

.text-grey-darken-1 {
    color: #666663;
}

.text-transparent {
    color: transparent;
}

.font-weight-bold {
    font-weight: 700;
}

.text-h2 {
    font-size: 3.5rem;
    line-height: 1;
}

.text-h3 {
    font-size: 2.2rem;
    line-height: 1;
}

.text-h6 {
    font-size: 1.25rem;
    line-height: 1.5;
}

.text-overline {
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.08em;
}

/* Vuetify responsive */
.pa-6 {
    padding: 1.5rem;
}

.pa-md-8 {
    @media (min-width: 960px) {
        padding: 2rem;
    }
}

.mb-2 {
    margin-bottom: 0.5rem;
}

.mb-4 {
    margin-bottom: 1rem;
}

.mb-6 {
    margin-bottom: 1.5rem;
}

.mb-8 {
    margin-bottom: 2rem;
}

.pa-3 {
    padding: 0.75rem;
}

.no-gutters {
    margin: 0 !important;
}

.no-gutters > * {
    padding: 0 !important;
}
</style>
