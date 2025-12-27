# 🔍 RÚBRICA DE VALIDACIÓN DE ESTADO - Por Módulo

**Tiempo estimado: 15-20 minutos**
**Frecuencia: Inicio de cada módulo + Cierre de cada semana**
**Responsabilidad: Líder técnico / Product owner**

---

## 🎯 Propósito

Esta rúbrica **responde en forma objetiva:**

- ¿En qué estado está el módulo hoy?
- ¿Qué falta realmente?
- ¿Qué riesgos hay?
- ¿Puedo empezar día X con confianza?

**NO es subjetiva.** Cada pregunta tiene respuesta verificable (SÍ/NO/N).

---

## 📋 PARTE 1: VALIDACIÓN DE REQUISITOS (5 min)

### 1.1 ¿Tengo memories.md?

```bash
# Comando:
ls -la docs/memories.md
wc -l docs/memories.md
```

| Criterio                 | Verificar              | Estado    | Evidencia                             |
| ------------------------ | ---------------------- | --------- | ------------------------------------- |
| Archivo existe           | ¿Existe memories.md?   | ☐ Sí ☐ No | `ls -la docs/memories.md`             |
| Tiene contenido          | ¿Más de 50 líneas?     | ☐ Sí ☐ No | `wc -l docs/memories.md`              |
| Sección 1: Contexto      | ¿Define el problema?   | ☐ Sí ☐ No | Lee líneas 1-20                       |
| Sección 2: Usuarios      | ¿Define quién lo usa?  | ☐ Sí ☐ No | Busca "Usuario" o "Rol"               |
| Sección 3: Requisitos    | ¿Lista requisitos?     | ☐ Sí ☐ No | Busca "Requisito" o "Debe"            |
| Sección 4: Casos de uso  | ¿Casos de uso claros?  | ☐ Sí ☐ No | Busca "Caso 1", "Caso 2"              |
| Sección 5: Restricciones | ¿Edge cases / límites? | ☐ Sí ☐ No | Busca "Restricción", "Edge case"      |
| Sección 6: Datos         | ¿Estructura clara?     | ☐ Sí ☐ No | Busca "Tabla", "Campos", "Relaciones" |

**Resultado:**

- ☐ ROJO (< 4 sí) → Completa memories.md ANTES de cualquier código
- ☐ AMARILLO (4-6 sí) → Memories existe pero incompleto, revisa mientras codificas
- ☐ VERDE (7/7 sí) → Listo para empezar

---

### 1.2 ¿Entiendo los Requisitos?

**Responde estas 5 preguntas (sin mirar memories.md):**

1. **¿Cuál es el problema que resuelve este módulo?**

    ```
    Respuesta: ________________________________
    Verificar en memories.md sección 1: ¿Coincide? ☐ Sí ☐ No
    ```

2. **¿Quién usa esto y qué quiere lograr?**

    ```
    Respuesta: ________________________________
    Verificar en memories.md sección 2-3: ¿Coincide? ☐ Sí ☐ No
    ```

3. **¿Cuáles son los 3 requisitos principales?**

    ```
    1. _____________________________________
    2. _____________________________________
    3. _____________________________________
    Verificar en memories.md: ¿Todos están allí? ☐ Sí ☐ No
    ```

4. **¿Cuál es el caso de uso más crítico?**

    ```
    Respuesta: ________________________________
    Verificar en memories.md sección 4: ¿Está documentado? ☐ Sí ☐ No
    ```

5. **¿Cuál es el MAYOR riesgo / edge case?**
    ```
    Respuesta: ________________________________
    Verificar en memories.md sección 5: ¿Está considerado? ☐ Sí ☐ No
    ```

**Resultado:**

- Si respondiste correctamente 5/5 → Conocimiento listo ✅
- Si respondiste 3-4/5 → Necesitas releer memories.md
- Si < 3 → STOP. No codifiques hasta entender requisitos.

---

## 📊 PARTE 2: VALIDACIÓN TÉCNICA (5 min)

### 2.1 Estado de Base de Datos

```bash
# Ejecuta estos comandos:
php artisan migrate:status | grep pending
php artisan tinker
>>> Schema::getTables()
>>> DB::table('tabla_del_modulo')->count()
exit
```

| Componente  | Verificar                  | ☐ Estado  | Comando                                      |
| ----------- | -------------------------- | --------- | -------------------------------------------- |
| Migraciones | ¿Todas migradas?           | ☐ Sí ☐ No | `php artisan migrate:status`                 |
| Tablas      | ¿Existen tablas esperadas? | ☐ Sí ☐ No | `php artisan tinker` → `Schema::getTables()` |
| Datos seed  | ¿Hay datos para probar?    | ☐ Sí ☐ No | `DB::table('x')->count()`                    |
| Relaciones  | ¿FK están definidas?       | ☐ Sí ☐ No | Revisa migraciones                           |

**Resultado:**

- ☐ ROJO → Executa `php artisan migrate && php artisan db:seed`
- ☐ VERDE → BD lista

---

### 2.2 Estado del Código Backend

```bash
# Terminal:
ls -la app/Models/ | grep -i "nombre_modulo"
ls -la app/Http/Controllers/ | grep -i "nombre_modulo"
grep -r "nombre_modulo" routes/api.php | wc -l
php artisan test
```

| Componente  | Verificar           | Estado    | Ubicación                                   |
| ----------- | ------------------- | --------- | ------------------------------------------- |
| Modelos     | ¿Existen?           | ☐ Sí ☐ No | `app/Models/NombreModelo.php`               |
| Controllers | ¿Existen?           | ☐ Sí ☐ No | `app/Http/Controllers/NombreController.php` |
| Rutas API   | ¿Están registradas? | ☐ Sí ☐ No | `routes/api.php` (grep "nombre")            |
| Tests       | ¿Tests existen?     | ☐ Sí ☐ No | `tests/Feature/*ModuleTest.php`             |
| Tests pasan | ¿Todos PASS?        | ☐ Sí ☐ No | `php artisan test`                          |

**Resultado:**

- ☐ ROJO (tests fallan) → Fix tests ANTES de frontend
- ☐ AMARILLO (incomplete) → Sigue plan según GUIA_DESARROLLO_ESTRUCTURADO.md
- ☐ VERDE → Backend listo para frontend

---

### 2.3 Estado del Frontend

```bash
# Terminal:
ls -la resources/js/Pages/ | grep -i "nombre_modulo"
ls -la resources/js/Components/ | grep -i "nombre_modulo"
npm run lint 2>&1 | grep error
```

| Componente       | Verificar          | Estado    | Ubicación                     |
| ---------------- | ------------------ | --------- | ----------------------------- |
| Páginas          | ¿Existen?          | ☐ Sí ☐ No | `resources/js/Pages/...`      |
| Componentes      | ¿Existen?          | ☐ Sí ☐ No | `resources/js/Components/...` |
| Composables      | ¿useApi integrado? | ☐ Sí ☐ No | `resources/js/composables/`   |
| Sin errores lint | ¿npm run lint OK?  | ☐ Sí ☐ No | Terminal                      |

**Resultado:**

- ☐ ROJO → Hay errores de sintaxis (fix primero)
- ☐ AMARILLO → Frontend incomplete
- ☐ VERDE → Frontend listo

---

## 🔄 PARTE 3: VALIDACIÓN DE CONTINUIDAD (3 min)

### 3.1 ¿Qué Falta?

```bash
# Responde basándote en memories.md:
```

**Matriz de Requisitos vs Implementación:**

```markdown
| Requisito (de memories.md) | Implementado        | %   | Evidencia         |
| -------------------------- | ------------------- | --- | ----------------- |
| Req 1: [descripción]       | ☐ Sí ☐ No ☐ Parcial | \_% | Endpoint/Página X |
| Req 2: [descripción]       | ☐ Sí ☐ No ☐ Parcial | \_% | Modelo/Service X  |
| Req 3: [descripción]       | ☐ Sí ☐ No ☐ Parcial | \_% | Test X            |
| Req 4: [descripción]       | ☐ Sí ☐ No ☐ Parcial | \_% | Lógica X          |
| Req 5: [descripción]       | ☐ Sí ☐ No ☐ Parcial | \_% | Documento X       |
```

**Cálculo de completitud:**

```
Total % = (SÍ × 100 + Parcial × 50) / Total de requisitos
```

---

### 3.2 Bloqueadores Actuales

```markdown
| Bloqueador    | Tipo                | Impacto         | Solución Propuesta |
| ------------- | ------------------- | --------------- | ------------------ |
| [Descripción] | Frontend/Backend/BD | Alto/Medio/Bajo | [Cómo resolver]    |
| [Descripción] | [Tipo]              | [Impacto]       | [Solución]         |
```

**Clasificación:**

- **CRÍTICO** (rojo) → Resuelve ANTES de continuar
- **IMPORTANTE** (amarillo) → Resuelve hoy
- **BAJO** (verde) → Puede esperar a mañana

---

### 3.3 ¿Puedo Empezar el Día X?

**Responde SÍ solo si:**

```
☐ memories.md está 100% completo
☐ Entiendo los 5 requisitos principales
☐ Sé cuál es mi responsabilidad del día X
☐ BD está migrada y con datos seed
☐ No hay bloqueadores CRÍTICOS
☐ Tengo acceso a código del día anterior
☐ Entiendo qué debe validar completitud hoy
☐ Tengo plan desglosado en 2 bloques
```

**Si 7 u 8 sí:** ✅ **LISTO PARA EMPEZAR**
**Si 5-6 sí:** ⚠️ **Resuelve lo rojo y amarillo primero**
**Si < 5 sí:** 🔴 **STOP - Necesitas más contexto**

---

## 📈 PARTE 4: MATRIZ DE PROGRESIÓN (2 min)

**Esta matriz te muestra dónde estás y adónde vas:**

```markdown
# Estado del Módulo [NOMBRE]

## Línea de Tiempo
```

Día 1: BD + Modelos [████████░░] 80%
Día 2: Seeders + Factory [██████░░░░] 60%
Día 3: Services [████░░░░░░] 40%
Día 4: Endpoints [██░░░░░░░░] 20%
Día 5: Documentación [░░░░░░░░░░] 0%
Día 6: Frontend Pages [░░░░░░░░░░] 0%
Día 7: Frontend Polish [░░░░░░░░░░] 0%

````

**Interpretación:**
- Si estás en Día 3 y BD no está en 100% → RIESGO (vas atrasado)
- Si estás en Día 4 y Services en 40% → Normal (vas en plan)
- Si estás en Día 6 y todo está en 100% hasta Día 5 → Perfecto

---

## 🎯 PARTE 5: VALIDACIÓN DE DOCUMENTACIÓN (2 min)

```bash
# Ejecuta:
find docs -name "*DIA*" -o -name "memories.md" | sort
````

| Documento     | Existe    | Actualizado | Útil      |
| ------------- | --------- | ----------- | --------- |
| memories.md   | ☐ Sí ☐ No | ☐ Sí ☐ No   | ☐ Sí ☐ No |
| PLAN_DIA_1.md | ☐ Sí ☐ No | ☐ Sí ☐ No   | ☐ Sí ☐ No |
| PLAN_DIA_2.md | ☐ Sí ☐ No | ☐ Sí ☐ No   | ☐ Sí ☐ No |
| DIA_1.md      | ☐ Sí ☐ No | ☐ Sí ☐ No   | ☐ Sí ☐ No |
| Endpoints doc | ☐ Sí ☐ No | ☐ Sí ☐ No   | ☐ Sí ☐ No |

**Criterio de "Actualizado":**

- Última modificación en los últimos 2 días ☐ Sí ☐ No

**Criterio de "Útil":**

- Puedo resolver dudas en < 1 min leyéndolo ☐ Sí ☐ No

---

## ✅ CHECKLIST FINAL - RÚBRICA COMPLETA

**Después de llenar todo, responde:**

```
REQUISITOS:
[ ] Entiendo qué hay que hacer (memories.md ✅)
[ ] Puedo describir el proyecto en una frase
[ ] Sé cuáles son los 3 riesgos principales

ESTADO TÉCNICO:
[ ] BD migrada 100% (0 pending)
[ ] Tests backend pasan (si existen)
[ ] npm run lint sin errores
[ ] Servidor Laravel corriendo
[ ] Vite corriendo

CONTINUIDAD:
[ ] Sé qué falta vs qué está hecho
[ ] No hay bloqueadores CRÍTICOS
[ ] Tengo plan para hoy/mañana
[ ] Documentación está actualizada

LISTO:
[ ] Completitud: ___% (calcula de Parte 3.1)
[ ] Estado: ☐ VERDE ☐ AMARILLO ☐ ROJO
[ ] Puedo empezar: ☐ SÍ ☐ NO

🟢 SI ES VERDE Y 100% → USA ECHADA_DE_ANDAR.md Y EMPIEZA
🟡 SI ES AMARILLO → RESUELVE ITEMS AMARILLOS PRIMERO
🔴 SI ES ROJO → DÉTENTE Y ALINEA CON LÍDER
```

---

## 💡 USO PRÁCTICO

### Escenario 1: Inicio de Nuevo Módulo

```
Día 0 (Preparación):
1. Crea/Completa memories.md
2. Llena PARTE 1 de esta rúbrica
3. ¿ROJO? → Completa memories
4. ¿VERDE? → Avanza a PARTE 2

Día 1:
1. Usa ECHADA_DE_ANDAR.md
2. Cada mañana, válida PARTE 2 (Estado técnico)
3. Cada noche, actualiza PARTE 3 (¿Qué falta?)
```

### Escenario 2: Continuación de Módulo

```
Cada mañana:
1. Abre esta rúbrica
2. Actualiza PARTE 2 (Estado técnico)
3. Actualiza PARTE 3 (Bloqueadores)
4. Si todo VERDE → Usa ECHADA_DE_ANDAR.md
5. Si algo ROJO → Resuelve antes de codificar
```

### Escenario 3: End of Week Review

```
Viernes 17:00:
1. Llena TODA la rúbrica
2. Calcula % de completitud (Parte 3.1)
3. Documenta bloqueadores para lunes
4. Si < 70% → Revisa plan vs realidad
5. Si > 70% → Celebra y planifica siguiente semana
```

---

## 📌 ÚLTIMA CLAVE

Esta rúbrica responde **preguntas objetivas, no opinables:**

❌ NO preguntes: "¿Avanzamos bien?" (subjetivo)
✅ Pregunta: "¿Completitud 70%? ¿Bloqueadores en rojo?" (objetivo)

**Uso correcto:**

- Lunes 08:00: Llena rúbrica → Define plan del día
- Viernes 17:00: Llena rúbrica → Valida si semana fue exitosa
- Cuando hay duda: Vuelve a preguntas de PARTE 1 (requisitos)

**Resultado:** Cero sorpresas, máxima confianza.

---

## 🔗 CONECTA CON OTRAS GUÍAS

| Necesito...           | Leo...                              |
| --------------------- | ----------------------------------- |
| Empezar hoy           | ECHADA_DE_ANDAR.md                  |
| Entender proceso      | GUIA_DESARROLLO_ESTRUCTURADO.md     |
| Verificar completitud | Esta rúbrica (VALIDACION_ESTADO.md) |
| Aprender de errores   | LECCIONES_APRENDIDAS_DIA1_5.md      |
| Recordar requisitos   | memories.md del módulo              |

---

## 🚀 COMANDO PARA IMPRIMIR ESTA RÚBRICA

```bash
# Guárdalo visible en tu semana:
cp docs/VALIDACION_ESTADO.md docs/VALIDACION_ESTADO_MODULO_[NOMBRE]_SEMANA_[N].md

# Y luego cada viernes:
cat docs/VALIDACION_ESTADO_MODULO_[NOMBRE]_SEMANA_[N].md > /tmp/reporte.txt
# Edita y guarda tu reporte de la semana
```

---

**Recuerda:** Una rúbrica clara = decisiones claras = proyecto en progresión consistente.

No es burocracia, es tranquilidad.
