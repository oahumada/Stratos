<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import {
    PhScroll,
    PhShieldCheck,
    PhSignature,
    PhRobot,
    PhCheckCircle,
    PhPlus,
    PhTrash,
    PhFloppyDisk,
    PhIdentificationCard,
    PhChartPieSlice
} from '@phosphor-icons/vue';
import { onMounted, ref } from 'vue';
import { useI18n } from 'vue-i18n';
import axios from 'axios';
import StButtonGlass from '@/components/StButtonGlass.vue';
import StBadgeGlass from '@/components/StBadgeGlass.vue';

const { t } = useI18n();

interface Blueprint {
    id?: number;
    mission: string;
    vision: string;
    values: string[];
    principles: string[];
    digital_signature: string | null;
    signed_at: string | null;
}

const blueprint = ref<Blueprint>({
    mission: '',
    vision: '',
    values: [],
    principles: [],
    digital_signature: null,
    signed_at: null,
});

const loading = ref(true);
const saving = ref(false);
const signing = ref(false);
const isVerified = ref(false);

const newValue = ref('');
const newPrinciple = ref('');

const loadBlueprint = async () => {
    loading.value = true;
    try {
        const { data } = await axios.get('/api/organization/cultural-blueprint');
        blueprint.value = data.data;
        isVerified.value = data.is_verified;
    } catch (e) {
        console.error('Failed to load blueprint', e);
    } finally {
        loading.value = false;
    }
};

const saveBlueprint = async () => {
    saving.value = true;
    try {
        await axios.post('/api/organization/cultural-blueprint', blueprint.value);
        // Show notification if available
    } catch (e) {
        console.error('Failed to save blueprint', e);
    } finally {
        saving.value = false;
    }
};

const signBlueprint = async () => {
    signing.value = true;
    try {
        const { data } = await axios.post('/api/organization/cultural-blueprint/sign');
        blueprint.value = data.data;
        isVerified.value = true;
    } catch (e) {
        console.error('Failed to sign blueprint', e);
    } finally {
        signing.value = false;
    }
};

const goToAnalytics = () => {
    console.log('Botón presionado: navegando a culture-analytics');
    router.visit('/controlcenter/culture-analytics');
};

const addItem = (type: 'values' | 'principles') => {
    const val = type === 'values' ? newValue.value : newPrinciple.value;
    if (!val.trim()) return;
    
    if (!blueprint.value[type]) blueprint.value[type] = [];
    blueprint.value[type].push(val.trim());
    
    if (type === 'values') newValue.value = '';
    else newPrinciple.value = '';
};

const removeItem = (type: 'values' | 'principles', index: number) => {
    blueprint.value[type].splice(index, 1);
};

onMounted(() => {
    loadBlueprint();
});
</script>

<template>
    <Head :title="t('culture.blueprint.title')" />
        <div class="pa-6 max-w-6xl mx-auto">
            <!-- Header Section -->
            <div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <div class="flex items-center gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl border border-fuchsia-500/30 bg-fuchsia-500/10 text-fuchsia-400">
                            <PhScroll :size="32" weight="duotone" />
                        </div>
                        <h1 class="text-4xl font-black tracking-tighter text-white">
                            {{ t('culture.blueprint.title') }}
                        </h1>
                    </div>
                    <p class="mt-2 text-lg font-medium text-white/40">
                        {{ t('culture.blueprint.subtitle') }}
                    </p>
                </div>

                <div class="flex gap-4">
                    <StButtonGlass
                        :icon="PhFloppyDisk"
                        :loading="saving"
                        @click="saveBlueprint"
                    >
                        {{ t('culture.blueprint.save') }}
                    </StButtonGlass>
                    <Link :href="'/controlcenter/culture-analytics'">
                        <v-btn
                            variant="flat"
                            color="cyan-accent-4"
                            prepend-icon="mdi-view-dashboard"
                            class="font-bold rounded-xl"
                            style="color: white !important;"
                        >
                            {{ t('culture.blueprint.dashboard') || 'Identity Analytics' }}
                        </v-btn>
                    </Link>
                    <StButtonGlass
                        variant="primary"
                        :icon="PhSignature"
                        :loading="signing"
                        @click="signBlueprint"
                        class="shadow-xl shadow-fuchsia-500/20"
                    >
                        {{ t('culture.blueprint.sign') }}
                    </StButtonGlass>
                </div>
            </div>

            <div v-if="loading" class="flex justify-center py-20">
                <div class="h-12 w-12 animate-spin rounded-full border-4 border-fuchsia-500/20 border-t-fuchsia-500" />
            </div>

            <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column: Core Definition -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Mission & Vision -->
                    <div class="glass-card rounded-[2.5rem] border border-white/10 bg-slate-900/40 p-8 backdrop-blur-3xl">
                        <div class="space-y-8">
                            <div>
                                <label for="mission" class="block text-xs font-black tracking-widest text-fuchsia-400 uppercase mb-4">
                                    {{ t('culture.blueprint.mission') }}
                                </label>
                                <textarea
                                    id="mission"
                                    v-model="blueprint.mission"
                                    rows="4"
                                    class="w-full rounded-2xl border border-white/10 bg-white/5 p-4 text-white focus:border-fuchsia-500/50 focus:outline-none transition-all"
                                    placeholder="Define el propósito superior de la organización..."
                                ></textarea>
                            </div>
                            
                            <div>
                                <label for="vision" class="block text-xs font-black tracking-widest text-indigo-400 uppercase mb-4">
                                    {{ t('culture.blueprint.vision') }}
                                </label>
                                <textarea
                                    id="vision"
                                    v-model="blueprint.vision"
                                    rows="4"
                                    class="w-full rounded-2xl border border-white/10 bg-white/5 p-4 text-white focus:border-indigo-500/50 focus:outline-none transition-all"
                                    placeholder="¿Hacia dónde se dirige la organización en los próximos años?"
                                ></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Values & Principles -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Values -->
                        <div class="glass-card rounded-[2.5rem] border border-white/10 bg-slate-900/40 p-8 backdrop-blur-3xl">
                            <label for="new-value" class="block text-xs font-black tracking-widest text-emerald-400 uppercase mb-6">
                                {{ t('culture.blueprint.values') }}
                            </label>
                            
                            <div class="flex gap-2 mb-6">
                                <input
                                    id="new-value"
                                    v-model="newValue"
                                    type="text"
                                    class="flex-1 rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm text-white focus:border-emerald-500/50 focus:outline-none"
                                    placeholder="Nuevo valor..."
                                    @keyup.enter="addItem('values')"
                                />
                                <StButtonGlass circle size="sm" :icon="PhPlus" @click="addItem('values')" />
                            </div>

                            <div class="space-y-3">
                                <div
                                    v-for="(val, i) in blueprint.values"
                                    :key="i"
                                    class="flex items-center justify-between group rounded-xl bg-white/5 px-4 py-3 border border-white/5"
                                >
                                    <span class="text-sm font-bold text-white/80">{{ val }}</span>
                                    <button @click="removeItem('values', i)" class="text-white/20 hover:text-red-400 transition-colors">
                                        <PhTrash :size="16" />
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Principles -->
                        <div class="glass-card rounded-[2.5rem] border border-white/10 bg-slate-900/40 p-8 backdrop-blur-3xl">
                            <label for="new-principle" class="block text-xs font-black tracking-widest text-amber-400 uppercase mb-6">
                                {{ t('culture.blueprint.principles') }}
                            </label>
                            
                            <div class="flex gap-2 mb-6">
                                <input
                                    id="new-principle"
                                    v-model="newPrinciple"
                                    type="text"
                                    class="flex-1 rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm text-white focus:border-amber-500/50 focus:outline-none"
                                    placeholder="Principio ético..."
                                    @keyup.enter="addItem('principles')"
                                />
                                <StButtonGlass circle size="sm" :icon="PhPlus" @click="addItem('principles')" />
                            </div>

                            <div class="space-y-3">
                                <div
                                    v-for="(p, i) in blueprint.principles"
                                    :key="i"
                                    class="flex items-center justify-between group rounded-xl bg-white/5 px-4 py-3 border border-white/5"
                                >
                                    <span class="text-sm font-bold text-white/80">{{ p }}</span>
                                    <button @click="removeItem('principles', i)" class="text-white/20 hover:text-red-400 transition-colors">
                                        <PhTrash :size="16" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Security & AI Integrity -->
                <div class="space-y-8">
                    <!-- AI Guardian Status -->
                    <div class="glass-card rounded-[2.5rem] border border-white/10 bg-fuchsia-500/5 p-8 backdrop-blur-3xl relative overflow-hidden">
                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-8">
                                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-fuchsia-500/20 text-fuchsia-400">
                                    <PhRobot :size="24" weight="bold" />
                                </div>
                                <StBadgeGlass variant="success" size="sm" v-if="isVerified">
                                    {{ t('culture.blueprint.guardian_active') }}
                                </StBadgeGlass>
                            </div>
                            
                            <h3 class="text-xl font-black text-white mb-2">
                                {{ t('culture.blueprint.guardian_status') }}
                            </h3>
                            <p class="text-sm text-white/40 leading-relaxed">
                                {{ t('culture.blueprint.guardian_desc') }}
                            </p>
                        </div>
                        <!-- Abstract glow -->
                        <div class="absolute -right-10 -bottom-10 h-32 w-32 bg-fuchsia-500/10 blur-3xl"></div>
                    </div>

                    <!-- Digital Seal -->
                    <div class="glass-card rounded-[2.5rem] border border-white/10 bg-slate-900/40 p-8 backdrop-blur-3xl">
                        <div class="flex items-center gap-3 mb-6">
                            <PhShieldCheck :size="24" class="text-indigo-400" />
                            <h3 class="text-sm font-black tracking-widest text-white/60 uppercase">
                                {{ t('culture.blueprint.seal') }}
                            </h3>
                        </div>

                        <div v-if="blueprint.digital_signature" class="space-y-6">
                            <div class="rounded-2xl border border-indigo-500/20 bg-indigo-500/5 p-4">
                                <div class="flex items-center gap-2 text-indigo-400 mb-2">
                                    <PhCheckCircle :size="18" weight="bold" />
                                    <span class="text-xs font-black uppercase tracking-tighter">Autenticidad Verificada</span>
                                </div>
                                <p class="text-[10px] font-mono break-all text-white/30 uppercase">
                                    {{ blueprint.digital_signature }}
                                </p>
                            </div>
                            
                            <div class="flex items-center gap-2 text-[10px] text-white/40">
                                <PhIdentificationCard :size="14" />
                                <span>Firmado el: {{ blueprint.signed_at }}</span>
                            </div>
                        </div>

                        <div v-else class="text-center py-6">
                            <v-icon icon="mdi-shield-off-outline" size="48" class="text-white/10 mb-4"></v-icon>
                            <p class="text-sm text-white/40">
                                El blueprint no ha sido sellado. La IA operará con parámetros genéricos.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</template>

<style scoped>
.glass-card {
    transition: all 0.3s ease;
}

textarea {
    resize: none;
}
</style>
