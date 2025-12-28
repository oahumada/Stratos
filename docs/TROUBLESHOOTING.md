# 🔧 TROUBLESHOOTING - ¿Qué Hacer Si...?

**Guía rápida de resolución para problemas comunes**  
**Tiempo: 2-5 min de lectura + 5-15 min de resolución**

---

## 🔴 PROBLEMAS CRÍTICOS (Resuelve ANTES de continuar)

### 1️⃣ ¿Tests fallan?

```bash
# Paso 1: Lee el error completo
php artisan test 2>&1 | tee /tmp/test_error.log

# Paso 2: Identifica qué falla
# Busca: FAILED / ERROR / AssertionError

# Paso 3: Entiende el contexto
git diff HEAD~1 app/  # ¿Qué cambié?

# Paso 4: Arreglalo
# Opción A: Revierte cambio que no funciona
git checkout app/[archivo].php

# Opción B: Ajusta código/test
# Edita el archivo y corre test nuevamente

# Paso 5: Valida
php artisan test  # ¿Pasa ahora?
```

**Referencia:** [LECCIONES_APRENDIDAS_DIA1_5.md](LECCIONES_APRENDIDAS_DIA1_5.md) - Sección "Errores a evitar"

---

### 2️⃣ ¿Hay errores de sintaxis (npm run lint falla)?

```bash
# Paso 1: Corre lint detalladamente
npm run lint 2>&1 | head -50

# Paso 2: Tipo de error
# ❌ "Expected semicolon" → Falta ;
# ❌ "Undefined variable" → Variable no existe
# ❌ "Import not found" → Ruta incorrecta
# ❌ "Unexpected token" → Sintaxis JavaScript incorrecta

# Paso 3: Busca línea exacta
# Lint te dice: archivo.ts:line:col → Abre en VS Code y ve esa línea

# Paso 4: Arregla (ejemplos comunes)
# [ ] Falta punto y coma → Agrega ;
# [ ] Comillas incorrectas → Cambia ' por " o viceversa
# [ ] Indentación → Verifica espacios vs tabs
# [ ] Import roto → Verifica ruta existe

# Paso 5: Valida
npm run lint  # ¿0 errores?
```

**Quick fix:**

```bash
# VS Code ayuda a encontrar errors:
# Ctrl/Cmd + Shift + M → Abre panel de problemas
# Haz click en cada línea roja y arregla
```

---

### 3️⃣ ¿API devuelve error 500 cuando probé endpoint?

```bash
# Paso 1: Chequea logs en terminal
# Mira la terminal con [server] y busca colores ROJOS
# Ejemplo: [server] Exception: ...

# Paso 2: Lee error detalladamente
# Log debe decir qué falla (línea exacta en qué archivo)

# Paso 3: Posibles causas
# [ ] BD no migrada → php artisan migrate
# [ ] Model/Class no existe → Verifica archivo existe
# [ ] Relación indefinida → Revisa @property en Model
# [ ] Validación falla → Revisa FormRequest
# [ ] Query incorrecta → Prueba en php artisan tinker

# Paso 4: Debugging
php artisan tinker
>>> Model::query()->first()  # ¿Datos existen?
>>> Route::current()         # ¿Ruta correcta?
>>> Auth::user()             # ¿Usuario authenticado?
exit

# Paso 5: Arregla y testa
# Edita archivo indicado en error
# Prueba endpoint nuevamente en Postman/cURL
```

**Referencia:** [CHEATSHEET_COMANDOS.md](CHEATSHEET_COMANDOS.md) - Sección "Debugging"

---

### 4️⃣ ¿BD no está migrada o tiene datos faltantes?

```bash
# Paso 1: Chequea estado
php artisan migrate:status

# Paso 2: Si hay migraciones pendientes
php artisan migrate  # Aplica todas

# Paso 3: Si necesitas seed (datos de ejemplo)
php artisan db:seed  # Corre todos los seeders

# Paso 4: Si seed falla
php artisan db:seed --class=TuSeeder  # Seeder específico

# Paso 5: Valida
php artisan tinker
>>> Model::count()  # ¿Hay registros?
exit
```

**Diferencia:**

- `migrate` = Estructura (tablas, columnas)
- `seed` = Datos (registros de ejemplo)

---

## ⚠️ PROBLEMAS IMPORTANTES (Resuelve hoy)

### 5️⃣ ¿Commit anterior me rompe el código?

```bash
# Paso 1: Identifica dónde empezó a fallar
git log --oneline -10  # Ve últimos 10 commits

# Paso 2: Prueba commit anterior
git checkout [commit-hash]  # Vuelve a ese punto
php artisan test  # ¿Pasa?

# Paso 3: Si ese sí pasa, retrocede y arregla
git checkout [tu-rama]  # Vuelve a rama actual

# Paso 4: Identifica qué cambio lo rompió
# Entre [commit-anterior] y [ahora] qué es diferente?
git diff [commit-anterior]..HEAD  # Ve cambios

# Paso 5: Arregla ese cambio específico
# O revert el commit problemático:
git revert [commit-malo]  # Deshace ese commit
```

**Mejor:** Nunca dejes commit sin validar primero

---

### 6️⃣ ¿Cambié algo y ahora nada funciona?

```bash
# Paso 1: Ve qué cambiaste
git diff HEAD~1

# Paso 2: ¿Mucho cambio? Posibles causas:
# [ ] Archivo importante modificado
# [ ] Ruta incorrecta en route
# [ ] Model relación rota
# [ ] Algo not imported

# Paso 3: Revierte cambio temporalmente
git stash  # Guarda cambios de lado
php artisan test  # ¿Funciona sin cambios?

# Paso 4: Si funciona sin tu cambio
# Recupera cambio y arréglalo lentamente
git stash pop  # Trae cambios de vuelta
# Edita archivo + test incremental

# Paso 5: Si no funciona ni sin cambio
# El problema era antes de tus cambios
git stash drop  # Descarta cambios
# Investiga qué rompió antes
```

---

### 7️⃣ ¿Servidor no inicia (Error al hacer php artisan serve)?

```bash
# Paso 1: Lee error en terminal
# ¿Puerto 8000 en uso? ¿BD no conecta? ¿Config error?

# Paso 2: Causas comunes
# [ ] Puerto 8000 ocupado
# [ ] Variables de entorno (.env) falta algo
# [ ] Permissions en storage/ folder
# [ ] App key no generada

# Paso 3: Arregla según causa
# Puerto ocupado:
php artisan serve --port=8001

# Variables de entorno:
cp .env.example .env
php artisan key:generate

# Permissions:
chmod -R 755 storage bootstrap/cache

# Paso 4: Intenta nuevamente
php artisan serve

# Paso 5: Si aún falla
# Lee línea exacta del error
# Busca en Google: [error exacto] Laravel
```

---

### 8️⃣ ¿Frontend no carga (Vite error)?

```bash
# Paso 1: Chequea si Vite está corriendo
npm run dev  # Debe estar en terminal separada

# Paso 2: Revisa puerto (debe ser 5173)
lsof -i :5173  # ¿Algo usando el puerto?

# Paso 3: Si está ocupado
# Mata proceso:
kill -9 [pid]  # Donde [pid] es el número que viste

# Paso 4: Problemas en recurso
# Si ve error en navegador de Vite:
# Busca "HMR" o "compilation error" en terminal Vite

# Paso 5: Arregla
# Generalmente es error en archivo .vue/.ts/. jsx
# Ve a línea indicada y arregla sintaxis
```

---

## 🟡 PROBLEMAS MENORES (Anota, resuelve mañana si no bloquea)

### 9️⃣ ¿Tests lento (tarda >30 segundos)?

```bash
# No es crítico si tests pasan, pero:

# Paso 1: Identifica test lento
php artisan test --verbose  # Ve cada test con tiempo

# Paso 2: Causas comunes
# [ ] Query N+1 (muchas queries innecesarias)
# [ ] Crear 1000 registros en seed
# [ ] Test sin isolation (usa datos globales)

# Paso 3: Optimiza (para siguiente sprint)
# Usa factories en lugar de seeders
# Agrupa relacionados: setUp() method
# Valida sin crear si es posible

# Para ahora: está bien si pasan
```

---

### 🔟 ¿Componente Vue no se renderiza?

```bash
# Paso 1: Abre browser console (F12)
# ¿Ves error en rojo?

# Paso 2: Causas comunes
# [ ] Componente no importado
# [ ] Nombre no matchea (case-sensitive)
# [ ] Template error (syntax)
# [ ] Props falta o tipo incorrecto

# Paso 3: Arregla
# [ ] Agrega import: import MyComponent from '...'
# [ ] Usa exact case: MyComponent no mycomponent
# [ ] Valida template: {{ }} vs [ ]
# [ ] Pasa prop correcto: :prop="value"

# Paso 4: Testa
# Refresca navegador (Cmd/Ctrl + R)
# ¿Se ve ahora?
```

---

### 1️⃣1️⃣ ¿Endpoint devuelve status 401 (Unauthorized)?

```bash
# Paso 1: Eres usuario autenticado?
# En Postman: ¿Hay header Authorization con token?

# Paso 2: Test sin auth
# Algunos endpoints permiten guest
# Revisa: protected vs public en Route

# Paso 3: Si necesita auth
# En Postman → Authorization tab → Bearer Token
# Pega tu token JWT/API
# O usa: curl -H "Authorization: Bearer TOKEN" [url]

# Paso 4: Token expirado?
# Login nuevamente para obtener nuevo token

# Paso 5: Route no requiere auth?
# Revisa en routes/api.php si tiene middleware 'auth'
```

---

## 📋 ÁRBOL DE DECISIÓN RÁPIDO

```
¿QUÉ ESTÁ FALLANDO?

├─ Test falla
│  └─ → Corre php artisan test, lee error, arregla, revalida
│
├─ Lint error (sintaxis)
│  └─ → npm run lint, ve línea exacta, arregla caracteres
│
├─ API devuelve 500
│  └─ → Mira logs, identifica línea, arregla lógica
│
├─ BD no migrada
│  └─ → php artisan migrate, php artisan db:seed
│
├─ Commit anterior roto
│  └─ → git checkout [anterior], test, si OK entonces arregla este
│
├─ Cambié algo y todo roto
│  └─ → git diff HEAD~1, git stash, test sin cambios
│
├─ Servidor no inicia
│  └─ → Revisa puerto/env/.env key, php artisan serve con fix
│
├─ Vite error
│  └─ → npm run dev en terminal separada, mata puerto si necesario
│
├─ Tests lento (pero pasan)
│  └─ → Nota para optimizar luego, por ahora OK
│
├─ Componente no muestra
│  └─ → Revisa console F12, import/case/template/props
│
└─ API 401 Unauthorized
   └─ → Agrega Authorization header o chequea si route necesita auth
```

---

## 🎯 PREVENCIÓN (Hazlo MIENTRAS CODIFICAS)

No esperes a que fallen:

```bash
# Cada 30 minutos:
php artisan test  # ¿Pasan?

# Cada archivo guardado:
npm run lint      # ¿0 errores?

# Cada endpoint creado:
# Pruébalo en Postman INMEDIATAMENTE
# No esperes a fin del día

# Cada componente creado:
# Abre en navegador Y valida que se renderiza
# No esperes a fin del bloque
```

**Clave:** 5 minutos de validación = 1 hora de debugging ahorrada

---

## 🆘 SI NADA DE ESTO FUNCIONA

```bash
# Paso 1: Respira. Sí se puede arreglar.

# Paso 2: Recopila información
echo "=== Diagnostico ===" > /tmp/diag.txt
echo "PHP:" >> /tmp/diag.txt && php -v >> /tmp/diag.txt
echo "Composer:" >> /tmp/diag.txt && composer --version >> /tmp/diag.txt
echo "Node:" >> /tmp/diag.txt && node -v >> /tmp/diag.txt
echo "Git:" >> /tmp/diag.txt && git log --oneline -3 >> /tmp/diag.txt
echo "Test:" >> /tmp/diag.txt && php artisan test >> /tmp/diag.txt 2>&1
cat /tmp/diag.txt

# Paso 3: Busca en:
# [ ] LECCIONES_APRENDIDAS_DIA1_5.md - Errores similares?
# [ ] Error en Google: "[código error] Laravel/Vue"
# [ ] StackOverflow si es error específico

# Paso 4: Alinea con líder
# Muestra: error exacto + qué intentaste + /tmp/diag.txt
# Líder probablemente lo ha visto antes

# Paso 5: Nuclear option (último recurso)
# Revierte a último commit conocido bueno:
git reset --hard HEAD~[N]  # Donde N es cuántos commits atrás

# Luego reconstruye cambios lentamente
```

---

## 🔗 REFERENCIAS RÁPIDAS

| Problema               | Documento                       |
| ---------------------- | ------------------------------- |
| Error en funcionalidad | LECCIONES_APRENDIDAS_DIA1_5.md  |
| Comando específico     | CHEATSHEET_COMANDOS.md          |
| Cómo usar endpoint     | dia5_api_endpoints.md           |
| Estado general         | VALIDACION_ESTADO.md            |
| Proceso completo       | GUIA_DESARROLLO_ESTRUCTURADO.md |

---

## ✅ DESPUÉS DE ARREGLAR

```bash
# 1. Valida completamente
php artisan test
npm run lint
npm run build (si frontend)

# 2. Commit con mensaje claro
git add .
git commit -m "Fix: [problema] - [cómo lo arreglaste]"

# 3. Continúa con tu día
# Tu plan no debe cambiar, solo ajusta tiempo si fue mucho
```

---

**Recuerda:** Los mejores debuggers validan temprano y seguido. No esperes a fin de día.

**Cada 30 minutos → `php artisan test && npm run lint`**

**No pasá? → Para, arregla, continúa.**

**Pasá? → Commit, avanza, sigue plan.**

---

**Este troubleshooting está basado en errores reales de Días 1-5. Se vuelven más fáciles con práctica.** 🚀
