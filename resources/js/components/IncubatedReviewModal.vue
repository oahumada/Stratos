<script setup lang="ts">
import StBadgeGlass from '@/components/StBadgeGlass.vue';
import StButtonGlass from '@/components/StButtonGlass.vue';
import StCardGlass from '@/components/StCardGlass.vue';
import { useApi } from '@/composables/useApi';
import { useNotification } from '@/composables/useNotification';
import { ref, watch } from 'vue';

const props = defineProps<{
    modelValue: boolean;
    item: any;
    scenarioId: number | string | null;
}>();

const emit = defineEmits(['update:modelValue', 'close', 'promoted']);

const api = useApi();
const { showSuccess, showError } = useNotification();

const promoting = ref(false);
const open = ref(props.modelValue);

watch(
    () => props.modelValue,
    (v) => {
        open.value = v;
    },
);
watch(open, (v) => {
    emit('update:modelValue', v);
});

const gotoEdit = () => {
    if (!props.item?.id) return;
    window.location.href = `/capabilities/${props.item.id}`;
};

const promote = async () => {
    if (!props.scenarioId || !props.item?.id) return;
    promoting.value = true;
    try {
        const res: any = await api.post(
            `/api/strategic-planning/scenarios/${props.scenarioId}/capabilities/${props.item.id}/promote`,
        );
        showSuccess('Neural structure promoted successfully');
        emit('promoted', res?.data ?? null);
        emit('close');
        open.value = false;
    } catch (e: any) {
        showError('Structure promotion failure');
    } finally {
        promoting.value = false;
    }
};
</script>

<template>
    <v-dialog
        v-model="open"
        max-width="800"
        transition="dialog-bottom-transition"
    >
        <StCardGlass
            variant="glass"
            class="pa-0 overflow-hidden border-white/10 bg-[#0f172a]/95 shadow-[0_30px_60px_-15px_rgba(0,0,0,0.5)] backdrop-blur-2xl"
            :no-hover="true"
        >
            <!-- Modal Header -->
            <div
                class="relative overflow-hidden border-b border-white/5 px-10 py-8"
            >
                <div
                    class="pointer-events-none absolute inset-0 bg-indigo-500/5"
                ></div>
                <div class="relative z-10 flex items-center justify-between">
                    <div>
                        <div class="mb-1 flex items-center gap-3">
                            <h2
                                class="text-2xl font-black tracking-tight text-white"
                            >
                                {{ item?.name || 'Reviewing Entity' }}
                            </h2>
                            <StBadgeGlass
                                variant="primary"
                                size="sm"
                                class="!px-3 text-[9px] tracking-widest uppercase"
                                >Incubating Node</StBadgeGlass
                            >
                        </div>
                        <p
                            class="mb-0 max-w-lg text-sm leading-relaxed font-medium text-white/40"
                        >
                            {{
                                item?.description ||
                                'Evaluating structural integrity for organizational integration.'
                            }}
                        </p>
                    </div>
                    <StButtonGlass
                        variant="ghost"
                        circle
                        size="sm"
                        icon="mdi-close"
                        @click="open = false"
                    />
                </div>
            </div>

            <div
                class="custom-scrollbar max-h-[70vh] overflow-y-auto px-10 py-10"
            >
                <div
                    v-if="!item"
                    class="flex flex-col items-center justify-center py-20 text-white/20"
                >
                    <v-icon size="64" color="white/5" class="mb-6"
                        >mdi-server-off</v-icon
                    >
                    <p class="text-sm font-black tracking-widest uppercase">
                        Neural data unavailable
                    </p>
                </div>

                <div v-else class="space-y-12">
                    <!-- Competencies Section -->
                    <section>
                        <div class="mb-6 flex items-center gap-4">
                            <div
                                class="flex h-8 w-8 items-center justify-center rounded-lg border border-indigo-500/20 bg-indigo-500/10"
                            >
                                <v-icon color="indigo-400" size="18"
                                    >mdi-molecule</v-icon
                                >
                            </div>
                            <h3
                                class="text-xs font-black tracking-[0.2em] text-white/40 uppercase"
                            >
                                Strategic Capacity Linkages
                            </h3>
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div
                                v-for="c in item.competencies || []"
                                :key="c.id"
                                class="group flex items-center gap-4 rounded-2xl border border-white/5 bg-white/5 p-4 transition-all duration-300 hover:border-indigo-500/30"
                            >
                                <div
                                    class="shadow-glow h-2 w-2 rounded-full bg-indigo-500/30 transition-all group-hover:bg-indigo-400"
                                ></div>
                                <span
                                    class="text-sm font-bold text-white/80 transition-colors group-hover:text-white"
                                    >{{ c.name }}</span
                                >
                            </div>
                        </div>
                    </section>

                    <!-- Technical Manifest Section -->
                    <section>
                        <div class="mb-6 flex items-center gap-4">
                            <div
                                class="flex h-8 w-8 items-center justify-center rounded-lg border border-purple-500/20 bg-purple-500/10"
                            >
                                <v-icon color="purple-400" size="18"
                                    >mdi-code-json</v-icon
                                >
                            </div>
                            <h3
                                class="text-xs font-black tracking-[0.2em] text-white/40 uppercase"
                            >
                                Neural Metadata Manifest
                            </h3>
                        </div>

                        <div class="group relative">
                            <div
                                class="absolute -inset-1 bg-indigo-500/10 opacity-0 blur-xl transition-opacity group-hover:opacity-100"
                            ></div>
                            <div
                                class="relative overflow-hidden rounded-2xl border border-white/5 bg-black/40 p-6"
                            >
                                <pre
                                    class="font-mono text-[11px] leading-relaxed text-white/30"
                                    >{{ JSON.stringify(item, null, 2) }}</pre
                                >
                            </div>
                        </div>
                    </section>
                </div>
            </div>

            <!-- Modal Footer -->
            <div
                class="flex items-center justify-end gap-3 border-t border-white/5 bg-[#020617]/40 px-10 py-8"
            >
                <StButtonGlass variant="ghost" @click="open = false"
                    >Cancel Review</StButtonGlass
                >
                <div class="mx-3 h-8 w-px bg-white/5"></div>
                <StButtonGlass
                    variant="secondary"
                    icon="mdi-pencil-outline"
                    @click="gotoEdit"
                    >Adjust Blueprint</StButtonGlass
                >
                <StButtonGlass
                    variant="primary"
                    icon="mdi-rocket-launch-outline"
                    @click="promote"
                    :loading="promoting"
                    class="!px-8"
                >
                    Sync to Production
                </StButtonGlass>
            </div>
        </StCardGlass>
    </v-dialog>
</template>

<style scoped>
.shadow-glow {
    box-shadow: 0 0 10px rgba(99, 102, 241, 0.4);
}
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 4px;
}
.custom-scrollbar:hover::-webkit-scrollbar-thumb {
    background: rgba(99, 102, 241, 0.3);
}
</style>
