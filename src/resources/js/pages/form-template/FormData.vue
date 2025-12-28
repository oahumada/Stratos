<script setup lang="ts">
import { ref, reactive, watch } from "vue";
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
    descripcion: string;
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

// Methods
const validate = (): boolean => {
    return form.value?.validate() ?? false;
};

const reset = (): void => {
    form.value?.reset();
};

const getSelectItems = (fieldKey: string): CatalogItem[] => {
    const catalogName = fieldKey.endsWith("_id")
        ? fieldKey.slice(0, -3)
        : fieldKey;

    return (props.catalogs && props.catalogs[catalogName]) || [];
};

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
                    v-for="field in fields"
                    :key="field.key"
                    cols="12"
                    :sm="fields.length === 1 ? 12 : 6"
                    :md="fields.length === 1 ? 12 : fields.length === 2 ? 6 : 4"
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
                    />

                    <!-- Select -->
                    <v-select
                        v-else-if="field.type === 'select'"
                        v-model="formData[field.key]"
                        :items="field.items || getSelectItems(field.key)"
                        :label="field.label"
                        :placeholder="field.placeholder"
                        item-title="descripcion"
                        item-value="id"
                        :rules="field.rules"
                        :required="field.required"
                        variant="outlined"
                        density="compact"
                        clearable
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
