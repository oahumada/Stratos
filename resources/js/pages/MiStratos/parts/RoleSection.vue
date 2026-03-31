<script setup lang="ts">
import StCardGlass from '@/components/StCardGlass.vue';
import {
    PhCube,
    PhIdentificationBadge,
    PhPuzzlePiece,
    PhQuotes,
    PhShapes,
    PhWarning,
} from '@phosphor-icons/vue';

interface Props {
    person: any;
    competencies: Array<any>;
    archetypeLabel: string | null;
    cubeLabel: string | null;
}

defineProps<Props>();

const getSkillColor = (current: number, required: number) => {
    if (current >= required) return 'bg-emerald-500';
    if (current >= required * 0.7) return 'bg-amber-500';
    return 'bg-rose-500';
};
</script>

<template>
    <div class="animate-in duration-700 fade-in slide-in-from-bottom-4">
        <h2 class="mb-6 flex items-center gap-3 text-2xl font-black text-white">
            <PhIdentificationBadge
                :size="28"
                weight="duotone"
                class="text-indigo-400"
            />
            Mi Rol
        </h2>

        <div v-if="person.role" class="space-y-6">
            <!-- Role Header Card -->
            <StCardGlass indicator="indigo" class="p-12!">
                <div
                    class="flex flex-col items-start gap-6 md:flex-row md:items-center"
                >
                    <div
                        class="rounded-2xl bg-indigo-500/10 p-4 text-indigo-400"
                    >
                        <PhIdentificationBadge :size="48" weight="duotone" />
                    </div>
                    <div class="grow">
                        <h3
                            class="mb-2 text-3xl font-black tracking-tight text-white"
                        >
                            {{ person.role.name }}
                        </h3>
                        <div class="flex flex-wrap gap-3">
                            <div
                                v-if="archetypeLabel"
                                class="flex items-center gap-2 rounded-full border border-cyan-500/20 bg-cyan-500/10 px-3 py-1 text-[10px] font-black tracking-wider text-cyan-300 uppercase"
                            >
                                <PhShapes :size="14" weight="duotone" />
                                {{ archetypeLabel }}
                            </div>
                            <div
                                v-if="cubeLabel"
                                class="flex items-center gap-2 rounded-full border border-amber-500/20 bg-amber-500/10 px-3 py-1 text-[10px] font-black tracking-wider text-amber-300 uppercase"
                            >
                                <PhCube :size="14" weight="duotone" />
                                Cubo {{ cubeLabel }}
                            </div>
                        </div>
                    </div>
                </div>
            </StCardGlass>

            <!-- Competencies Section -->
            <div class="grid grid-cols-1 gap-6">
                <div class="mb-2 flex items-center justify-between">
                    <h4
                        class="flex items-center gap-2 text-lg font-bold text-white/80"
                    >
                        <PhPuzzlePiece
                            :size="24"
                            weight="duotone"
                            class="text-fuchsia-400"
                        />
                        Competencias Asignadas
                    </h4>
                    <div
                        class="text-[10px] font-black tracking-[0.2em] text-white/30 uppercase"
                    >
                        {{ competencies.length }} Bloques
                    </div>
                </div>

                <div
                    v-if="competencies.length === 0"
                    class="flex flex-col items-center justify-center rounded-3xl border border-dashed border-white/5 bg-white/5 py-12 opacity-50"
                >
                    <PhQuotes :size="48" weight="thin" />
                    <p class="mt-4 text-sm font-light">
                        Sin competencias configuradas para este rol
                    </p>
                </div>

                <div v-else class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <StCardGlass
                        v-for="comp in competencies"
                        :key="comp.id"
                        indicator="fuchsia"
                        class="p-12! transition-colors hover:border-white/10"
                    >
                        <div class="mb-6 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/5 text-indigo-400"
                                >
                                    <PhPuzzlePiece :size="20" weight="bold" />
                                </div>
                                <h5 class="font-bold text-white">
                                    {{ comp.name }}
                                </h5>
                            </div>
                            <span
                                class="rounded-lg bg-indigo-500/10 px-2 py-1 text-[10px] font-black tracking-widest text-indigo-300 uppercase"
                            >
                                {{ comp.skills.length }} Skills
                            </span>
                        </div>

                        <div class="space-y-4">
                            <div
                                v-for="skill in comp.skills"
                                :key="skill.id"
                                class="skill-item group"
                            >
                                <div
                                    class="mb-1.5 flex items-center justify-between"
                                >
                                    <div class="flex items-center gap-2">
                                        <PhWarning
                                            v-if="skill.is_critical"
                                            :size="14"
                                            weight="fill"
                                            class="text-rose-400"
                                        />
                                        <span
                                            class="text-xs font-medium text-white/70 transition-colors group-hover:text-white"
                                        >
                                            {{ skill.name }}
                                        </span>
                                    </div>
                                    <span
                                        class="text-[10px] font-bold text-white/40"
                                    >
                                        {{ skill.current_level }} /
                                        {{ skill.required_level }}
                                    </span>
                                </div>
                                <div
                                    class="h-1.5 w-full overflow-hidden rounded-full bg-white/5"
                                >
                                    <div
                                        class="h-full transition-all duration-1000"
                                        :class="
                                            getSkillColor(
                                                skill.current_level,
                                                skill.required_level,
                                            )
                                        "
                                        :style="{
                                            width: `${Math.min(100, (skill.current_level / skill.required_level) * 100)}%`,
                                        }"
                                    />
                                </div>
                            </div>
                        </div>
                    </StCardGlass>
                </div>
            </div>
        </div>

        <div
            v-else
            class="flex flex-col items-center justify-center rounded-3xl border border-dashed border-white/5 bg-white/5 py-20"
        >
            <div class="mb-6 rounded-3xl bg-white/5 p-6 text-white/20">
                <PhIdentificationBadge :size="64" weight="thin" />
            </div>
            <h3 class="mb-2 text-xl font-bold text-white/60">
                No tienes un rol asignado
            </h3>
            <p class="max-w-xs text-center text-sm font-light text-white/40">
                Contacta a tu líder o al equipo de Talento para vincular tu
                perfil.
            </p>
        </div>
    </div>
</template>
