# 🌌 Stratos Node Aligner: La Génesis del Gemelo Digital

El módulo **Stratos Node Aligner** no es solo un importador; es la herramienta estratégica diseñada para la creación y calibración del **[Gemelo Digital](./Architecture/DIGITAL_TWIN.md)** de la organización. Su propósito principal es transformar nóminas de personal fragmentadas en una estructura organizacional viva, alineada y auditable que sirve como el "Estado Maestro" del sistema.

## 🧠 El Concepto: Unificación de Nodos Gravitacionales

A diferencia de un importador estándar, el **Node Aligner** utiliza el concepto de "Nodos Gravitacionales". Esto significa que el sistema no solo busca coincidencias exactas de texto, sino que entiende que diferentes nombres (alias) pueden orbitar alrededor de una misma identidad organizacional (nodo).

- **Departamentos**: Actúan como nodos padres. El sistema utiliza una capa de `aliases` para unificar términos como "Ventas Sucursal A" y "Vtas Nacionales" bajo el nodo único "Ventas".
- **Roles**: Los cargos en el archivo se alinean con roles existentes en la ontología de la empresa o se proponen como nuevos roles estratégicos si no existe match.

---

## 🏗️ Proceso de Importación (Wizard de 4 Pasos)

El flujo está diseñado para garantizar que un responsable humano (consultor o jefe de área) valide la estructura antes de que los datos impacten el núcleo del sistema.

### 1. Carga de Datos (Phase: Upload)
- **Entrada**: Archivos CSV/Excel.
- **Acción**: El sistema realiza un parseo local para previsualizar los registros.
- **Detalle Técnico**: Se gestiona mediante el componente `BulkPeopleImporter.vue` con soporte para múltiples formatos.

### 2. Vista Previa y Mapeo (Phase: Preview)
- **Acción**: Muestra una tabla con los primeros registros detectados.
- **Objetivo**: Asegurar que las columnas de nombres, correos, cargos y departamentos se han identificado correctamente.

### 3. Alineación Organizacional (Phase: Resolution)
Es la fase de mayor valor. El backend (`BulkPeopleImportController@analyze`) cruza los datos del archivo con la base de datos de Stratos.
- **Identificación de Nodos**: El sistema clasifica cada entidad detectada:
  - <span style="color:#10b981">**Verde (Existing)**</span>: Se detectó un match perfecto o mediante un alias conocido.
  - <span style="color:#f59e0b">**Ámbar (New)**</span>: El sistema no reconoce el nodo y propone crearlo, o sugiere una alineación estratégica.
- **Aprendizaje de Alias**: Si el usuario confirma un match para un nombre abreviado, ese nombre se guarda como `alias` en el nodo correspondiente para futuras importaciones.

### 4. Aprobación y Baseline (Phase: Signature)
Para establecer una cultura de responsabilidad y auditoría:
- **Firma Digital**: El responsable debe realizar una firma digital simbólica (token de validación).
- **Creación de Baseline**: Al confirmar, el sistema genera automáticamente un **`OrganizationSnapshot`**. Este "Snapshot Versión 0" es crítico para trackear la evolución de la empresa (Learning Velocity, Gap Closure) a partir de este punto exacto.

---

## 🛠️ Especificaciones Técnicas

### 📂 Backend (Laravel)
- **Controlador**: `app/Http/Controllers/Api/BulkPeopleImportController.php`
- **Rutas**:
  - `POST /api/talent/bulk-import/analyze`: Análisis semántico.
  - `POST /api/talent/bulk-import/stage`: Creación de `ChangeSet` (borrador).
  - `POST /api/talent/bulk-import/{id}/approve`: Commit atómico y firma.

### 📂 Frontend (Vue 3 + Stratos Glass)
- **Componente**: `resources/js/components/Talent/BulkPeopleImporter.vue`
- **Visual**: Wizard con estética **Glassmorphism**, desenfoques progresivos y micro-interacciones que comunican un sentimiento de "tecnología de vanguardia".

---

## 📋 Recomendaciones Operativas

1. **Limpieza Previa**: Aunque el sistema es inteligente, se recomienda que el archivo CSV tenga los encabezados claros (`name`, `last_name`, `email`, `department`, `role`).
2. **Revisión de Nodos Ámbar**: Es vital que el consultor revise los departamentos marcados como "Nuevos" para evitar duplicidad de estructuras (ej. evitar crear "Dpto de Ventas" si ya existe "Ventas").
3. **Audit Trail**: Todos los cambios de esta fase se guardan bajo el modelo `ChangeSet`, permitiendo auditar quién cargó qué datos, desde qué IP y en qué fecha exacta.

---

> [!IMPORTANT]
> **Stratos Node Aligner** no es solo un gestor de carga de datos; es el guardián de la integridad de la estructura organizacional que permite que el motor de IA de Stratos funcione con precisión milimétrica.
