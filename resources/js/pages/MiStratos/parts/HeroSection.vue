<script setup lang="ts">
import { PhIdentificationBadge, PhCube, PhShapes, PhBuildings, PhStar, PhUser, PhDna } from '@phosphor-icons/vue';

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
    <div class="hero-header relative overflow-hidden p-12 mb-8 rounded-3xl animate-in fade-in slide-in-from-top-4 duration-1000">
        <div class="hero-backdrop absolute inset-0 bg-linear-to-br from-indigo-600/20 via-transparent to-fuchsia-600/10 backdrop-blur-3xl border border-white/10" />
        
        <!-- Decoration Glows -->
        <div class="absolute -top-24 -left-24 w-64 h-64 bg-indigo-500/10 rounded-full blur-3xl animate-pulse" />
        <div class="absolute -bottom-24 -right-24 w-64 h-64 bg-fuchsia-500/10 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s" />

        <div class="relative hero-content flex items-center flex-wrap gap-8">
            <!-- Avatar with Glow -->
            <div class="relative">
                <div class="absolute inset-0 bg-indigo-500/30 rounded-full blur-xl animate-pulse" />
                <div class="relative w-24 h-24 md:w-32 md:h-32 rounded-full border-2 border-white/20 overflow-hidden shadow-2xl bg-white/5 flex items-center justify-center">
                    <img 
                        v-if="person.photo_url"
                        :src="person.photo_url" 
                        class="w-full h-full object-cover"
                        alt="Avatar"
                    />
                    <PhUser v-else :size="48" weight="duotone" class="text-white/20" />
                </div>
            </div>

            <!-- Identity Info -->
            <div class="grow min-w-[300px]">
                <p class="text-indigo-300 font-medium tracking-wide mb-1 text-lg">
                    {{ greeting }}, {{ person.first_name }}!
                </p>
                <h1 class="text-4xl md:text-5xl font-black text-white tracking-tight mb-3 drop-shadow-lg">
                    {{ person.full_name }}
                </h1>
                <p class="text-white/60 text-lg font-light leading-relaxed max-w-xl">
                    Tu punto de partida hoy: rol, brechas y próxima ruta de crecimiento estratégico.
                </p>

                <!-- Status Chips Section -->
                <div class="flex flex-wrap items-center gap-3 mt-6">
                    <div v-if="person.role" class="flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/5 border border-white/10 text-xs font-bold text-white/80 uppercase tracking-wider backdrop-blur-md">
                        <PhIdentificationBadge :size="16" weight="duotone" class="text-indigo-400" />
                        {{ person.role.name }}
                    </div>
                    
                    <div v-if="cubeLabel" class="flex items-center gap-2 px-3 py-1.5 rounded-full bg-amber-500/20 border border-amber-500/30 text-xs font-bold text-amber-200 uppercase tracking-wider backdrop-blur-md">
                        <PhCube :size="16" weight="fill" />
                        Cubo {{ cubeLabel }}
                    </div>

                    <div v-if="archetypeLabel" class="flex items-center gap-2 px-3 py-1.5 rounded-full bg-cyan-500/20 border border-cyan-500/30 text-xs font-bold text-cyan-200 uppercase tracking-wider backdrop-blur-md">
                        <PhShapes :size="16" weight="duotone" />
                        {{ archetypeLabel }}
                    </div>

                    <div v-if="person.department" class="flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-500/20 border border-slate-500/30 text-xs font-bold text-slate-200 uppercase tracking-wider backdrop-blur-md">
                        <PhBuildings :size="16" weight="duotone" />
                        {{ person.department.name }}
                    </div>

                    <div v-if="person.is_high_potential" class="flex items-center gap-2 px-3 py-1.5 rounded-full bg-fuchsia-500/20 border border-fuchsia-500/30 text-xs font-bold text-fuchsia-200 uppercase tracking-wider backdrop-blur-md animate-pulse">
                        <PhStar :size="16" weight="fill" />
                        Talento Alto Potencial
                    </div>
                </div>
            </div>

            <!-- Match Score Circle -->
            <div v-if="kpis" class="hidden lg:flex flex-col items-center gap-3 p-12! rounded-3xl bg-white/5 border border-white/10 backdrop-blur-xl shadow-2xl">
                <div class="relative flex items-center justify-center">
                    <svg class="w-24 h-24 transform -rotate-90">
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
                            :style="{ strokeDasharray: 264, strokeDashoffset: 264 - (264 * kpis.potential) / 100 }"
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
                    <div class="absolute flex flex-col items-center justify-center">
                        <PhDna :size="32" weight="duotone" class="text-white/10 absolute -top-4" />
                        <div class="text-2xl font-black text-white relative z-10">
                            {{ kpis.potential }}%
                        </div>
                    </div>
                </div>
                <span class="text-[10px] font-black uppercase tracking-[0.2em] text-white/50">
                    Match con tu rol
                </span>
            </div>
        </div>
    </div>
</template>
