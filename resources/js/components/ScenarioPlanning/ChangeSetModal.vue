<template>
    <v-dialog
        v-model="internalShow"
        max-width="800"
        scrollable
        @click:outside="handleClose"
    >
        <StCardGlass
            variant="glass"
            border-accent="indigo"
            class="flex max-h-[85vh] flex-col overflow-hidden bg-[#0f172a]/95! p-0! backdrop-blur-xl"
        >
            <!-- Header -->
            <div
                class="flex shrink-0 items-center justify-between border-b border-white/10 bg-white/5 px-6 py-4"
            >
                <div class="flex items-center gap-3">
                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-xl border border-indigo-500/20 bg-indigo-500/10"
                    >
                        <v-icon color="indigo-400" size="20"
                            >mdi-source-pull</v-icon
                        >
                    </div>
                    <div>
                        <h3
                            class="text-lg font-black text-white"
                            :id="`changeset-title-${id}`"
                        >
                            {{ title || 'ChangeSet Review' }}
                        </h3>
                        <p
                            class="text-[10px] font-bold tracking-widest text-white/50 uppercase"
                            v-if="preview?.ops?.length"
                        >
                            {{ preview.ops.length }} Operations Detected
                        </p>
                    </div>
                </div>
                <StButtonGlass
                    variant="ghost"
                    icon="mdi-close"
                    size="sm"
                    circle
                    @click="handleClose"
                />
            </div>

            <!-- Content -->
            <div class="custom-scrollbar grow overflow-y-auto p-6">
                <div v-if="preview && preview.ops && preview.ops.length">
                    <div class="space-y-4">
                        <div
                            v-for="(op, i) in preview.ops"
                            :key="i"
                            class="overflow-hidden rounded-xl border border-white/5 bg-black/20 transition-all duration-200"
                            :class="[
                                op._ignored ? 'opacity-50 grayscale' : '',
                                opClass(op.type).bgClass,
                            ]"
                        >
                            <!-- Operation Header -->
                            <div
                                class="flex cursor-pointer items-center justify-between border-b border-white/5 px-4 py-3 transition-colors hover:bg-white/5"
                                @click="toggle(i)"
                            >
                                <div class="flex items-center gap-3">
                                    <span class="text-lg">{{
                                        opIcon(op.type)
                                    }}</span>
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="text-xs font-bold text-white/50"
                                            >{{ i + 1 }}.</span
                                        >
                                        <StBadgeGlass
                                            :variant="
                                                opClass(op.type).badgeVariant
                                            "
                                            size="sm"
                                        >
                                            {{ op.type.toUpperCase() }}
                                        </StBadgeGlass>
                                    </div>
                                </div>

                                <div
                                    class="flex items-center gap-2"
                                    @click.stop
                                >
                                    <StBadgeGlass
                                        v-if="op._reverted"
                                        variant="secondary"
                                        size="sm"
                                        >Reverted</StBadgeGlass
                                    >
                                    <StButtonGlass
                                        variant="ghost"
                                        size="sm"
                                        :icon="
                                            op._ignored
                                                ? 'mdi-eye-off'
                                                : 'mdi-eye'
                                        "
                                        @click.stop="ignoreOp(i)"
                                        title="Ignore Operation"
                                    />
                                    <StButtonGlass
                                        variant="ghost"
                                        size="sm"
                                        :icon="
                                            op._reverted
                                                ? 'mdi-undo-variant'
                                                : 'mdi-undo'
                                        "
                                        @click.stop="revertOp(i)"
                                        :title="
                                            op._reverted
                                                ? 'Undo Revert'
                                                : 'Revert Operation'
                                        "
                                    />
                                    <StButtonGlass
                                        variant="ghost"
                                        size="sm"
                                        :icon="
                                            collapsed[i]
                                                ? 'mdi-chevron-down'
                                                : 'mdi-chevron-up'
                                        "
                                        @click.stop="toggle(i)"
                                    />
                                </div>
                            </div>

                            <!-- Operation Details -->
                            <div
                                v-show="!collapsed[i]"
                                class="p-4"
                                :id="`op-details-${i}`"
                            >
                                <div
                                    v-if="
                                        op.payload &&
                                        typeof op.payload === 'object' &&
                                        !showRaw[i]
                                    "
                                    class="space-y-2"
                                >
                                    <div
                                        class="space-y-1 rounded-lg border border-white/5 bg-white/2 p-3"
                                    >
                                        <div
                                            v-for="(val, key) in op.payload"
                                            :key="key"
                                            class="grid grid-cols-12 gap-2 text-xs"
                                        >
                                            <div
                                                class="col-span-3 font-semibold text-white/60 capitalize"
                                            >
                                                {{
                                                    String(key).replace(
                                                        /_/g,
                                                        ' ',
                                                    )
                                                }}:
                                            </div>
                                            <div
                                                class="col-span-9 font-mono break-words text-white/90"
                                            >
                                                <template
                                                    v-if="
                                                        typeof val === 'object'
                                                    "
                                                    >{{
                                                        JSON.stringify(val)
                                                    }}</template
                                                >
                                                <template v-else>{{
                                                    String(val)
                                                }}</template>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <pre
                                    v-else
                                    class="custom-scrollbar overflow-x-auto rounded-lg border border-white/5 bg-black/40 p-3 font-mono text-[11px] text-white/80"
                                    >{{ JSON.stringify(op, null, 2) }}</pre
                                >

                                <div
                                    class="mt-3 flex items-center justify-end gap-2 border-t border-white/5 pt-3"
                                >
                                    <StButtonGlass
                                        variant="ghost"
                                        size="sm"
                                        icon="mdi-content-copy"
                                        @click="copyOp(i)"
                                        >Copy Data</StButtonGlass
                                    >
                                    <StButtonGlass
                                        variant="ghost"
                                        size="sm"
                                        :icon="
                                            showRaw[i]
                                                ? 'mdi-code-braces-box'
                                                : 'mdi-code-json'
                                        "
                                        @click="toggleRaw(i)"
                                    >
                                        {{
                                            showRaw[i]
                                                ? 'Structured View'
                                                : 'Raw JSON'
                                        }}
                                    </StButtonGlass>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    v-else
                    class="flex flex-col items-center justify-center py-12 text-center"
                >
                    <div
                        v-if="
                            preview && preview.ops && preview.ops.length === 0
                        "
                    >
                        <v-icon size="48" color="white/20" class="mb-4"
                            >mdi-check-all</v-icon
                        >
                        <h4 class="font-medium text-white/60">
                            No Operations Present
                        </h4>
                        <p class="mt-1 text-xs text-white/40">
                            This ChangeSet does not contain any executable
                            operations.
                        </p>
                    </div>
                    <div v-else-if="preview">
                        <pre
                            class="custom-scrollbar w-full overflow-x-auto rounded-lg bg-black/40 p-4 text-left font-mono text-[11px] text-white/80"
                            >{{ JSON.stringify(preview, null, 2) }}</pre
                        >
                    </div>
                    <div v-else class="flex flex-col items-center gap-3">
                        <v-progress-circular
                            v-if="loading"
                            indeterminate
                            color="indigo-400"
                            size="32"
                        />
                        <span class="text-xs font-medium text-white/40">{{
                            loading
                                ? 'Analyzing changes...'
                                : 'No preview available'
                        }}</span>
                    </div>
                </div>
            </div>

            <!-- Footer Actions -->
            <div
                class="flex shrink-0 items-center justify-between border-t border-white/10 bg-black/20 px-6 py-4"
            >
                <StButtonGlass variant="ghost" @click="handleClose"
                    >Close</StButtonGlass
                >
                <div class="flex items-center gap-3">
                    <StButtonGlass
                        v-if="canApply"
                        variant="secondary"
                        icon="mdi-close-circle"
                        :disabled="loading"
                        @click="reject"
                        >Reject</StButtonGlass
                    >
                    <StButtonGlass
                        v-if="canApply"
                        variant="primary"
                        icon="mdi-check-circle"
                        class="border-emerald-500/50! bg-emerald-500/20! hover:bg-emerald-500/30!"
                        :disabled="loading"
                        @click="approve"
                        >Approve</StButtonGlass
                    >
                    <StButtonGlass
                        variant="primary"
                        icon="mdi-play-circle"
                        :loading="loading"
                        :disabled="!canApply"
                        @click="apply"
                        >Execute Apply</StButtonGlass
                    >
                </div>
            </div>
        </StCardGlass>
    </v-dialog>
</template>

<script lang="ts">
import StBadgeGlass from '@/components/StBadgeGlass.vue';
import StButtonGlass from '@/components/StButtonGlass.vue';
import StCardGlass from '@/components/StCardGlass.vue';
import { useChangeSetStore } from '@/stores/changeSetStore';
import { defineComponent, onBeforeUnmount, onMounted, ref, watch } from 'vue';

export default defineComponent({
    components: { StCardGlass, StButtonGlass, StBadgeGlass },
    props: {
        id: { type: Number, required: true },
        title: { type: String, default: 'ChangeSet' },
        modelValue: { type: Boolean, default: true },
    },
    emits: ['close', 'update:modelValue'],
    setup(props, { emit }) {
        const store = useChangeSetStore();
        const preview = ref<any>(null);
        const loading = ref(false);
        const canApply = ref(false);

        const internalShow = ref(props.modelValue);

        watch(
            () => props.modelValue,
            (val) => {
                internalShow.value = val;
                if (val) loadPreview();
            },
        );

        const handleClose = () => {
            internalShow.value = false;
            emit('update:modelValue', false);
            emit('close');
        };

        const collapsed = ref<boolean[]>([]);
        const ignored = ref<Record<number, boolean>>({});
        const showRaw = ref<Record<number, boolean>>({});

        const onKeyDown = (e: KeyboardEvent) => {
            if (e.key === 'Escape' || e.key === 'Esc') {
                handleClose();
            }
        };

        const loadPreview = async () => {
            loading.value = true;
            try {
                const res = await store.previewChangeSet(props.id);
                preview.value = res.preview ?? res.data ?? res;
                const ops = preview.value?.ops ?? [];
                collapsed.value = ops.map(() => false);
                try {
                    const pem = await store.canApplyChangeSet(props.id);
                    canApply.value =
                        pem?.can_apply ?? pem?.data?.can_apply ?? false;
                } catch (error_) {
                    canApply.value = false;
                }
            } finally {
                loading.value = false;
            }
        };

        const revertOp = (i: number) => {
            if (!preview.value || !Array.isArray(preview.value.ops)) return;
            const op = preview.value.ops[i];
            if (!op) return;
            op._reverted = !op._reverted;
            if (op._reverted) {
                ignored.value[i] = true;
                preview.value.ops = preview.value.ops
                    .map((o: any, idx: number) =>
                        ignored.value[idx] ? { ...o, _ignored: true } : o,
                    )
                    .filter((o: any) => !o._ignored);
                collapsed.value = preview.value.ops.map(() => false);
            } else {
                ignored.value = {};
                loadPreview();
            }
        };

        const toggle = (i: number) => {
            collapsed.value[i] = !collapsed.value[i];
        };

        const ignoreOp = (i: number) => {
            ignored.value[i] = !ignored.value[i];
            if (preview.value && Array.isArray(preview.value.ops)) {
                preview.value.ops[i]._ignored = ignored.value[i];
            }
        };

        const copyOp = (i: number) => {
            const op = preview.value?.ops?.[i];
            if (!op) return;
            const text = JSON.stringify(op, null, 2);
            navigator.clipboard?.writeText(text).catch(() => {});
        };

        const toggleRaw = (i: number) => {
            showRaw.value[i] = !showRaw.value[i];
        };

        const opClass = (type: string) => {
            const t = String(type || '').toLowerCase();
            if (t.startsWith('create'))
                return {
                    bgClass: 'border-l-4 border-l-emerald-500',
                    badgeVariant: 'success',
                };
            if (t.startsWith('update'))
                return {
                    bgClass: 'border-l-4 border-l-amber-500',
                    badgeVariant: 'secondary',
                };
            if (t.startsWith('delete') || t.includes('sunset'))
                return {
                    bgClass: 'border-l-4 border-l-rose-500',
                    badgeVariant: 'danger',
                };
            return {
                bgClass: 'border-l-4 border-l-indigo-400',
                badgeVariant: 'primary',
            };
        };

        const opIcon = (type: string) => {
            const t = String(type || '').toLowerCase();
            if (t.startsWith('create')) return '➕';
            if (t.startsWith('update')) return '✏️';
            if (t.startsWith('delete') || t.includes('sunset')) return '🗑️';
            return '⚙️';
        };

        const apply = async () => {
            loading.value = true;
            try {
                const ignoredIndexes = Object.keys(ignored.value)
                    .filter((k) => ignored.value[Number(k)])
                    .map(Number);
                const payload = ignoredIndexes.length
                    ? { ignored_indexes: ignoredIndexes }
                    : undefined;
                await store.applyChangeSet(props.id, payload);
                globalThis.location.reload();
            } finally {
                loading.value = false;
            }
        };

        const approve = async () => {
            loading.value = true;
            try {
                await store.approveChangeSet(props.id);
                globalThis.location.reload();
            } finally {
                loading.value = false;
            }
        };
        const reject = async () => {
            loading.value = true;
            try {
                await store.rejectChangeSet(props.id);
                globalThis.location.reload();
            } finally {
                loading.value = false;
            }
        };

        onMounted(() => {
            globalThis.addEventListener('keydown', onKeyDown);
            if (internalShow.value) loadPreview();
        });
        onBeforeUnmount(() => {
            globalThis.removeEventListener('keydown', onKeyDown);
        });

        return {
            preview,
            loading,
            apply,
            canApply,
            approve,
            reject,
            collapsed,
            toggle,
            ignoreOp,
            copyOp,
            opClass,
            opIcon,
            revertOp,
            ignored,
            showRaw,
            toggleRaw,
            internalShow,
            handleClose,
        };
    },
});
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
    height: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 4px;
}
.custom-scrollbar:hover::-webkit-scrollbar-thumb {
    background: rgba(99, 102, 241, 0.3);
}
</style>
