<script setup lang="ts">
import {
    PhBuildings,
    PhCube,
    PhDna,
    PhIdentificationBadge,
    PhShapes,
    PhStar,
    PhUser,
} from '@phosphor-icons/vue';

interface Props {
    person: any;
    greeting: string;
    archetypeLabel: string | null;
    cubeLabel: string | null;
    kpis: any;
}

defineProps<Props>();

const circleColor = (value: number) => {
    if (value >= 80) return '#10b981'; // emerald-500
    if (value >= 60) return '#f59e0b'; // amber-500
    if (value >= 40) return '#f97316'; // orange-500
    return '#f43f5e'; // rose-500
};
</script>

<template>
    <div
        class="hero-header relative mb-8 animate-in overflow-hidden rounded-3xl p-12 duration-1000 fade-in slide-in-from-top-4"
    >
        <div
            class="hero-backdrop absolute inset-0 border border-white/10 bg-linear-to-br from-indigo-600/20 via-transparent to-fuchsia-600/10 backdrop-blur-3xl"
        />

        <!-- Decoration Glows -->
        <div
            class="absolute -top-24 -left-24 h-64 w-64 animate-pulse rounded-full bg-indigo-500/10 blur-3xl"
        />
        <div
            class="absolute -right-24 -bottom-24 h-64 w-64 animate-pulse rounded-full bg-fuchsia-500/10 blur-3xl"
            style="animation-delay: 1s"
        />

        <div class="hero-content relative flex flex-wrap items-center gap-8">
            <!-- Avatar with Glow -->
            <div class="relative">
                <div
                    class="absolute inset-0 animate-pulse rounded-full bg-indigo-500/30 blur-xl"
                />
                <div
                    class="relative flex h-24 w-24 items-center justify-center overflow-hidden rounded-full border-2 border-white/20 bg-white/5 shadow-2xl md:h-32 md:w-32"
                >
                    <img
                        v-if="person.photo_url"
                        :src="person.photo_url"
                        class="h-full w-full object-cover"
                        alt="Avatar"
                    />
                    <PhUser
                        v-else
                        :size="48"
                        weight="duotone"
                        class="text-white/20"
                    />
                </div>
            </div>

            <!-- Identity Info -->
            <div class="min-w-[300px] grow">
                <p
                    class="mb-1 text-lg font-medium tracking-wide text-indigo-300"
                >
                    {{ greeting }}, {{ person.first_name }}!
                </p>
                <h1
                    class="mb-3 text-4xl font-black tracking-tight text-white drop-shadow-lg md:text-5xl"
                >
                    {{ person.full_name }}
                </h1>
                <p
                    class="max-w-xl text-lg leading-relaxed font-light text-white/60"
                >
                    Tu punto de partida hoy: rol, brechas y próxima ruta de
                    crecimiento estratégico.
                </p>

                <!-- Status Chips Section -->
                <div class="mt-6 flex flex-wrap items-center gap-3">
                    <div
                        v-if="person.role"
                        class="flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-3 py-1.5 text-xs font-bold tracking-wider text-white/80 uppercase backdrop-blur-md"
                    >
                        <PhIdentificationBadge
                            :size="16"
                            weight="duotone"
                            class="text-indigo-400"
                        />
                        {{ person.role.name }}
                    </div>

                    <div
                        v-if="cubeLabel"
                        class="flex items-center gap-2 rounded-full border border-amber-500/30 bg-amber-500/20 px-3 py-1.5 text-xs font-bold tracking-wider text-amber-200 uppercase backdrop-blur-md"
                    >
                        <PhCube :size="16" weight="fill" />
                        Cubo {{ cubeLabel }}
                    </div>

                    <div
                        v-if="archetypeLabel"
                        class="flex items-center gap-2 rounded-full border border-cyan-500/30 bg-cyan-500/20 px-3 py-1.5 text-xs font-bold tracking-wider text-cyan-200 uppercase backdrop-blur-md"
                    >
                        <PhShapes :size="16" weight="duotone" />
                        {{ archetypeLabel }}
                    </div>

                    <div
                        v-if="person.department"
                        class="flex items-center gap-2 rounded-full border border-slate-500/30 bg-slate-500/20 px-3 py-1.5 text-xs font-bold tracking-wider text-slate-200 uppercase backdrop-blur-md"
                    >
                        <PhBuildings :size="16" weight="duotone" />
                        {{ person.department.name }}
                    </div>

                    <div
                        v-if="person.is_high_potential"
                        class="flex animate-pulse items-center gap-2 rounded-full border border-fuchsia-500/30 bg-fuchsia-500/20 px-3 py-1.5 text-xs font-bold tracking-wider text-fuchsia-200 uppercase backdrop-blur-md"
                    >
                        <PhStar :size="16" weight="fill" />
                        Talento Alto Potencial
                    </div>
                </div>
            </div>

            <!-- Match Score Circle -->
            <div
                v-if="kpis"
                class="hidden flex-col items-center gap-3 rounded-3xl border border-white/10 bg-white/5 p-12! shadow-2xl backdrop-blur-xl lg:flex"
            >
                <div class="relative flex items-center justify-center">
                    <svg class="h-24 w-24 -rotate-90 transform">
                        <circle
                            class="text-white/10"
                            stroke-width="8"
                            stroke="currentColor"
                            fill="transparent"
                            r="42"
                            cx="48"
                            cy="48"
                        />
                        <circle
                            :style="{
                                strokeDasharray: 264,
                                strokeDashoffset:
                                    264 - (264 * kpis.potential) / 100,
                            }"
                            stroke-width="8"
                            :stroke="circleColor(kpis.potential)"
                            stroke-linecap="round"
                            fill="transparent"
                            r="42"
                            cx="48"
                            cy="48"
                            class="transition-all duration-1000 ease-out"
                        />
                    </svg>
                    <div
                        class="absolute flex flex-col items-center justify-center"
                    >
                        <PhDna
                            :size="32"
                            weight="duotone"
                            class="absolute -top-4 text-white/10"
                        />
                        <div
                            class="relative z-10 text-2xl font-black text-white"
                        >
                            {{ kpis.potential }}%
                        </div>
                    </div>
                </div>
                <span
                    class="text-[10px] font-black tracking-[0.2em] text-white/50 uppercase"
                >
                    Match con tu rol
                </span>
            </div>
        </div>
    </div>
</template>
