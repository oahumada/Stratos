# 📅 TEMPLATE - Plan Día [N] - [NOMBRE_MÓDULO]

**Módulo:** [NOMBRE]  
**Día:** [N] de [X]  
**Fecha:** [DÍA SEMANA] [DD/MM/YYYY]  
**Responsabilidad Principal:** [Una sola cosa]  
**Documentación base:** memories.md → Sección [X]

---

## 🎯 RESPONSABILIDAD DEL DÍA

**Una sola responsabilidad clara:**

```
Hoy entrego: ________________________________

Esto valida que completé el día si: ________________________________

Conecta con el día anterior porque: ________________________________

Prepara para el siguiente porque: ________________________________
```

---

## 📋 OBJETIVOS VERIFICABLES

Para confirmar que completé el día, debo poder responder SÍ a:

- [ ] Archivos creados/modificados: ******\_\_\_******
- [ ] Tests pasan: `php artisan test` = All tests passed ✓
- [ ] 0 errores de sintaxis: `npm run lint` = 0 errors
- [ ] API endpoints funcionan: [POST/GET/PUT] [ruta] = 200 OK
- [ ] Documentación actualizada: [archivo].md ✓
- [ ] Git commit coherente: `git log --oneline -1` describe qué hice

---

## ⏱️ ESTRUCTURA DEL DÍA

### 08:00-08:30: ECHADA DE ANDAR

```
Checklist:
[ ] Leí memories.md sección [X]
[ ] Entiendo qué debo entregar hoy
[ ] Validé estado de BD/servidor/código
[ ] Planifiqué mis 2 bloques de trabajo
[ ] No hay bloqueadores CRÍTICOS
```

### 08:30-09:30: LECTURA + SETUP (1 hora)

**Actividades:**

- [ ] Revisar qué quedó ayer (archivo DIA\_[N-1].md)
- [ ] Leer sección relevante de memories.md (10 min)
- [ ] Identificar archivos que voy a tocar
- [ ] Abrir archivos en VS Code
- [ ] Hacer primer commit del día (boilerplate/setup)

**Comandos:**

```bash
# Alfinal:
git add .
git commit -m "Día [N]: Setup inicial - [descripción breve]"
```

---

## 💪 BLOQUE 1 (09:30-12:00) - 2.5 HORAS

**Enfoque:** [Principal tarea del bloque]

### Tareas:

#### 1.1 [Tarea específica - líneas estimadas]

**Archivo(s):** `app/[ruta]`
**Verificación:** [Qué comando valida que está bien?]
**Tiempo estimado:** X min

- [ ] Crear/modificar estructura
- [ ] Agregar lógica
- [ ] Agregar tests
- [ ] ✅ Validación: `[comando]`

```bash
# Comandos para esta tarea:
php artisan make:model [nombre]
php artisan test [test específico]
```

#### 1.2 [Tarea específica - líneas estimadas]

**Archivo(s):** `app/[ruta]`
**Verificación:** [Qué comando valida que está bien?]
**Tiempo estimado:** X min

- [ ] Crear/modificar estructura
- [ ] Agregar lógica
- [ ] Agregar tests
- [ ] ✅ Validación: `[comando]`

### ✅ CHECKPOINT 1 (11:45-12:00) - 15 min

Antes de almuerzo, valida:

```bash
# Terminal:
php artisan test              # ¿Pasan tests?
npm run lint                 # ¿0 errores?
git diff HEAD~1 | head -50  # ¿Cambios coherentes?

# Commit si todo pasa:
git add .
git commit -m "Día [N]: Bloque 1 completado - [descripción]"
```

**Checklist:**

- [ ] Tests: 0 fallos
- [ ] Lint: 0 errores
- [ ] Cambios: Coherentes con plan
- [ ] Git: Commit hecho

**Si algo falla:** Arregla ANTES de almuerzo.

---

## 🍽️ 12:00-13:00: ALMUERZO

_Descansa, cambia de contexto, desconecta 1 hora._

---

## 💪 BLOQUE 2 (13:00-16:00) - 3 HORAS

**Enfoque:** [Principal tarea del bloque]

### Tareas:

#### 2.1 [Tarea específica - líneas estimadas]

**Archivo(s):** `[ruta]`
**Verificación:** [Qué comando valida que está bien?]
**Tiempo estimado:** X min

- [ ] Crear/modificar estructura
- [ ] Agregar lógica/UI
- [ ] Agregar tests
- [ ] ✅ Validación: `[comando]`

#### 2.2 [Tarea específica - líneas estimadas]

**Archivo(s):** `[ruta]`
**Verificación:** [Qué comando valida que está bien?]
**Tiempo estimado:** X min

- [ ] Crear/modificar estructura
- [ ] Agregar lógica/UI
- [ ] Agregar tests
- [ ] ✅ Validación: `[comando]`

### ✅ CHECKPOINT 2 (15:45-16:00) - 15 min

Antes de testing final:

```bash
# Terminal:
php artisan test              # ¿Pasan tests?
npm run lint                 # ¿0 errores?
npm run build (si frontend)  # ¿Build OK?

# Commit si todo pasa:
git add .
git commit -m "Día [N]: Bloque 2 completado - [descripción]"
```

**Checklist:**

- [ ] Tests: 0 fallos
- [ ] Lint: 0 errores
- [ ] Frontend build: OK (si aplica)
- [ ] Git: Commit hecho

**Si algo falla:** Arregla ANTES de testing final.

---

## 🧪 16:00-17:00: TESTING + VALIDACIÓN FINAL (1 hora)

### Testing Suite Completa

```bash
# 1. Tests automáticos
php artisan test

# 2. Linting
npm run lint

# 3. Si es API - probar endpoints en Postman:
# [GET/POST/PUT] [ruta] → Status 200/201 ✓

# 4. Si es frontend - probar en navegador:
# http://127.0.0.1:8000/[página] → Carga correctamente ✓

# 5. Revisar logs para advertencias:
# Terminal debe estar limpia de colores rojos
```

### Validación API (si creaste endpoints)

| Endpoint | Método | Status | Response         | ✓   |
| -------- | ------ | ------ | ---------------- | --- |
| [ruta]   | GET    | 200    | JSON válido      | [ ] |
| [ruta]   | POST   | 201    | ID retornado     | [ ] |
| [ruta]   | PUT    | 200    | Actualización OK | [ ] |
| [ruta]   | DELETE | 204    | Eliminado OK     | [ ] |

### Validación Frontend (si creaste UI)

| Página  | Carga | Datos | Interacción | ✓   |
| ------- | ----- | ----- | ----------- | --- |
| [/ruta] | ✓     | ✓     | ✓           | [ ] |
| [/ruta] | ✓     | ✓     | ✓           | [ ] |

### Validación de Integración

```bash
# ¿Flujo de usuario completo funciona?
# 1. User login → ✓
# 2. Navigate to feature → ✓
# 3. Perform action → ✓
# 4. See result → ✓
```

**Resultado:**

- [ ] Todo pasa → ✅ Continúa a documentación
- [ ] Algo falla → 🔴 Arregla ANTES de finalizar
- [ ] Error crítico → Documenta bloqueador, continúa mañana

---

## 📝 17:00-18:00: DOCUMENTACIÓN + CIERRE (1 hora)

### 1. Documentación de Cambios

**Actualiza estos archivos:**

- [ ] `README.md` (si cambió arquitectura)
- [ ] `memories.md` (si hay nuevos aprendizajes)
- [ ] `DIA_[N].md` (resumen del día - ver template abajo)
- [ ] API docs (si creaste endpoints)

**Comando rápido:**

```bash
# Crea archivo de resumen:
cp docs/TEMPLATE_DIA_N.md docs/DIA_[N].md
# Edita y completa
```

### 2. Archivo de Resumen del Día

**Crear: `docs/DIA_[N].md`**

```markdown
# Día [N] - [Módulo]

## ✅ Completado

- [x] Tarea 1.1
- [x] Tarea 1.2
- [x] Tarea 2.1
- [x] Tarea 2.2
- [x] Todos los tests pasan
- [x] 0 errores de sintaxis
- [x] Documentación actualizada

## 📊 Métricas

- Archivos creados: [N]
- Líneas de código: [N]
- Tests: [N/N] PASS
- Tiempo BLOQUE 1: [Xh Ymin]
- Tiempo BLOQUE 2: [Xh Ymin]
- Tiempo total: [Xh Ymin]

## 🔗 Archivos Generados

- `app/Models/...php` (N líneas)
- `app/Http/Controllers/...php` (N líneas)
- `routes/...php` (actualizado)
- `tests/Feature/...Test.php` (N líneas)

## 📝 Notas

[Aprendizajes, decisiones, cambios vs plan]

## 🔴 Incompleto / Bloqueadores

[Si algo no se completó, documenta aquí para mañana]

## 🔗 Conecta con Día [N+1]

[Qué necesita el siguiente día para continuar]
```

### 3. Commit Final del Día

```bash
git add docs/
git commit -m "Día [N]: Documentación final

- Completadas [X] tareas
- Archivos creados/modificados: [lista]
- Métrica: [velocidad]
- Tests: [N/N] PASS
- Estado: [LISTO PARA DÍA N+1 / BLOQUEADOR]"
```

### 4. Git Log Limpio

```bash
# Verifica que todos tus commits del día sean coherentes:
git log --oneline HEAD~[N]..HEAD

# Debe mostrar:
# 1. Setup inicial
# 2. Bloque 1 completado
# 3. Bloque 2 completado
# 4. Documentación final
```

---

## 📊 FINAL DEL DÍA - RESUMEN

Responde estas preguntas HONESTAMENTE:

```markdown
## Estado Final

### ¿Cumplí mi responsabilidad?

☐ SÍ - Completamente, sin bloqueadores
☐ PARCIAL - Completé X de Y tareas
☐ NO - Hay bloqueadores críticos

### Completitud

Completé: \_\_% de lo planeado
Esto es: ☐ En plan ☐ Adelantado ☐ Atrasado

### Calidad

Tests: [N/N] PASS = \_\_% pasando
Errors: [N] errores restantes
Estado: ☐ PRODUCTION READY ☐ NEEDS FIXES ☐ CRITICAL

### Confianza

¿Puedo empezar Día [N+1]?
☐ SÍ - Completamente listo
☐ CON RESERVAS - Hay detalles menores
☐ NO - Hay bloqueadores

### Aprendizajes

Hoy aprendí: **************\_\_\_\_**************
Mañana haré diferente: **************\_\_\_\_**************
Algo inesperado: **************\_\_\_\_**************
```

---

## 🎯 TEMPLATE QUICK REFERENCE

**Copia esto al terminal para tener plan visible:**

```bash
clear
echo "=== DÍA [N] - [MÓDULO] ===" && \
echo "" && \
echo "📌 RESPONSABILIDAD:" && \
echo "[Tu responsabilidad única]" && \
echo "" && \
echo "⏱️ HORARIOS:" && \
echo "09:30-12:00: BLOQUE 1" && \
echo "13:00-16:00: BLOQUE 2" && \
echo "16:00-17:00: Testing" && \
echo "17:00-18:00: Docs" && \
echo "" && \
echo "✅ VALIDACIONES:" && \
echo "php artisan test" && \
echo "npm run lint" && \
echo "" && \
echo "🚀 Abre: docs/PLAN_DIA_[N].md"
```

---

## 🔑 CLAVES CRÍTICAS

1. **Una sola responsabilidad por día**
    - No intentes hacer todo
    - Enfócate en 1 cosa
    - Hazla bien

2. **Validate temprano y seguido**
    - Checkpoint cada 2.5 horas
    - No esperes al final del día
    - Tests pasan = confianza

3. **Documenta mientras haces**
    - No dejes docs para el final
    - Actualiza README ahora
    - Comenta código mientras escribes

4. **Respeta bloques de 2.5 horas**
    - Trabajo concentrado
    - Sin distracciones
    - Break después

5. **Commits coherentes**
    - 1 commit por tarea
    - Mensaje claro y conciso
    - Al menos 3 commits por día

---

## 🔗 HERRAMIENTAS ASOCIADAS

| Necesito...            | Leo...                           |
| ---------------------- | -------------------------------- |
| Validar estado general | VALIDACION_ESTADO.md             |
| Checklist rápido       | ECHADA_DE_ANDAR.md               |
| Entender proceso       | GUIA_DESARROLLO_ESTRUCTURADO.md  |
| Recordar requisitos    | memories.md                      |
| Ver días anteriores    | DIA*[N-1].md, DIA*[N-2].md, etc. |

---

**Recuerda:** Este template es la brújula del día. Si lo sigues, no te pierdes. Si algo se desvía, ajusta aquí, no durante codificación.

Éxito en el Día [N]. 🚀
