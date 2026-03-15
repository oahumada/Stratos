# Guía de Implementación: Stratos Floating Glass Modals

Para replicar el diseño de modales premium en cualquier parte de la aplicación que no dependa de `FormSchema.vue`, sigue este patrón estándar.

## 1. Estructura Base del Componente
Utiliza `v-dialog` de Vuetify combinado con el componente `StCardGlass`.

```html
<v-dialog
    v-model="miDialogo"
    width="1100" <!-- Ajustable según contenido -->
    transition="dialog-transition"
>
    <!-- Clase st-modal-glass aplica los márgenes y altura máxima -->
    <StCardGlass
        class="st-modal-glass pa-0" 
        :no-hover="true"
    >
        <!-- CABECERA: Fondo gradiente sutil y división limpia -->
        <div class="st-modal-header-gradient">
            <div class="text-h6 font-premium text-white">Título del Modal</div>
            <v-spacer />
            <v-btn icon variant="text" @click="miDialogo = false" class="rounded-lg">
                <PhX :size="20" />
            </v-btn>
        </div>

        <!-- CUERPO: Con scroll interno automático -->
        <div class="st-modal-body">
            <!-- Tu contenido aquí -->
        </div>

        <!-- PIE DE PÁGINA: Botones de acción alineados a la derecha -->
        <div class="st-modal-footer">
            <v-btn variant="text" color="slate-400" @click="miDialogo = false">
                Cancelar
            </v-btn>
            <v-btn variant="elevated" color="indigo-accent-2" rounded="lg">
                Confirmar Acción
            </v-btn>
        </div>
    </StCardGlass>
</v-dialog>
```

## 2. Clases Estándar Disponibles
He añadido estas clases globalmente en `app.css` para que puedas usarlas en cualquier archivo:

- `.st-modal-glass`: Define el comportamiento de capa flotante (85vh de alto, margen de 2rem y flex column).
- `.st-modal-header-gradient`: Aplica el fondo premium con gradiente y el destello de luz lateral.
- `.st-modal-body`: Configura el padding estándar y habilita el scroll vertical (`overflow-y-auto`).
- `.st-modal-footer`: Define el contenedor para los botones de acción con su borde superior.

## 3. Mejores Prácticas
- **Ancho Preferido**: Usa `width="1200"` para formularios complejos o `width="700"` para mensajes informativos.
- **Acciones**: Siempre usa `variant="elevated"` o `variant="tonal"` para el botón principal y `variant="text"` para el secundario/cancelar.
- **Iconos**: Utiliza los iconos de la librería Phosphor (como `PhX`, `PhPlus`, etc.) para mantener la coherencia.
