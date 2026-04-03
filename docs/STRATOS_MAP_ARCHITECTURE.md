# 🌌 Stratos Map: Nodos Gravitacionales y Mapa Cerberos

Este documento detalla la funcionalidad de visualización organizacional avanzada de Stratos, que permite pasar de una jerarquía estática a una representación dinámica del talento y el liderazgo.

## 1. Conceptos Fundamentales

Stratos rompe el paradigma del organigrama tradicional mediante dos capas de visualización complementarias:

### A. Nodos Gravitacionales (Masa de Talento)
Representa la organización como un sistema de cuerpos celestes donde los **Departamentos** son nodos cuya relevancia es proporcional a su "masa".

*   **Masa Gravitacional**: Definida por el Headcount (número de personas), Payroll (costo salarial) o Densidad de Skills.
*   **Afinidad**: La cercanía entre nodos no es jerárquica, sino relacional (departamentos que comparten procesos o competencias orbitan más cerca).
*   **Estética**: Diseño **Stratos Glass** con esferas translúcidas, brillos dinámicos y conexiones de "fuerza" invisibles.

### B. Mapa Cerberos (Estructura de Liderazgo)
Utiliza la relación de supervisión directa e indirecta para mapear el alcance real de los líderes en la organización.

*   **Los Tres "Cabezas" de Cerberos**:
    1.  **Núcleo**: El Líder (Manager) como punto de origen.
    2.  **Línea de Defensa**: Reportes directos inmediatos.
    3.  **Radio de Influencia**: Reportes de segundo nivel y subordinados en cascada.
*   **Propósito**: Identificar cuellos de botella en la supervisión, tramos de control excesivos y la "salud" del liderazgo en tiempo real.

---

## 2. Infraestructura Técnica

La funcionalidad se apoya en una estructura de base de datos optimizada para consultas recursivas:

### Modelo de Datos (`People.php`)
Se ha implementado el campo `supervised_by` para habilitar relaciones de jerarquía infinita:

```php
// Relaciones de jerarquía directa
public function supervisor() {
    return $this->belongsTo(People::class, 'supervised_by');
}

public function directReports() {
    return $this->hasMany(People::class, 'supervised_by');
}
```

### Sanitización y Normalización
Para garantizar que el mapa sea preciso, se han implementado herramientas de limpieza automática en `TalentDataSanitizer.php`:
*   **Limpieza de Nombres**: Eliminación automática de comillas y caracteres especiales en la importación.
*   **Normalización de Fechas**: Conversión automática de formatos `d/m/Y` a estándar de base de datos para evitar errores 500 durante la edición.

---

## 3. Gestión y Mantenimiento

### Importación Masiva
El componente `BulkPeopleImporter.vue` ahora cuenta con un **Parser CSV Robusto** que:
1.  Maneja campos encerrados entre comillas.
2.  Detecta automáticamente relaciones de supervisión si el archivo incluye la columna correspondiente.
3.  Sanitiza los datos en caliente antes de la previsualización.

### Edición Manual
Desde el módulo de **Personas**, el usuario puede:
*   Asignar o cambiar supervisores mediante buscadores inteligentes (Catalogs).
*   Visualizar en la tabla principal quién es el supervisor de cada colaborador de forma inmediata.

---

## 4. Visualización

El motor de renderizado es híbrido y se adapta a la naturaleza del dato:

*   **Vue Flow + Dagre**: Utilizado para el **Organigrama Activo**. Permite un lienzo infinito de alto rendimiento con auto-layout jerárquico y reestructuración interactiva mediante Drag & Drop.
*   **D3.js**: Utilizado para los **Nodos Gravitacionales** y el **Mapa Cerberos**. Maneja simulaciones de física (fuerzas, colisiones) para representar la masa de talento y afinidad relacional.
*   **Diseño Stratos Glass**: Todas las visualizaciones aplican efectos de transparencia, desenfoque de fondo y brillos dinámicos para una experiencia premium.
