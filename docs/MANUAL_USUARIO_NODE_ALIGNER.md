# 🎨 Manual de Usuario: Stratos Node Aligner

El **Stratos Node Aligner** es la interfaz premium diseñada para que los líderes de talento sincronicen la realidad operativa de la empresa con el Gemelo Digital de Stratos. Este manual guía al usuario a través del proceso de carga y validación.

## Etapa 1: Carga y Parsing Inteligente
El proceso comienza subiendo un archivo CSV/Excel con la nómina actualizada.
- **Detección Automática**: Stratos lee las cabeceras y mapea campos como `email`, `department`, `role` y `hire_date`.
- **Mapeo de Nodos**: El sistema utiliza el motor de unificación para identificar si los departamentos escritos en el archivo ya existen o son nuevos.

## Etapa 2: El Tablero de Alineación (Alineación y Resolución)
Antes de aplicar cambios, el usuario visualiza un análisis de impacto dividido en tres dimensiones:

### 1. Nodos Estructurales
- **Departamentos**: Se muestran los matches encontrados y los nuevos nodos que se crearán.
- **Roles/Cargos**: Stratos sugiere la alineación con los roles estratégicos existentes para evitar la fragmentación de la estructura.

### 2. Dinámica de Trayectoria (Lifecycle)
El sistema presenta un resumen visual de:
- **Nuevos Ingresos**: Personas detectadas por primera vez.
- **Traslados y Ascensos**: Cambios detectados en el departamento o cargo de empleados existentes.
- **Egresos**: Alertas sobre personas que ya no figuran en la nómina nueva, permitiendo gestionar su salida del Gemelo Digital.

## Etapa 3: Auditoría y Firma Digital
Para garantizar que la estructura organizacional tiene un responsable claro, el paso final requiere una firma digital.

- **Establecimiento de Baseline**: Al firmar, se crea una "Versión 0" o "Versión Actual" de la organización.
- **Metadata de Auditoría**: Se registra quién aprobó la carga, desde qué IP y en qué fecha exacta.
- **Snapshot Organizacional**: Se congela una foto del **Stratos IQ** en ese momento, permitiendo ver la evolución histórica tras cada sincronización.

---

## 💡 Mejores Prácticas
1. **Validación de Emails**: El email es la clave única. Asegúrate de que los correos electrónicos sean consistentes entre cargas.
2. **Revisión de Alias**: Si un departamento aparece como "Nuevo" pero ya existe con otro nombre, es recomendable cancelar y añadir el alias correspondiente en la configuración de departamentos antes de reintentar.
3. **Firma con Propósito**: La firma digital no es solo un trámite; establece la responsabilidad legal y administrativa sobre los movimientos de personal en el sistema.

---

> [!NOTE]
> **Integridad Referencial**: Stratos no borra datos físicamente durante los egresos; los marca como inactivos en el historial de `person_movements` para mantener la integridad de los análisis de rotación histórica.
