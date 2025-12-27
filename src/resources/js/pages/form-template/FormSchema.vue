<script setup>
import { ref, reactive, computed, onMounted } from "vue";
import { post, put, remove, show, fetchCatalogs } from "@/apiHelper.ts";
import { useNotification } from "@kyvg/vue3-notification";
import FormData from "./FormData.vue";
import moment from "moment";
import ConfirmDialog from "@/components/ConfirmDialog.vue";
import { usePage } from "@inertiajs/vue3";
const page = usePage();
const user = computed(() => page.props.auth.user);

const { notify } = useNotification();

const isDevelopment = import.meta.env.MODE === "development";

const props = defineProps({
    pacienteId: {
        type: Number,
        required: true,
    },
    config: {
        type: Object,
        default: () => ({}),
    },
    tableConfig: {
        type: Object,
        default: () => ({}),
    },
    itemForm: {
        type: Object,
        default: () => ({}),
    },
});

// Use computed properties for config handling
const mergedConfig = computed(() => ({
    // Default configuration
    endpoints: {
        index: "",
        apiUrl: "",
    },
    titulo: "Registros",
    permisos: { crear: true, editar: true, eliminar: true },
    ...props.config,
}));

const mergedTableConfig = computed(() => ({
    // Default table config
    headers: [],
    options: { pagination: true, search: true },
    // Merge with props
    ...props.tableConfig,
}));

const mergedItemForm = computed(() => ({
    // Default form config
    fields: [],
    catalogs: [],
    layout: "single-column",
    // Merge with props
    ...props.itemForm,
}));

// Destructure from computed properties
const { endpoints, titulo, permisos } = mergedConfig.value;
const headers = mergedTableConfig.value.headers;
const formFields = mergedItemForm.value.fields;
const catalogs = mergedItemForm.value.catalogs;
console.log(catalogs);

// Referencia al componente FormData
const form = ref(null);

const state = reactive({
    tableItems: [],
    dialogForm: false,
    editedItem: {},
    editedIndex: -1,
    formTitle: "",
    loading: false,
    saving: false,
    deleting: false,
    errors: {}, // Add errors object
    catalogs: catalogs || [], // Initialize catalogs from itemForm
    dialogDelete: false,
    itemToDelete: null,
});

// Métodos CRUD básicos (bosquejo)
const editedItemTitle = computed(() =>
    state.editedIndex === -1 ? state.formCrear : state.formEdit,
);

function openFormCreate() {
    state.editedItem = { ...state.defaultItem };
    state.errors = {};
    state.dialogForm = true;
    state.formTitle = "Nuevo registro de " + titulo;
}
function openFormEdit(item) {
    const editedItem = { ...item };

    // Identificar campos de fecha en la configuración
    const dateFields = props.itemForm.fields
        .filter((field) => field.type === "date")
        .map((field) => field.key);

    // Filtrar solo los campos que están definidos en la configuración
    const definedFields = props.itemForm.fields.map((field) => field.key);

    // CORREGIDO: Crear objeto limpio pero SIEMPRE incluir el ID
    const cleanedItem = {};

    // CRÍTICO: Siempre preservar el ID para updates
    if (editedItem.id) {
        cleanedItem.id = editedItem.id;
    }

    // Agregar campos definidos en la configuración
    definedFields.forEach((fieldKey) => {
        cleanedItem[fieldKey] = editedItem[fieldKey];
    });

    // OPCIONAL: También preservar otros campos críticos del sistema
    ["created_at", "updated_at", "paciente_id"].forEach((systemField) => {
        if (editedItem[systemField]) {
            cleanedItem[systemField] = editedItem[systemField];
        }
    });

    // DEBUGGING ESPECÍFICO PARA fecha_vencimiento en edición
    console.log("=== DEBUG EDICIÓN fecha_vencimiento ===");
    console.log("Item original:", editedItem);
    console.log("fecha_vencimiento original:", editedItem.fecha_vencimiento);

    // Convertir campos de fecha de formato 'DD/MM/YYYY' a formato ISO (YYYY-MM-DD) para el input
    dateFields.forEach((field) => {
        if (cleanedItem[field]) {
            console.log(`Campo ${field} original en edición:`, cleanedItem[field]);

            // Verificar si es la fecha problemática
            if (cleanedItem[field] === '01/01/1900' || cleanedItem[field] === '1900-01-01') {
                console.log(`⚠️ DETECTADA FECHA PROBLEMÁTICA en edición para ${field}: ${cleanedItem[field]}`);
                cleanedItem[field] = null;
                console.log(`✅ Campo ${field} limpiado en edición`);
                return;
            }

            // Validar que la fecha no sea null, undefined o "Invalid date"
            if (
                cleanedItem[field] === null ||
                cleanedItem[field] === undefined ||
                cleanedItem[field] === "Invalid date" ||
                cleanedItem[field] === ""
            ) {
                cleanedItem[field] = null;
                console.log(`Campo ${field} limpiado por ser inválido`);
                return;
            }

            // Si viene en formato DD/MM/YYYY, convertir a YYYY-MM-DD
            if (
                typeof cleanedItem[field] === "string" &&
                cleanedItem[field].includes("/")
            ) {
                const momentDate = moment(cleanedItem[field], "DD/MM/YYYY");
                if (momentDate.isValid()) {
                    const convertedDate = momentDate.format("YYYY-MM-DD");
                    console.log(`Campo ${field} convertido en edición:`, convertedDate);
                    cleanedItem[field] = convertedDate;
                } else {
                    console.log(`Campo ${field} inválido en edición, limpiando`);
                    cleanedItem[field] = null;
                }
            } else {
                // Si ya está en formato ISO, validar y mantenerlo
                const momentDate = moment(cleanedItem[field]);
                if (momentDate.isValid()) {
                    const maintainedDate = momentDate.format("YYYY-MM-DD");
                    console.log(`Campo ${field} mantenido en edición:`, maintainedDate);
                    cleanedItem[field] = maintainedDate;
                } else {
                    console.log(`Campo ${field} inválido en formato ISO en edición, limpiando`);
                    cleanedItem[field] = null;
                }
            }
        }
    });

    console.log("=== FIN DEBUG EDICIÓN fecha_vencimiento ===");
    console.log("fecha_vencimiento final en edición:", cleanedItem.fecha_vencimiento);

    state.editedItem = cleanedItem;
    state.editedIndex = state.tableItems.indexOf(item);
    state.formTitle = "Editar";
    state.dialogForm = true;
}

// TODO: trabajar en la validaciones en el frontend
async function guardarItem() {
    const formData = form.value?.formData || {};

    // Identificar campos de fecha y convertirlos al formato correcto para el servidor
    const dateFields = props.itemForm.fields
        .filter((field) => field.type === "date")
        .map((field) => field.key);

    // DEBUGGING ESPECÍFICO PARA fecha_vencimiento
    console.log("=== DEBUG FECHA_VENCIMIENTO ===");
    console.log("FormData original:", formData);
    console.log("fecha_vencimiento en formData:", formData.fecha_vencimiento);
    console.log("Campos de fecha detectados:", dateFields);

    // Convertir fechas del formato yyyy-mm-dd (input) al formato que espera el servidor
    const processedData = { ...formData };
    dateFields.forEach((field) => {
        console.log(`Procesando campo fecha: ${field}`);
        console.log(`Valor original de ${field}:`, processedData[field]);
        
        if (processedData[field]) {
            // Verificar si el valor es la fecha problemática por defecto
            if (processedData[field] === '1900-01-01') {
                console.log(`⚠️ DETECTADA FECHA PROBLEMÁTICA en ${field}: ${processedData[field]}`);
                // Cambiar a null en lugar de la fecha problemática
                processedData[field] = null;
                console.log(`✅ Campo ${field} limpiado a null`);
            } else {
                // Convertir de yyyy-mm-dd a formato ISO para el servidor
                const convertedDate = moment(processedData[field], "YYYY-MM-DD").format("YYYY-MM-DD");
                console.log(`Campo ${field} convertido de ${processedData[field]} a ${convertedDate}`);
                processedData[field] = convertedDate;
            }
        } else {
            console.log(`Campo ${field} está vacío o es null`);
        }
    });

    const data = {
        ...processedData,
        paciente_id: props.pacienteId,
    };

    console.log("=== FIN DEBUG FECHA_VENCIMIENTO ===");
    console.log("Data final a enviar:", data);
    console.log("fecha_vencimiento final:", data.fecha_vencimiento);

    // CRÍTICO: Asegurar que el ID se preserve en updates
    if (state.editedItem.id) {
        data.id = state.editedItem.id;
        console.log("Update con ID:", data.id); // Debug
    }

    // Verificar que tenemos datos
    if (!formData || Object.keys(formData).length === 0) {
        notify({
            title: "Error",
            text: "No se pudieron obtener los datos del formulario",
            type: "error",
        });
        return;
    }
    state.saving = true;
    state.errors = {};
    try {
        if (state.editedItem.id) {
            console.log("Ejecutando PUT para ID:", state.editedItem.id); // Debug
            console.log("URL:", endpoints.apiUrl + "/" + state.editedItem.id); // Debug
            console.log("Data enviada:", data); // Debug

            await put(endpoints.apiUrl + "/" + state.editedItem.id, {
                data,
            });
            notify({
                title: "Éxito",
                text: "Registro actualizado",
                type: "success",
            });
        } else {
            console.log("Ejecutando POST (nuevo registro)"); // Debug
            await post(endpoints.apiUrl, { data });
            notify({
                title: "Éxito",
                text: "Registro creado",
                type: "success",
            });
        }
        state.errors = {};
        state.dialogForm = false;
        await cargarItems();
    } catch (e) {
        if (
            e.response &&
            e.response.status === 422 &&
            e.response.data &&
            e.response.data.errors
        ) {
            state.errors = e.response.data.errors;
            notify({
                title: "Error de validación",
                text: "Corrige los campos marcados en el formulario.",
                type: "error",
            });
        } else {
            notify({
                title: "Error",
                text: e.message || "No se pudo guardar el paciente",
                type: "error",
            });
        }
    } finally {
        state.saving = false;
    }
}

async function eliminarItem(item) {
    state.itemToDelete = item;
    state.dialogDelete = true;
}

async function deleteItemConfirmed() {
    if (!state.itemToDelete) return;
    state.deleting = true;
    try {
        await remove(endpoints.apiUrl + "/" + state.itemToDelete.id);
        notify({
            title: "Éxito",
            text: "Registro eliminado correctamente",
            type: "success",
        });
        cargarItems();
    } catch (error) {
        console.error("Error eliminando item:", error);
        notify({
            title: "Error",
            text: "No se pudo eliminar el registro",
            type: "error",
        });
    } finally {
        state.dialogDelete = false;
        state.itemToDelete = null;
        state.deleting = false;
    }
}

async function cargarItems() {
    state.loading = true;
    console.log("withRelations =>", catalogs);
    try {
        const dataTable = await show(endpoints.apiUrl, props.pacienteId, {
            withRelations: catalogs,
        });
        console.log("dataTable =>", dataTable);

        // Identificar campos de fecha desde la configuración de headers
        const dateFields = props.tableConfig.headers
            .filter((header) => header.type === "date")
            .map((header) => header.key);

        const formatDateFields = (item) => {
            const newItem = { ...item };
            dateFields.forEach((field) => {
                if (newItem[field]) {
                    // Parsear la fecha en formato ISO y formatear como DD/MM/YYYY
                    const parsedDate = moment(newItem[field], "YYYY-MM-DD");
                    if (parsedDate.isValid()) {
                        newItem[field] = parsedDate.format("DD/MM/YYYY");
                    } else {
                        // Si no es válida en formato ISO, intentar otros formatos
                        const fallbackDate = moment(newItem[field]);
                        if (fallbackDate.isValid()) {
                            newItem[field] = fallbackDate.format("DD/MM/YYYY");
                        }
                    }
                }
            });
            return newItem;
        };

        state.tableItems = Array.isArray(dataTable)
            ? dataTable.map(formatDateFields)
            : (dataTable?.data ?? []).map(formatDateFields);
    } catch (error) {
        console.error("Error cargando items:", error);
    } finally {
        state.loading = false;
    }
}

// Add method to load catalogs
const loadCatalogs = async () => {
    state.loading = true;
    try {
        state.list = await fetchCatalogs(state.catalogs);
    } catch (e) {
        notify({
            title: "Error",
            text: "No se pudieron cargar los catálogos",
            type: "error",
        });
    } finally {
        state.loading = false;
    }
};

function closeForm() {
    state.dialogForm = false;
}

// Call loadCatalogs on component mount
onMounted(async () => {
    await cargarItems();
    await loadCatalogs();
});
</script>

<template>
    <v-container fluid>
        <v-toolbar-title elevation="6" class="ma-4 pa-6" data-cy="titulo">
            {{ titulo }}
        </v-toolbar-title>
        <v-divider thickness="8px" color="#662d91" />
        <div data-cy="tabla">
            <v-data-table
                :headers="headers"
                :items="state.tableItems"
                :options.sync="state.options"
                :server-items-length="state.totalItems"
                :loading="state.loading"
                class="elevation-1"
            >
                <!-- Dynamic slots for each column -->
                <template
                    v-for="header in headers"
                    v-slot:[`item.${header.value}`]="{ item }"
                >
                    <slot :name="`column.${header.value}`" :item="item">
                        {{ item[header.value] }}
                    </slot>
                </template>

                <!-- Default actions slot -->
                <template v-slot:top>
                    <v-toolbar flat>
                        <v-btn
                            icon="mdi-account-multiple-plus"
                            variant="tonal"
                            class="ma-4"
                            color="#662d91"
                            @click="openFormCreate"
                            v-if="user.rol != 'admin-ext'"
                        />
                    </v-toolbar>
                </template>
                <template v-slot:item.actions="{ item }">
                    <slot name="actions" :item="item">
                        <v-icon
                            color="#662d91"
                            small
                            class="mr-2"
                            @click="openFormEdit(item)"
                        >
                            mdi-file-document-edit
                        </v-icon>
                        <v-icon
                            color="#662d91"
                            small
                            @click="eliminarItem(item)"
                            v-if="user.rol != 'admin-ext'"
                        >
                            mdi-delete
                        </v-icon>
                    </slot>
                </template>
            </v-data-table>
        </div>
        <v-dialog
            v-model="state.dialogForm"
            :max-width="
                formFields.length === 1
                    ? 500
                    : formFields.length === 2
                      ? 800
                      : 1200
            "
            data-cy="form-dialog"
        >
            <v-card>
                <v-card-title color="#009AA4">
                    <v-toolbar-title
                        elevation="6"
                        class="ma-6"
                        data-cy="titulo"
                    >
                        {{ state.formTitle }}
                    </v-toolbar-title>
                </v-card-title>
                <v-divider thickness="4px" color="#009AA4" />
                <v-card-text>
                    <FormData
                        :fields="formFields"
                        :initial-data="state.editedItem"
                        :catalogs="state.list"
                        ref="form"
                    />
                </v-card-text>
                <v-card-actions>
                    <v-btn
                        @click="guardarItem"
                        data-cy="btn-guardar"
                        :disabled="state.saving"
                        variant="tonal"
                        color="#009AA4"
                        v-if="user.rol != 'admin-ext'"
                    >
                        <v-spacer></v-spacer>
                        <v-progress-circular
                            v-if="state.saving"
                            indeterminate
                            size="20"
                            width="2"
                        ></v-progress-circular>
                        <span v-else>Guardar</span>
                    </v-btn>

                    <v-btn color="purple" variant="tonal" @click="closeForm"
                        >Cancelar</v-btn
                    >
                </v-card-actions>
            </v-card>
        </v-dialog>
        <ConfirmDialog
            v-model="state.dialogDelete"
            question="¿Está seguro que desea eliminar este registro?"
            description="Esta acción no se puede deshacer y eliminará permanentemente el registro del paciente."
            :confirm-color="'red-darken-1'"
            :confirm-icon="'mdi-delete'"
            :confirm-text="'Eliminar'"
            :cancel-text="'Cancelar'"
            @confirm="deleteItemConfirmed"
            @cancel="closeDelete"
        />
    </v-container>
</template>
<style scoped>
/* Puedes agregar estilos personalizados aquí */
</style>
