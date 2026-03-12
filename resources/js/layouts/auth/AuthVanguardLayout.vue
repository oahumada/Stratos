<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { home } from '@/routes';
import { Link } from '@inertiajs/vue3';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div
        class="relative flex min-h-svh flex-col items-center justify-center overflow-hidden bg-zinc-950 p-6 md:p-10"
    >
        <!-- Blueprint Grid Pattern -->
        <div
            class="absolute inset-0 z-0 opacity-[0.03]"
            style="
                background-image: radial-gradient(#fff 1px, transparent 1px);
                background-size: 40px 40px;
            "
        ></div>
        <div
            class="absolute inset-0 z-0 opacity-[0.02]"
            style="
                background-image:
                    linear-gradient(#fff 1px, transparent 1px),
                    linear-gradient(90deg, #fff 1px, transparent 1px);
                background-size: 200px 200px;
            "
        ></div>

        <!-- Decorative Orbs (AI & Human Colors) -->
        <div
            class="pointer-events-none absolute -top-48 -left-48 h-[500px] w-[500px] rounded-full bg-indigo-500/10 blur-[120px]"
        ></div>
        <div
            class="pointer-events-none absolute -right-48 -bottom-48 h-[500px] w-[500px] rounded-full bg-emerald-500/10 blur-[120px]"
        ></div>

        <!-- Subtle pulsing center light -->
        <div
            class="pointer-events-none absolute top-1/2 left-1/2 h-[600px] w-[600px] -translate-x-1/2 -translate-y-1/2 animate-pulse rounded-full bg-white/2 blur-[150px]"
        ></div>

        <div class="z-10 w-full max-w-[440px]">
            <div class="flex flex-col gap-8 md:gap-10">
                <!-- Header -->
                <div class="flex flex-col items-center gap-6">
                    <Link
                        :href="home()"
                        class="group relative flex flex-col items-center gap-4"
                    >
                        <div
                            class="absolute -inset-8 rounded-full bg-indigo-500/15 opacity-0 blur-2xl transition-all duration-700 group-hover:opacity-100"
                        ></div>
                        <div
                            class="relative flex h-20 w-20 items-center justify-center rounded-4xl border border-white/10 bg-white/5 shadow-2xl backdrop-blur-2xl transition-all duration-500 group-hover:scale-110 group-hover:border-indigo-500/30"
                        >
                            <AppLogoIcon
                                class="size-12 fill-current text-white drop-shadow-[0_0_15px_rgba(255,255,255,0.3)]"
                            />
                        </div>
                    </Link>

                    <div class="space-y-3 text-center">
                        <h1
                            class="text-4xl font-black tracking-tight text-white uppercase sm:text-5xl"
                        >
                            {{ title }}
                        </h1>
                        <p
                            class="max-w-[280px] text-sm font-medium tracking-wide text-balance text-white/40"
                        >
                            {{ description }}
                        </p>
                    </div>
                </div>

                <!-- Main Card -->
                <div class="relative">
                    <!-- Premium Border/Glow -->
                    <div
                        class="absolute -inset-px rounded-[2.5rem] bg-linear-to-b from-white/10 via-white/5 to-transparent opacity-50"
                    ></div>
                    <div
                        class="absolute -inset-0.5 rounded-[2.5rem] bg-indigo-500/10 opacity-20 blur-xl"
                    ></div>

                    <div
                        class="relative overflow-hidden rounded-[2.2rem] border border-white/10 bg-white/5 p-10 shadow-2xl backdrop-blur-3xl md:p-14"
                    >
                        <!-- Top Light Catch -->
                        <div
                            class="absolute inset-x-0 top-0 h-px bg-linear-to-r from-transparent via-white/20 to-transparent"
                        ></div>

                        <!-- Side Accents -->
                        <div
                            class="absolute top-20 bottom-20 -left-1 w-px bg-linear-to-b from-transparent via-indigo-500/20 to-transparent"
                        ></div>
                        <div
                            class="absolute top-20 -right-1 bottom-20 w-px bg-linear-to-b from-transparent via-emerald-500/20 to-transparent"
                        ></div>

                        <!-- Real slot for content -->
                        <slot />
                    </div>
                </div>

                <!-- Footer / Legal -->
                <div class="flex flex-col items-center gap-4 text-center">
                    <div class="h-px w-12 bg-white/10"></div>
                    <p
                        class="text-[9px] font-black tracking-[0.3em] text-white/20 uppercase"
                    >
                        Stratos Vanguard Core • Architecture v2.4
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
@keyframes pulse-subtle {
    0%,
    100% {
        opacity: 0.02;
        transform: translate(-50%, -50%) scale(1);
    }
    50% {
        opacity: 0.04;
        transform: translate(-50%, -50%) scale(1.05);
    }
}

.animate-pulse-subtle {
    animation: pulse-subtle 8s ease-in-out infinite;
}

:deep(.v-field) {
    --v-field-padding-start: 16px;
    --v-field-padding-end: 16px;
}
</style>
