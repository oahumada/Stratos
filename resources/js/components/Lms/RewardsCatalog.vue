<script setup lang="ts">
import StBadgeGlass from '@/components/StBadgeGlass.vue';
import StCardGlass from '@/components/StCardGlass.vue';
import axios from 'axios';
import {
    CheckCircle2,
    Gift,
    History,
    Loader2,
    Sparkles,
    X,
} from 'lucide-vue-next';
import { onMounted, ref } from 'vue';

interface Reward {
    id: number;
    title: string;
    description: string;
    points_cost: number;
    category: string;
    stock: number;
    image_url: string | null;
}

interface Redemption {
    id: number;
    reward_title: string;
    points_spent: number;
    status: string;
    created_at: string;
}

const props = defineProps<{
    peopleId: number;
    currentPoints: number;
}>();

const emit = defineEmits(['close', 'redeemed']);

const rewards = ref<Reward[]>([]);
const history = ref<Redemption[]>([]);
const loading = ref(true);
const activeTab = ref('catalog');
const processingRedemption = ref<number | null>(null);

const fetchData = async () => {
    loading.value = true;
    try {
        const [rewardsRes, historyRes] = await Promise.all([
            axios.get('/api/gamification/rewards'),
            axios.get(`/api/gamification/people/${props.peopleId}/history`),
        ]);

        if (rewardsRes.data.success) rewards.value = rewardsRes.data.data;
        if (historyRes.data.success) history.value = historyRes.data.data;
    } catch (error) {
        console.error('Error fetching rewards data:', error);
    } finally {
        loading.value = false;
    }
};

const handleRedeem = async (reward: Reward) => {
    if (props.currentPoints < reward.points_cost) return;

    processingRedemption.value = reward.id;
    try {
        const response = await axios.post(
            `/api/gamification/people/${props.peopleId}/redeem`,
            {
                reward_id: reward.id,
            },
        );

        if (response.data.success) {
            emit('redeemed', reward.points_cost);
            fetchData();
        }
    } catch (error: any) {
        alert(error.response?.data?.message || 'Error al procesar el canje');
    } finally {
        processingRedemption.value = null;
    }
};

onMounted(fetchData);
</script>

<template>
    <div
        class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-950/80 p-4 backdrop-blur-sm"
    >
        <div
            class="flex h-[85vh] w-full max-w-5xl flex-col overflow-hidden rounded-3xl border border-white/10 bg-slate-900 shadow-2xl"
        >
            <!-- Header -->
            <div
                class="flex items-center justify-between border-b border-white/10 bg-white/[0.02] p-6"
            >
                <div class="flex items-center gap-4">
                    <div class="rounded-2xl bg-primary/20 p-3 text-primary">
                        <Gift :size="24" />
                    </div>
                    <div>
                        <h2 class="text-2xl leading-tight font-bold text-white">
                            Catálogo de Beneficios
                        </h2>
                        <div
                            class="flex items-center gap-2 text-sm text-slate-400"
                        >
                            <span
                                >Tienes
                                <span class="font-bold text-yellow-500">{{
                                    currentPoints
                                }}</span>
                                Stratos Credits disponibles</span
                            >
                        </div>
                    </div>
                </div>
                <button
                    @click="emit('close')"
                    class="rounded-full p-2 transition-colors hover:bg-white/10"
                >
                    <X class="text-slate-400" :size="24" />
                </button>
            </div>

            <!-- Tabs -->
            <div class="flex gap-8 border-b border-white/10 px-6 pt-4">
                <button
                    @click="activeTab = 'catalog'"
                    class="relative pb-4 text-sm font-semibold transition-all"
                    :class="
                        activeTab === 'catalog'
                            ? 'text-primary'
                            : 'text-slate-500 hover:text-slate-300'
                    "
                >
                    Explorar Premios
                    <div
                        v-if="activeTab === 'catalog'"
                        class="absolute right-0 bottom-0 left-0 h-0.5 rounded-full bg-primary"
                    ></div>
                </button>
                <button
                    @click="activeTab = 'history'"
                    class="relative pb-4 text-sm font-semibold transition-all"
                    :class="
                        activeTab === 'history'
                            ? 'text-primary'
                            : 'text-slate-500 hover:text-slate-300'
                    "
                >
                    Mi Historial
                    <div
                        v-if="activeTab === 'history'"
                        class="absolute right-0 bottom-0 left-0 h-0.5 rounded-full bg-primary"
                    ></div>
                </button>
            </div>

            <!-- Content -->
            <div class="custom-scrollbar flex-1 overflow-y-auto p-8">
                <div
                    v-if="loading"
                    class="flex h-full flex-col items-center justify-center gap-4"
                >
                    <Loader2 class="animate-spin text-primary" :size="48" />
                    <p class="text-slate-400">
                        Cargando beneficios exclusivos...
                    </p>
                </div>

                <div
                    v-else-if="activeTab === 'catalog'"
                    class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3"
                >
                    <StCardGlass
                        v-for="reward in rewards"
                        :key="reward.id"
                        class="group flex h-full flex-col border-white/5 transition-all hover:border-primary/30"
                    >
                        <div
                            class="relative mb-4 aspect-video w-full overflow-hidden rounded-2xl bg-white/5"
                        >
                            <img
                                v-if="reward.image_url"
                                :src="reward.image_url"
                                :alt="reward.title"
                                class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                            />
                            <div
                                v-else
                                class="flex h-full w-full items-center justify-center"
                            >
                                <Sparkles class="text-white/10" :size="48" />
                            </div>
                            <div class="absolute top-3 right-3">
                                <StBadgeGlass
                                    variant="glass"
                                    class="bg-black/40 backdrop-blur-md"
                                >
                                    {{ reward.category }}
                                </StBadgeGlass>
                            </div>
                        </div>

                        <h3 class="mb-2 text-lg font-bold text-white">
                            {{ reward.title }}
                        </h3>
                        <p
                            class="mb-6 line-clamp-2 flex-1 text-sm text-slate-400"
                        >
                            {{ reward.description }}
                        </p>

                        <div class="mt-auto flex items-center justify-between">
                            <div class="flex flex-col">
                                <span
                                    class="text-2xl font-black text-yellow-500"
                                    >{{ reward.points_cost }}</span
                                >
                                <span
                                    class="text-[10px] font-bold tracking-widest text-slate-500 uppercase"
                                    >Credits</span
                                >
                            </div>

                            <button
                                @click="handleRedeem(reward)"
                                :disabled="
                                    currentPoints < reward.points_cost ||
                                    processingRedemption === reward.id
                                "
                                class="rounded-xl px-6 py-2.5 text-sm font-bold transition-all disabled:cursor-not-allowed disabled:opacity-50"
                                :class="
                                    currentPoints >= reward.points_cost
                                        ? 'bg-primary text-white shadow-lg shadow-primary/20 hover:bg-primary/80'
                                        : 'border border-white/10 bg-white/5 text-slate-500'
                                "
                            >
                                <Loader2
                                    v-if="processingRedemption === reward.id"
                                    class="animate-spin"
                                    :size="18"
                                />
                                <span v-else>{{
                                    currentPoints >= reward.points_cost
                                        ? 'Canjear Ahora'
                                        : 'Faltan Créditos'
                                }}</span>
                            </button>
                        </div>
                    </StCardGlass>
                </div>

                <div
                    v-else-if="activeTab === 'history'"
                    class="mx-auto max-w-3xl"
                >
                    <div v-if="history.length === 0" class="py-20 text-center">
                        <History
                            class="mx-auto mb-4 text-slate-700"
                            :size="64"
                        />
                        <h4 class="text-xl font-bold text-slate-300">
                            Aún no has canjeado premios
                        </h4>
                        <p class="text-slate-500">
                            ¡Sigue acumulando puntos para obtener beneficios
                            exclusivos!
                        </p>
                    </div>

                    <div v-else class="space-y-3">
                        <div
                            v-for="item in history"
                            :key="item.id"
                            class="flex items-center justify-between rounded-2xl border border-white/5 bg-white/[0.03] p-4"
                        >
                            <div class="flex items-center gap-4">
                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-500/10 text-emerald-500"
                                >
                                    <CheckCircle2 :size="20" />
                                </div>
                                <div>
                                    <p class="font-bold text-white">
                                        {{ item.reward_title }}
                                    </p>
                                    <p class="text-xs text-slate-500">
                                        {{
                                            new Date(
                                                item.created_at,
                                            ).toLocaleDateString()
                                        }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex flex-col items-end">
                                <span class="text-sm font-bold text-yellow-500"
                                    >-{{ item.points_spent }} pts</span
                                >
                                <StBadgeGlass
                                    :variant="
                                        item.status === 'completed'
                                            ? 'success'
                                            : item.status === 'pending'
                                              ? 'warning'
                                              : 'error'
                                    "
                                    size="sm"
                                    class="mt-1"
                                >
                                    {{ item.status }}
                                </StBadgeGlass>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.2);
}
</style>
