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

onMounted(fetchRbac);

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
