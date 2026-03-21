<script setup lang="ts">
import { computed, ref } from 'vue';
import { 
    PhAnchor, PhChartBar, PhCube, PhBrain, 
    PhIdentificationCard, PhCheckCircle,
    PhCrown, PhCrosshair, PhNavigationArrow, PhInfo, PhXCircle,
    PhUserCircle, PhRocketLaunch, PhDna
} from '@phosphor-icons/vue';
import StCardGlass from '@/components/StCardGlass.vue';
import StGlowDivider from '@/components/StGlowDivider.vue';
import StButtonGlass from '@/components/StButtonGlass.vue';
import StBadgeGlass from '@/components/StBadgeGlass.vue';

interface Props {
    role: any;
    modelValue: boolean;
}

const props = defineProps<Props>();
const emit = defineEmits(['update:modelValue', 'close', 'edit']);

const close = () => {
    emit('update:modelValue', false);
    emit('close');
};

const currentTab = ref(0);

// --- Computed Data ---
const formattedJustification = computed(() => {
    if (!props.role?.cube?.justification) return [];
    return props.role.cube.justification.split('\n').filter((l: string) => l.trim().length > 0).map((line: string) => {
        const cleanLine = line.trim();
        const isItem = /^[*-]\s|^\d+\.\s/.test(cleanLine);
        const text = isItem ? cleanLine.replace(/^[*-]\s|^\d+\.\s/, '') : cleanLine;
        return { text, isItem };
    });
});

const getStatusColor = (status: string) => {
    switch (status) {
        case 'active': return 'success';
        case 'approved': return 'primary';
        case 'pending_signature': return 'warning';
        case 'pending_design': return 'sky';
        default: return 'glass';
    }
};

const getStatusText = (status: string) => {
    switch (status) {
        case 'active': return 'Activo (Vigente)';
        case 'approved': return 'Aprobado';
        case 'pending_signature': return 'Por Firmar';
        case 'pending_design': return 'Diseño Pendiente';
        default: return (status || '').replace('_', ' ').toUpperCase();
    }
};

const computedCompetencies = computed(() => {
    let result: any[] = [];
    const item = props.role;

    // 1. Try direct competencies relation
    if (item?.competencies && item.competencies.length > 0) {
        result = item.competencies.map((comp: any) => ({
            id: comp.id,
            name: comp.name,
            level: comp.pivot?.required_level || 3,
            rationale: comp.pivot?.rationale || comp.description,
        }));
    } 
    // 2. Fallback: Extract from skills
    else if (item?.skills && item.skills.length > 0) {
        result = item.skills.map((skill: any) => ({
            id: skill.id,
            name: skill.name,
            level: skill.pivot?.required_level || skill.level || 3,
            rationale: skill.pivot?.rationale || skill.rationale,
        }));
    }
    // 3. Fallback: Check AI suggested competencies in config
    else if (item?.ai_archetype_config?.core_competencies && item.ai_archetype_config.core_competencies.length > 0) {
        result = item.ai_archetype_config.core_competencies.map((comp: any, index: number) => ({
            id: `ai-${index}`,
            name: comp.name,
            level: comp.level || 3,
            rationale: comp.rationale,
        }));
    }

    return result;
});

</script>

<template>
    <v-dialog 
        :model-value="modelValue" 
        @update:model-value="emit('update:modelValue', $event)"
        max-width="1200"
        persistent
        scrollable
    >
        <div class="relative overflow-hidden rounded-[2.5rem] border border-white/10 bg-black/60 backdrop-blur-3xl shadow-2xl">
            <!-- Header Glow -->
            <div class="absolute -top-40 left-1/2 h-80 w-full -translate-x-1/2 bg-indigo-500/10 blur-[120px]"></div>

            <div class="relative flex h-[85vh] flex-col">
                <!-- Toolbar -->
                <header class="flex flex-col border-b border-white/5 bg-black/20 backdrop-blur-md">
                    <div class="flex items-center justify-between px-10 py-6">
                        <div class="flex items-center gap-6">
                            <div class="h-12 w-12 rounded-xl bg-indigo-500/10 flex items-center justify-center border border-indigo-500/20 shadow-glow-sm">
                                <PhCube :size="24" class="text-indigo-400" weight="duotone" />
                            </div>
                            <div>
                                <div class="flex items-center gap-3">
                                    <h1 class="text-2xl font-black text-white italic tracking-tighter uppercase">{{ role.name }}</h1>
                                    <StBadgeGlass :variant="getStatusColor(role.status)" size="sm">
                                        {{ getStatusText(role.status) }}
                                    </StBadgeGlass>
                                </div>
                                <div class="text-[9px] font-black text-white/30 tracking-[0.2em] uppercase mt-0.5">
                                    Documento Maestro de Arquitectura Organizacional
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-4">
                            <StButtonGlass
                                variant="ghost"
                                circle
                                size="sm"
                                :icon="PhXCircle"
                                @click="close"
                                class="text-white/30!"
                            />
                        </div>
                    </div>

                    <!-- Navigation Tabs -->
                    <div class="flex px-10 gap-8 border-t border-white/5">
                        <button 
                            v-for="(label, idx) in ['Arquitectura Estratégica', 'Modelo de Talento', 'Estructura de Ocupación']" 
                            :key="idx"
                            @click="currentTab = idx"
                            class="relative py-4 text-[10px] font-black tracking-widest uppercase transition-colors"
                            :class="currentTab === idx ? 'text-indigo-400' : 'text-white/30 hover:text-white/50'"
                        >
                            {{ label }}
                            <div v-if="currentTab === idx" class="absolute bottom-0 left-0 right-0 h-0.5 bg-indigo-500 shadow-glow"></div>
                        </button>
                    </div>
                </header>

                <main class="flex-1 overflow-y-auto px-10 py-12 custom-scrollbar">
                    <transition name="fade-slide" mode="out-in">
                        <!-- Tab 1: Architecture -->
                        <div v-if="currentTab === 0" :key="0" class="mx-auto max-w-6xl space-y-16">
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                                <StCardGlass class="p-10! bg-indigo-500/2! border-indigo-500/10 shadow-glow-sm">
                                    <div class="flex items-center gap-4 mb-6">
                                        <div class="h-10 w-10 rounded-xl bg-indigo-500/10 flex items-center justify-center border border-indigo-500/20">
                                            <PhAnchor :size="20" class="text-indigo-400" weight="duotone" />
                                        </div>
                                        <h3 class="text-xs font-black tracking-widest text-white/40 uppercase">Propósito del Nodo</h3>
                                    </div>
                                    <p class="text-xl leading-relaxed text-white italic-quote font-medium">
                                        "{{ role.purpose || 'Sin definir' }}"
                                    </p>
                                </StCardGlass>

                                <StCardGlass class="p-10! bg-emerald-500/2! border-emerald-500/10">
                                    <div class="flex items-center gap-4 mb-6">
                                        <div class="h-10 w-10 rounded-xl bg-emerald-500/10 flex items-center justify-center border border-emerald-500/20">
                                            <PhChartBar :size="20" class="text-emerald-400" weight="duotone" />
                                        </div>
                                        <h3 class="text-xs font-black tracking-widest text-white/40 uppercase">Resultados de Valor</h3>
                                    </div>
                                    <div class="space-y-4">
                                        <div v-for="(line, idx) in (role.expected_results || '').split('\n')" :key="idx" class="flex gap-4 text-sm text-white/70">
                                            <span class="text-emerald-500/40 mt-1 font-black text-[8px] uppercase">OKR</span>
                                            <span class="leading-relaxed">{{ line }}</span>
                                        </div>
                                    </div>
                                </StCardGlass>
                            </div>

                            <section class="space-y-10">
                                <div class="flex items-center gap-3">
                                    <PhRocketLaunch :size="20" class="text-indigo-400" weight="duotone" />
                                    <h3 class="text-[11px] font-black tracking-[0.3em] text-white/20 uppercase italic">Matriz de Posicionamiento</h3>
                                </div>
                                
                                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                                    <!-- X Axis -->
                                    <div class="p-8 rounded-4xl border border-white/5 bg-white/2 hover:border-indigo-500/30 transition-all">
                                        <div class="text-[9px] font-black text-white/20 uppercase tracking-[0.2em] mb-4">Dimensión Arquetipal (Eje X)</div>
                                        <div class="flex items-center gap-5">
                                            <div class="h-12 w-12 rounded-2xl bg-white/5 border border-indigo-500/30 flex items-center justify-center text-indigo-300 shadow-glow-sm">
                                                <component 
                                                    :is="role.cube?.x_archetype === 'Strategic' ? PhCrown : role.cube?.x_archetype === 'Tactical' ? PhCrosshair : PhNavigationArrow" 
                                                    :size="24" 
                                                    weight="duotone" 
                                                />
                                            </div>
                                            <div>
                                                <div class="text-2xl font-black text-white italic uppercase tracking-tighter">{{ role.cube?.x_archetype || 'N/A' }}</div>
                                                <div class="text-[10px] font-bold text-indigo-400/50 uppercase">Horizonte Temporal</div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Y Axis -->
                                    <div class="p-8 rounded-4xl border border-white/5 bg-white/2 hover:border-amber-500/30 transition-all">
                                        <div class="text-[9px] font-black text-white/20 uppercase tracking-[0.2em] mb-4">Grado de Maestría (Eje Y)</div>
                                        <div class="flex items-center gap-5">
                                            <div class="h-12 w-12 rounded-2xl bg-white/5 border border-amber-500/30 flex items-center justify-center text-amber-300">
                                                <span class="text-xl font-black font-mono">T{{ role.cube?.y_mastery_level || 1 }}</span>
                                            </div>
                                            <div>
                                                <div class="text-2xl font-black text-white italic uppercase tracking-tighter">Tier {{ role.cube?.y_mastery_level || 1 }}</div>
                                                <div class="text-[10px] font-bold text-amber-400/50 uppercase">Complejidad Técnica</div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Z Axis -->
                                    <div class="p-8 rounded-4xl border border-white/5 bg-white/2 hover:border-emerald-500/30 transition-all">
                                        <div class="text-[9px] font-black text-white/20 uppercase tracking-[0.2em] mb-4">Anclaje de Proceso (Eje Z)</div>
                                        <div class="flex items-center gap-5">
                                            <div class="h-12 w-12 rounded-2xl bg-white/5 border border-emerald-500/30 flex items-center justify-center text-emerald-300">
                                                <PhAnchor :size="24" weight="duotone" />
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="text-2xl font-black text-white italic uppercase truncate tracking-tighter">{{ role.cube?.z_business_process || 'Sin Proceso' }}</div>
                                                <div class="text-[10px] font-bold text-emerald-400/50 uppercase">Zona de Influencia</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="p-10 rounded-4xl border border-white/5 bg-white/2" v-if="formattedJustification.length > 0">
                                    <div class="flex items-center gap-3 mb-6">
                                        <PhBrain :size="20" class="text-indigo-400" weight="duotone" />
                                        <h4 class="text-[10px] font-black text-indigo-400 uppercase tracking-widest">Racional de Arquitectura AI</h4>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-6">
                                        <div v-for="(line, idx) in formattedJustification" :key="idx" class="flex items-start gap-4">
                                            <PhCheckCircle v-if="line.isItem" :size="16" weight="fill" class="text-indigo-500/20 mt-1" />
                                            <p :class="line.isItem ? 'text-sm text-white/90 leading-relaxed font-medium' : 'text-[11px] text-white/30 italic uppercase tracking-widest mt-6 border-t border-white/5 pt-4 w-full'">{{ line.text }}</p>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </div>

                        <!-- Tab 2: Talent Model -->
                        <div v-else-if="currentTab === 1" :key="1" class="mx-auto max-w-6xl space-y-20">
                            <!-- BARS Section -->
                            <section class="space-y-12">
                                <div class="flex items-center gap-3">
                                    <PhIdentificationCard :size="24" class="text-indigo-400" weight="duotone" />
                                    <h3 class="text-xl font-black text-white italic uppercase tracking-tighter">Protocolo de Desempeño (BARS)</h3>
                                </div>

                                <div v-if="role.bars" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                                    <div v-for="(val, key) in { behavior: 'Conducta', attitude: 'Actitud', responsibility: 'Responsabilidad', skill: 'Habilidades' }" :key="key" 
                                        class="p-8 rounded-4xl bg-indigo-500/2 border border-white/5 hover:bg-indigo-500/5 transition-all">
                                        <div class="text-[10px] font-black text-indigo-400/70 uppercase tracking-widest mb-6">{{ val }}</div>
                                        <p class="text-xs leading-relaxed text-white/90 font-medium italic-quote">
                                            {{ (role.bars as any)[key] }}
                                        </p>
                                    </div>
                                </div>
                                <div v-else class="py-16 text-center rounded-[2.5rem] border border-dashed border-white/5 bg-white/1">
                                    <PhIdentificationCard :size="48" class="mx-auto text-white/5 mb-4" weight="thin" />
                                    <p class="text-xs font-bold text-white/20 uppercase tracking-widest italic">Protocolo BARS no generado aún para este rol</p>
                                </div>
                            </section>

                            <StGlowDivider />

                            <!-- DNA Section -->
                            <section class="space-y-12">
                                <div class="flex items-center gap-3">
                                    <PhDna :size="24" class="text-emerald-400" weight="duotone" />
                                    <h3 class="text-xl font-black text-white italic uppercase tracking-tighter">Capacidades Estratégicas (DNA)</h3>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                    <div v-for="skill in computedCompetencies" :key="skill.id" 
                                        class="group p-8 rounded-[2.5rem] border border-white/5 bg-white/2 hover:bg-indigo-500/2 transition-all">
                                        <div class="flex items-center gap-4 mb-6">
                                            <div class="h-10 w-10 rounded-xl bg-indigo-500/10 flex items-center justify-center border border-indigo-500/20 text-indigo-400 shadow-glow-sm">
                                                <PhBrain :size="20" weight="duotone" />
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="text-sm font-black text-white italic tracking-tight uppercase truncate">{{ skill.name }}</div>
                                                <div class="text-[9px] font-bold text-white/20 uppercase tracking-widest">Maestría Requerida: {{ skill.level }}/5</div>
                                            </div>
                                        </div>
                                        
                                        <div class="grid grid-cols-5 gap-2 pt-2">
                                            <div v-for="l in 5" :key="l" class="h-1 rounded-full overflow-hidden bg-white/5">
                                                <div v-if="l <= skill.level" class="h-full bg-indigo-500 shadow-glow-sm"></div>
                                            </div>
                                        </div>
                                        
                                        <p class="mt-8 text-xs leading-relaxed text-white/40 italic font-medium line-clamp-3 group-hover:line-clamp-none transition-all duration-500">
                                            {{ skill.rationale || 'Sin justificación disponible para esta capacidad.' }}
                                        </p>
                                    </div>
                                </div>
                                <div v-if="computedCompetencies.length === 0" class="py-16 text-center rounded-[2.5rem] border border-dashed border-white/5 bg-white/1">
                                    <PhBrain :size="48" class="mx-auto text-white/5 mb-4" weight="thin" />
                                    <p class="text-xs font-bold text-white/20 uppercase tracking-widest italic">No se han definido capacidades para este rol</p>
                                </div>
                            </section>
                        </div>

                        <!-- Tab 3: Structure -->
                        <div v-else-if="currentTab === 2" :key="2" class="mx-auto max-w-6xl space-y-12 pb-20">
                            <div class="flex items-center gap-3">
                                <PhUserCircle :size="24" class="text-indigo-400" weight="duotone" />
                                <h3 class="text-xl font-black text-white italic uppercase tracking-tighter">Estructura de Ocupación</h3>
                            </div>

                            <div v-if="role.people && role.people.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                                <div v-for="person in role.people" :key="person.id" 
                                    class="p-8 rounded-4xl border border-white/5 bg-white/2 flex items-center gap-6 hover:bg-white/4 transition-all">
                                    <v-avatar color="indigo-accent-1" size="64" class="border border-indigo-500/30">
                                        <span class="text-xl font-black">{{ person.name?.[0] || '?' }}</span>
                                    </v-avatar>
                                    <div class="min-w-0">
                                        <div class="text-base font-black text-white uppercase italic truncate tracking-tight">{{ person.name }}</div>
                                        <div class="text-xs font-bold text-white/20 truncate mb-3">{{ person.email || 'Sin correo' }}</div>
                                        <div class="inline-flex items-center gap-2 rounded-lg bg-emerald-500/10 border border-emerald-500/20 px-3 py-1">
                                            <div class="h-1 w-1 rounded-full bg-emerald-400"></div>
                                            <span class="text-[8px] font-black text-emerald-400 uppercase tracking-widest">Identity Confirmed</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="py-24 text-center rounded-[3rem] border border-dashed border-white/10 opacity-40">
                                <PhUserCircle :size="64" class="mx-auto text-white/5 mb-6" weight="thin" />
                                <p class="text-sm font-black text-white uppercase tracking-[0.3em]">Sin personas asignadas actualmente</p>
                            </div>
                        </div>
                    </transition>
                </main>

                <!-- Footer -->
                <footer class="flex items-center justify-end border-t border-white/5 px-10 py-6 backdrop-blur-md">
                    <div class="flex items-center gap-4">
                        <StButtonGlass
                            variant="ghost"
                            :icon="PhInfo"
                            @click="close"
                        >Finalizar Consulta</StButtonGlass>
                        
                        <StButtonGlass
                            v-if="['draft', 'pending_design', 'pending_review'].includes(role.status)"
                            variant="primary"
                            :icon="PhRocketLaunch"
                            @click="emit('edit', role)"
                            class="px-12!"
                        >Continuar Arquitectura</StButtonGlass>
                    </div>
                </footer>
            </div>
        </div>
    </v-dialog>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.1);
}

.shadow-glow {
    box-shadow: 0 0 20px rgba(99, 102, 241, 0.3);
}
.shadow-glow-sm {
    box-shadow: 0 0 15px rgba(99, 102, 241, 0.15);
}

.italic-quote {
    position: relative;
    padding-left: 1rem;
}
.italic-quote::before {
    content: '"';
    position: absolute;
    left: 0;
    top: -5px;
    font-size: 32px;
    color: rgba(99, 102, 241, 0.2);
    font-family: serif;
    font-weight: bold;
}

/* Animations */
.fade-slide-enter-active, .fade-slide-leave-active {
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}
.fade-slide-enter-from {
  opacity: 0;
  transform: translateX(10px);
}
.fade-slide-leave-to {
  opacity: 0;
  transform: translateX(-10px);
}
</style>
