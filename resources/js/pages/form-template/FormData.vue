<script setup lang="ts">
import moment from 'moment';
import { computed, reactive, ref, watch } from 'vue';

interface FormField {
    key: string;
    type:
        | 'text'
        | 'email'
        | 'password'
        | 'select'
        | 'date'
        | 'time'
        | 'switch'
        | 'checkbox'
        | 'number'
        | 'textarea';
    label: string;
    placeholder?: string;
    required?: boolean;
    rules?: Array<(v: any) => boolean | string>;
    items?: any[];
}

interface CatalogItem {
    id: number | string;
    name: string;
}

// Props
const props = withDefaults(
    defineProps<{
        fields: FormField[];
        initialData?: Record<string, any>;
        catalogs?: Record<string, CatalogItem[]>;
    }>(),
    {
        initialData: () => ({}),
        catalogs: () => ({}),
    },
);

// Refs
const form = ref<{ validate: () => boolean; reset: () => void } | null>(null);
const valid = ref(false);
const formData = reactive<Record<string, any>>({ ...props.initialData });

// Watch for initialData changes
watch(
    () => props.initialData,
    (newVal) => {
        if (newVal) {
            Object.assign(formData, newVal);
        }
    },
    { deep: true },
);

// Watch for catalogs changes
watch(
    () => props.catalogs,
    (newVal) => {
        console.log('[FormData] Catalogs updated:', newVal);
    },
    { deep: true },
);

// mark moment referenced to avoid unused-import during refactor
void moment;

// Convert string rules to validation functions
const parseRules = (
    rules?: Array<string | ((v: any) => boolean | string)>,
): Array<(v: any) => boolean | string> => {
    if (!rules) return [];

    return rules.map((rule) => {
        // Si ya es una función, devolver tal cual
        if (typeof rule === 'function') {
            return rule;
        }

        // Si es un string, convertir a función
        if (typeof rule === 'string') {
            const [ruleType, ...params] = rule.split(':');

            switch (ruleType.toLowerCase()) {
                case 'required':
                    return (v: any) => {
                        const value = String(v || '').trim();
                        return value !== '' || 'This field is required';
                    };

                case 'email':
                    return (v: any) => {
                        if (!v) return true; // Skip if empty (let 'required' handle it)
                        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                        return (
                            emailRegex.test(v) || 'Please enter a valid email'
                        );
                    };

                case 'min':
                    const minLength = parseInt(params[0]) || 0;
                    return (v: any) => {
                        if (!v) return true; // Skip if empty
                        const length = String(v).length;
                        return (
                            length >= minLength ||
                            `Minimum ${minLength} characters required`
                        );
                    };

                case 'max':
                    const maxLength = parseInt(params[0]) || 0;
                    return (v: any) => {
                        if (!v) return true; // Skip if empty
                        const length = String(v).length;
                        return (
                            length <= maxLength ||
                            `Maximum ${maxLength} characters allowed`
                        );
                    };

                default:
                    return () => true;
            }
        }

        return () => true;
    });
};

// Methods
const validate = (): boolean => {
    return form.value?.validate() ?? false;
};

const reset = (): void => {
    form.value?.reset();
};

// Reactive function to get select items based on field key
const getSelectItems = (fieldKey: string): any[] => {
    // Extract catalog name from field key
    // department_id -> department, role_id -> role
    const singularName = fieldKey.endsWith('_id')
        ? fieldKey.slice(0, -3)
        : fieldKey;

    // Map singular to plural for catalog names
    const pluralMap: Record<string, string> = {
        department: 'departments',
        role: 'roles',
        skill: 'skills',
        person: 'people',
        related_person: 'people',
    };

    const catalogName = pluralMap[singularName] || singularName;

    const items = (props.catalogs && props.catalogs[catalogName]) || [];

    console.log(`[getSelectItems] Field: '${fieldKey}'`, {
        singularName,
        catalogName,
        itemsCount: items.length,
        items,
        availableCatalogs: Object.keys(props.catalogs || {}),
        allCatalogs: props.catalogs,
    });

    return items;
};

// Computed property that enriches fields with parsed rules
const enrichedFields = computed(() => {
    return props.fields.map((field) => ({
        ...field,
        rules: parseRules(
            field.rules as Array<string | ((v: any) => boolean | string)>,
        ),
    }));
});

// Expose methods to parent component
defineExpose({
    validate,
    reset,
    formData,
});
</script>

<template>
    <v-form ref="form" v-model="valid" class="form-wrapper">
        <v-container class="form-container">
            <v-row class="form-row">
                <v-col
                    v-for="field in enrichedFields"
                    :key="field.key"
                    cols="12"
                    sm="6"
                    class="form-col"
                >
                    <!-- Text Input -->
                    <v-text-field
                        v-if="field.type === 'text'"
                        v-model="formData[field.key]"
                        :label="field.label"
                        :placeholder="field.placeholder"
                        :rules="field.rules"
                        :required="field.required"
                        variant="outlined"
                        density="comfortable"
                        validate-on="blur"
                        prepend-inner-icon="mdi-format-text"
                        class="form-field"
                    />

                    <!-- Email Input -->
                    <v-text-field
                        v-else-if="field.type === 'email'"
                        v-model="formData[field.key]"
                        :label="field.label"
                        :placeholder="field.placeholder"
                        type="email"
                        :rules="field.rules"
                        :required="field.required"
                        variant="outlined"
                        density="comfortable"
                        validate-on="blur"
                        prepend-inner-icon="mdi-email"
                        class="form-field"
                    />

                    <!-- Password Input -->
                    <v-text-field
                        v-else-if="field.type === 'password'"
                        v-model="formData[field.key]"
                        :label="field.label"
                        type="password"
                        :rules="field.rules"
                        :required="field.required"
                        variant="outlined"
                        density="comfortable"
                        validate-on="blur"
                        prepend-inner-icon="mdi-lock"
                        class="form-field"
                    />

                    <!-- Number Input -->
                    <v-text-field
                        v-else-if="field.type === 'number'"
                        v-model.number="formData[field.key]"
                        :label="field.label"
                        type="number"
                        :rules="field.rules"
                        :required="field.required"
                        variant="outlined"
                        density="comfortable"
                        validate-on="blur"
                        prepend-inner-icon="mdi-numeric"
                        class="form-field"
                    />

                    <!-- Textarea -->
                    <v-textarea
                        v-else-if="field.type === 'textarea'"
                        v-model="formData[field.key]"
                        :label="field.label"
                        :placeholder="field.placeholder"
                        :rules="field.rules"
                        :required="field.required"
                        variant="outlined"
                        density="comfortable"
                        rows="3"
                        validate-on="blur"
                        prepend-inner-icon="mdi-text-box"
                        class="form-field"
                    />

                    <!-- Select -->
                    <v-select
                        v-else-if="field.type === 'select'"
                        v-model="formData[field.key]"
                        :items="field.items || getSelectItems(field.key)"
                        :label="field.label"
                        :placeholder="field.placeholder"
                        item-title="name"
                        item-value="id"
                        :rules="field.rules"
                        :required="field.required"
                        variant="outlined"
                        density="comfortable"
                        clearable
                        validate-on="blur"
                        prepend-inner-icon="mdi-list-box"
                        class="form-field"
                    />

                    <!-- Date Picker -->
                    <v-text-field
                        v-else-if="field.type === 'date'"
                        v-model="formData[field.key]"
                        :label="field.label"
                        type="date"
                        :rules="field.rules"
                        :required="field.required"
                        variant="outlined"
                        density="comfortable"
                        validate-on="blur"
                        prepend-inner-icon="mdi-calendar"
                        class="form-field"
                    />

                    <!-- Time Picker -->
                    <v-text-field
                        v-else-if="field.type === 'time'"
                        v-model="formData[field.key]"
                        :label="field.label"
                        type="time"
                        :rules="field.rules"
                        :required="field.required"
                        variant="outlined"
                        density="comfortable"
                        validate-on="blur"
                        prepend-inner-icon="mdi-clock"
                        class="form-field"
                    />

                    <!-- Checkbox -->
                    <div
                        v-else-if="field.type === 'checkbox'"
                        class="checkbox-wrapper"
                    >
                        <v-checkbox
                            v-model="formData[field.key]"
                            :label="field.label"
                            :rules="field.rules"
                            class="form-checkbox"
                        />
                    </div>

                    <!-- Switch -->
                    <div
                        v-else-if="field.type === 'switch'"
                        class="switch-wrapper"
                    >
                        <v-switch
                            v-model="formData[field.key]"
                            :label="field.label"
                            :rules="field.rules"
                            class="form-switch"
                        />
                    </div>
                </v-col>
            </v-row>
        </v-container>
    </v-form>
</template>

<style scoped>
/* Form Container Styles */
.form-container {
    padding: 0 !important;
    max-width: 100%;
}

.form-row {
    margin: 0 !important;
}

.form-col {
    padding: 0.75rem !important;
}

/* Input Field Styles - Stratos Glass Edition */
:deep(.form-field .v-field) {
    background: rgba(255, 255, 255, 0.03) !important;
    border-radius: 12px !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid rgba(255, 255, 255, 0.05) !important;
}

:deep(.form-field:focus-within .v-field) {
    background: rgba(255, 255, 255, 0.06) !important;
    border: 1px solid rgba(99, 102, 241, 0.4) !important; /* indigo-500/40 */
    box-shadow: 0 0 20px rgba(99, 102, 241, 0.1) !important;
}

:deep(.form-field .v-field__outline) {
    display: none !important; /* Hide standard outline to use our custom border */
}

:deep(.v-label) {
    color: #94a3b8 !important; /* slate-400 */
    font-weight: 500;
    font-size: 0.875rem;
    opacity: 1 !important;
}

:deep(.form-field:focus-within .v-label) {
    color: #818cf8 !important; /* indigo-400 */
}

:deep(.v-field__prepend-inner .v-icon) {
    color: #64748b !important; /* slate-500 */
    transition: color 0.2s ease;
}

:deep(.form-field:focus-within .v-field__prepend-inner .v-icon) {
    color: #818cf8 !important;
}

:deep(.v-field__input) {
    color: #f1f5f9 !important; /* slate-100 */
    font-size: 0.95rem;
}

:deep(.v-field__input::placeholder) {
    color: #475569 !important; /* slate-600 */
    opacity: 1 !important;
}

/* Checkbox and Switch Styles */
.checkbox-wrapper,
.switch-wrapper {
    display: flex;
    align-items: center;
    padding: 0.5rem 0;
}

:deep(.v-selection-control__label) {
    color: #cbd5e1 !important; /* slate-300 */
    font-weight: 500;
}

:deep(.v-checkbox .v-selection-control__input .v-icon),
:deep(.v-switch .v-selection-control__input .v-icon) {
    color: #6366f1 !important;
}

/* Validation Errors */
:deep(.v-messages) {
    font-size: 0.75rem;
    margin-top: 0.25rem;
    color: #fb7185 !important; /* rose-400 */
    opacity: 1 !important;
}

@media (max-width: 960px) {
    .form-row {
        gap: 0.5rem;
    }
}
</style>
