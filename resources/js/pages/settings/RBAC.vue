<script setup lang="ts">
import StButtonGlass from '@/components/StButtonGlass.vue';
import StCardGlass from '@/components/StCardGlass.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import {
    PhChartLineUp,
    PhClipboardText,
    PhCrown,
    PhEye,
    PhFloppyDisk,
    PhGear,
    PhIdentificationCard,
    PhPuzzlePiece,
    PhRobot,
    PhShieldCheck,
    PhShieldStar,
    PhUser,
    PhUserFocus,
    PhUsersThree,
} from '@phosphor-icons/vue';
import axios from 'axios';
import { computed, markRaw, onMounted, ref } from 'vue';

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
    const icons: Record<string, any> = {
        admin: markRaw(PhCrown),
        hr_leader: markRaw(PhUserFocus),
        manager: markRaw(PhUsersThree),
        collaborator: markRaw(PhUser),
        observer: markRaw(PhEye),
    };
    return icons[role] || markRaw(PhUser);
};

const roleColorClasses = (role: string) => {
    const colors: Record<string, string> = {
        admin: 'bg-red-500/10 text-red-400 shadow-[0_0_15px_rgba(239,68,68,0.2)]',
        hr_leader:
            'bg-indigo-500/10 text-indigo-400 shadow-[0_0_15px_rgba(99,102,241,0.2)]',
        manager:
            'bg-teal-500/10 text-teal-400 shadow-[0_0_15px_rgba(20,184,166,0.2)]',
        collaborator:
            'bg-slate-500/10 text-slate-400 shadow-[0_0_15px_rgba(100,116,139,0.2)]',
        observer:
            'bg-amber-500/10 text-amber-400 shadow-[0_0_15px_rgba(245,158,11,0.2)]',
    };
    return colors[role] || 'bg-slate-500/10 text-slate-400';
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
    const icons: Record<string, any> = {
        scenarios: markRaw(PhChartLineUp),
        roles: markRaw(PhIdentificationCard),
        competencies: markRaw(PhPuzzlePiece),
        assessments: markRaw(PhClipboardText),
        people: markRaw(PhUsersThree),
        agents: markRaw(PhRobot),
        settings: markRaw(PhGear),
    };
    return icons[module] || markRaw(PhShieldCheck);
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

    <div class="mx-auto max-w-6xl space-y-6 p-6">
        <div class="mb-6 flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1
                    class="flex items-center text-2xl font-black tracking-tight text-white"
                >
                    <div
                        class="mr-3 rounded-lg border border-indigo-500/20 bg-indigo-500/10 p-2"
                    >
                        <component
                            :is="PhShieldStar"
                            :size="24"
                            class="text-indigo-400"
                            weight="duotone"
                        />
                    </div>
                    Roles y Permisos
                </h1>
                <p class="mt-1 ml-[3.5rem] text-sm text-white/60">
                    Configura las capacidades de cada rol del sistema.
                </p>
            </div>

            <StButtonGlass
                variant="primary"
                :loading="saving"
                :disabled="selectedRole === 'admin'"
                :icon="PhFloppyDisk"
                @click="savePermissions"
            >
                GUARDAR CAMBIOS
            </StButtonGlass>
        </div>

        <div v-if="loading" class="flex justify-center py-12">
            <v-progress-circular
                indeterminate
                color="indigo"
                size="48"
                class="animate-pulse"
            />
        </div>

        <template v-else>
            <!-- Role Selector Tabs -->
            <div class="mb-6 flex flex-wrap gap-3">
                <div
                    v-for="(desc, roleName) in roles"
                    :key="roleName"
                    class="flex cursor-pointer items-center gap-3 rounded-2xl border px-4 py-3 backdrop-blur-md transition-all duration-300"
                    :class="[
                        selectedRole === roleName
                            ? 'border-white/20 bg-white/10 shadow-[0_10px_40px_-10px_rgba(0,0,0,0.5)]'
                            : 'border-white/5 bg-white/5 hover:bg-white/10',
                    ]"
                    @click="selectedRole = roleName"
                >
                    <div
                        class="flex items-center justify-center rounded-lg p-2 transition-all duration-300"
                        :class="[
                            roleColorClasses(roleName).split('shadow-')[0],
                            selectedRole === roleName
                                ? roleColorClasses(roleName).split(' ')[2]
                                : '',
                        ]"
                    >
                        <component
                            :is="roleIcon(roleName)"
                            :size="22"
                            weight="duotone"
                        />
                    </div>
                    <div class="flex flex-col pr-2">
                        <span
                            class="text-sm font-bold tracking-tight text-white capitalize"
                            >{{ roleName.replace('_', ' ') }}</span
                        >
                        <span class="text-xs text-white/50"
                            >{{ permCountForRole(roleName) }} permisos</span
                        >
                    </div>
                </div>
            </div>

            <!-- Admin Notice -->
            <div
                v-if="selectedRole === 'admin'"
                class="mb-6 flex animate-in items-center gap-3 rounded-xl border border-red-500/20 bg-red-500/10 p-4 text-red-300 backdrop-blur-sm duration-500 fade-in slide-in-from-top-2"
            >
                <component :is="PhCrown" :size="24" weight="duotone" />
                <span class="text-sm"
                    >El rol <strong class="text-white">Admin</strong> tiene
                    todos los permisos por defecto y no puede ser
                    modificado.</span
                >
            </div>

            <!-- Permission Matrix -->
            <div
                class="grid animate-in grid-cols-1 gap-5 duration-500 fade-in slide-in-from-bottom-2 md:grid-cols-2 lg:grid-cols-3"
            >
                <StCardGlass
                    v-for="module in modules"
                    :key="module"
                    class="flex flex-col overflow-hidden transition-all duration-300"
                >
                    <div
                        class="relative flex items-center justify-center border-b border-white/10 bg-black/20 p-4"
                    >
                        <div class="flex items-center gap-3">
                            <div
                                class="rounded-md border border-indigo-500/20 bg-indigo-500/10 p-1.5"
                            >
                                <component
                                    :is="moduleIcon(module)"
                                    class="text-indigo-400"
                                    :size="18"
                                    weight="duotone"
                                />
                            </div>
                            <span
                                class="text-sm font-bold tracking-tight text-white/90"
                                >{{ moduleLabel(module) }}</span
                            >
                        </div>
                        <div class="absolute right-4">
                            <v-checkbox
                                :model-value="isModuleAllChecked(module)"
                                :indeterminate="isModulePartial(module)"
                                :disabled="selectedRole === 'admin'"
                                density="compact"
                                hide-details
                                color="indigo-accent-2"
                                @click.stop="toggleModule(module)"
                            />
                        </div>
                    </div>

                    <div class="flex-grow bg-white/[0.01] p-3">
                        <div
                            v-for="perm in permissionsByModule[module]"
                            :key="perm.id"
                            @click="
                                selectedRole !== 'admin'
                                    ? togglePermission(perm.id)
                                    : null
                            "
                            class="flex items-start gap-3 rounded-lg p-2.5 transition-colors duration-200"
                            :class="[
                                selectedRole !== 'admin'
                                    ? 'cursor-pointer hover:bg-white/5'
                                    : 'opacity-80',
                                isChecked(perm.id) ? 'bg-indigo-500/5' : '',
                            ]"
                        >
                            <div class="pt-1">
                                <v-checkbox
                                    :model-value="
                                        selectedRole === 'admin' ||
                                        isChecked(perm.id)
                                    "
                                    :disabled="selectedRole === 'admin'"
                                    density="compact"
                                    hide-details
                                    color="indigo-accent-2"
                                    @click.stop="togglePermission(perm.id)"
                                />
                            </div>
                            <div class="flex flex-col pt-1 pb-1">
                                <span
                                    class="mb-0.5 font-mono text-[10px] tracking-widest text-[#818cf8] uppercase"
                                >
                                    {{ perm.name }}
                                </span>
                                <span
                                    class="text-sm leading-tight text-white/70"
                                >
                                    {{ perm.description }}
                                </span>
                            </div>
                        </div>
                    </div>
                </StCardGlass>
            </div>
        </template>

        <v-snackbar
            v-model="snackbar"
            :color="snackbarColor"
            timeout="3000"
            location="top"
            variant="elevated"
        >
            {{ snackbarMsg }}
        </v-snackbar>
    </div>
</template>

<style scoped>
/* Scoped styles are mostly replaced by Tailwind CSS utilities in the template */
/* Ensuring checkboxes align correctly without Vuetify's extra margins */
:deep(.v-input--density-compact .v-input__control) {
    min-height: auto;
}
:deep(.v-selection-control) {
    min-height: auto;
}
</style>
