<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { computed, onMounted, ref } from 'vue';

interface PermissionItem {
    id: number;
    name: string;
    module: string;
    action: string;
    description: string;
}

const loading = ref(true);
const saving = ref(false);
const roles = ref<Record<string, string>>({});
const permissions = ref<PermissionItem[]>([]);
const mappings = ref<Record<string, number[]>>({});
const selectedRole = ref<string>('');
const snackbar = ref(false);
const snackbarMsg = ref('');
const snackbarColor = ref('success');

const fetchData = async () => {
    loading.value = true;
    try {
        const { data } = await axios.get('/api/rbac');
        roles.value = data.roles;
        permissions.value = data.permissions;
        mappings.value = data.mappings;

        if (!selectedRole.value && Object.keys(data.roles).length > 0) {
            selectedRole.value = Object.keys(data.roles)[0];
        }
    } catch {
        snackbarMsg.value = 'Error al cargar datos RBAC';
        snackbarColor.value = 'error';
        snackbar.value = true;
    } finally {
        loading.value = false;
    }
};

// Group permissions by module
const permissionsByModule = computed(() => {
    const grouped: Record<string, PermissionItem[]> = {};
    for (const p of permissions.value) {
        if (!grouped[p.module]) grouped[p.module] = [];
        grouped[p.module].push(p);
    }
    return grouped;
});

const modules = computed(() => Object.keys(permissionsByModule.value).sort());

const isChecked = (permId: number): boolean => {
    return (mappings.value[selectedRole.value] ?? []).includes(permId);
};

const togglePermission = (permId: number) => {
    if (selectedRole.value === 'admin') return; // Admin always has everything
    const current = mappings.value[selectedRole.value] ?? [];
    if (current.includes(permId)) {
        mappings.value[selectedRole.value] = current.filter(
            (id: number) => id !== permId,
        );
    } else {
        mappings.value[selectedRole.value] = [...current, permId];
    }
};

const toggleModule = (module: string) => {
    if (selectedRole.value === 'admin') return;
    const modulePerms = permissionsByModule.value[module] ?? [];
    const permIds = modulePerms.map((p) => p.id);
    const current = mappings.value[selectedRole.value] ?? [];
    const allChecked = permIds.every((id) => current.includes(id));

    if (allChecked) {
        mappings.value[selectedRole.value] = current.filter(
            (id: number) => !permIds.includes(id),
        );
    } else {
        const newPerms = new Set([...current, ...permIds]);
        mappings.value[selectedRole.value] = Array.from(newPerms);
    }
};

const isModuleAllChecked = (module: string): boolean => {
    const modulePerms = permissionsByModule.value[module] ?? [];
    const current = mappings.value[selectedRole.value] ?? [];
    return modulePerms.every((p) => current.includes(p.id));
};

const isModulePartial = (module: string): boolean => {
    const modulePerms = permissionsByModule.value[module] ?? [];
    const current = mappings.value[selectedRole.value] ?? [];
    const count = modulePerms.filter((p) => current.includes(p.id)).length;
    return count > 0 && count < modulePerms.length;
};

const savePermissions = async () => {
    saving.value = true;
    try {
        await axios.post('/api/rbac', {
            role: selectedRole.value,
            permissions: mappings.value[selectedRole.value] ?? [],
        });
        snackbarMsg.value = 'Permisos guardados correctamente';
        snackbarColor.value = 'success';
        snackbar.value = true;
    } catch {
        snackbarMsg.value = 'Error al guardar los permisos';
        snackbarColor.value = 'error';
        snackbar.value = true;
    } finally {
        saving.value = false;
    }
};

const roleIcon = (role: string) => {
    const icons: Record<string, string> = {
        admin: 'mdi-shield-crown',
        hr_leader: 'mdi-account-tie',
        manager: 'mdi-account-supervisor',
        collaborator: 'mdi-account',
        observer: 'mdi-eye',
    };
    return icons[role] || 'mdi-account';
};

const roleColor = (role: string) => {
    const colors: Record<string, string> = {
        admin: 'red',
        hr_leader: 'blue',
        manager: 'teal',
        collaborator: 'grey',
        observer: 'amber',
    };
    return colors[role] || 'grey';
};

const moduleLabel = (module: string) => {
    const labels: Record<string, string> = {
        scenarios: 'Planeación Estratégica',
        roles: 'Roles de Negocio',
        competencies: 'Competencias & Skills',
        assessments: 'Evaluación 360°',
        people: 'Talento / Personas',
        agents: 'Agentes AI',
        settings: 'Configuración del Sistema',
    };
    return labels[module] || module.charAt(0).toUpperCase() + module.slice(1);
};

const moduleIcon = (module: string) => {
    const icons: Record<string, string> = {
        scenarios: 'mdi-chart-timeline-variant',
        roles: 'mdi-badge-account',
        competencies: 'mdi-puzzle',
        assessments: 'mdi-clipboard-check',
        people: 'mdi-account-group',
        agents: 'mdi-robot',
        settings: 'mdi-cog',
    };
    return icons[module] || 'mdi-key';
};

const permCountForRole = (roleName: string) => {
    if (roleName === 'admin') return permissions.value.length;
    return (mappings.value[roleName] ?? []).length;
};

onMounted(fetchData);

defineOptions({ layout: AppLayout });
</script>

<template>
    <Head title="Roles y Permisos — RBAC" />

    <div class="rbac-container">
        <div class="rbac-header">
            <div>
                <h1 class="rbac-title">
                    <v-icon class="mr-2" color="primary" size="28"
                        >mdi-shield-lock</v-icon
                    >
                    Roles y Permisos
                </h1>
                <p class="rbac-subtitle">
                    Configura las capacidades de cada rol del sistema.
                </p>
            </div>
            <v-btn
                :loading="saving"
                :disabled="selectedRole === 'admin'"
                color="primary"
                variant="flat"
                prepend-icon="mdi-content-save"
                @click="savePermissions"
            >
                Guardar Cambios
            </v-btn>
        </div>

        <div v-if="loading" class="py-12 text-center">
            <v-progress-circular indeterminate color="primary" size="48" />
        </div>

        <template v-else>
            <!-- Role Selector Tabs -->
            <div class="role-tabs mb-6">
                <div
                    v-for="(desc, roleName) in roles"
                    :key="roleName"
                    class="role-tab"
                    :class="{ active: selectedRole === roleName }"
                    @click="selectedRole = roleName"
                >
                    <v-icon
                        :icon="roleIcon(roleName)"
                        :color="roleColor(roleName)"
                        size="20"
                    />
                    <div class="role-tab-info">
                        <span class="role-tab-name">{{
                            roleName.replace('_', ' ')
                        }}</span>
                        <span class="role-tab-count"
                            >{{ permCountForRole(roleName) }} permisos</span
                        >
                    </div>
                </div>
            </div>

            <!-- Admin Notice -->
            <v-alert
                v-if="selectedRole === 'admin'"
                type="info"
                variant="tonal"
                class="mb-4"
                density="compact"
                icon="mdi-shield-crown"
            >
                El rol <strong>Administrador</strong> tiene todos los permisos
                por defecto y no puede ser modificado.
            </v-alert>

            <!-- Permission Matrix -->
            <div class="permissions-grid">
                <v-card
                    v-for="module in modules"
                    :key="module"
                    class="module-card"
                    variant="outlined"
                >
                    <v-card-title class="module-header">
                        <div class="d-flex align-center flex-grow-1 gap-2">
                            <v-icon
                                :icon="moduleIcon(module)"
                                color="primary"
                                size="20"
                            />
                            <span>{{ moduleLabel(module) }}</span>
                        </div>
                        <v-checkbox
                            :model-value="isModuleAllChecked(module)"
                            :indeterminate="isModulePartial(module)"
                            :disabled="selectedRole === 'admin'"
                            density="compact"
                            hide-details
                            color="primary"
                            @click.stop="toggleModule(module)"
                        />
                    </v-card-title>
                    <v-divider />
                    <v-card-text class="pa-0">
                        <v-list density="compact" bg-color="transparent">
                            <v-list-item
                                v-for="perm in permissionsByModule[module]"
                                :key="perm.id"
                                @click="togglePermission(perm.id)"
                                class="perm-row"
                                :class="{ 'perm-active': isChecked(perm.id) }"
                            >
                                <template #prepend>
                                    <v-checkbox
                                        :model-value="
                                            selectedRole === 'admin' ||
                                            isChecked(perm.id)
                                        "
                                        :disabled="selectedRole === 'admin'"
                                        density="compact"
                                        hide-details
                                        color="primary"
                                        @click.stop="togglePermission(perm.id)"
                                    />
                                </template>
                                <v-list-item-title class="perm-name">{{
                                    perm.name
                                }}</v-list-item-title>
                                <v-list-item-subtitle>{{
                                    perm.description
                                }}</v-list-item-subtitle>
                            </v-list-item>
                        </v-list>
                    </v-card-text>
                </v-card>
            </div>
        </template>

        <v-snackbar v-model="snackbar" :color="snackbarColor" timeout="3000">
            {{ snackbarMsg }}
        </v-snackbar>
    </div>
</template>

<style scoped>
.rbac-container {
    padding: 24px;
    max-width: 1100px;
    margin: 0 auto;
}

.rbac-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 16px;
}

.rbac-title {
    font-size: 1.6rem;
    font-weight: 700;
    display: flex;
    align-items: center;
}

.rbac-subtitle {
    color: rgba(255, 255, 255, 0.6);
    margin-top: 4px;
    font-size: 0.9rem;
}

/* Role Tabs */
.role-tabs {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.role-tab {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 18px;
    border-radius: 12px;
    border: 1px solid rgba(255, 255, 255, 0.1);
    background: rgba(255, 255, 255, 0.03);
    cursor: pointer;
    transition: all 0.2s ease;
}

.role-tab:hover {
    background: rgba(255, 255, 255, 0.07);
}

.role-tab.active {
    background: rgba(103, 58, 183, 0.15);
    border-color: rgba(103, 58, 183, 0.4);
    box-shadow: 0 2px 12px rgba(103, 58, 183, 0.15);
}

.role-tab-info {
    display: flex;
    flex-direction: column;
}

.role-tab-name {
    font-weight: 600;
    font-size: 0.85rem;
    text-transform: capitalize;
}

.role-tab-count {
    font-size: 0.75rem;
    color: rgba(255, 255, 255, 0.5);
}

/* Permission Grid */
.permissions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 16px;
}

.module-card {
    border-radius: 12px !important;
    border-color: rgba(255, 255, 255, 0.1) !important;
    background: rgba(255, 255, 255, 0.02) !important;
}

.module-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 0.95rem;
    font-weight: 600;
    padding: 12px 16px;
}

.perm-row {
    transition: background 0.15s ease;
    cursor: pointer;
}

.perm-row:hover {
    background: rgba(255, 255, 255, 0.04);
}

.perm-row.perm-active {
    background: rgba(103, 58, 183, 0.06);
}

.perm-name {
    font-weight: 500;
    font-family: 'Roboto Mono', monospace;
    font-size: 0.85rem;
}
</style>
