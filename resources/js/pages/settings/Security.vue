<script setup lang="ts">
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { computed, onMounted, ref } from 'vue';

interface Permission {
    id: number;
    name: string;
    module: string;
    description: string;
}

interface RbacData {
    roles: Record<string, string>;
    permissions: Permission[];
    mappings: Record<string, number[]>;
}

interface SecurityAccessLog {
    id: number;
    user_id: number | null;
    organization_id: number | null;
    event: string;
    email: string | null;
    ip_address: string | null;
    user_agent: string | null;
    role: string | null;
    mfa_used: boolean;
    occurred_at: string;
    user?: {
        id: number;
        name: string;
        email: string;
    } | null;
}

interface SecuritySummary {
    total_events: number;
    events_last_24h: number;
    successful_logins: number;
    failed_logins: number;
    logouts: number;
    failed_logins_24h: number;
    mfa_used_percentage: number;
    top_ips: Record<string, number>;
    events_by_type: Record<string, number>;
}

const loading = ref(true);
const saving = ref(false);
const data = ref<RbacData | null>(null);
const activeRole = ref<string>('');
const selectedPermissions = ref<number[]>([]);
const search = ref('');
const alert = ref<{
    show: boolean;
    type: 'success' | 'error' | 'info' | 'warning';
    message: string;
}>({
    show: false,
    type: 'success',
    message: '',
});

const securityLoading = ref(false);
const securitySummary = ref<SecuritySummary | null>(null);
const securityLogs = ref<SecurityAccessLog[]>([]);
const securityPage = ref(1);
const securityLastPage = ref(1);
const securityPerPage = ref(25);
const securityTotal = ref(0);
const securityFilters = ref({
    event: '',
    email: '',
    from: '',
    to: '',
});

const securityEventOptions = [
    { title: 'Todos', value: '' },
    { title: 'Login exitoso', value: 'login' },
    { title: 'Logout', value: 'logout' },
    { title: 'Login fallido', value: 'login_failed' },
];

const fetchRbac = async () => {
    try {
        loading.value = true;
        const response = await axios.get('/api/rbac');
        data.value = response.data;

        // Select first role by default
        if (data.value && Object.keys(data.value.roles).length > 0) {
            selectRole(Object.keys(data.value.roles)[0]);
        }
    } catch (error) {
        console.error('Error fetching RBAC:', error);
    } finally {
        loading.value = false;
    }
};

const selectRole = (role: string) => {
    activeRole.value = role;
    if (data.value) {
        selectedPermissions.value = [...(data.value.mappings[role] || [])];
    }
};

const savePermissions = async () => {
    if (!activeRole.value) return;

    try {
        saving.value = true;
        await axios.post('/api/rbac', {
            role: activeRole.value,
            permissions: selectedPermissions.value,
        });

        // Update local mappings
        if (data.value) {
            data.value.mappings[activeRole.value] = [
                ...selectedPermissions.value,
            ];
        }

        alert.value = {
            show: true,
            type: 'success',
            message: `Permisos actualizados correctamente para ${activeRole.value}`,
        };
    } catch (error) {
        console.error('Error saving permissions:', error);
        alert.value = {
            show: true,
            type: 'error',
            message: 'Error al actualizar los permisos.',
        };
    } finally {
        saving.value = false;
        setTimeout(() => (alert.value.show = false), 3000);
    }
};

const modules = computed(() => {
    if (!data.value) return [];
    const mods = [...new Set(data.value.permissions.map((p) => p.module))];
    return mods.sort();
});

const filteredPermissions = (module: string) => {
    if (!data.value) return [];
    return data.value.permissions.filter(
        (p) =>
            p.module === module &&
            (p.name.toLowerCase().includes(search.value.toLowerCase()) ||
                p.description
                    ?.toLowerCase()
                    .includes(search.value.toLowerCase())),
    );
};

const toggleAllModule = (module: string, event: any) => {
    const modulePerms = filteredPermissions(module).map((p) => p.id);
    if (event.target.checked) {
        selectedPermissions.value = [
            ...new Set([...selectedPermissions.value, ...modulePerms]),
        ];
    } else {
        selectedPermissions.value = selectedPermissions.value.filter(
            (id) => !modulePerms.includes(id),
        );
    }
};

const isModuleFullySelected = (module: string) => {
    const modulePerms = filteredPermissions(module).map((p) => p.id);
    return modulePerms.every((id) => selectedPermissions.value.includes(id));
};

const buildSecurityQuery = (page = 1) => {
    const params = new URLSearchParams();

    params.set('page', String(page));
    params.set('per_page', String(securityPerPage.value));

    if (securityFilters.value.event) {
        params.set('event', securityFilters.value.event);
    }

    if (securityFilters.value.email) {
        params.set('email', securityFilters.value.email);
    }

    if (securityFilters.value.from) {
        params.set('from', securityFilters.value.from);
    }

    if (securityFilters.value.to) {
        params.set('to', securityFilters.value.to);
    }

    return params.toString();
};

const fetchSecuritySummary = async () => {
    const response = await axios.get('/api/security/access-logs/summary');
    securitySummary.value = response.data?.data ?? null;
};

const fetchSecurityLogs = async (page = 1) => {
    const query = buildSecurityQuery(page);
    const response = await axios.get(`/api/security/access-logs?${query}`);

    const payload = response.data?.data ?? {};
    securityLogs.value = payload.data ?? [];
    securityPage.value = payload.current_page ?? 1;
    securityLastPage.value = payload.last_page ?? 1;
    securityPerPage.value = payload.per_page ?? securityPerPage.value;
    securityTotal.value = payload.total ?? securityLogs.value.length;
};

const fetchSecurityData = async (page = 1) => {
    try {
        securityLoading.value = true;
        await Promise.all([fetchSecuritySummary(), fetchSecurityLogs(page)]);
    } catch (error) {
        console.error('Error loading security access logs:', error);
        alert.value = {
            show: true,
            type: 'error',
            message: 'Error al cargar el monitoreo de seguridad.',
        };
    } finally {
        securityLoading.value = false;
    }
};

const applySecurityFilters = async () => {
    await fetchSecurityData(1);
};

const resetSecurityFilters = async () => {
    securityFilters.value = {
        event: '',
        email: '',
        from: '',
        to: '',
    };
    await fetchSecurityData(1);
};

const formatDateTime = (value: string) => {
    if (!value) return '-';

    return new Date(value).toLocaleString('es-MX', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const eventLabel = (event: string) => {
    const labels: Record<string, string> = {
        login: 'Inicio de sesión',
        logout: 'Cierre de sesión',
        login_failed: 'Intento fallido',
    };

    return labels[event] ?? event;
};

const eventColor = (event: string) => {
    const colors: Record<string, string> = {
        login: 'success',
        logout: 'info',
        login_failed: 'error',
    };

    return colors[event] ?? 'primary';
};

onMounted(async () => {
    await fetchRbac();
    await fetchSecurityData();
});

defineOptions({ layout: SettingsLayout });
</script>

<template>
    <SettingsLayout>
        <Head title="Seguridad y RBAC">
            <title>Seguridad y RBAC | Stratos</title>
        </Head>

        <v-container fluid class="pa-0">
            <div class="mb-6">
                <h2 class="text-h4 font-weight-black mb-2">
                    Seguridad y Roles
                </h2>
                <p class="text-body-1 text-grey-darken-1">
                    Gestiona los permisos de acceso para los diferentes roles
                    del sistema.
                </p>
            </div>

            <v-alert
                v-if="alert.show"
                :type="alert.type"
                variant="tonal"
                closable
                class="animate-fade-in mb-6"
            >
                {{ alert.message }}
            </v-alert>

            <v-row v-if="loading">
                <v-col cols="12" class="py-10 text-center">
                    <v-progress-circular
                        indeterminate
                        color="primary"
                        size="64"
                    />
                </v-col>
            </v-row>

            <v-row v-else-if="data">
                <!-- Roles Sidebar -->
                <v-col cols="12" md="4">
                    <v-card border flat class="overflow-hidden rounded-lg">
                        <v-list density="comfortable" class="pa-2">
                            <v-list-subheader
                                class="font-weight-bold text-uppercase"
                                >Roles del Sistema</v-list-subheader
                            >
                            <v-list-item
                                v-for="(desc, role) in data.roles"
                                :key="role"
                                :active="activeRole === role"
                                color="primary"
                                class="mb-1 rounded-lg"
                                @click="selectRole(role)"
                            >
                                <template v-slot:prepend>
                                    <v-icon
                                        :icon="
                                            role === 'admin'
                                                ? 'mdi-shield-check'
                                                : 'mdi-account-group'
                                        "
                                    />
                                </template>
                                <v-list-item-title class="font-weight-bold">{{
                                    role.toUpperCase()
                                }}</v-list-item-title>
                                <v-list-item-subtitle class="text-wrap">{{
                                    desc.split(' — ')[1] || desc
                                }}</v-list-item-subtitle>
                            </v-list-item>
                        </v-list>

                        <v-divider />

                        <div class="pa-4 bg-grey-lighten-5">
                            <v-alert
                                type="warning"
                                variant="tonal"
                                density="compact"
                                text="El rol ADMIN tiene acceso total ignorando estas asignaciones."
                                class="text-caption"
                            />
                        </div>
                    </v-card>
                </v-col>

                <!-- Permissions Grid -->
                <v-col cols="12" md="8">
                    <v-card
                        border
                        flat
                        class="d-flex flex-column h-100 rounded-lg"
                    >
                        <div
                            class="pa-4 d-flex align-center flex-wrap gap-4 border-b"
                        >
                            <h3 class="text-h6 font-weight-bold">
                                Permisos para:
                                <span class="text-primary">{{
                                    activeRole.toUpperCase()
                                }}</span>
                            </h3>
                            <v-spacer />
                            <v-text-field
                                v-model="search"
                                label="Buscar permiso..."
                                prepend-inner-icon="mdi-magnify"
                                hide-details
                                density="compact"
                                variant="outlined"
                                class="max-width-300"
                            />
                            <v-btn
                                color="primary"
                                prepend-icon="mdi-content-save"
                                :loading="saving"
                                :disabled="activeRole === 'admin'"
                                @click="savePermissions"
                            >
                                Guardar Cambios
                            </v-btn>
                        </div>

                        <div
                            class="pa-4 flex-grow-1 overflow-auto"
                            style="max-height: 600px"
                        >
                            <div
                                v-for="module in modules"
                                :key="module"
                                class="mb-8"
                            >
                                <div
                                    class="d-flex align-center mb-4 border-b pb-2"
                                >
                                    <v-icon color="primary" class="mr-2"
                                        >mdi-cube-outline</v-icon
                                    >
                                    <h4
                                        class="text-subtitle-1 font-weight-black text-uppercase"
                                    >
                                        {{ module }}
                                    </h4>
                                    <v-spacer />
                                    <v-checkbox
                                        :model-value="
                                            isModuleFullySelected(module)
                                        "
                                        label="Seleccionar todo"
                                        density="compact"
                                        hide-details
                                        color="primary"
                                        @change="
                                            toggleAllModule(module, $event)
                                        "
                                        :disabled="activeRole === 'admin'"
                                    />
                                </div>

                                <v-row>
                                    <v-col
                                        v-for="perm in filteredPermissions(
                                            module,
                                        )"
                                        :key="perm.id"
                                        cols="12"
                                        sm="6"
                                    >
                                        <v-hover v-slot="{ isHovering, props }">
                                            <v-card
                                                v-bind="props"
                                                border
                                                flat
                                                class="pa-3 rounded-lg transition-all"
                                                :class="
                                                    isHovering
                                                        ? 'bg-blue-lighten-5'
                                                        : ''
                                                "
                                            >
                                                <v-checkbox
                                                    v-model="
                                                        selectedPermissions
                                                    "
                                                    :value="perm.id"
                                                    :label="
                                                        perm.name
                                                            .split('.')[1]
                                                            .replace('_', ' ')
                                                            .toUpperCase()
                                                    "
                                                    :hint="perm.description"
                                                    persistent-hint
                                                    density="comfortable"
                                                    color="primary"
                                                    hide-details
                                                    :disabled="
                                                        activeRole === 'admin'
                                                    "
                                                />
                                                <div
                                                    class="text-caption text-grey-darken-1 mt-1 ml-10"
                                                >
                                                    {{
                                                        perm.description ||
                                                        'Sin descripción'
                                                    }}
                                                </div>
                                            </v-card>
                                        </v-hover>
                                    </v-col>
                                </v-row>
                            </div>
                        </div>
                    </v-card>
                </v-col>

                <v-col cols="12" class="mt-2">
                    <v-card border flat class="rounded-lg">
                        <div
                            class="pa-4 d-flex align-center flex-wrap gap-4 border-b"
                        >
                            <h3 class="text-h6 font-weight-bold">
                                Monitoreo de Accesos (Admin)
                            </h3>
                            <v-spacer />
                            <v-btn
                                color="primary"
                                variant="tonal"
                                prepend-icon="mdi-refresh"
                                :loading="securityLoading"
                                @click="fetchSecurityData(securityPage)"
                            >
                                Actualizar
                            </v-btn>
                        </div>

                        <div class="pa-4">
                            <v-progress-linear
                                v-if="securityLoading"
                                indeterminate
                                color="primary"
                                class="mb-4"
                            />

                            <v-row class="mb-2">
                                <v-col cols="12" sm="6" md="3">
                                    <v-card border flat class="pa-3 rounded-lg">
                                        <div
                                            class="text-caption text-grey-darken-1"
                                        >
                                            Total eventos
                                        </div>
                                        <div class="text-h6 font-weight-bold">
                                            {{
                                                securitySummary?.total_events ??
                                                0
                                            }}
                                        </div>
                                    </v-card>
                                </v-col>
                                <v-col cols="12" sm="6" md="3">
                                    <v-card border flat class="pa-3 rounded-lg">
                                        <div
                                            class="text-caption text-grey-darken-1"
                                        >
                                            Logins fallidos (24h)
                                        </div>
                                        <div
                                            class="text-h6 font-weight-bold text-error"
                                        >
                                            {{
                                                securitySummary?.failed_logins_24h ??
                                                0
                                            }}
                                        </div>
                                    </v-card>
                                </v-col>
                                <v-col cols="12" sm="6" md="3">
                                    <v-card border flat class="pa-3 rounded-lg">
                                        <div
                                            class="text-caption text-grey-darken-1"
                                        >
                                            Uso MFA en logins
                                        </div>
                                        <div class="text-h6 font-weight-bold">
                                            {{
                                                securitySummary?.mfa_used_percentage ??
                                                0
                                            }}%
                                        </div>
                                    </v-card>
                                </v-col>
                                <v-col cols="12" sm="6" md="3">
                                    <v-card border flat class="pa-3 rounded-lg">
                                        <div
                                            class="text-caption text-grey-darken-1"
                                        >
                                            Eventos últimas 24h
                                        </div>
                                        <div class="text-h6 font-weight-bold">
                                            {{
                                                securitySummary?.events_last_24h ??
                                                0
                                            }}
                                        </div>
                                    </v-card>
                                </v-col>
                            </v-row>

                            <v-row class="mb-2">
                                <v-col cols="12" md="3">
                                    <v-select
                                        v-model="securityFilters.event"
                                        :items="securityEventOptions"
                                        label="Tipo de evento"
                                        density="compact"
                                        variant="outlined"
                                        hide-details
                                    />
                                </v-col>
                                <v-col cols="12" md="3">
                                    <v-text-field
                                        v-model="securityFilters.email"
                                        label="Email"
                                        density="compact"
                                        variant="outlined"
                                        hide-details
                                    />
                                </v-col>
                                <v-col cols="12" md="2">
                                    <v-text-field
                                        v-model="securityFilters.from"
                                        label="Desde"
                                        type="date"
                                        density="compact"
                                        variant="outlined"
                                        hide-details
                                    />
                                </v-col>
                                <v-col cols="12" md="2">
                                    <v-text-field
                                        v-model="securityFilters.to"
                                        label="Hasta"
                                        type="date"
                                        density="compact"
                                        variant="outlined"
                                        hide-details
                                    />
                                </v-col>
                                <v-col cols="12" md="2" class="d-flex gap-2">
                                    <v-btn
                                        color="primary"
                                        block
                                        @click="applySecurityFilters"
                                    >
                                        Filtrar
                                    </v-btn>
                                    <v-btn
                                        variant="tonal"
                                        block
                                        @click="resetSecurityFilters"
                                    >
                                        Limpiar
                                    </v-btn>
                                </v-col>
                            </v-row>

                            <v-table
                                density="compact"
                                class="rounded-lg border"
                            >
                                <thead>
                                    <tr>
                                        <th scope="col">Fecha</th>
                                        <th scope="col">Evento</th>
                                        <th scope="col">Usuario / Email</th>
                                        <th scope="col">Rol</th>
                                        <th scope="col">IP</th>
                                        <th scope="col">MFA</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="!securityLogs.length">
                                        <td
                                            colspan="6"
                                            class="text-grey-darken-1 py-6 text-center"
                                        >
                                            Sin eventos para los filtros
                                            seleccionados.
                                        </td>
                                    </tr>
                                    <tr
                                        v-for="log in securityLogs"
                                        :key="log.id"
                                    >
                                        <td>
                                            {{
                                                formatDateTime(log.occurred_at)
                                            }}
                                        </td>
                                        <td>
                                            <v-chip
                                                size="small"
                                                :color="eventColor(log.event)"
                                                variant="tonal"
                                            >
                                                {{ eventLabel(log.event) }}
                                            </v-chip>
                                        </td>
                                        <td>
                                            {{ log.user?.name || '-' }}<br />
                                            {{ log.email || '-' }}
                                        </td>
                                        <td>{{ log.role || '-' }}</td>
                                        <td>{{ log.ip_address || '-' }}</td>
                                        <td>
                                            <v-chip
                                                size="small"
                                                :color="
                                                    log.mfa_used
                                                        ? 'success'
                                                        : 'grey'
                                                "
                                                variant="tonal"
                                            >
                                                {{ log.mfa_used ? 'Sí' : 'No' }}
                                            </v-chip>
                                        </td>
                                    </tr>
                                </tbody>
                            </v-table>

                            <div
                                class="d-flex align-center justify-space-between mt-4"
                            >
                                <span class="text-caption text-grey-darken-1">
                                    Mostrando {{ securityLogs.length }} de
                                    {{ securityTotal }} eventos
                                </span>
                                <v-pagination
                                    v-model="securityPage"
                                    :length="securityLastPage"
                                    density="comfortable"
                                    @update:model-value="fetchSecurityData"
                                />
                            </div>
                        </div>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </SettingsLayout>
</template>

<style scoped>
.max-width-300 {
    max-width: 300px;
}

.transition-all {
    transition: all 0.2s ease-out;
}

.animate-fade-in {
    animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
