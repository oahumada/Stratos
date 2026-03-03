<script setup lang="ts">
import StButtonGlass from '@/components/StButtonGlass.vue';
import StCardGlass from '@/components/StCardGlass.vue';
import { useApi } from '@/composables/useApi';
import { useNotification } from '@/composables/useNotification';
import { useScenarioPlanning } from '@/composables/useStrategicPlanningScenarios';
import { router } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';

const props = defineProps<{ scenarioId?: number }>();

const api = useApi();
const { createPlan } = useScenarioPlanning();
const { showSuccess, showError } = useNotification();

const loading = ref(false);
const form = ref({
    name: '',
    description: '',
    start_date: new Date().toISOString().split('T')[0],
    end_date: new Date(Date.now() + 365 * 24 * 60 * 60 * 1000)
        .toISOString()
        .split('T')[0],
    planning_horizon_months: 12,
    scope_type: 'organization_wide',
    owner_user_id: null as number | null,
    sponsor_user_id: null as number | null,
    fiscal_year: new Date().getFullYear(),
    strategic_context: '',
});

const planData = ref<any>(null);
const skillDemands = ref<any[]>([]);
const scenarioData = ref<any>(null);
const showRolesDialog = ref(false);
const showSkillsDialog = ref(false);
const editedSkills = ref<any[]>([]);
const editedRoles = ref<any[]>([]);

const users = ref<any[]>([]);

const initEdited = () => {
    const skills = planData.value?.skill_demands || skillDemands.value || [];
    editedSkills.value = skills.map((s: any) => ({
        id: s.id,
        skill_id: s.skill_id,
        include: true,
        action: 'include',
        role_id: s.role_id || null,
        required_headcount: s.required_headcount || 0,
        required_level: s.required_level || null,
        priority: s.priority || 'medium',
        rationale: s.rationale || '',
    }));

    const roles = planData.value?.scope_roles || [];
    editedRoles.value = roles.map((r: any) => ({
        id: r.id,
        role_id: r.role_id,
        inclusion_reason: r.inclusion_reason,
        notes: r.notes || '',
        include: true,
    }));
};

const loadUsers = async () => {
    try {
        const res: any = await api.get('/api/users');
        users.value = res?.data ?? res ?? [];
    } catch (e) {
        console.error('Failed to load neural identities', e);
    }
};

const mapScope = (s: string) => {
    switch (s) {
        case 'organization':
            return 'organization_wide';
        case 'department':
            return 'department';
        case 'role_family':
            return 'critical_roles_only';
        default:
            return 'organization_wide';
    }
};

onMounted(async () => {
    try {
        const raw = localStorage.getItem('wfp_prefill');
        if (raw) {
            const data = JSON.parse(raw);
            if (data.name) form.value.name = data.name;
            if (data.time_horizon_weeks)
                form.value.planning_horizon_months = Math.round(
                    (data.time_horizon_weeks || 0) / 4,
                );
            if (data.scope_type)
                form.value.scope_type = mapScope(data.scope_type);
            localStorage.removeItem('wfp_prefill');
        }
    } catch (e) {
        console.error(e);
    }

    if (props.scenarioId) {
        try {
            const resp: any = await api.get(
                `/api/strategic-planning/scenarios/${props.scenarioId}`,
            );
            const data = resp?.data ?? resp;
            scenarioData.value = data;
            const plan = data?.workforce_plan ?? null;
            if (plan) {
                planData.value = plan;
                initEdited();
                if (plan.name) form.value.name = plan.name;
                if (plan.planning_horizon_months)
                    form.value.planning_horizon_months =
                        plan.planning_horizon_months;
                if (plan.start_date) form.value.start_date = plan.start_date;
                if (plan.end_date) form.value.end_date = plan.end_date;
                if (plan.scope_type) form.value.scope_type = plan.scope_type;
            }
            if (data?.skill_demands && Array.isArray(data.skill_demands)) {
                skillDemands.value = data.skill_demands;
            }
            await loadUsers();
        } catch (e) {
            console.error(e);
        }
    }
});

const submit = async () => {
    loading.value = true;
    try {
        const plan = await createPlan(form.value as any);
        showSuccess('Neural workforce plan initialized');
        if (plan) planData.value = plan;
    } catch (e) {
        showError('Neural architecture error: failed to initialize plan');
    } finally {
        loading.value = false;
    }
};

const openPlan = () => {
    if (planData.value?.id) {
        router.visit(
            `/strategic-planning/scenario-planning/${planData.value.id}`,
        );
    }
};

const saveStep1 = async () => {
    if (!props.scenarioId) return;
    try {
        const payload = {
            step1: {
                metadata: {
                    name: form.value.name,
                    start_date: form.value.start_date,
                    end_date: form.value.end_date,
                    planning_horizon_months: form.value.planning_horizon_months,
                    scope_type: form.value.scope_type,
                },
                skills: editedSkills.value,
                roles: editedRoles.value,
            },
        };
        await api.patch(
            `/api/strategic-planning/scenarios/${props.scenarioId}`,
            payload,
        );
        showSuccess('Neural foundation updated');
    } catch (e) {
        showError('Critical sync failure');
    }
};
</script>

<template>
    <div
        class="create-plan-wizard animate-in space-y-8 duration-700 fade-in slide-in-from-bottom-4"
    >
        <!-- Dashboard Header -->
        <div class="flex items-center justify-between">
            <div class="flex flex-col gap-1">
                <h3 class="text-2xl font-black tracking-tight text-white">
                    System Foundation
                </h3>
                <p class="text-sm font-medium text-white/50">
                    Configure tactical parameters and strategic scope.
                </p>
            </div>
            <StButtonGlass
                v-if="planData"
                variant="primary"
                icon="mdi-open-in-new"
                @click="openPlan"
            >
                Open Operational Plan
            </StButtonGlass>
        </div>

        <div v-if="!planData">
            <StCardGlass variant="glass" class="space-y-6">
                <!-- Name & Description -->
                <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                    <div class="space-y-4 md:col-span-2">
                        <div class="space-y-1.5">
                            <label
                                class="ml-1 text-[10px] font-black tracking-widest text-indigo-400 uppercase"
                                >Plan Identifier</label
                            >
                            <input
                                v-model="form.name"
                                type="text"
                                class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white placeholder-white/20 transition-colors focus:border-indigo-500/50 focus:outline-none"
                                placeholder="Strategic Alpha Horizon..."
                            />
                        </div>
                        <div class="space-y-1.5">
                            <label
                                class="ml-1 text-[10px] font-black tracking-widest text-white/30 uppercase"
                                >Contextual Description</label
                            >
                            <textarea
                                v-model="form.description"
                                rows="3"
                                class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white placeholder-white/20 transition-colors focus:border-indigo-500/50 focus:outline-none"
                                placeholder="Describe the strategic intent..."
                            ></textarea>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div class="space-y-1.5">
                            <label
                                class="ml-1 text-[10px] font-black tracking-widest text-white/30 uppercase"
                                >Architectural Scope</label
                            >
                            <select
                                v-model="form.scope_type"
                                class="w-full appearance-none rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white transition-colors focus:border-indigo-500/50 focus:outline-none"
                            >
                                <option value="organization_wide">
                                    Organization Wide
                                </option>
                                <option value="business_unit">
                                    Business Unit
                                </option>
                                <option value="department">Departmental</option>
                                <option value="critical_roles_only">
                                    Critical Roles Only
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                    <div class="space-y-1.5">
                        <label
                            class="ml-1 text-[10px] font-black tracking-widest text-white/30 uppercase"
                            >Deployment Start</label
                        >
                        <input
                            v-model="form.start_date"
                            type="date"
                            class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white transition-colors focus:border-indigo-500/50 focus:outline-none"
                        />
                    </div>
                    <div class="space-y-1.5">
                        <label
                            class="ml-1 text-[10px] font-black tracking-widest text-white/30 uppercase"
                            >Neural Termination</label
                        >
                        <input
                            v-model="form.end_date"
                            type="date"
                            class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white transition-colors focus:border-indigo-500/50 focus:outline-none"
                        />
                    </div>
                    <div class="space-y-1.5">
                        <label
                            class="ml-1 text-[10px] font-black tracking-widest text-white/30 uppercase"
                            >Horizon (Months)</label
                        >
                        <input
                            v-model="form.planning_horizon_months"
                            type="number"
                            class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white transition-colors focus:border-indigo-500/50 focus:outline-none"
                        />
                    </div>
                </div>

                <div
                    class="grid grid-cols-1 gap-6 border-t border-white/5 pt-4 md:grid-cols-3"
                >
                    <div class="space-y-1.5">
                        <label
                            class="ml-1 text-[10px] font-black tracking-widest text-white/30 uppercase"
                            >Operational Owner</label
                        >
                        <select
                            v-model="form.owner_user_id"
                            class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white transition-colors focus:border-indigo-500/50 focus:outline-none"
                        >
                            <option :value="null">Unassigned</option>
                            <option
                                v-for="u in users"
                                :key="u.id"
                                :value="u.id"
                            >
                                {{ u.name }}
                            </option>
                        </select>
                    </div>
                    <div class="space-y-1.5">
                        <label
                            class="ml-1 text-[10px] font-black tracking-widest text-white/30 uppercase"
                            >Strategic Sponsor</label
                        >
                        <select
                            v-model="form.sponsor_user_id"
                            class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white transition-colors focus:border-indigo-500/50 focus:outline-none"
                        >
                            <option :value="null">Unassigned</option>
                            <option
                                v-for="u in users"
                                :key="u.id"
                                :value="u.id"
                            >
                                {{ u.name }}
                            </option>
                        </select>
                    </div>
                    <div class="space-y-1.5">
                        <label
                            class="ml-1 text-[10px] font-black tracking-widest text-white/30 uppercase"
                            >Fiscal Year</label
                        >
                        <input
                            v-model="form.fiscal_year"
                            type="number"
                            class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white transition-colors focus:border-indigo-500/50 focus:outline-none"
                        />
                    </div>
                </div>

                <div class="flex justify-end pt-4">
                    <StButtonGlass
                        variant="primary"
                        :loading="loading"
                        @click="submit"
                        class="px-12!"
                    >
                        Initialize Plan
                    </StButtonGlass>
                </div>
            </StCardGlass>
        </div>

        <!-- Pre-filled Data (Phase 1) -->
        <div v-if="planData || skillDemands.length" class="space-y-6">
            <StCardGlass
                variant="glass"
                border-accent="indigo"
                class="overflow-hidden p-0!"
            >
                <div
                    class="flex items-center gap-3 border-b border-white/10 bg-white/5 px-6 py-4"
                >
                    <v-icon
                        icon="mdi-database-eye-outline"
                        color="indigo-400"
                        size="18"
                    />
                    <h4
                        class="text-xs font-black tracking-widest text-white uppercase"
                    >
                        Neural Artifact Extraction
                    </h4>
                </div>

                <div class="grid grid-cols-1 gap-8 p-6 md:grid-cols-2">
                    <!-- Scope Units -->
                    <div class="space-y-4">
                        <h5
                            class="text-[10px] font-black tracking-widest text-white/40 uppercase"
                        >
                            Associated Business Nodes
                        </h5>
                        <div
                            v-if="planData?.scope_units?.length"
                            class="space-y-2"
                        >
                            <div
                                v-for="u in planData.scope_units"
                                :key="u.id"
                                class="flex items-center justify-between rounded-xl border border-white/5 bg-white/5 p-3"
                            >
                                <div>
                                    <div class="text-sm font-bold text-white">
                                        {{ u.unit_name }}
                                    </div>
                                    <div
                                        class="text-[10px] font-bold text-white/30 uppercase"
                                    >
                                        {{ u.unit_type }}
                                    </div>
                                </div>
                                <div
                                    class="text-[10px] font-black text-indigo-400 uppercase opacity-60"
                                >
                                    {{ u.inclusion_reason }}
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-xs text-white/20 italic">
                            No business nodes registered.
                        </div>
                    </div>

                    <!-- Skill Demands -->
                    <div class="space-y-4">
                        <h5
                            class="text-[10px] font-black tracking-widest text-white/40 uppercase"
                        >
                            Capability Deficit Projections
                        </h5>
                        <div v-if="editedSkills.length" class="space-y-2">
                            <div
                                v-for="s in editedSkills.slice(0, 5)"
                                :key="s.id"
                                class="flex items-center justify-between rounded-xl border border-white/5 bg-white/5 p-3"
                            >
                                <div>
                                    <div class="text-sm font-bold text-white">
                                        Neural Pattern #{{ s.skill_id }}
                                    </div>
                                    <div
                                        class="text-[10px] font-bold text-white/30 uppercase"
                                    >
                                        RQ: {{ s.required_headcount }} FTEs
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div
                                        class="h-1.5 w-1.5 rounded-full"
                                        :class="
                                            s.priority === 'critical'
                                                ? 'bg-rose-500'
                                                : 'bg-amber-500'
                                        "
                                    ></div>
                                    <div
                                        class="text-[10px] font-black text-white/40 uppercase"
                                    >
                                        {{ s.priority }}
                                    </div>
                                </div>
                            </div>
                            <div
                                v-if="editedSkills.length > 5"
                                class="text-right"
                            >
                                <button
                                    @click="showSkillsDialog = true"
                                    class="text-[10px] font-black text-indigo-400 uppercase hover:text-indigo-300"
                                >
                                    View All
                                    {{ editedSkills.length }} Projections
                                </button>
                            </div>
                        </div>
                        <div v-else class="text-xs text-white/20 italic">
                            No neural deficits identified.
                        </div>
                    </div>
                </div>

                <div
                    class="flex items-center justify-between border-t border-white/5 bg-indigo-500/5 px-6 py-4"
                >
                    <p class="text-[11px] font-medium text-white/40 italic">
                        System foundation is immutable once operational. Adjust
                        in configuration node if necessary.
                    </p>
                    <StButtonGlass variant="ghost" size="sm" @click="saveStep1"
                        >Commit Foundation Adjustments</StButtonGlass
                    >
                </div>
            </StCardGlass>
        </div>
    </div>
</template>

<style scoped>
.no-scrollbar::-webkit-scrollbar {
    display: none;
}
.no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

select option {
    background-color: #111827;
    color: white;
}
</style>
