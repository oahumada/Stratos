<script setup lang="ts">
import { fetchCatalogs, post, put, remove } from '@/apiHelper';
import StCardGlass from '@/components/StCardGlass.vue';
import { usePage } from '@inertiajs/vue3';
import { useNotification } from '@kyvg/vue3-notification';
import {
    PhFolderOpen,
    PhFunnel,
    PhMagnifyingGlass,
    PhPencil,
    PhPlus,
    PhPlusCircle,
    PhTrash,
    PhX,
} from '@phosphor-icons/vue';
import { computed, onMounted, reactive, ref } from 'vue';
import { useI18n } from 'vue-i18n';
import { useTheme as useVuetifyTheme } from 'vuetify';
import FormData from './FormData.vue';

import {
    Config,
    FilterConfig,
    ItemForm,
    TableConfig,
    TableHeader,
} from '@/types/form-schema';

interface CatalogItem {
    id: number | string;
    name: string;
}

interface TableItem {
    id: number;
    [key: string]: any;
}

const page = usePage();
const user = computed(() => page.props.auth.user as any);
const { notify } = useNotification();
const vuetifyTheme = useVuetifyTheme();
const { t } = useI18n();

// Convertir hex a RGB
const hexToRgb = (hex: string): string => {
    const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})/i.exec(hex);
    return result
        ? `${parseInt(result[1], 16)}, ${parseInt(result[2], 16)}, ${parseInt(result[3], 16)}`
        : '0, 0, 0';
};

// Get theme colors reactively
const dialogHeaderGradient = computed(() => {
    const theme = vuetifyTheme.global.current.value;
    return `linear-gradient(135deg, ${theme.colors.primary} 0%, ${theme.colors.secondary} 100%)`;
});

const dialogWarningGradient = computed(() => {
    const theme = vuetifyTheme.global.current.value;
    return `linear-gradient(135deg, ${theme.colors.accent} 0%, ${theme.colors.error} 100%)`;
});

// mark some bindings referenced to avoid unused-var during refactor
void user.value;
void hexToRgb;
void dialogWarningGradient.value;

const emit = defineEmits(['sync-role']);

const props = withDefaults(
    defineProps<{
        peopleId?: number;
        config?: Config;
        tableConfig?: TableConfig;
        itemForm?: ItemForm;
        filters?: FilterConfig[];
        enableRowDetail?: boolean;
    }>(),
    {
        config: () => ({
            endpoints: { index: '', apiUrl: '' },
            titulo: 'Registros',
            descripcion: 'Manage your records',
            permisos: { crear: true, editar: true, eliminar: true },
        }),
        tableConfig: () => ({
            headers: [],
            options: { pagination: true, search: true },
        }),
        itemForm: () => ({
            fields: [],
            catalogs: [] as any[],
            layout: 'single-column',
        }),
        filters: () => [],
        enableRowDetail: false,
    },
);

// Merged configs
const mergedConfig = computed<Config>(() => {
    const baseConfig: Config = {
        endpoints: { index: '', apiUrl: '' },
        titulo: 'Registros',
        descripcion: 'Manage your records',
        permisos: { crear: true, editar: true, eliminar: true },
        detail: false,
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
        layout: 'single-column',
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
const searchQuery = ref('');
const filterValues = reactive<Record<string, any>>({});
const detailOpen = ref(false);
const detailItem = ref<TableItem | null>(null);
const detailTab = ref<'active' | 'history'>('active');
const detailEnabled = computed(
    () => props.enableRowDetail || mergedConfig.value.detail === true,
);
const setDetailTab = (value: 'active' | 'history') => {
    detailTab.value = value;
};

// Initialize filter values
const initializeFilters = () => {
    props.filters?.forEach((filter) => {
        filterValues[filter.field] = null;
    });
};

// Load data
const loadItems = async () => {
    if (!mergedConfig.value.endpoints.index) return;

    loading.value = true;
    error.value = null;

    try {
        const response = await fetch(mergedConfig.value.endpoints.index, {
            headers: { Accept: 'application/json' },
        });

        const raw = await response.text();

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${raw.slice(0, 200)}`);
        }

        let parsed: any;
        try {
            parsed = raw ? JSON.parse(raw) : null;
        } catch (e) {
            void e;
            throw new Error(
                `Respuesta no JSON (status ${response.status}): ${raw.slice(0, 200)}`,
            );
        }

        items.value = parsed?.data || parsed || [];

        // Update detail item if open
        if (detailOpen.value && detailItem.value) {
            const updated = items.value.find(
                (i) => i.id === detailItem.value?.id,
            );
            if (updated) {
                detailItem.value = updated;
            }
        }
    } catch (err: any) {
        error.value = err.message || 'Failed to load records';
        console.error('loadItems error', err);
    } finally {
        loading.value = false;
    }
};

const loadCatalogs = async () => {
    if (
        !mergedItemForm.value.catalogs ||
        mergedItemForm.value.catalogs.length === 0
    ) {
        console.log('No catalogs to load');
        return;
    }
    console.log('Loading catalogs:', mergedItemForm.value.catalogs);
    const data_form = mergedItemForm.value.catalogs || [];

    try {
        const response = await fetchCatalogs(data_form as any);
        console.log('Catalogs loaded from API:', response);
        console.log('Catalogs keys:', Object.keys(response));
        console.log('All catalogs:', {
            departments: response.departments,
            roles: response.roles,
            skills: response.skills,
            responseFull: response,
        });
        console.log('Departments length:', response.departments?.length);
        console.log('Roles length:', response.roles?.length);
        catalogs.value = response;
        console.log('Catalogs assigned to state:', catalogs.value);
    } catch (err) {
        console.error('Failed to load catalogs', err);
    }
};

// Map filters with their catalog data
const enrichedFilters = computed(() => {
    return (
        props.filters?.map((filter) => {
            const catalogName = filter.catalogKey;
            const resolvedItems =
                filter.items ??
                (catalogName ? catalogs.value[catalogName] || [] : []);
            return {
                ...filter,
                items: resolvedItems,
            };
        }) || []
    );
});

// Filtered items
const filteredItems = computed(() => {
    let result = items.value;

    // Apply search
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        result = result.filter((item) =>
            Object.values(item).some((val) =>
                String(val).toLowerCase().includes(query),
            ),
        );
    }

    // Apply custom filters
    const normalize = (value: any) => {
        if (value === null || value === undefined) return null;
        // Force numbers to numbers and everything else to string for consistent comparison
        const num = Number(value);
        if (!Number.isNaN(num) && String(value).trim() !== '') {
            return num;
        }
        return String(value);
    };

    const resolveItemValue = (item: any, field: string) => {
        // Direct field on item
        if (Object.prototype.hasOwnProperty.call(item, field)) {
            return item[field];
        }
        // Fallback: if field ends with _id, try related object .id (e.g., role_id -> item.role.id)
        if (field.endsWith('_id')) {
            const relation = field.replace(/_id$/, '');
            const related = (item as any)?.[relation];
            if (
                related &&
                Object.prototype.hasOwnProperty.call(related, 'id')
            ) {
                return related.id;
            }
        }
        return undefined;
    };

    props.filters?.forEach((filter) => {
        const selected = filterValues[filter.field];
        if (selected === null || selected === undefined || selected === '')
            return;

        // Text filters: case-insensitive contains; others: strict match
        if (filter.type === 'text') {
            const selectedText = String(selected).toLowerCase();
            result = result.filter((item) => {
                const itemValue = resolveItemValue(item, filter.field);
                if (itemValue === null || itemValue === undefined) return false;
                return String(itemValue).toLowerCase().includes(selectedText);
            });
        } else {
            const normalizedSelected = normalize(selected);
            result = result.filter((item) => {
                const itemValue = normalize(
                    resolveItemValue(item, filter.field),
                );
                return itemValue === normalizedSelected;
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
    detailOpen.value = false;
    dialogOpen.value = true;
};

const closeDialog = () => {
    dialogOpen.value = false;
    editingItem.value = null;
    formDataRef.value?.reset();
};

const saveItem = async () => {
    if (!formDataRef.value?.validate()) {
        notify({ type: 'error', text: 'Please fill all required fields' });
        return;
    }

    saving.value = true;
    error.value = null;

    try {
        const payload = { ...formDataRef.value.formData };
        if (editingItem.value?.id) {
            payload.id = editingItem.value.id;
        }

        const body = { data: payload };

        if (editingItem.value && editingItem.value.id) {
            await put(
                `${mergedConfig.value.endpoints.apiUrl}/${editingItem.value.id}`,
                body,
            );
            notify({ type: 'success', text: 'Record updated successfully' });
        } else {
            await post(mergedConfig.value.endpoints.apiUrl, body);
            notify({ type: 'success', text: 'Record created successfully' });
        }

        closeDialog();
        loadItems();
    } catch (err: any) {
        error.value = err.message || 'Failed to save record';
        notify({ type: 'error', text: error.value || 'Failed to save record' });
        console.error(err);
    } finally {
        saving.value = false;
    }
};

const openDeleteDialog = (item: TableItem) => {
    detailOpen.value = false;
    itemToDelete.value = item;
    deleteDialogOpen.value = true;
};

const deleteItem = async () => {
    if (!itemToDelete.value) return;

    saving.value = true;
    error.value = null;

    try {
        await remove(
            `${mergedConfig.value.endpoints.apiUrl}/${itemToDelete.value.id}`,
        );
        notify({ type: 'success', text: 'Record deleted successfully' });
        deleteDialogOpen.value = false;
        loadItems();
    } catch (err: any) {
        error.value = err.message || 'Failed to delete record';
        notify({
            type: 'error',
            text: error.value || 'Failed to delete record',
        });
        console.error(err);
    } finally {
        saving.value = false;
    }
};

const resetFilters = () => {
    searchQuery.value = '';
    Object.keys(filterValues).forEach((key) => {
        filterValues[key] = null;
    });
};

const onRowClick = (_event: any, row: any) => {
    if (!detailEnabled.value) return;
    const raw = row?.item?.raw ?? row?.item ?? row;
    openDetail(raw as TableItem);
};

const syncWithRole = () => {
    if (!detailItem.value) return;
    emit('sync-role', detailItem.value.id);
};

const openDetail = (item: TableItem) => {
    detailItem.value = item;
    detailTab.value = 'active';
    detailOpen.value = true;
};

defineExpose({ openDetail, loadItems });

const displayHeaders = computed(() => {
    return mergedTableConfig.value.headers.map((header: TableHeader) => {
        const origKey = header.value || header.key;
        const safeKey = String(origKey).replace(/\./g, '__');
        return {
            ...header,
            title: header.text || header.title,
            key: safeKey,
            value: safeKey,
            origKey,
        };
    });
});

// Process items to add derived fields for nested keys (replace dots with __)
const processedItems = computed<TableItem[]>(() => {
    const headers = mergedTableConfig.value.headers || [];
    return filteredItems.value.map((item: TableItem) => {
        const out = { ...item } as TableItem;
        headers.forEach((h: TableHeader) => {
            const orig = h.value || h.key;
            if (orig && String(orig).includes('.')) {
                const safe = String(orig).replace(/\./g, '__');
                out[safe] = getNestedValue(item, orig as string);
            }
            if (h.type === 'date' && orig) {
                const safe = String(h.value || h.key).replace(/\./g, '__');
                out[safe] = formatDate(item[h.value as keyof typeof item]);
            }
        });
        return out;
    });
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
        void e;
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
    <div class="pa-6">
        <!-- Dashboard-style Header -->
        <header class="mb-8">
            <div
                class="d-flex align-center justify-space-between flex-wrap gap-4"
            >
                <div>
                    <h1 class="text-h4 font-weight-black mb-1 text-white">
                        {{ mergedConfig.titulo }}
                        <span class="st-badge-live ml-2">{{
                            t('form_schema.active_badge')
                        }}</span>
                    </h1>
                    <p class="text-subtitle-1 text-slate-400">
                        {{ mergedConfig.descripcion }}
                    </p>
                </div>
                <div class="header-actions d-flex gap-3">
                    <v-btn
                        v-if="mergedConfig.permisos?.crear"
                        @click="openCreateDialog"
                        color="indigo-accent-2"
                        rounded="lg"
                        variant="elevated"
                        elevation="4"
                        size="large"
                    >
                        <component :is="PhPlus" :size="20" class="mr-2" />
                        {{
                            t('form_schema.new_btn', {
                                title: mergedConfig.titulo.replace(/s$/i, ''),
                            })
                        }}
                    </v-btn>
                    <slot name="extra-actions"></slot>
                </div>
            </div>
        </header>

        <!-- Filters Section in Glass Card -->
        <StCardGlass v-if="filters && filters.length > 0" class="mb-6">
            <div class="d-flex align-center mb-4 gap-2">
                <component
                    :is="PhFunnel"
                    class="text-indigo-accent-2"
                    :size="18"
                />
                <span class="text-overline font-weight-bold text-slate-300">
                    {{ t('form_schema.filters_title') }}
                </span>
            </div>
            <v-row dense>
                <v-col cols="12" sm="6" :md="12 / (filters.length + 1)">
                    <v-text-field
                        v-model="searchQuery"
                        :label="t('form_schema.global_search')"
                        :placeholder="t('form_schema.search_placeholder')"
                        variant="outlined"
                        density="comfortable"
                        rounded="lg"
                        hide-details
                        class="glass-input"
                    >
                        <template #prepend-inner>
                            <component
                                :is="PhMagnifyingGlass"
                                :size="18"
                                class="text-slate-400"
                            />
                        </template>
                    </v-text-field>
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
                        density="comfortable"
                        rounded="lg"
                        hide-details
                        class="glass-input"
                    />
                    <v-select
                        v-else-if="filter.type === 'select'"
                        v-model="filterValues[filter.field]"
                        :label="filter.label"
                        :items="filter.items || []"
                        item-title="name"
                        item-value="id"
                        variant="outlined"
                        density="comfortable"
                        rounded="lg"
                        hide-details
                        class="glass-input"
                    />
                    <v-text-field
                        v-else-if="filter.type === 'date'"
                        v-model="filterValues[filter.field]"
                        :label="filter.label"
                        type="date"
                        variant="outlined"
                        density="comfortable"
                        rounded="lg"
                        hide-details
                        class="glass-input"
                    />
                </v-col>
                <v-col
                    cols="12"
                    sm="6"
                    :md="12 / (filters.length + 1)"
                    class="d-flex align-center"
                >
                    <v-btn
                        variant="text"
                        color="slate-400"
                        @click="resetFilters"
                        class="text-none"
                    >
                        {{ t('form_schema.clear_filters') }}
                    </v-btn>
                </v-col>
            </v-row>
        </StCardGlass>

        <!-- Search Bar (if no custom filters) -->
        <v-card v-else class="mb-4" variant="outlined">
            <v-card-text>
                <v-text-field
                    v-model="searchQuery"
                    :label="t('form_schema.global_search')"
                    :placeholder="t('form_schema.search_placeholder')"
                    variant="outlined"
                    density="compact"
                    clearable
                >
                    <template #prepend-inner>
                        <component :is="PhMagnifyingGlass" :size="18" />
                    </template>
                </v-text-field>
            </v-card-text>
        </v-card>

        <!-- Loading & Error States -->
        <v-card v-if="loading" class="mb-4">
            <v-card-text class="py-8 text-center">
                <v-progress-circular indeterminate color="indigo-accent-1" />
                <p class="mt-4 text-slate-400">
                    {{ t('form_schema.loading_records') }}
                </p>
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

        <!-- Main Content Table in Glass Card -->
        <StCardGlass
            v-if="!loading"
            class="pa-0 overflow-hidden"
            :no-hover="true"
        >
            <v-data-table
                :headers="displayHeaders"
                :items="processedItems"
                class="glass-table"
                density="comfortable"
                hover
                mobile-breakpoint="md"
                @click:row="onRowClick"
            >
                <!-- Actions Column -->
                <template #[`item.actions`]="{ item }">
                    <div class="d-flex gap-2">
                        <v-btn
                            v-if="mergedConfig.permisos?.editar"
                            icon
                            size="small"
                            color="indigo-accent-1"
                            variant="tonal"
                            @click.stop="openEditDialog(item)"
                            class="rounded-lg"
                        >
                            <component :is="PhPencil" :size="16" />
                        </v-btn>
                        <v-btn
                            v-if="mergedConfig.permisos?.eliminar"
                            icon
                            size="small"
                            color="rose-accent-2"
                            variant="tonal"
                            @click.stop="openDeleteDialog(item)"
                            class="rounded-lg"
                        >
                            <component :is="PhTrash" :size="16" />
                        </v-btn>
                    </div>
                </template>

                <!-- No data -->
                <template #no-data>
                    <div class="py-12 text-center">
                        <component
                            :is="PhFolderOpen"
                            :size="48"
                            class="mb-3 text-slate-600 opacity-50"
                        />
                        <p class="text-h6 text-slate-400">
                            {{ t('form_schema.no_records') }}
                        </p>
                        <v-btn
                            variant="text"
                            color="indigo-accent-2"
                            @click="resetFilters"
                            class="text-none mt-2"
                        >
                            {{ t('form_schema.reset_filters') }}
                        </v-btn>
                    </div>
                </template>
            </v-data-table>
        </StCardGlass>
    </div>

    <!-- Form Dialog -->
    <v-dialog
        v-model="dialogOpen"
        max-width="960px"
        persistent
        transition="dialog-transition"
    >
        <StCardGlass class="pa-0 overflow-hidden" :no-hover="true">
            <!-- Header with gradient -->
            <div
                class="dialog-header-gradient"
                :style="{ background: dialogHeaderGradient }"
            >
                <div
                    class="text-h5 font-weight-bold d-flex align-center font-premium text-white"
                >
                    <component
                        :is="editingItem ? PhPencil : PhPlusCircle"
                        size="24"
                        class="mr-3 text-white"
                    />
                    {{
                        editingItem
                            ? t('form_schema.dialog.edit')
                            : t('form_schema.dialog.create')
                    }}
                    {{ mergedConfig.titulo.replace(/s$/i, '') }}
                </div>
            </div>

            <div
                class="pa-8"
                :style="
                    {
                        '--form-primary':
                            vuetifyTheme.global.current.value.colors.primary,
                    } as any
                "
            >
                <FormData
                    ref="formDataRef"
                    :fields="mergedItemForm.fields"
                    :initial-data="editingItem || undefined"
                    :catalogs="catalogs"
                />
            </div>

            <v-divider class="border-white/5" />

            <div class="pa-4 d-flex justify-end gap-3">
                <v-btn
                    color="slate-400"
                    variant="text"
                    @click="closeDialog"
                    size="large"
                    class="text-none"
                >
                    {{ t('form_schema.dialog.cancel') }}
                </v-btn>
                <v-btn
                    @click="saveItem"
                    :loading="saving"
                    size="large"
                    variant="elevated"
                    color="indigo-accent-2"
                    rounded="lg"
                    class="px-8"
                >
                    {{
                        editingItem
                            ? t('form_schema.dialog.save')
                            : t('form_schema.dialog.create_record')
                    }}
                </v-btn>
            </div>
        </StCardGlass>
    </v-dialog>

    <!-- Delete Confirmation Dialog -->
    <v-dialog v-model="deleteDialogOpen" max-width="400px">
        <StCardGlass class="pa-0" :no-hover="true">
            <div class="pa-6">
                <div class="text-h6 font-premium mb-2 text-white">
                    {{ t('form_schema.dialog.delete_confirm') }}
                </div>
                <div class="text-body-1 text-slate-400">
                    {{ t('form_schema.dialog.delete_ask') }}
                    <br />
                    <small class="text-rose-accent-2">
                        {{ t('form_schema.dialog.delete_warning') }}
                    </small>
                </div>
            </div>
            <v-divider class="border-white/5" />
            <div class="pa-4 d-flex justify-end gap-2">
                <v-btn
                    color="slate-400"
                    variant="text"
                    @click="deleteDialogOpen = false"
                >
                    {{ t('form_schema.dialog.cancel') }}
                </v-btn>
                <v-btn
                    color="rose-accent-4"
                    variant="elevated"
                    @click="deleteItem"
                    :loading="saving"
                    rounded="lg"
                >
                    {{ t('form_schema.dialog.delete_btn') }}
                </v-btn>
            </div>
        </StCardGlass>
    </v-dialog>

    <!-- Detail Drawer (row click) using dialog to avoid layout injection requirement -->
    <v-dialog
        v-if="detailEnabled"
        v-model="detailOpen"
        width="720"
        persistent
        scrim="transparent"
        transition="dialog-right-transition"
    >
        <StCardGlass
            class="pa-6"
            height="100%"
            style="
                min-height: 100vh;
                border-left: 1px solid rgba(255, 255, 255, 0.1);
            "
            :no-hover="true"
        >
            <div class="d-flex align-center justify-space-between mb-3">
                <div>
                    <div class="text-subtitle-1 font-weight-medium">
                        {{ t('form_schema.active_badge') }}
                    </div>
                    <div class="text-body-2 text-secondary">
                        {{ t('form_schema.no_records') }}
                    </div>
                </div>
                <v-btn
                    icon
                    variant="text"
                    @click="detailOpen = false"
                    class="rounded-lg"
                >
                    <component :is="PhX" :size="20" />
                </v-btn>
            </div>

            <div v-if="detailItem" class="d-flex flex-column gap-4">
                <slot
                    name="detail"
                    :item="detailItem"
                    :tab="detailTab"
                    :set-tab="setDetailTab"
                    :sync="syncWithRole"
                    :refresh="loadItems"
                    :close="
                        () => {
                            detailOpen = false;
                        }
                    "
                >
                    <v-card flat border class="pa-3">
                        <div class="text-subtitle-2 mb-2">
                            Detalle del registro
                        </div>
                        <div class="text-body-2 mb-2 text-secondary">
                            Personaliza este contenido usando el slot "detail".
                        </div>
                        <div
                            class="d-flex flex-column gap-1"
                            style="max-height: 320px; overflow: auto"
                        >
                            <div
                                v-for="(value, key) in detailItem"
                                :key="key"
                                class="text-body-2"
                            >
                                <strong>{{ key }}:</strong> {{ value }}
                            </div>
                        </div>
                    </v-card>
                </slot>
            </div>

            <div v-else class="mt-6 text-center text-secondary">
                Selecciona una fila para ver detalle.
            </div>
        </StCardGlass>
    </v-dialog>
</template>

<style scoped>
/* Form Dialog Styles */
.dialog-header-gradient {
    padding: 1.5rem 2rem;
    display: flex;
    align-items: center;
    position: relative;
    overflow: hidden;
}

.dialog-header-gradient::before {
    content: '';
    position: absolute;
    top: 0;
    right: -20%;
    width: 60%;
    height: 100%;
    background: radial-gradient(
        circle,
        rgba(255, 255, 255, 0.15) 0%,
        transparent 70%
    );
    z-index: 0;
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

.glass-input :deep(.v-field__outline) {
    --v-field-border-opacity: 0.1;
}

.glass-input :deep(.v-field--focused .v-field__outline) {
    --v-field-border-opacity: 0.4;
    color: var(--stratos-accent-indigo) !important;
}

.glass-table {
    background: transparent !important;
    color: #cbd5e1 !important;
}

:deep(.v-data-table) {
    background: transparent !important;
}

:deep(.v-data-table thead) {
    background: rgba(255, 255, 255, 0.03) !important;
}

:deep(.v-data-table thead th) {
    color: #94a3b8 !important;
    text-transform: uppercase !important;
    font-size: 0.75rem !important;
    font-weight: 700 !important;
    letter-spacing: 0.05em !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05) !important;
}

:deep(.v-data-table tbody td) {
    border-bottom: 1px solid rgba(255, 255, 255, 0.03) !important;
    padding: 1rem !important;
}

:deep(.v-data-table tbody tr:hover) {
    background: rgba(255, 255, 255, 0.02) !important;
}

.text-slate-400 {
    color: #94a3b8 !important;
}

.text-slate-300 {
    color: #cbd5e1 !important;
}

.text-slate-600 {
    color: #475569 !important;
}
</style>
