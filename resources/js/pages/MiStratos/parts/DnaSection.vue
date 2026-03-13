<script setup lang="ts">
import { 
    PhDna, 
    PhBrain, 
    PhEyeSlash, 
    PhArrowLeft, 
    PhSparkle,
    PhWarningCircle
} from '@phosphor-icons/vue';
import StCardGlass from '@/components/StCardGlass.vue';
import StButtonGlass from '@/components/StButtonGlass.vue';
import AssessmentChat from '@/components/Assessments/AssessmentChat.vue';

interface Props {
    person: any;
    data: any;
    isRetakingDna: boolean;
    kpiColor: (score: number) => string;
}

defineProps<Props>();
const emit = defineEmits(['update:isRetakingDna', 'refresh']);

</script>

<template>
    <div class="animate-in fade-in slide-in-from-right-4 duration-700">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h2 class="text-2xl font-black text-white flex items-center gap-3">
                    <PhDna :size="28" weight="duotone" class="text-indigo-400" />
                    Mi ADN Profesional
                </h2>
                <p class="text-sm text-white/40 mt-1 font-light">Análisis psicométrico profundo derivado por Cerbero IA.</p>
            </div>
            
            <div v-if="data?.psychometric && !isRetakingDna" class="flex gap-3">
                <v-btn
                    color="white"
                    variant="outlined"
                    size="small"
                    class="rounded-xl border-white/20 hover:bg-white/5 font-black uppercase tracking-widest text-[10px]"
                    @click="emit('update:isRetakingDna', true)"
                >
                    <PhSparkle :size="14" class="mr-2" />
                    Reevaluar Potencial
                </v-btn>
            </div>
            <div v-else-if="isRetakingDna" class="flex gap-3">
                <v-btn
                    color="white"
                    variant="text"
                    size="small"
                    class="rounded-xl font-black uppercase tracking-widest text-[10px]"
                    @click="emit('update:isRetakingDna', false)"
                >
                    <PhArrowLeft :size="14" class="mr-2" />
                    Regresar al Perfil
                </v-btn>
            </div>
        </div>

        <div v-if="data?.psychometric && !isRetakingDna" class="space-y-8">
            <!-- Traits Section -->
            <StCardGlass class="p-8">
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-12 h-12 rounded-2xl bg-purple-500/10 flex items-center justify-center text-purple-400 border border-purple-500/20">
                        <PhBrain :size="24" weight="duotone" />
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white leading-none">Perfil Psicométrico</h3>
                        <p class="text-xs text-white/40 mt-1 uppercase tracking-widest font-black">Rasgos de Personalidad predominantes</p>
                    </div>
                </div>

                <div v-if="data.psychometric.traits" class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div 
                        v-for="trait in data.psychometric.traits" 
                        :key="trait.name"
                        class="p-4 rounded-2xl bg-white/2 border border-white/5 hover:bg-white/5 transition-all group"
                    >
                        <div class="flex items-center justify-between mb-3">
                            <span class="font-bold text-white group-hover:text-purple-300 transition-colors">{{ trait.name }}</span>
                            <span 
                                class="text-sm font-black"
                                :style="{ color: kpiColor(trait.score * 100) }"
                            >
                                {{ Math.round(trait.score * 100) }}%
                            </span>
                        </div>
                        <div class="h-1.5 w-full bg-white/5 rounded-full overflow-hidden mb-3">
                            <div 
                                class="h-full transition-all duration-1000 ease-out"
                                :style="{ 
                                    width: `${trait.score * 100}%`,
                                    backgroundColor: kpiColor(trait.score * 100)
                                }"
                            />
                        </div>
                        <p class="text-[11px] leading-relaxed text-white/40 italic">
                            "{{ trait.rationale }}"
                        </p>
                    </div>
                </div>
            </StCardGlass>

            <!-- Blind Spots -->
            <div 
                v-if="data?.psychometric?.blind_spots?.length > 0"
                class="p-8 rounded-3xl border border-rose-500/20 bg-linear-to-br from-rose-500/5 to-transparent backdrop-blur-xl"
            >
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-rose-500/10 flex items-center justify-center text-rose-400 border border-rose-500/20">
                        <PhEyeSlash :size="20" weight="duotone" />
                    </div>
                    <h4 class="text-lg font-bold text-rose-100">Blind Spots (Puntos Ciegos)</h4>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div 
                        v-for="(spot, i) in data?.psychometric?.blind_spots" 
                        :key="i"
                        class="flex items-start gap-3 p-4 rounded-2xl bg-white/5 border border-rose-500/10 group hover:border-rose-500/30 transition-colors"
                    >
                        <PhWarningCircle :size="18" weight="fill" class="text-rose-500 shrink-0 mt-0.5" />
                        <p class="text-sm leading-relaxed text-white/70">
                            {{ spot }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div v-else class="rounded-3xl border border-dashed border-white/10 bg-white/5 overflow-hidden">
            <AssessmentChat
                v-if="person"
                :person-id="person.id"
                type="psychometric"
                @completed="emit('refresh')"
            />
        </div>
    </div>
</template>
