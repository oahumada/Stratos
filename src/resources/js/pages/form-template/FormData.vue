<script setup lang="ts">
import { ref, reactive, watch, computed } from "vue";
import moment from "moment";

interface FormField {
    key: string;
    type: 'text' | 'email' | 'password' | 'select' | 'date' | 'time' | 'switch' | 'checkbox' | 'number' | 'textarea';
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
    }
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
    { deep: true }
);

// Watch for catalogs changes
watch(
    () => props.catalogs,
    (newVal) => {
        console.log('[FormData] Catalogs updated:', newVal);
    },
    { deep: true }
);

// Convert string rules to validation functions
const parseRules = (rules?: Array<string | ((v: any) => boolean | string)>): Array<(v: any) => boolean | string> => {
    if (!rules) return [];
    
    return rules.map(rule => {
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
                        return emailRegex.test(v) || 'Please enter a valid email';
                    };
                
                case 'min':
                    const minLength = parseInt(params[0]) || 0;
                    return (v: any) => {
                        if (!v) return true; // Skip if empty
                        const length = String(v).length;
                        return length >= minLength || `Minimum ${minLength} characters required`;
                    };
                
                case 'max':
                    const maxLength = parseInt(params[0]) || 0;
                    return (v: any) => {
                        if (!v) return true; // Skip if empty
                        const length = String(v).length;
                        return length <= maxLength || `Maximum ${maxLength} characters allowed`;
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
    let singularName = fieldKey.endsWith("_id")
        ? fieldKey.slice(0, -3)
        : fieldKey;
    
    // Map singular to plural for catalog names
    const pluralMap: Record<string, string> = {
        'department': 'departments',
        'role': 'roles',
        'skill': 'skills',
    };
    
    const catalogName = pluralMap[singularName] || singularName;
    
    const items = (props.catalogs && props.catalogs[catalogName]) || [];
    
    console.log(`[getSelectItems] Field: '${fieldKey}'`, {
        singularName,
        catalogName,
        itemsCount: items.length,
        items,
        availableCatalogs: Object.keys(props.catalogs || {}),
        allCatalogs: props.catalogs
    });
    
    return items;
};

// Computed property that enriches fields with parsed rules
const enrichedFields = computed(() => {
    return props.fields.map(field => ({
        ...field,
        rules: parseRules(field.rules as Array<string | ((v: any) => boolean | string)>)
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
    <v-form ref="form" v-model="valid">
        <v-container fluid>
            
            <v-row dense>
                <v-col
                    v-for="field in enrichedFields"
                    :key="field.key"
                    cols="12"
                    :sm="enrichedFields.length === 1 ? 12 : 6"
                    :md="enrichedFields.length === 1 ? 12 : enrichedFields.length === 2 ? 6 : 4"
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
                        density="compact"
                        validate-on="blur"
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
                        density="compact"
                        validate-on="blur"
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
                        density="compact"
                        validate-on="blur"
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
                        density="compact"
                        validate-on="blur"
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
                        density="compact"
                        rows="3"
                        validate-on="blur"
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
                        density="compact"
                        clearable
                        validate-on="blur"
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
                        density="compact"
                        validate-on="blur"
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
                        density="compact"
                        validate-on="blur"
                    />

                    <!-- Checkbox -->
                    <div v-else-if="field.type === 'checkbox'" class="pt-2">
                        <v-checkbox
                            v-model="formData[field.key]"
                            :label="field.label"
                            :rules="field.rules"
                        />
                    </div>

                    <!-- Switch -->
                    <div v-else-if="field.type === 'switch'" class="pt-2">
                        <v-switch
                            v-model="formData[field.key]"
                            :label="field.label"
                            :rules="field.rules"
                        />
                    </div>
                </v-col>
            </v-row>
        </v-container>
    </v-form>
</template>

<style scoped>
:deep(.v-form) {
    padding: 0;
}

:deep(.v-container) {
    padding: 0;
}
</style>
