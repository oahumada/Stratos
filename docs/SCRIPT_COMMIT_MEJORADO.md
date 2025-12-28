# Cómo Funciona el Script Mejorado

## Antes (Sin cambios)

```
./scripts/commit.sh

═══════════════════════════════════════════════════════════════
  Asistente de Commits Semánticos - TalentIA
═══════════════════════════════════════════════════════════════

1. Selecciona el tipo de cambio:
   1) feat      - Nueva funcionalidad
   2) fix       - Corrección de bugs
   ...
```

---

## Ahora (Con Análisis Automático) 🎯

```
./scripts/commit.sh

═══════════════════════════════════════════════════════════════
  Asistente de Commits Semánticos - TalentIA
═══════════════════════════════════════════════════════════════

📝 CAMBIOS PREPARADOS (git diff):

 scripts/commit.sh | 120 ++++++++++++++++++++++++++++++++++++++++++++--
 1 file changed, 115 insertions(+), 5 deletions(-)

💡 Tipo sugerido basado en cambios: chore

1. Selecciona el tipo de cambio:
   1) feat      - Nueva funcionalidad
   2) fix       - Corrección de bugs
   ...
   8) chore     - Cambios en build, dependencias
   ...
   (presionar Enter para usar sugerencia: chore)

Elige una opción (1-10) o Enter para sugerencia: [Enter]
✓ Usando sugerencia: chore

2. Ingresa el scope (opcional, ej: auth, forms, api, models):
   Scopes sugeridos: scripts
   Scope [presionar Enter para omitir]: scripts

✓ Scope: (scripts)

3. Describe el cambio (máximo 100 caracteres, modo imperativo):
   Ej: agregar validación de email, corregir filtro de búsqueda
   Archivo modificado: commit.sh
   Archivos afectados: 1

Subject: mejorar script de commits con análisis automático de cambios

✓ Subject: mejorar script de commits con análisis automático de cambios
```

---

## ✨ Lo Que Hace Ahora

### 1️⃣ **Muestra Git Diff Automáticamente**

- Ve TODOS tus cambios preparados
- Entiende qué archivos tocaste

### 2️⃣ **Sugiere Tipo Automáticamente**

- ¿Modificaste tests? → `test`
- ¿Modificaste dependencias? → `chore`
- ¿Modificaste docs? → `docs`
- ¿Otro? → `feat` (default)
- Presiona Enter para aceptar la sugerencia

### 3️⃣ **Sugiere Scope Automáticamente**

- Detecta qué archivos fueron modificados
- Sugiere scopes relevantes (forms, api, etc.)

### 4️⃣ **Muestra Contexto**

- Archivos afectados
- Cantidad de cambios
- Preview de lo modificado

---

## Ejemplo Práctico

```bash
# 1. Haces cambios
echo "nuevo código" > src/components/Form.vue
echo "otro cambio" > src/api/handler.ts

# 2. Preparas cambios
git add .

# 3. Ejecutas script
./scripts/commit.sh

# Salida:
# ✓ Ve tus cambios automáticamente
# ✓ Sugiere tipo: feat (porque no son tests ni config)
# ✓ Sugiere scope: forms (detecta Form.vue)
# ✓ Tú escribes: "agregar validación de email"
# ✓ Genera: feat(forms): agregar validación de email
```

---

## 🎯 Flujo Actual

```
git add .                    # Preparas cambios
   ↓
./scripts/commit.sh          # Ejecutas script
   ↓
   ├─ Muestra git diff       📝 VE TUS CAMBIOS
   ├─ Sugiere tipo           💡 TIPO AUTOMÁTICO
   ├─ Sugiere scope          💡 SCOPE AUTOMÁTICO
   └─ Tú escribes subject    ✍️  BASADO EN LO QUE VES
   ↓
CommitLint valida            ✅ FORMATO CORRECTO
   ↓
Commit exitoso! 🎉
```

---

## Resumiendo

**Antes:** Debías escribir todo manualmente  
**Ahora:** El script te ayuda analizando automáticamente `git diff`
