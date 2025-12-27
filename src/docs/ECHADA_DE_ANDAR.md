# ⚡ ECHADA DE ANDAR - Checklist de Inicio (Diario)

**Tiempo estimado: 20-25 minutos**
**Frecuencia: Cada mañana al comenzar (08:00-08:30)**
**Responsabilidad: Líder técnico / Ejecutor del módulo**

---

## 🎯 Propósito

Este checklist es tu **primer acto cada día/semana** antes de escribir código. Garantiza:

- ✅ Conocimiento actualizado del proyecto
- ✅ Ambiente funcionando correctamente
- ✅ Plan claro y verificable
- ✅ Continuidad sin pérdida de contexto
- ✅ Consistencia con el patrón Días 1-5

---

## 📋 SECCIÓN 1: VALIDACIÓN DE CONTEXTO (5 min)

### 1.1 Antes de Cualquier Cosa - Lee Esto Primero

**Pregunta clave:** ¿Qué estoy haciendo hoy y por qué?

Responde en orden:

```
1. Estoy en el Módulo: _______________
2. Estamos en el Día: ________________
3. El objetivo de hoy es: ____________
4. Esto contribuye a: ________________ (qué entrega final)
```

### 1.2 Revisa memories.md (3 min)

```bash
# En terminal:
# Ve a tu memories.md del módulo actual
grep -E "^## " memories.md  # Ver secciones principales
head -50 memories.md        # Primeros 50 líneas para recordar contexto
```

**Checklist mientras lees:**

- [ ] ¿Recuerdo cuáles son los 3-5 requisitos principales?
- [ ] ¿Sé cuál es el usuario final y sus necesidades?
- [ ] ¿Conozco las restricciones y edge cases?
- [ ] ¿Entiendo la arquitectura propuesta?

### 1.3 Estado Actual del Proyecto (2 min)

```bash
# Terminal - Ejecuta estos comandos de validación

# 1. ¿Git limpio o hay cambios pendientes?
git status

# 2. ¿Rama correcta?
git branch -v

# 3. ¿Último commit coherente?
git log --oneline -5

# 4. ¿Base de datos sincronizada?
php artisan migrate:status | grep pending

# 5. ¿Dependencias instaladas?
composer check-platform-reqs
```

**Checklist:**

- [ ] Rama correcta (no estoy en otra rama)
- [ ] No hay cambios no commiteados que me afecten
- [ ] Migraciones pendientes? Si hay → `php artisan migrate`
- [ ] Último commit es coherente con lo que empecé ayer

---

## 📊 SECCIÓN 2: VALIDACIÓN AMBIENTAL (5 min)

### 2.1 Base de Datos

```bash
# Verifica tablas esperadas
php artisan tinker
>>> Schema::getTables()  # Ver todas las tablas
>>> Table::count()       # Verificar datos de seed
exit
```

**Checklist:**

- [ ] Tengo las tablas esperadas del módulo
- [ ] Tengo datos de seed para probar
- [ ] La conexión funciona sin errores

### 2.2 Servidor y Vite

```bash
# Terminal 1:
composer run dev

# Terminal 2 (nueva):
curl http://127.0.0.1:8000/api/health
curl http://127.0.0.1:5173
```

**Checklist:**

- [ ] Servidor Laravel está corriendo (puerto 8000)
- [ ] Vite está corriendo (puerto 5173)
- [ ] No hay errores en la terminal
- [ ] CSS y JS se cargan correctamente

### 2.3 API Reference

```bash
# Verifica endpoints del módulo actual
grep -r "Route::" routes/api.php | grep -i "tu-modulo"

# O si tienes Postman:
# Abre TalentIA_API_Postman.json
```

**Checklist:**

- [ ] Sé qué endpoints existen del módulo
- [ ] Sé cuáles tengo que crear hoy
- [ ] Tengo lista de endpoints en Postman/Insomnia

---

## 📅 SECCIÓN 3: PLAN DEL DÍA (8-10 min)

### 3.1 Revisa el Template de Tu Día

**Patrón de Días 1-5 (ajusta para tu módulo):**

```markdown
# Plan Día [N]

## Responsabilidad Principal

[Una sola cosa: migraciones, servicios, endpoints, frontend, etc.]

## Objetivo Verificable

[Qué prueba demuestra que completé el día?]

## Tareas Desglosadas

### Bloque 1 (09:30-12:00) - 2.5 horas

- [ ] Tarea 1.1
- [ ] Tarea 1.2
- [ ] Validación Bloque 1

### Bloque 2 (13:00-16:00) - 3 horas

- [ ] Tarea 2.1
- [ ] Tarea 2.2
- [ ] Validación Bloque 2

## Validaciones

- [ ] Tests pasan
- [ ] 0 errores de sintaxis
- [ ] API responses correctas
- [ ] Documentación actualizada

## Archivos Esperados

- [ ] Archivo 1 (líneas estimadas)
- [ ] Archivo 2 (líneas estimadas)
```

### 3.2 Crea Plan Específico de Hoy

**Si es Día 1 de nuevo módulo:**

```bash
# Copia este template:
cp docs/ECHADA_DE_ANDAR.md docs/PLAN_DIA_1.md
# Edita:
# - Reemplaza [N] con número
# - Reemplaza tareas genéricas con específicas
```

**Si es continuación:**

```bash
# Lee qué faltó ayer:
tail -30 docs/DIA_[ANTERIOR].md  # Ver "Incompleto/Bloqueadores"

# Ajusta plan:
# - Si ayer no completé tarea X → hoy empieza con eso
# - Si ayer completé todo → continúa con siguiente responsabilidad
```

**CLAVE: Usa el checklist de GUIA_DESARROLLO_ESTRUCTURADO.md Sección 3**

### 3.3 Establece Checkpoints

Antes de empezar a codificar, establece dónde vas a validar:

```
09:45 - Arquitectura/estructura definida (reviews rápido)
11:30 - Primer archivo funcional + test pasando
12:00 - Bloque 1 validado antes de almuerzo
15:30 - Segundo bloque halfway
16:30 - Todo validado antes de documentación
17:30 - Documentación completa, list ready
```

---

## 🔧 SECCIÓN 4: VERIFICACIÓN PRE-CÓDIGO (2 min)

Antes de tocar un archivo, verifica:

```bash
# Copia el checklist de tu día al terminal para tenerlo visible
echo "=== PLAN DE HOY ===" && cat docs/PLAN_DIA_[N].md | head -30

# Abre en VS Code los archivos que vas a editar
code app/Models/[Modelo].php app/Http/Controllers/[Controller].php routes/api.php
```

**Checklist final:**

- [ ] ¿Entiendo qué voy a hacer?
- [ ] ¿Sé dónde van los archivos?
- [ ] ¿Tengo la estructura clara?
- [ ] ¿Mis checkpoints están en horario?
- [ ] ¿Mi documentación está lista para actualizar?

---

## ⏱️ CICLO OPERATIVO DEL DÍA

Con plan confirmado, sigue este ciclo:

```
08:00-08:30  ← TÚ ESTÁS AQUÍ (ECHADA DE ANDAR)
08:30-09:30  Lectura profunda + setup final
09:30-12:00  BLOQUE 1 + Validación cada 1.5 horas
12:00-13:00  Almuerzo
13:00-16:00  BLOQUE 2 + Validación cada 1.5 horas
16:00-17:00  Testing + Validación final
17:00-18:00  Documentación + Cierre
```

**Clave:** En cada checkpoint (09:45, 11:30, 15:30, etc.):

```bash
# Corre estos comandos sin fallar:
php artisan test            # Tests pasan
npm run lint               # Sin errores de sintaxis
php artisan tinker         # Verifica datos si aplica
# Revisa logs en terminal principal (sin rojo/error)
```

---

## 🚀 SECCIÓN 5: ACCESOS RÁPIDOS

Mientras trabajas hoy, estos son tus bookmarks:

| Necesito...              | Leo...                                   |
| ------------------------ | ---------------------------------------- |
| Recordar proceso del día | GUIA_DESARROLLO_ESTRUCTURADO.md §3       |
| Qué hacía ayer           | DIA\_[ANTERIOR].md                       |
| Endpoint específico      | dia5_api_endpoints.md                    |
| Comando útil             | CHEATSHEET_COMANDOS.md                   |
| ¿Cómo estructura X?      | Buscar en LECCIONES_APRENDIDAS_DIA1_5.md |
| Memoria de negocio       | memories.md                              |
| ¿Qué sale mal?           | Log en terminal o `php artisan pail`     |

---

## ✅ CHECKLIST FINAL - ANTES DE EMPEZAR A CODIFICAR

```
CONTEXTO:
[ ] Leí secciones relevantes de memories.md
[ ] Entiendo el objetivo del día
[ ] Revisé estado de git (rama, cambios)
[ ] Verifiqué migraciones pendientes

AMBIENTE:
[ ] Servidor Laravel corriendo (http://127.0.0.1:8000)
[ ] Vite corriendo (http://127.0.0.1:5173)
[ ] Base de datos accesible
[ ] Sin errores en terminal principal

PLAN:
[ ] Tengo PLAN_DIA_[N].md actualizado
[ ] Tareas desglosadas en 2 bloques
[ ] Checkpoints establecidos
[ ] Entiendo qué valida completitud hoy

LISTO:
[ ] Puedo describir en una frase qué hago hoy
[ ] Sé dónde irán mis archivos
[ ] Tengo template de documentación listos
[ ] Commiteo inicial del día está hecho (si necesario)

🟢 SI TODOS LOS CHECKS ESTÁN VERDES → EMPIEZA A CODIFICAR
🔴 SI ALGO ESTÁ EN ROJO → RESUELVE ANTES DE CONTINUAR
```

---

## 📝 AL FINAL DEL DÍA

Completa esta sección antes de irte:

```markdown
## Resumen Cierre del Día [N]

### ✅ Completado

- [ ] Tarea 1.1
- [ ] Tarea 1.2
- [ ] Tests pasan: SÍ / NO
- [ ] 0 errores sintaxis: SÍ / NO
- [ ] Documentación actualizada: SÍ / NO

### ⏳ Pendiente para Mañana

- [ ] Tarea X (descripción corta)
- [ ] Tarea Y (descripción corta)

### 🔴 Bloqueadores

- [ ] Bloqueador A (solución propuesta)

### 📊 Métricas

- Archivos creados: N
- Líneas de código: N
- Tests pasando: N/N
- Tiempo en BLOQUE 1: Xh Ymin
- Tiempo en BLOQUE 2: Xh Ymin

### 💡 Notas

(Algo importante para mañana?)
```

---

## 🎓 NOTAS IMPORTANTES

1. **Este checklist es flexible, no rígido:**
    - Si un checkpoint toma menos tiempo, perfecto
    - Si un bloque requiere más tiempo, es OK (ajusta siguiente)
    - El objetivo es consistencia, no velocidad

2. **memories.md es tu fuente de verdad:**
    - Cualquier duda → vuelve a memories.md
    - Si memories dice X, eso prima sobre todo

3. **La documentación es concurrente:**
    - No dejes documentación para el final
    - Actualiza README/API docs mientras codificas

4. **Validación = Confianza:**
    - Cada checkpoint validado = puedes dormir tranquilo
    - Tests fallando = no avances al siguiente bloque

5. **Progresión significa:**
    - Hoy + Ayer + Semana anterior = coherencia
    - No empieces con "borra todo de ayer"
    - Cada día se apila sobre el anterior

---

## 🔑 LA CLAVE FINAL

**Antes de codificar hoy:**

1. **¿Cuál es la ÚNICA responsabilidad de hoy?** (debe caber en una frase)
2. **¿Qué entrega verificable lo demuestra?** (test, endpoint, página)
3. **¿Cuáles son los 2 checkpoints críticos?** (horarios específicos)
4. **¿Cómo lo documento mientras lo hago?** (no después)

Si puedes responder estas 4 preguntas en menos de 2 minutos → estás listo.

**Si no → revuelve más antes de empezar.**

---

## 🚀 COMANDO RÁPIDO PARA EMPEZAR

```bash
# Ejecuta esto cada mañana:
clear
echo "=== ECHADA DE ANDAR $(date +%d/%m/%Y) ===" && \
echo "" && \
echo "📍 Estado de git:" && \
git status && \
echo "" && \
echo "📋 Plan del día:" && \
grep "^##" docs/PLAN_DIA_*.md | head -5 && \
echo "" && \
echo "✅ Ambiente:" && \
curl -s http://127.0.0.1:8000/api/health && \
echo "" && \
echo "🎯 Listo para empezar? Abre docs/PLAN_DIA_[N].md"
```

**Guárdalo como alias:**

```bash
# Agrega a ~/.bashrc o ~/.zshrc:
alias echada="clear && echo '=== ECHADA DE ANDAR ===' && git status && echo '📋 Tu plan:' && cat docs/PLAN_DIA_*.md | head -20"

# Luego simplemente:
echada
```

---

## 📌 Última Cosa

Este checklist **reemplaza conversaciones vagas** del tipo:

- ❌ "¿Qué hago hoy?"
- ❌ "¿Por dónde empiezo?"
- ❌ "¿Recuerdo qué falta?"

Responde automáticamente:

- ✅ Qué hago (lee PLAN*DIA*[N].md)
- ✅ Por dónde empiezo (sección 3 de esta guía)
- ✅ Qué falta (revisa DIA anterior + checkpoints)

**Tiempo ahorrado = 30-60 min por día**
**Coherencia garantizada = sin sorpresas**

---

**Última última cosa:** Cada día, 10 minutos de esta echada de andar = 8 horas de tranquilidad el resto del día. Vale completamente la pena.
