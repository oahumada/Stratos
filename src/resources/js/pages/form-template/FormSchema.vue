<script setup lang="ts">
import { ref, reactive, computed, onMounted } from "vue";
import { post, put, remove, fetchCatalogs } from "@/apiHelper";
import { useNotification } from "@kyvg/vue3-notification";
import { useTheme as useVuetifyTheme } from 'vuetify';
import FormData from "./FormData.vue";
import { usePage } from "@inertiajs/vue3";

interface TableHeader {
    key: string;
    text: string;
    value: string;
    type?: 'date' | 'text' | 'number';
    sortable?: boolean;
    filterable?: boolean;
}

interface FormField {
    key: string;
    type: 'text' | 'select' | 'date' | 'time' | 'switch' | 'checkbox' | 'number' | 'email' | 'password' | 'textarea';
    label: string;
    required?: boolean;
    rules?: Array<any>;
    placeholder?: string;
    items?: any[];
}

interface CatalogItem {
    id: number | string;
    name: string;
}

interface FilterConfig {
    field: string;
    type: 'text' | 'select' | 'date';
    label: string;
    items?: any[];
    placeholder?: string;
}

interface Config {
    endpoints: {
        index: string;
        apiUrl: string;
    };
    titulo: string;
    descripcion?: string;
    permisos?: {
        crear: boolean;
        editar: boolean;
        eliminar: boolean;
    };
}

interface TableConfig {
    headers: TableHeader[];
    options?: Record<string, any>;
}

interface ItemForm {
    fields: FormField[];
    catalogs?: any[];
    layout?: string;
}

interface TableItem {
    id: number;
    [key: string]: any;
}

const page = usePage();
const user = computed(() => (page.props.auth.user as any));
const { notify } = useNotification();
const vuetifyTheme = useVuetifyTheme();

// Convertir hex a RGB
const hexToRgb = (hex: string): string => {
    const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})/i.exec(hex);
    return result ? `${parseInt(result[1], 16)}, ${parseInt(result[2], 16)}, ${parseInt(result[3], 16)}` : '0, 0, 0';
};

// Get theme colors reactively
const headerGradient = computed(() => {
    const theme = vuetifyTheme.global.current.value;
    return `linear-gradient(135deg, ${theme.colors.primary} 0%, ${theme.colors.secondary} 100%)`;
});

const createBtnGradient = computed(() => {
    const theme = vuetifyTheme.global.current.value;
    return `linear-gradient(135deg, ${theme.colors.accent} 0%, ${theme.colors.error} 100%)`;
});

const tableHeaderGradient = computed(() => {
    const theme = vuetifyTheme.global.current.value;
    return `linear-gradient(135deg, ${theme.colors.primary} 0%, ${theme.colors.secondary} 100%)`;
});

const dialogHeaderGradient = computed(() => {
    const theme = vuetifyTheme.global.current.value;
    return `linear-gradient(135deg, ${theme.colors.primary} 0%, ${theme.colors.secondary} 100%)`;
});

const dialogWarningGradient = computed(() => {
    const theme = vuetifyTheme.global.current.value;
    return `linear-gradient(135deg, ${theme.colors.accent} 0%, ${theme.colors.error} 100%)`;
});

const props = withDefaults(
    defineProps<{
        peopleId?: number;
        config?: Config;
        tableConfig?: TableConfig;
        itemForm?: ItemForm;
        filters?: FilterConfig[];
    }>(),
    {
        config: () => ({
            endpoints: { index: "", apiUrl: "" },
            titulo: "Registros",
            descripcion: "Manage your records",
            permisos: { crear: true, editar: true, eliminar: true },
        }),
        tableConfig: () => ({
            headers: [],
            options: { pagination: true, search: true },
        }),
        itemForm: () => ({
            fields: [],
            catalogs: [] as any[],
            layout: "single-column",
        }),
        filters: () => [],
    }
);

// Merged configs
const mergedConfig = computed<Config>(() => {
    const baseConfig: Config = {
        endpoints: { index: "", apiUrl: "" },
        titulo: "Registros",
        descripcion: "Manage your records",
        permisos: { crear: true, editar: true, eliminar: true },
    };
    return { ...baseConfig, ...(props.config || {}) };
});

const mergedTableConfig = computed<TableConfig>(() => {
    const baseTableConfig: TableConfig = {
        headers: [],
        options: { pagination: true, search: true },
    };
    return { ...baseTableConfig, ...(props.tableConfig || {}) };
});

const mergedItemForm = computed<ItemForm>(() => {
    const baseItemForm: ItemForm = {
        fields: [],
        catalogs: [] as any[],
        layout: "single-column",
    };
    return { ...baseItemForm, ...(props.itemForm || {}) };
});

// State
const items = ref<TableItem[]>([]);
const loading = ref(false);
const saving = ref(false);
const error = ref<string | null>(null);
const dialogOpen = ref(false);
const deleteDialogOpen = ref(false);
const formDataRef = ref<any>(null);
const editingItem = ref<TableItem | null>(null);
const itemToDelete = ref<TableItem | null>(null);
const catalogs = ref<Record<string, CatalogItem[]>>({});
const searchQuery = ref("");
const filterValues = reactive<Record<string, any>>({});

// Initialize filter values
const initializeFilters = () => {
    props.filters?.forEach(filter => {
        filterValues[filter.field] = null;
    });
};

// Load data
const loadItems = async () => {
    if (!mergedConfig.value.endpoints.index) return;

    loading.value = true;
    error.value = null;

    try {
        const response = await fetch(mergedConfig.value.endpoints.index);
        const data = await response.json();
        items.value = data.data || data;
    } catch (err: any) {
        error.value = err.message || "Failed to load records";
        console.error(err);
    } finally {
        loading.value = false;
    }
};

const loadCatalogs = async () => {
    if (!mergedItemForm.value.catalogs || mergedItemForm.value.catalogs.length === 0) {
        console.log('No catalogs to load');
        return;
    }
    console.log('Loading catalogs:', mergedItemForm.value.catalogs);
    const data_form: string[] = mergedItemForm.value.catalogs || [];

    try {
        const response = await fetchCatalogs(data_form);
        console.log('Catalogs loaded from API:', response);
        console.log('Catalogs keys:', Object.keys(response));
        console.log('All catalogs:', {
            departments: response.departments,
            roles: response.roles,
            skills: response.skills,
            responseFull: response
        });
        console.log('Departments length:', response.departments?.length);
        console.log('Roles length:', response.roles?.length);
        catalogs.value = response;
        console.log('Catalogs assigned to state:', catalogs.value);
    } catch (err) {
        console.error("Failed to load catalogs", err);
    }
};

// Map filters with their catalog data
const enrichedFilters = computed(() => {
    const pluralMap: Record<string, string> = {
        'department_id': 'departments',
        'role_id': 'roles',
        'skill_id': 'skills',
        'department': 'departments',
        'role': 'roles',
        'skill': 'skills',
    };

    return props.filters?.map(filter => {
        const catalogName = pluralMap[filter.field];
        const items = catalogName ? catalogs.value[catalogName] || [] : [];
        console.log(`Filter field: ${filter.field}, Catalog: ${catalogName}, Items count: ${items.length}`);
        return {
            ...filter,
            items: items
        };
    }) || [];
});

// Filtered items
const filteredItems = computed(() => {
    let result = items.value;

    // Apply search
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        result = result.filter(item =>
            Object.values(item).some(val =>
                String(val).toLowerCase().includes(query)
            )
        );
    }

    // Apply custom filters
    props.filters?.forEach(filter => {
        if (filterValues[filter.field] !== null && filterValues[filter.field] !== undefined) {
            result = result.filter(item => {
                const itemValue = item[filter.field];
                return itemValue === filterValues[filter.field];
            });
        }
    });

    return result;
});

// Dialog handlers
const openCreateDialog = () => {
    editingItem.value = null;
    dialogOpen.value = true;
};

const openEditDialog = (item: TableItem) => {
    editingItem.value = { ...item };
    dialogOpen.value = true;
};

const closeDialog = () => {
    dialogOpen.value = false;
    editingItem.value = null;
    formDataRef.value?.reset();
};

const saveItem = async () => {
    if (!formDataRef.value?.validate()) {
        notify({ type: "error", text: "Please fill all required fields" });
        return;
    }

    saving.value = true;
    error.value = null;

    try {
        const payload = { ...formDataRef.value.formData };

        if (editingItem.value && editingItem.value.id) {
            await put(`${mergedConfig.value.endpoints.apiUrl}/${editingItem.value.id}`, payload);
            notify({ type: "success", text: "Record updated successfully" });
        } else {
            await post(mergedConfig.value.endpoints.apiUrl, payload);
            notify({ type: "success", text: "Record created successfully" });
        }

        closeDialog();
        loadItems();
    } catch (err: any) {
        error.value = err.message || "Failed to save record";
        notify({ type: "error", text: error.value || "Failed to save record" });
        console.error(err);
    } finally {
        saving.value = false;
    }
};

const openDeleteDialog = (item: TableItem) => {
    itemToDelete.value = item;
    deleteDialogOpen.value = true;
};

const deleteItem = async () => {
    if (!itemToDelete.value) return;

    saving.value = true;
    error.value = null;

    try {
        await remove(`${mergedConfig.value.endpoints.apiUrl}/${itemToDelete.value.id}`);
        notify({ type: "success", text: "Record deleted successfully" });
        deleteDialogOpen.value = false;
        loadItems();
    } catch (err: any) {
        error.value = err.message || "Failed to delete record";
        notify({ type: "error", text: error.value || "Failed to delete record" });
        console.error(err);
    } finally {
        saving.value = false;
    }
};

const resetFilters = () => {
    searchQuery.value = "";
    Object.keys(filterValues).forEach(key => {
        filterValues[key] = null;
    });
};

const displayHeaders = computed(() => {
    return mergedTableConfig.value.headers.map((header: any) => ({
        title: header.text || header.title,
        key: header.value || header.key,
        ...header,
    }));
});

// Helper function to format dates as dd/mm/yyyy
const formatDate = (value: any): string => {
    if (!value) return '';
    try {
        const date = new Date(value);
        if (!isNaN(date.getTime())) {
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();
            return `${day}/${month}/${year}`;
        }
    } catch (e) {
        return value;
    }
    return value;
};

// Helper function to get nested values (e.g., 'department.name')
const getNestedValue = (obj: any, path: string) => {
    const value = path.split('.').reduce((current, key) => current?.[key], obj);
    
    // Format dates as dd/mm/yyyy if the path contains 'date' or 'fecha'
    if (value && (path.includes('date') || path.includes('fecha'))) {
        return formatDate(value);
    }
    
    return value;
};

// Lifecycle
onMounted(() => {
    initializeFilters();
    loadItems();
    loadCatalogs();
});
</script>

<template>
    <div class="pa-4" :style="{ '--table-gradient': tableHeaderGradient } as any">
        <!-- Header -->
        <div class="d-flex justify-space-between align-center mb-4" :style="{ background: headerGradient }" style="padding: 1.5rem; border-radius: 8px;">
            <div>
                <h1 class="text-h4 font-weight-bold mb-2" style="color: white;">{{ mergedConfig.titulo }}</h1>
                <p class="text-subtitle2" style="color: rgba(255,255,255,0.85);">{{ mergedConfig.descripcion }}</p>
            </div>
            <v-btn
                v-if="mergedConfig.permisos?.crear"
                prepend-icon="mdi-plus"
                @click="openCreateDialog"
                size="large"
                variant="tonal"
                :style="{ background: createBtnGradient, color: 'white' }"
            >
                New Record
            </v-btn>
        </div>

        <!-- Filters -->
        <v-card v-if="filters && filters.length > 0" class="mb-4" variant="outlined">
            <v-card-text>
                <v-row dense>
                    <v-col cols="12" sm="6" :md="12 / (filters.length + 1)">
                        <v-text-field
                            v-model="searchQuery"
                            label="Search"
                            placeholder="Search records..."
                            prepend-icon="mdi-magnify"
                            variant="outlined"
                            density="compact"
                            clearable
                        />
                    </v-col>
                    <v-col
                        v-for="filter in enrichedFilters"
                        :key="filter.field"
                        cols="12"
                        sm="6"
                        :md="12 / (enrichedFilters.length + 1)"
                    >
                        <v-text-field
                            v-if="filter.type === 'text'"
                            v-model="filterValues[filter.field]"
                            :label="filter.label"
                            :placeholder="filter.placeholder"
                            variant="outlined"
                            density="compact"
                            clearable
                        />
                        <v-select
                            v-else-if="filter.type === 'select'"
                            v-model="filterValues[filter.field]"
                            :label="filter.label"
                            :items="filter.items || []"
                            item-title="name"
                            item-value="id"
                            variant="outlined"
                            density="compact"
                            clearable
                        />
                        <v-text-field
                            v-else-if="filter.type === 'date'"
                            v-model="filterValues[filter.field]"
                            :label="filter.label"
                            type="date"
                            variant="outlined"
                            density="compact"
                            clearable
                        />
                    </v-col>
                    <v-col cols="12" sm="6" :md="12 / (filters.length + 1)">
                        <v-btn
                            color="secondary"
                            variant="outlined"
                            block
                            @click="resetFilters"
                        >
                            Reset Filters
                        </v-btn>
                    </v-col>
                </v-row>
            </v-card-text>
        </v-card>

        <!-- Search Bar (if no custom filters) -->
        <v-card v-else class="mb-4" variant="outlined">
            <v-card-text>
                <v-text-field
                    v-model="searchQuery"
                    label="Search"
                    placeholder="Search records..."
                    prepend-icon="mdi-magnify"
                    variant="outlined"
                    density="compact"
                    clearable
                />
            </v-card-text>
        </v-card>

        <!-- Loading & Error States -->
        <v-card v-if="loading" class="mb-4">
            <v-card-text class="text-center py-8">
                <v-progress-circular indeterminate color="primary" />
                <p class="mt-4">Loading records...</p>
            </v-card-text>
        </v-card>

        <v-alert
            v-if="error"
            type="error"
            closable
            class="mb-4"
            @click:close="error = null"
        >
            {{ error }}
        </v-alert>

        <!-- Data Table -->
        <v-card v-if="!loading">
            <v-data-table
                :headers="displayHeaders"
                :items="filteredItems"
                class="elevation-0"
                density="comfortable"
                hover
                mobile-breakpoint="md"
                :style="{ '--table-gradient': tableHeaderGradient } as any"
            >
                <!-- Custom rendering for relationship columns -->
                <template v-for="header in mergedTableConfig.headers.filter(h => h.key && h.key.includes('.'))" :key="header.value" #[`item.${header.value}`]="{ item }">
                    {{ getNestedValue(item, header.key) }}
                </template>

                <!-- Custom rendering for date columns -->
                <template v-for="header in mergedTableConfig.headers.filter(h => h.type === 'date' && h.value)" :key="header.value" #[`item.${header.value}`]="{ item }">
                    {{ formatDate(item[header.value as keyof typeof item]) }}
                </template>

                <!-- Actions Column -->
                <template #item.actions="{ item }">
                    <div class="d-flex gap-2">
                        <v-btn
                            v-if="mergedConfig.permisos?.editar"
                            class="action-btn"
                            icon="mdi-pencil"
                            size="large"
                            color="primary"
                            variant="tonal"
                            @click="openEditDialog(item)"
                            title="Editar"
                        />
                        <v-btn
                            v-if="mergedConfig.permisos?.eliminar"
                            class="action-btn"
                            icon="mdi-trash-can"
                            size="large"
                            color="error"
                            variant="tonal"
                            @click="openDeleteDialog(item)"
                            title="Borrar"
                        />
                    </div>
                </template>

                <!-- No data -->
                <template #no-data>
                    <div class="text-center py-8 text-grey">
                        <v-icon size="48" class="mb-4">mdi-inbox-outline</v-icon>
                        <p>No records found</p>
                    </div>
                </template>
            </v-data-table>
        </v-card>
    </div>

    <!-- Form Dialog -->
    <v-dialog
        v-model="dialogOpen"
        max-width="960px"
        persistent
        transition="dialog-transition"
    >
        <v-card class="form-dialog" elevation="24">
            <!-- Header with gradient -->
            <div class="dialog-header-gradient" :style="{ background: dialogHeaderGradient }">
                <v-card-title class="text-white text-h5 font-weight-bold">
                    <v-icon class="mr-2">{{ editingItem ? 'mdi-pencil' : 'mdi-plus-circle' }}</v-icon>
                    {{ editingItem ? 'Edit Record' : 'New Record' }}
                </v-card-title>
            </div>

            <v-card-text class="py-6" :style="{ '--form-primary': vuetifyTheme.global.current.value.colors.primary } as any">
                <FormData
                    ref="formDataRef"
                    :fields="mergedItemForm.fields"
                    :initial-data="editingItem || undefined"
                    :catalogs="catalogs"
                />
            </v-card-text>

            <v-divider />

            <v-card-actions class="pa-4">
                <v-spacer />
                <v-btn
                    color="secondary"
                    variant="outlined"
                    @click="closeDialog"
                    size="large"
                >
                    <v-icon left>mdi-close</v-icon>
                    Cancel
                </v-btn>
                <v-btn
                    @click="saveItem"
                    :loading="saving"
                    size="large"
                    variant="tonal"
                    :style="{ background: createBtnGradient, color: 'white' }"
                >
                    <v-icon left>{{ editingItem ? 'mdi-check-circle' : 'mdi-plus-circle' }}</v-icon>
                    {{ editingItem ? 'Update' : 'Create' }}
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>

    <!-- Delete Confirmation Dialog -->
    <v-dialog
        v-model="deleteDialogOpen"
        max-width="400px"
    >
        <v-card>
            <v-card-title>Confirm Delete</v-card-title>
            <v-card-text>
                Are you sure you want to delete this record?
                <br>
                <small class="text-error">This action cannot be undone.</small>
            </v-card-text>
            <v-card-actions>
                <v-spacer />
                <v-btn
                    color="secondary"
                    variant="text"
                    @click="deleteDialogOpen = false"
                >
                    Cancel
                </v-btn>
                <v-btn
                    color="error"
                    @click="deleteItem"
                    :loading="saving"
                >
                    Delete
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<style scoped>
/* Form Dialog Styles */
.form-dialog {
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15) !important;
}

.dialog-header-gradient {
    padding: 1.5rem;
    display: flex;
    align-items: center;
    position: relative;
    overflow: hidden;
}

.dialog-header-gradient::before {
    content: '';
    position: absolute;
    top: 0;
    right: -50%;
    width: 100%;
    height: 100%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    z-index: 0;
}

.dialog-header-gradient :deep(.v-card-title) {
    position: relative;
    z-index: 1;
    display: flex;
    align-items: center;
}

/* Action buttons: avoid shared focus/hover states */
:deep(.action-btn) {
    outline: none;
    box-shadow: none;
}

:deep(.action-btn:focus) {
    outline: none;
    box-shadow: none;
}

:deep(.action-btn:focus-visible) {
    outline: none;
    box-shadow: 0 0 0 2px rgba(0,0,0,0.08);
}

:deep(.v-data-table) {
    background: transparent;
}

/* Mobile responsive styles */
@media (max-width: 960px) {
    :deep(.v-data-table__mobile-row) {
        display: grid;
        gap: 1rem;
        padding: 1.5rem 1rem;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        margin-bottom: 1rem;
        background: white;
    }

    :deep(.v-data-table__mobile-row__header) {
        display: contents;
    }

    :deep(.v-data-table__mobile-row__cell) {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.5rem;
        align-items: center;
        padding: 0.5rem 0;
        border-bottom: 1px solid #f0f0f0;
        font-size: 0.875rem;
    }

    :deep(.v-data-table__mobile-row__cell::before) {
        content: attr(data-header);
        font-weight: 600;
        color: #424242;
        grid-column: 1;
    }

    :deep(.v-data-table__mobile-row__cell > *) {
        grid-column: 2;
    }

    /* Button styling in mobile */
    :deep(.v-data-table__mobile-row__cell .d-flex) {
        gap: 0.5rem;
        margin-top: 0.5rem;
    }

    :deep(.v-btn--small) {
        min-width: auto;
        padding: 0.5rem;
    }
}

/* Desktop styles */
:deep(.v-data-table thead tr) {
    background: var(--table-gradient, linear-gradient(135deg, #667eea 0%, #764ba2 100%));
}

:deep(.v-data-table thead th) {
    font-weight: 700;
    color: white !important;
}

:deep(.v-data-table tbody tr:hover) {
    background-color: #f5f5f5 !important;
}
</style>
