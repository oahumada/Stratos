<script setup>
import { ref, reactive, watch, defineProps, defineExpose } from "vue";
import moment from "moment";

// Props
const props = defineProps({
    fields: {
        type: Array,
        required: true,
    },
    initialData: {
        type: Object,
        default: () => ({}),
    },
    catalogs: {
        type: Object,
        default: () => ({}),
    },
});

// Refs
const form = ref(null);
const valid = ref(false);
const formData = reactive({ ...props.initialData });
const datePickerMenus = ref({});

// Watch for initialData changes
watch(
    () => props.initialData,
    (newVal) => {
        console.log("FormData recibiendo initialData:", newVal);
        Object.assign(formData, newVal);
        console.log("FormData después de assign:", formData);
    },
    { deep: true },
);

// Methods
const validate = () => {
    return form.value?.validate();
};

const reset = () => {
    form.value?.reset();
};

const getSelectItems = (fieldKey) => {
    // Mapeo automático: remover '_id' del final del fieldKey para obtener el nombre del catálogo
    // Ejemplo: 'accidente_id' -> 'accidente', 'tipo_atencion_id' -> 'tipo_atencion'
    const catalogName = fieldKey.endsWith("_id")
        ? fieldKey.slice(0, -3) // Remover '_id' del final
        : fieldKey; // Si no termina en '_id', usar el fieldKey tal como está

    // Buscar el catálogo en props.catalogs
    if (props.catalogs && props.catalogs[catalogName]) {
        return props.catalogs[catalogName];
    }

    // Si no se encuentra, devolver array vacío
    return [];
};

// Función para formatear fecha para mostrar
const formatDateForDisplay = (dateValue) => {
    if (!dateValue) return "";
    const momentDate = moment(dateValue, "YYYY-MM-DD");
    return momentDate.isValid() ? momentDate.format("DD/MM/YYYY") : "";
};

// Función para parsear fecha del display
const parseDateFromDisplay = (displayValue) => {
    if (!displayValue) return null;
    const momentDate = moment(displayValue, "DD/MM/YYYY");
    return momentDate.isValid() ? momentDate.format("YYYY-MM-DD") : null;
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
            <v-row>
                <v-col
                    v-for="field in fields"
                    :key="field.key"
                    cols="12"
                    sm="6"
                    :md="fields.length === 1 ? 12 : fields.length === 2 ? 6 : 4"
                >
                    <v-text-field
                        v-if="field.type === 'text'"
                        v-model="formData[field.key]"
                        :label="field.label"
                        :rules="field.rules"
                        :required="field.required"
                    />
                    <v-select
                        v-else-if="field.type === 'select'"
                        v-model="formData[field.key]"
                        :items="getSelectItems(field.key)"
                        :label="field.label"
                        item-title="descripcion"
                        item-value="id"
                        :loading="
                            !catalogs || Object.keys(catalogs).length === 0
                        "
                    />
                    <v-menu
                        v-else-if="field.type === 'date'"
                        v-model="datePickerMenus[field.key]"
                        :close-on-content-click="false"
                        transition="scale-transition"
                        offset-y
                        max-width="290px"
                        min-width="auto"
                    >
                        <template v-slot:activator="{ props }">
                            <v-text-field
                                :model-value="
                                    formatDateForDisplay(formData[field.key])
                                "
                                :label="field.label"
                                prepend-icon="mdi-calendar"
                                readonly
                                v-bind="props"
                                placeholder="dd/mm/yyyy"
                                clearable
                                @click:clear="formData[field.key] = null"
                            />
                        </template>
                        <v-date-picker
                            :model-value="formData[field.key]"
                            @update:model-value="
                                formData[field.key] = $event;
                                datePickerMenus[field.key] = false;
                            "
                            no-title
                            scrollable
                            locale="es-ES"
                            :first-day-of-week="1"
                        />
                    </v-menu>
                    <v-text-field
                        v-else-if="field.type === 'time'"
                        v-model="formData[field.key]"
                        :label="field.label"
                        type="time"
                    />
                    <v-switch
                        v-else-if="field.type === 'switch'"
                        v-model="formData[field.key]"
                        :label="field.label"
                        variant="underlined"
                        color="green-darken-3"
                        inset
                    />
                    <v-checkbox
                        v-else-if="field.type === 'checkbox'"
                        v-model="formData[field.key]"
                        :label="field.label"
                    />
                    <v-text-field
                        v-else-if="field.type === 'number'"
                        v-model="formData[field.key]"
                        :label="field.label"
                        type="number"
                    />
                </v-col>
            </v-row>
        </v-container>
    </v-form>
</template>
