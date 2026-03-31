<script setup lang="ts">
import AssessmentChat from '@/components/Assessments/AssessmentChat.vue';
import StCardGlass from '@/components/StCardGlass.vue';
import {
    PhArrowLeft,
    PhBrain,
    PhDna,
    PhEyeSlash,
    PhSparkle,
    PhWarningCircle,
} from '@phosphor-icons/vue';

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
    <div class="animate-in duration-700 fade-in slide-in-from-right-4">
        <div
            class="mb-8 flex flex-col justify-between gap-4 md:flex-row md:items-center"
        >
            <div>
                <h2
                    class="flex items-center gap-3 text-2xl font-black text-white"
                >
                    <PhDna
                        :size="28"
                        weight="duotone"
                        class="text-indigo-400"
                    />
                    Mi ADN Profesional
                </h2>
                <p class="mt-1 text-sm font-light text-white/40">
                    Análisis psicométrico profundo derivado por Cerbero IA.
                </p>
            </div>

            <div v-if="data?.psychometric && !isRetakingDna" class="flex gap-3">
                <StButtonGlass
                    variant="glass"
                    size="sm"
                    class="rounded-xl border-white/20 text-[10px] font-black tracking-widest uppercase hover:bg-white/5"
                    @click="emit('update:isRetakingDna', true)"
                >
                    <PhSparkle :size="14" class="mr-2" />
                    Reevaluar Potencial
                </StButtonGlass>
            </div>
            <div v-else-if="isRetakingDna" class="flex gap-3">
                <StButtonGlass
                    variant="ghost"
                    size="sm"
                    class="rounded-xl text-[10px] font-black tracking-widest uppercase"
                    @click="emit('update:isRetakingDna', false)"
                >
                    <PhArrowLeft :size="14" class="mr-2" />
                    Regresar al Perfil
                </StButtonGlass>
            </div>
        </div>

        <div v-if="data?.psychometric && !isRetakingDna" class="space-y-8">
            <!-- Traits Section -->
            <StCardGlass indicator="purple" class="p-12!">
                <div class="mb-8 flex items-center gap-4">
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-2xl border border-purple-500/20 bg-purple-500/10 text-purple-400"
                    >
                        <PhBrain :size="24" weight="duotone" />
                    </div>
                    <div>
                        <h3 class="text-lg leading-none font-bold text-white">
                            Perfil Psicométrico
                        </h3>
                        <p
                            class="mt-1 text-xs font-black tracking-widest text-white/40 uppercase"
                        >
                            Rasgos de Personalidad predominantes
                        </p>
                    </div>
                </div>

                <div
                    v-if="data.psychometric.traits"
                    class="grid grid-cols-1 gap-8 md:grid-cols-2"
                >
                    <div
                        v-for="trait in data.psychometric.traits"
                        :key="trait.name"
                        class="group rounded-2xl border border-white/5 bg-white/2 p-4 transition-all hover:bg-white/5"
                    >
                        <div class="mb-3 flex items-center justify-between">
                            <span
                                class="font-bold text-white transition-colors group-hover:text-purple-300"
                                >{{ trait.name }}</span
                            >
                            <span
                                class="text-sm font-black"
                                :style="{ color: kpiColor(trait.score * 100) }"
                            >
                                {{ Math.round(trait.score * 100) }}%
                            </span>
                        </div>
                        <div
                            class="mb-3 h-1.5 w-full overflow-hidden rounded-full bg-white/5"
                        >
                            <div
                                class="h-full transition-all duration-1000 ease-out"
                                :style="{
                                    width: `${trait.score * 100}%`,
                                    backgroundColor: kpiColor(
                                        trait.score * 100,
                                    ),
                                }"
                            />
                        </div>
                        <p
                            class="text-[11px] leading-relaxed text-white/40 italic"
                        >
                            "{{ trait.rationale }}"
                        </p>
                    </div>
                </div>
            </StCardGlass>

            <!-- Blind Spots -->
            <StCardGlass
                v-if="data?.psychometric?.blind_spots?.length > 0"
                indicator="rose"
                class="border-rose-500/20 bg-linear-to-br from-rose-500/5 to-transparent p-12! backdrop-blur-xl"
            >
                <div class="mb-6 flex items-center gap-4">
                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-xl border border-rose-500/20 bg-rose-500/10 text-rose-400"
                    >
                        <PhEyeSlash :size="20" weight="duotone" />
                    </div>
                    <h4 class="text-lg font-bold text-rose-100">
                        Blind Spots (Puntos Ciegos)
                    </h4>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div
                        v-for="(spot, i) in data?.psychometric?.blind_spots"
                        :key="i"
                        class="group flex items-start gap-3 rounded-2xl border border-rose-500/10 bg-white/5 p-4 transition-colors hover:border-rose-500/30"
                    >
                        <PhWarningCircle
                            :size="18"
                            weight="fill"
                            class="mt-0.5 shrink-0 text-rose-500"
                        />
                        <p class="text-sm leading-relaxed text-white/70">
                            {{ spot }}
                        </p>
                    </div>
                </div>
            </StCardGlass>
        </div>

        <div
            v-else
            class="overflow-hidden rounded-3xl border border-dashed border-white/10 bg-white/5"
        >
            <AssessmentChat
                v-if="person"
                :person-id="person.id"
                type="psychometric"
                @completed="emit('refresh')"
            />
        </div>
    </div>
</template>
