<script setup lang="ts">
import StBadgeGlass from '@/components/StBadgeGlass.vue';
import StButtonGlass from '@/components/StButtonGlass.vue';
import StCardGlass from '@/components/StCardGlass.vue';
import axios from 'axios';
import { onMounted, ref } from 'vue';

interface AuditEvent {
    id: string;
    event_name: string;
    aggregate_type: string;
    aggregate_id: string;
    actor_id: number | null;
    organization_id: number;
    payload: Record<string, unknown>;
    occurred_at: string;
}

interface AuditSummary {
    total_events: number;
    events_last_24h: number;
    unique_event_names: number;
    unique_aggregates: number;
    top_event_names: Record<string, number>;
}

interface Iso30414RoleRisk {
    role_name: string | null;
    people_count: number;
    avg_replacement_cost: number;
}

interface Iso30414ReplacementCost {
    total_headcount: number;
    total_estimated_replacement_cost: number;
    average_estimated_replacement_cost: number;
    highest_risk_roles: Iso30414RoleRisk[];
}

interface Iso30414DepartmentMaturity {
    id: number;
    name: string;
    headcount: number;
    readiness_ratio: number | null;
    avg_current_level: number | null;
    avg_required_level: number | null;
    gap_records: number;
}

interface Iso30414CapabilityGap {
    skill_id: number;
    skill_name: string;
    domain_tag: string | null;
    assessed_people: number;
    people_with_gap: number;
    avg_gap_level: number;
}

interface Iso30414Summary {
    replacement_cost: Iso30414ReplacementCost;
    talent_maturity_by_department: Iso30414DepartmentMaturity[];
    transversal_capability_gaps: Iso30414CapabilityGap[];
}

interface AuditWizardRole {
    role_id: number;
    role_name: string;
    department_name: string | null;
    status: string;
    critical_skills_count: number;
    signature_status: 'current' | 'expired' | 'missing';
    signature_age_days: number | null;
    signed_at: string | null;
    signature_version: string | null;
    is_compliant: boolean;
}

interface InternalAuditWizard {
    signature_valid_days: number;
    summary: {
        total_critical_roles: number;
        compliant_roles: number;
        non_compliant_roles: number;
        compliance_rate: number;
    };
    roles: AuditWizardRole[];
}

interface RoleCredential {
    id: string;
    type: string[];
    issuer: {
        id: string;
        name: string;
    };
    issuanceDate: string;
    credentialSubject: {
        id: string;
        role: {
            id: number;
            name: string;
            level: string | null;
            status: string | null;
            department: string | null;
        };
    };
}

interface CredentialVerification {
    is_valid: boolean;
    checks: {
        model_signature_valid: boolean;
        proof_matches_role_signature: boolean;
        issuer_matches_expected: boolean;
        credential_subject_role_matches: boolean;
    };
}

const loading = ref(true);
const events = ref<AuditEvent[]>([]);
const summary = ref<AuditSummary | null>(null);
const iso30414 = ref<Iso30414Summary | null>(null);
const internalAudit = ref<InternalAuditWizard | null>(null);
const roleCredential = ref<RoleCredential | null>(null);
const credentialVerification = ref<CredentialVerification | null>(null);
const verifyingCredential = ref(false);
const roleIdForCredential = ref<number | null>(null);
const signatureValidDays = ref<number>(365);

const eventNameFilter = ref('');
const aggregateTypeFilter = ref('');

const loadDashboard = async () => {
    loading.value = true;

    try {
        const [
            eventsResponse,
            summaryResponse,
            iso30414Response,
            internalAuditResponse,
        ] = await Promise.all([
            axios.get('/api/compliance/audit-events', {
                params: {
                    per_page: 50,
                    event_name: eventNameFilter.value || undefined,
                    aggregate_type: aggregateTypeFilter.value || undefined,
                },
            }),
            axios.get('/api/compliance/audit-events/summary'),
            axios.get('/api/compliance/iso30414/summary'),
            axios.get('/api/compliance/internal-audit-wizard', {
                params: {
                    signature_valid_days: signatureValidDays.value,
                },
            }),
        ]);

        events.value = eventsResponse.data?.data?.data ?? [];
        summary.value = summaryResponse.data?.data ?? null;
        iso30414.value = iso30414Response.data?.data ?? null;
        internalAudit.value = internalAuditResponse.data?.data ?? null;
    } catch (error) {
        console.error('Failed to load compliance audit dashboard', error);
    } finally {
        loading.value = false;
    }
};

const exportRoleCredential = async () => {
    if (!roleIdForCredential.value) {
        return;
    }

    try {
        const response = await axios.get(
            `/api/compliance/credentials/roles/${roleIdForCredential.value}`,
        );
        roleCredential.value = response.data?.data ?? null;
        credentialVerification.value = null;
    } catch (error) {
        console.error('Failed to export role credential', error);
        roleCredential.value = null;
        credentialVerification.value = null;
    }
};

const verifyRoleCredential = async () => {
    if (!roleIdForCredential.value || !roleCredential.value) {
        return;
    }

    verifyingCredential.value = true;

    try {
        const response = await axios.post(
            `/api/compliance/credentials/roles/${roleIdForCredential.value}/verify`,
            {
                credential: roleCredential.value,
            },
        );

        credentialVerification.value = response.data?.data ?? null;
    } catch (error) {
        console.error('Failed to verify role credential', error);
        credentialVerification.value = null;
    } finally {
        verifyingCredential.value = false;
    }
};

const formatMoney = (amount: number): string => {
    return new Intl.NumberFormat('es-CL', {
        style: 'currency',
        currency: 'USD',
        maximumFractionDigits: 0,
    }).format(amount || 0);
};

const formatPercent = (value: number | null): string => {
    if (value === null || value === undefined) {
        return '0%';
    }

    return `${Math.round(value * 100)}%`;
};

const formatDate = (dateString: string): string => {
    return new Date(dateString).toLocaleString();
};

onMounted(loadDashboard);
</script>

<template>
    <div class="space-y-10" data-testid="compliance-dashboard-root">
        <div
            class="flex flex-col justify-between gap-3 md:flex-row md:items-center"
        >
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-white">
                    Compliance Audit Dashboard
                </h1>
                <p class="text-sm text-white/60">
                    Visión centralizada del Audit Trail para ISO 9001 y
                    gobernanza interna.
                </p>
            </div>
            <StButtonGlass variant="primary" @click="loadDashboard"
                >Actualizar</StButtonGlass
            >
        </div>

        <div
            class="grid grid-cols-1 gap-x-10 gap-y-8 px-12 md:grid-cols-2 xl:grid-cols-4 xl:gap-x-12"
            data-testid="compliance-summary-grid"
            style="padding-bottom: 12px; padding-top: 12px"
            v-if="summary"
        >
            <StCardGlass class="overflow-hidden p-0">
                <div
                    class="flex min-h-[148px] flex-col justify-between px-8 py-7 md:px-10 md:py-8"
                    data-testid="summary-card-content"
                >
                    <p class="text-xs tracking-wider text-white/50 uppercase">
                        Eventos Totales
                    </p>
                    <p class="mt-4 text-3xl font-bold text-white">
                        {{ summary.total_events }}
                    </p>
                </div>
            </StCardGlass>
            <StCardGlass class="overflow-hidden p-0">
                <div
                    class="flex min-h-[148px] flex-col justify-between px-8 py-7 md:px-10 md:py-8"
                >
                    <p class="text-xs tracking-wider text-white/50 uppercase">
                        Eventos (24h)
                    </p>
                    <p class="mt-4 text-3xl font-bold text-white">
                        {{ summary.events_last_24h }}
                    </p>
                </div>
            </StCardGlass>
            <StCardGlass class="overflow-hidden p-0">
                <div
                    class="flex min-h-[148px] flex-col justify-between px-8 py-7 md:px-10 md:py-8"
                >
                    <p class="text-xs tracking-wider text-white/50 uppercase">
                        Tipos de Evento
                    </p>
                    <p class="mt-4 text-3xl font-bold text-white">
                        {{ summary.unique_event_names }}
                    </p>
                </div>
            </StCardGlass>
            <StCardGlass class="overflow-hidden p-0">
                <div
                    class="flex min-h-[148px] flex-col justify-between px-8 py-7 md:px-10 md:py-8"
                >
                    <p class="text-xs tracking-wider text-white/50 uppercase">
                        Agregados Únicos
                    </p>
                    <p class="mt-4 text-3xl font-bold text-white">
                        {{ summary.unique_aggregates }}
                    </p>
                </div>
            </StCardGlass>
        </div>

        <StCardGlass class="overflow-hidden p-0">
            <div
                class="grid grid-cols-1 gap-4 p-6 md:grid-cols-3"
                style="padding: 14px"
            >
                <v-text-field
                    v-model="eventNameFilter"
                    label="Filtrar por event_name"
                    density="compact"
                    variant="outlined"
                    hide-details
                    color="white"
                    class="text-white"
                />
                <v-text-field
                    v-model="aggregateTypeFilter"
                    label="Filtrar por aggregate_type"
                    density="compact"
                    variant="outlined"
                    hide-details
                    color="white"
                    class="text-white"
                />
                <StButtonGlass variant="glass" @click="loadDashboard"
                    >Aplicar filtros</StButtonGlass
                >
            </div>

            <div v-if="loading" class="px-6 py-10 text-center text-white/50">
                Cargando eventos de auditoría...
            </div>
            <div
                v-else-if="events.length === 0"
                class="px-6 py-10 text-center text-white/50"
            >
                No hay eventos para los filtros seleccionados.
            </div>

            <div v-else class="overflow-x-auto px-2 pb-2 md:px-4 md:pb-4">
                <table class="min-w-full divide-y divide-white/10">
                    <thead>
                        <tr
                            class="text-left text-xs tracking-wider text-white/70 uppercase"
                        >
                            <th class="px-4 py-3">Evento</th>
                            <th class="px-4 py-3">Agregado</th>
                            <th class="px-4 py-3">Actor</th>
                            <th class="px-4 py-3">Fecha</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        <tr
                            v-for="item in events"
                            :key="item.id"
                            class="hover:bg-white/5"
                        >
                            <td class="px-4 py-3">
                                <StBadgeGlass variant="glass" size="sm">{{
                                    item.event_name
                                }}</StBadgeGlass>
                            </td>
                            <td class="px-4 py-3 text-sm text-white/80">
                                {{ item.aggregate_type }} #{{
                                    item.aggregate_id
                                }}
                            </td>
                            <td class="px-4 py-3 text-sm text-white/60">
                                {{ item.actor_id ?? 'Sistema' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-white/60">
                                {{ formatDate(item.occurred_at) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </StCardGlass>

        <div
            v-if="iso30414"
            class="grid grid-cols-1 gap-x-10 gap-y-8 px-12 md:grid-cols-2 xl:grid-cols-3 xl:gap-x-12"
            style="padding-top: 12px; padding-bottom: 12px"
        >
            <StCardGlass class="overflow-hidden p-0">
                <div
                    class="flex min-h-[168px] flex-col justify-between px-8 py-7 md:px-10 md:py-8"
                >
                    <p class="text-xs tracking-wider text-white/50 uppercase">
                        Costo Total de Sustitución
                    </p>
                    <div class="space-y-2">
                        <p class="text-3xl font-bold text-white">
                            {{
                                formatMoney(
                                    iso30414.replacement_cost
                                        .total_estimated_replacement_cost,
                                )
                            }}
                        </p>
                        <p class="text-xs text-white/50">
                            Headcount evaluado:
                            {{ iso30414.replacement_cost.total_headcount }}
                        </p>
                    </div>
                </div>
            </StCardGlass>
            <StCardGlass class="overflow-hidden p-0">
                <div
                    class="flex min-h-[168px] flex-col justify-between px-8 py-7 md:px-10 md:py-8"
                >
                    <p class="text-xs tracking-wider text-white/50 uppercase">
                        Costo Promedio por Persona
                    </p>
                    <p class="mt-4 text-3xl font-bold text-white">
                        {{
                            formatMoney(
                                iso30414.replacement_cost
                                    .average_estimated_replacement_cost,
                            )
                        }}
                    </p>
                </div>
            </StCardGlass>
            <StCardGlass class="overflow-hidden p-0">
                <div
                    class="flex min-h-[168px] flex-col justify-between px-8 py-7 md:px-10 md:py-8"
                >
                    <p class="text-xs tracking-wider text-white/50 uppercase">
                        Skills Transversales con Brecha
                    </p>
                    <p class="mt-4 text-3xl font-bold text-white">
                        {{ iso30414.transversal_capability_gaps.length }}
                    </p>
                </div>
            </StCardGlass>
        </div>

        <StCardGlass v-if="iso30414" class="overflow-hidden p-0">
            <div class="border-b border-white/10 px-6 py-5">
                <h3 class="text-sm font-bold text-white">
                    Madurez de Talento por Departamento
                </h3>
            </div>
            <div class="overflow-x-auto px-2 pb-2 md:px-4 md:pb-4">
                <table class="min-w-full divide-y divide-white/10">
                    <thead>
                        <tr
                            class="text-left text-xs tracking-wider text-white/70 uppercase"
                        >
                            <th class="px-4 py-3">Departamento</th>
                            <th class="px-4 py-3">Headcount</th>
                            <th class="px-4 py-3">Readiness</th>
                            <th class="px-4 py-3">Nivel Actual</th>
                            <th class="px-4 py-3">Nivel Requerido</th>
                            <th class="px-4 py-3">Brechas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        <tr
                            v-for="dept in iso30414.talent_maturity_by_department"
                            :key="dept.id"
                            class="hover:bg-white/5"
                        >
                            <td class="px-4 py-3 text-sm text-white/80">
                                {{ dept.name }}
                            </td>
                            <td class="px-4 py-3 text-sm text-white/60">
                                {{ dept.headcount }}
                            </td>
                            <td class="px-4 py-3 text-sm text-white/60">
                                {{ formatPercent(dept.readiness_ratio) }}
                            </td>
                            <td class="px-4 py-3 text-sm text-white/60">
                                {{ dept.avg_current_level ?? 0 }}
                            </td>
                            <td class="px-4 py-3 text-sm text-white/60">
                                {{ dept.avg_required_level ?? 0 }}
                            </td>
                            <td class="px-4 py-3 text-sm text-white/60">
                                {{ dept.gap_records }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </StCardGlass>

        <StCardGlass v-if="iso30414" class="overflow-hidden p-0">
            <div class="border-b border-white/10 px-6 py-5">
                <h3 class="text-sm font-bold text-white">
                    Top Brechas de Capacidades Transversales
                </h3>
            </div>
            <div class="overflow-x-auto px-2 pb-2 md:px-4 md:pb-4">
                <table class="min-w-full divide-y divide-white/10">
                    <thead>
                        <tr
                            class="text-left text-xs tracking-wider text-white/70 uppercase"
                        >
                            <th class="px-4 py-3">Skill</th>
                            <th class="px-4 py-3">Dominio</th>
                            <th class="px-4 py-3">Personas Evaluadas</th>
                            <th class="px-4 py-3">Personas con Brecha</th>
                            <th class="px-4 py-3">Brecha Promedio</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        <tr
                            v-for="gap in iso30414.transversal_capability_gaps"
                            :key="gap.skill_id"
                            class="hover:bg-white/5"
                        >
                            <td class="px-4 py-3 text-sm text-white/80">
                                {{ gap.skill_name }}
                            </td>
                            <td class="px-4 py-3 text-sm text-white/60">
                                {{ gap.domain_tag ?? 'general' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-white/60">
                                {{ gap.assessed_people }}
                            </td>
                            <td class="px-4 py-3 text-sm text-white/60">
                                {{ gap.people_with_gap }}
                            </td>
                            <td class="px-4 py-3 text-sm text-white/60">
                                {{ gap.avg_gap_level }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </StCardGlass>

        <div
            v-if="internalAudit"
            class="grid grid-cols-1 gap-x-10 gap-y-8 px-12 md:grid-cols-3 xl:gap-x-12"
            style="padding-top: 12px; padding-bottom: 12px"
        >
            <StCardGlass class="overflow-hidden p-0">
                <div
                    class="flex min-h-[148px] flex-col justify-between px-8 py-7 md:px-10 md:py-8"
                    style="padding-bottom: 12px"
                >
                    <p class="text-xs tracking-wider text-white/50 uppercase">
                        Roles Críticos
                    </p>
                    <p class="mt-4 text-3xl font-bold text-white">
                        {{ internalAudit.summary.total_critical_roles }}
                    </p>
                </div>
            </StCardGlass>
            <StCardGlass class="overflow-hidden p-0">
                <div
                    class="flex min-h-[148px] flex-col justify-between px-8 py-7 md:px-10 md:py-8"
                >
                    <p class="text-xs tracking-wider text-white/50 uppercase">
                        Roles Cumpliendo
                    </p>
                    <p class="mt-4 text-3xl font-bold text-white">
                        {{ internalAudit.summary.compliant_roles }}
                    </p>
                </div>
            </StCardGlass>
            <StCardGlass class="overflow-hidden p-0">
                <div
                    class="flex min-h-[148px] flex-col justify-between px-8 py-7 md:px-10 md:py-8"
                >
                    <p class="text-xs tracking-wider text-white/50 uppercase">
                        Cumplimiento de Firma
                    </p>
                    <p class="mt-4 text-3xl font-bold text-white">
                        {{ internalAudit.summary.compliance_rate }}%
                    </p>
                </div>
            </StCardGlass>
        </div>

        <StCardGlass v-if="internalAudit" class="overflow-hidden p-0">
            <div
                class="flex flex-col gap-3 border-b border-white/10 px-6 py-5 md:flex-row md:items-center md:justify-between"
            >
                <h3 class="text-sm font-bold text-white">
                    Internal Audit Wizard · Roles Críticos y Firma Vigente
                </h3>
                <div class="flex items-center gap-2">
                    <v-text-field
                        v-model.number="signatureValidDays"
                        type="number"
                        density="compact"
                        variant="outlined"
                        hide-details
                        label="Vigencia (días)"
                        color="white"
                        class="text-white"
                        style="max-width: 180px"
                    />
                    <StButtonGlass variant="glass" @click="loadDashboard"
                        >Recalcular</StButtonGlass
                    >
                </div>
            </div>
            <div class="overflow-x-auto px-2 pb-2 md:px-4 md:pb-4">
                <table class="min-w-full divide-y divide-white/10">
                    <thead>
                        <tr
                            class="text-left text-xs tracking-wider text-white/70 uppercase"
                        >
                            <th class="px-4 py-3">Role</th>
                            <th class="px-4 py-3">Depto</th>
                            <th class="px-4 py-3">Skills críticas</th>
                            <th class="px-4 py-3">Estado firma</th>
                            <th class="px-4 py-3">Edad firma (días)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        <tr
                            v-for="role in internalAudit.roles"
                            :key="role.role_id"
                            class="hover:bg-white/5"
                        >
                            <td class="px-4 py-3 text-sm text-white/80">
                                {{ role.role_name }}
                            </td>
                            <td class="px-4 py-3 text-sm text-white/60">
                                {{ role.department_name ?? 'N/A' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-white/60">
                                {{ role.critical_skills_count }}
                            </td>
                            <td class="px-4 py-3 text-sm text-white/60">
                                {{ role.signature_status }}
                            </td>
                            <td class="px-4 py-3 text-sm text-white/60">
                                {{ role.signature_age_days ?? 'N/A' }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </StCardGlass>

        <StCardGlass class="overflow-hidden p-0">
            <div
                class="flex flex-col gap-3 border-b border-white/10 px-6 py-5 md:flex-row md:items-center md:justify-between"
            >
                <h3 class="text-sm font-bold text-white">
                    Exportación Verifiable Credential (VC/JSON-LD)
                </h3>
                <div class="flex items-center gap-2">
                    <v-text-field
                        v-model.number="roleIdForCredential"
                        type="number"
                        density="compact"
                        variant="outlined"
                        hide-details
                        label="Role ID"
                        color="white"
                        class="text-white"
                        style="max-width: 140px"
                    />
                    <StButtonGlass variant="glass" @click="exportRoleCredential"
                        >Exportar VC</StButtonGlass
                    >
                    <StButtonGlass
                        variant="glass"
                        :disabled="!roleCredential || verifyingCredential"
                        @click="verifyRoleCredential"
                    >
                        {{
                            verifyingCredential
                                ? 'Verificando...'
                                : 'Verificar VC'
                        }}
                    </StButtonGlass>
                </div>
            </div>
            <div v-if="credentialVerification" class="px-6 pt-5">
                <StBadgeGlass
                    :variant="
                        credentialVerification.is_valid ? 'primary' : 'glass'
                    "
                    size="sm"
                >
                    {{
                        credentialVerification.is_valid
                            ? 'VC Válida'
                            : 'VC Inválida'
                    }}
                </StBadgeGlass>
                <div class="mt-2 text-xs text-white/70">
                    checks → model_signature_valid:
                    {{
                        credentialVerification.checks.model_signature_valid
                            ? 'ok'
                            : 'fail'
                    }}
                    · proof_matches_role_signature:
                    {{
                        credentialVerification.checks
                            .proof_matches_role_signature
                            ? 'ok'
                            : 'fail'
                    }}
                    · issuer_matches_expected:
                    {{
                        credentialVerification.checks.issuer_matches_expected
                            ? 'ok'
                            : 'fail'
                    }}
                    · credential_subject_role_matches:
                    {{
                        credentialVerification.checks
                            .credential_subject_role_matches
                            ? 'ok'
                            : 'fail'
                    }}
                </div>
            </div>
            <div v-if="roleCredential" class="p-6">
                <pre
                    class="overflow-x-auto rounded bg-white/5 p-4 text-xs text-white/90"
                    >{{ JSON.stringify(roleCredential, null, 2) }}</pre
                >
            </div>
            <div v-else class="p-6 text-sm text-white/50">
                Ingresa un Role ID para exportar su credencial verificable en
                formato JSON-LD.
            </div>
        </StCardGlass>
    </div>
</template>
