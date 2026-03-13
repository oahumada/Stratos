<script setup lang="ts">
import { 
    PhIdentificationBadge, 
    PhPuzzlePiece, 
    PhQuotes, 
    PhInfo,
    PhCube,
    PhShapes,
    PhWarning
} from '@phosphor-icons/vue';
import StCardGlass from '@/components/StCardGlass.vue';

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
    <div class="animate-in fade-in slide-in-from-bottom-4 duration-700">
        <h2 class="text-2xl font-black text-white mb-6 flex items-center gap-3">
            <PhIdentificationBadge :size="28" weight="duotone" class="text-indigo-400" />
            Mi Rol
        </h2>

        <div v-if="person.role" class="space-y-6">
            <!-- Role Header Card -->
            <StCardGlass class="p-8">
                <div class="flex flex-col md:flex-row items-start md:items-center gap-6">
                    <div class="p-4 rounded-2xl bg-indigo-500/10 text-indigo-400">
                        <PhIdentificationBadge :size="48" weight="duotone" />
                    </div>
                    <div class="grow">
                        <h3 class="text-3xl font-black text-white tracking-tight mb-2">
                            {{ person.role.name }}
                        </h3>
                        <div class="flex flex-wrap gap-3">
                            <div v-if="archetypeLabel" class="flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-[10px] font-black uppercase tracking-wider text-cyan-300">
                                <PhShapes :size="14" weight="duotone" />
                                {{ archetypeLabel }}
                            </div>
                            <div v-if="cubeLabel" class="flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/10 border border-amber-500/20 text-[10px] font-black uppercase tracking-wider text-amber-300">
                                <PhCube :size="14" weight="duotone" />
                                Cubo {{ cubeLabel }}
                            </div>
                        </div>
                    </div>
                </div>
            </StCardGlass>

            <!-- Competencies Section -->
            <div class="grid grid-cols-1 gap-6">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="text-lg font-bold text-white/80 flex items-center gap-2">
                        <PhPuzzlePiece :size="24" weight="duotone" class="text-fuchsia-400" />
                        Competencias Asignadas
                    </h4>
                    <div class="text-[10px] font-black text-white/30 uppercase tracking-[0.2em]">
                        {{ competencies.length }} Bloques
                    </div>
                </div>

                <div v-if="competencies.length === 0" class="py-12 flex flex-col items-center justify-center bg-white/5 rounded-3xl border border-white/5 border-dashed opacity-50">
                    <PhQuotes :size="48" weight="thin" />
                    <p class="mt-4 text-sm font-light">Sin competencias configuradas para este rol</p>
                </div>

                <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <StCardGlass 
                        v-for="comp in competencies" 
                        :key="comp.id"
                        class="p-6 hover:border-white/10 transition-colors"
                    >
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center text-indigo-400">
                                    <PhPuzzlePiece :size="20" weight="bold" />
                                </div>
                                <h5 class="font-bold text-white">{{ comp.name }}</h5>
                            </div>
                            <span class="text-[10px] font-black px-2 py-1 rounded-lg bg-indigo-500/10 text-indigo-300 uppercase tracking-widest">
                                {{ comp.skills.length }} Skills
                            </span>
                        </div>

                        <div class="space-y-4">
                            <div v-for="skill in comp.skills" :key="skill.id" class="skill-item group">
                                <div class="flex justify-between items-center mb-1.5">
                                    <div class="flex items-center gap-2">
                                        <PhWarning v-if="skill.is_critical" :size="14" weight="fill" class="text-rose-400" />
                                        <span class="text-xs font-medium text-white/70 group-hover:text-white transition-colors">
                                            {{ skill.name }}
                                        </span>
                                    </div>
                                    <span class="text-[10px] font-bold text-white/40">
                                        {{ skill.current_level }} / {{ skill.required_level }}
                                    </span>
                                </div>
                                <div class="h-1.5 w-full bg-white/5 rounded-full overflow-hidden">
                                    <div 
                                        class="h-full transition-all duration-1000"
                                        :class="getSkillColor(skill.current_level, skill.required_level)"
                                        :style="{ width: `${Math.min(100, (skill.current_level / skill.required_level) * 100)}%` }"
                                    />
                                </div>
                            </div>
                        </div>
                    </StCardGlass>
                </div>
            </div>
        </div>

        <div v-else class="py-20 flex flex-col items-center justify-center bg-white/5 rounded-3xl border border-white/5 border-dashed">
            <div class="p-6 rounded-3xl bg-white/5 mb-6 text-white/20">
                <PhIdentificationBadge :size="64" weight="thin" />
            </div>
            <h3 class="text-xl font-bold text-white/60 mb-2">No tienes un rol asignado</h3>
            <p class="text-sm text-white/40 max-w-xs text-center font-light">
                Contacta a tu líder o al equipo de Talento para vincular tu perfil.
            </p>
        </div>
    </div>
</template>
