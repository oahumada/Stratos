<script setup lang="ts">
import {
    PhBell,
    PhCheckCircle,
    PhGear,
    PhInfo,
    PhLightning,
    PhProjectorScreenChart,
    PhStudent,
} from '@phosphor-icons/vue';
import axios from 'axios';
import { onMounted, onUnmounted, ref } from 'vue';

interface SmartAlert {
    id: number;
    level: 'info' | 'success' | 'warning' | 'danger';
    category: 'talent' | 'scenario' | 'learning' | 'infrastructure' | 'system';
    title: string;
    message: string;
    action_link?: { text: string; url: string };
    created_at: string;
}

const alerts = ref<SmartAlert[]>([]);
const showDropdown = ref(false);
const loading = ref(false);
let refreshInterval: any = null;

const fetchAlerts = async () => {
    try {
        const response = await axios.get('/api/smart-alerts');
        if (response.data.success) {
            alerts.value = response.data.data;
        }
    } catch (e) {
        console.error('Error fetching alerts', e);
    }
};

const markAsRead = async (id: number) => {
    try {
        await axios.post(`/api/smart-alerts/${id}/read`);
        alerts.value = alerts.value.filter((a) => a.id !== id);
    } catch (e) {
        console.error('Error marking alert as read', e);
    }
};

const getCategoryIcon = (category: string) => {
    switch (category) {
        case 'talent':
            return PhLightning;
        case 'scenario':
            return PhProjectorScreenChart;
        case 'learning':
            return PhStudent;
        case 'infrastructure':
            return PhGear;
        default:
            return PhInfo;
    }
};

const getLevelColor = (level: string) => {
    switch (level) {
        case 'success':
            return 'text-emerald-400 bg-emerald-500/10 border-emerald-500/20';
        case 'warning':
            return 'text-amber-400 bg-amber-500/10 border-amber-500/20';
        case 'danger':
            return 'text-rose-400 bg-rose-500/10 border-rose-500/20';
        default:
            return 'text-sky-400 bg-sky-500/10 border-sky-500/20';
    }
};

onMounted(() => {
    fetchAlerts();
    refreshInterval = setInterval(fetchAlerts, 60000); // Polling cada minuto
});

onUnmounted(() => {
    if (refreshInterval) clearInterval(refreshInterval);
});
</script>

<template>
    <div class="relative">
        <!-- Bell Toggle -->
        <button
            @click="showDropdown = !showDropdown"
            class="relative flex h-10 w-10 items-center justify-center rounded-xl border border-white/10 bg-white/5 text-white transition-all hover:bg-white/10"
            :class="{
                'border-indigo-500/30 bg-indigo-500/20 text-indigo-400':
                    showDropdown,
            }"
        >
            <PhBell
                :size="20"
                :weight="alerts.length > 0 ? 'fill' : 'regular'"
            />
            <!-- Badge -->
            <span
                v-if="alerts.length > 0"
                class="absolute -top-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full bg-rose-500 text-[10px] font-black text-white shadow-lg shadow-rose-500/40"
            >
                {{ alerts.length }}
            </span>
        </button>

        <!-- Glass Dropdown -->
        <div
            v-if="showDropdown"
            class="absolute right-0 z-50 mt-3 w-80 origin-top-right animate-in overflow-hidden rounded-3xl border border-white/10 bg-black/60 p-1 shadow-2xl backdrop-blur-2xl duration-200 zoom-in-95 fade-in"
        >
            <div
                class="flex items-center justify-between border-b border-white/5 p-4"
            >
                <h3
                    class="text-xs font-black tracking-widest text-white/50 uppercase"
                >
                    Smart Alerts
                </h3>
                <span
                    v-if="alerts.length"
                    class="text-[10px] font-bold tracking-tighter text-indigo-400 uppercase"
                    >{{ alerts.length }} pendings</span
                >
            </div>

            <div class="custom-scrollbar max-h-[400px] overflow-y-auto p-2">
                <div v-if="alerts.length === 0" class="py-12 text-center">
                    <div
                        class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full border border-white/5 bg-white/2 text-white/10"
                    >
                        <PhCheckCircle :size="24" />
                    </div>
                    <p
                        class="text-[0.65rem] font-bold tracking-widest text-white/20 uppercase"
                    >
                        Everything clear
                    </p>
                </div>

                <div
                    v-for="alert in alerts"
                    :key="alert.id"
                    class="group relative mb-2 overflow-hidden rounded-2xl border border-white/5 bg-white/2 p-4 transition-all last:mb-0 hover:bg-white/5"
                >
                    <!-- Accent Line -->
                    <div
                        class="absolute top-0 left-0 h-full w-1"
                        :class="[
                            alert.level === 'success' ? 'bg-emerald-500' : '',
                            alert.level === 'warning' ? 'bg-amber-500' : '',
                            alert.level === 'danger' ? 'bg-rose-500' : '',
                            alert.level === 'info' ? 'bg-sky-500' : '',
                        ]"
                    ></div>

                    <div class="flex gap-3">
                        <div
                            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border"
                            :class="getLevelColor(alert.level)"
                        >
                            <component
                                :is="getCategoryIcon(alert.category)"
                                :size="16"
                            />
                        </div>
                        <div class="flex-1 pr-4">
                            <h4
                                class="text-xs font-black text-white transition-colors group-hover:text-indigo-300"
                            >
                                {{ alert.title }}
                            </h4>
                            <p
                                class="mt-1 text-[0.7rem] leading-relaxed text-white/50"
                            >
                                {{ alert.message }}
                            </p>

                            <div v-if="alert.action_link" class="mt-3">
                                <a
                                    :href="alert.action_link.url"
                                    class="text-[0.6rem] font-black tracking-widest text-indigo-400 uppercase underline underline-offset-4 hover:text-indigo-300"
                                >
                                    {{ alert.action_link.text }}
                                </a>
                            </div>
                        </div>
                        <button
                            @click="markAsRead(alert.id)"
                            class="absolute top-2 right-2 text-white/20 transition-colors hover:text-white"
                        >
                            <v-icon icon="mdi-close" size="14" />
                        </button>
                    </div>
                </div>
            </div>

            <div v-if="alerts.length" class="border-t border-white/5 p-2">
                <button
                    class="w-full rounded-xl py-2 text-[0.6rem] font-black tracking-widest text-white/40 uppercase transition-all hover:bg-white/5 hover:text-white"
                >
                    View All Notifications
                </button>
            </div>
        </div>

        <!-- Backdrop for closing -->
        <div
            v-if="showDropdown"
            @click="showDropdown = false"
            class="fixed inset-0 z-40"
        ></div>
    </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 10px;
}
</style>
