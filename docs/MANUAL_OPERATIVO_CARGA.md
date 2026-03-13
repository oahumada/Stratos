# 📔 Manual de Operación: Carga Maestra de Datos Stratos

Este documento sirve como guía administrativa y operativa para la implementación de Stratos en una nueva organización. Siga este orden estrictamente para garantizar que los motores de IA (Impact, Vanguard, Cerbero) funcionen con datos íntegros.

---

## 🏗️ Nivel 1: El Esqueleto (Estructura Organizacional)
**Frecuencia:** Una vez al inicio o ante cambios estructurales.

### 1.1 Departamentos (Nodos Gravitacionales)
Antes de cargar personas, la organización debe estar definida en Stratos.
- **Acción:** Cargar el organigrama actual.
- **Configuración Crítica:** Para cada departamento, defina sus **Aliases**.
    - *¿Por qué?* Diferentes sistemas (ERP, RRHH, Finanzas) llaman al mismo depto de forma distinta (Ej: "Mkt", "Marketing", "101-MKT"). El alias permite que Stratos unifique la información automáticamente.

### 1.2 Roles y Competencias
- **Acción:** Definir el catálogo de cargos.
- **Configuración Crítica:** Cada rol DEBE tener niveles requeridos de competencia (`required_level`) del 1 al 5.
- **Impacto:** Sin niveles requeridos, no existe el "Gap Analysis" y el ROI de capacitación será $0.

---

## 👥 Nivel 2: El Gemelo Digital (Capital Humano)
**Frecuencia:** Mensual o ante altas/bajas.

### 2.1 Personas
- **Acción:** Carga masiva de empleados.
- **Vinculación Obligatoria:** Cada persona debe estar vinculada a un `department_id` y un `role_id` existentes.
- **Identificadores:** Use siempre el mismo identificador único (Email o ID de empleado) para evitar duplicidad en el grafo social.

---

## 📊 Nivel 3: El Pulso de Negocio (Impact Engine)
**Frecuencia:** Mensual o Trimestral.

### 3.1 Métricas Financieras y Operativas
- **Comando:** `php artisan stratos:ingest-metrics {archivo.csv}`
- **Campos Mínimos Necesarios (CSV):**
    - `revenue`: Ingresos totales de la unidad.
    - `opex`: Gastos operativos totales.
    - `payroll_cost`: Costo total de nómina e impuestos.
    - `headcount`: Número de empleados (FTE).
- **Procedimiento de Unificación:** El comando buscará el nombre del departamento en la columna `department`. Si coincide con un nombre real o con un **Alias**, la métrica se asignará correctamente.

---

## 🛡️ Nivel 4: Control de Calidad e Integridad
**Frecuencia:** Antes de cada reporte ejecutivo.

Antes de presentar datos a la dirección, el operador administrativo DEBE correr el comando de integridad:

```bash
php artisan stratos:check-integrity --org=1
```

### Qué revisar en el reporte de integridad:
- **Personas Huérfanas:** Si hay personas sin depto/rol, el HCVA estará subestimado.
- **Métricas Faltantes:** Si falta `payroll_cost`, el Impact Engine no podrá separar el valor agregado humano de los gastos generales.
- **Gaps de Evaluación:** Si hay personas sin habilidades cargadas, Stratos no podrá sugerir planes de movilidad.

---

## 📈 Resumen del Flujo de Éxito
1. **Configurar Deptos + Aliases** (Nodo Gravitacional).
2. **Configurar Roles + Skill Targets**.
3. **Cargar Personas**.
4. **Cargar Métricas de Negocio**.
5. **Ejecutar Check de Integridad**.
6. **Explorar resultados en Radar Landing**.
