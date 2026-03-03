<template>
    <v-dialog
        :model-value="visible"
        max-width="560px"
        @update:model-value="$emit('close')"
        persistent
    >
        <StCardGlass
            variant="glass"
            border-accent="indigo"
            class="overflow-hidden bg-[#0d1425]/95 p-0! backdrop-blur-3xl"
            :no-hover="true"
        >
            <!-- Header -->
            <div
                class="flex items-center gap-4 border-b border-white/10 bg-indigo-500/10 px-8 py-6"
            >
                <div
                    class="flex h-12 w-12 items-center justify-center rounded-xl border border-indigo-400/30 bg-indigo-500/20 shadow-lg"
                >
                    <v-icon color="indigo-300" size="24"
                        >mdi-account-plus-outline</v-icon
                    >
                </div>
                <div>
                    <h2 class="text-xl leading-tight font-black text-white">
                        Add Strategic Role
                    </h2>
                    <div
                        class="text-[10px] font-black tracking-widest text-indigo-400 uppercase"
                    >
                        Architecture Node Insertion
                    </div>
                </div>
            </div>

            <!-- Body -->
            <div class="px-8 py-8">
                <div class="space-y-6">
                    <!-- Role Selection Logic -->
                    <div class="space-y-3">
                        <label
                            id="op-type-label"
                            class="text-[11px] font-black tracking-widest text-white/40 uppercase"
                            >Operation Type</label
                        >
                        <v-radio-group
                            v-model="formData.type"
                            inline
                            hide-details
                            class="mt-1"
                            aria-labelledby="op-type-label"
                        >
                            <div class="flex gap-6">
                                <v-radio
                                    value="existing"
                                    color="indigo-400"
                                    class="text-white/70!"
                                >
                                    <template #label
                                        ><span
                                            class="text-sm font-bold text-white/70"
                                            >Existing Asset</span
                                        ></template
                                    >
                                </v-radio>
                                <v-radio
                                    value="new"
                                    color="indigo-400"
                                    class="text-white/70!"
                                >
                                    <template #label
                                        ><span
                                            class="text-sm font-bold text-white/70"
                                            >New Prototype</span
                                        ></template
                                    >
                                </v-radio>
                            </div>
                        </v-radio-group>
                    </div>

                    <!-- Existing Role Selection -->
                    <div
                        v-if="formData.type === 'existing'"
                        class="animate-in duration-300 fade-in slide-in-from-top-2"
                    >
                        <label
                            for="role-select"
                            class="mb-2 block text-[11px] font-black tracking-widest text-white/40 uppercase"
                            >Select from Catalog</label
                        >
                        <v-select
                            id="role-select"
                            v-model="formData.role_id"
                            :items="availableRoles"
                            item-title="name"
                            item-value="id"
                            placeholder="Search roles..."
                            variant="outlined"
                            density="comfortable"
                            base-color="white/10"
                            color="indigo-400"
                            class="custom-glass-input"
                        >
                            <template #prepend-inner>
                                <v-icon size="18" class="mr-2 opacity-30"
                                    >mdi-magnify</v-icon
                                >
                            </template>
                        </v-select>
                    </div>

                    <!-- New Role Form -->
                    <div
                        v-if="formData.type === 'new'"
                        class="animate-in space-y-5 duration-300 fade-in slide-in-from-top-2"
                    >
                        <div class="space-y-2">
                            <label
                                for="role-name"
                                class="text-[11px] font-black tracking-widest text-white/40 uppercase"
                                >Role Designation</label
                            >
                            <v-text-field
                                id="role-name"
                                v-model="formData.role_name"
                                placeholder="e.g., Lead AI Engineer"
                                variant="outlined"
                                density="comfortable"
                                hide-details
                                base-color="white/10"
                                color="indigo-400"
                            />
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label
                                    for="fte-field"
                                    class="text-[11px] font-black tracking-widest text-white/40 uppercase"
                                    >Initial FTE</label
                                >
                                <v-text-field
                                    id="fte-field"
                                    v-model.number="formData.fte"
                                    type="number"
                                    variant="outlined"
                                    density="comfortable"
                                    hide-details
                                    base-color="white/10"
                                    color="indigo-400"
                                />
                            </div>
                            <div class="space-y-2">
                                <label
                                    for="evolution-select"
                                    class="text-[11px] font-black tracking-widest text-white/40 uppercase"
                                    >Evolution Vector</label
                                >
                                <v-select
                                    id="evolution-select"
                                    v-model="formData.evolution_type"
                                    :items="evolutionTypes"
                                    variant="outlined"
                                    density="comfortable"
                                    hide-details
                                    base-color="white/10"
                                    color="indigo-400"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Common Analysis Fields -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label
                                for="change-select"
                                class="text-[11px] font-black tracking-widest text-white/40 uppercase"
                                >Change Delta</label
                            >
                            <v-select
                                id="change-select"
                                v-model="formData.role_change"
                                :items="roleChangeTypes"
                                variant="outlined"
                                density="comfortable"
                                hide-details
                                base-color="white/10"
                                color="indigo-400"
                            />
                        </div>
                        <div class="space-y-2">
                            <label
                                for="impact-select"
                                class="text-[11px] font-black tracking-widest text-white/40 uppercase"
                                >Impact criticalitY</label
                            >
                            <v-select
                                id="impact-select"
                                v-model="formData.impact_level"
                                :items="impactLevels"
                                variant="outlined"
                                density="comfortable"
                                hide-details
                                base-color="white/10"
                                color="indigo-400"
                            />
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label
                            for="rationale-area"
                            class="text-[11px] font-black tracking-widest text-white/40 uppercase"
                            >Strategic Rationale</label
                        >
                        <v-textarea
                            id="rationale-area"
                            v-model="formData.rationale"
                            placeholder="Define the purpose of this insertion..."
                            variant="outlined"
                            density="comfortable"
                            rows="3"
                            hide-details
                            base-color="white/10"
                            color="indigo-400"
                        />
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div
                class="flex items-center justify-end gap-3 border-t border-white/10 bg-white/2 px-8 py-6"
            >
                <StButtonGlass variant="ghost" @click="$emit('close')">
                    Discard Changes
                </StButtonGlass>
                <StButtonGlass
                    variant="primary"
                    icon="mdi-plus-thick"
                    @click="handleSave"
                    :loading="saving"
                >
                    Integrate into Design
                </StButtonGlass>
            </div>
        </StCardGlass>
    </v-dialog>
</template>

<script setup lang="ts">
import StButtonGlass from '@/components/StButtonGlass.vue';
import StCardGlass from '@/components/StCardGlass.vue';
import { usePage } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';

interface Props {
    visible: boolean;
}

interface Emits {
    (e: 'save', data: any): void;
    (e: 'close'): void;
}

defineProps<Props>();
const emit = defineEmits<Emits>();

const saving = ref(false);
const availableRoles = ref<any[]>([]);

const formData = ref({
    type: 'existing' as 'existing' | 'new',
    role_id: null as number | null,
    role_name: '',
    fte: 1,
    role_change: 'create' as string,
    impact_level: 'medium' as string,
    evolution_type: 'new_role' as string,
    rationale: '',
});

const roleChangeTypes = [
    { title: 'New Creation', value: 'create' },
    { title: 'Structural Modification', value: 'modify' },
    { title: 'Lifecycle Termination', value: 'eliminate' },
    { title: 'Preservation', value: 'maintain' },
];

const impactLevels = [
    { title: 'Mission Critical', value: 'critical' },
    { title: 'High Significance', value: 'high' },
    { title: 'Standard Impact', value: 'medium' },
    { title: 'Supporting Utility', value: 'low' },
];

const evolutionTypes = [
    { title: 'Fresh Role Prototype', value: 'new_role' },
    { title: 'Skill Enhancement Upgrade', value: 'upgrade_skills' },
    { title: 'Full Architectural Transformation', value: 'transformation' },
    { title: 'Efficiency Downsizing', value: 'downsize' },
    { title: 'Structural Elimination', value: 'elimination' },
];

const handleSave = async () => {
    saving.value = true;
    try {
        emit('save', {
            ...formData.value,
            role_id:
                formData.value.type === 'existing'
                    ? formData.value.role_id
                    : null,
            role_name:
                formData.value.type === 'new' ? formData.value.role_name : null,
        });
    } finally {
        saving.value = false;
    }
};

// Load available roles: prefer API `/api/roles`, fallback to Inertia page props
const loadAvailableRoles = async () => {
    try {
        // Try API first
        try {
            const r = await fetch('/api/roles');
            if (r.ok) {
                const body = await r.json();
                let roles = [];
                if (Array.isArray(body?.data)) {
                    roles = body.data;
                } else if (Array.isArray(body)) {
                    roles = body;
                }

                availableRoles.value = roles;
                return;
            }
        } catch {
            // ignore and fallback to page props
        }

        // Fallback: use Inertia page props if provided server-side
        try {
            const page = usePage();
            availableRoles.value = (page.props as any).roles || [];
        } catch (err) {
            console.error('Error loading roles from page props:', err);
            availableRoles.value = [];
        }
    } catch (err) {
        console.error('Error loading roles:', err);
        availableRoles.value = [];
    }
};

onMounted(() => {
    loadAvailableRoles();
});
</script>

<style scoped>
:deep(.v-field__outline) {
    --v-border-opacity: 0.15;
}
:deep(.v-label) {
    font-size: 0.85rem;
}
:deep(.v-radio-group .v-label) {
    opacity: 1;
}
</style>
