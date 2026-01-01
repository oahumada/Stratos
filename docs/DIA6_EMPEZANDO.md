# 🚀 EMPEZANDO DÍA 6 - PASO A PASO

**Hora:** 09:30-12:00 (Prioridad 1)  
**Objetivo:** 3 páginas CRUD funcionando (/People, /roles, /skills)

---

## ✅ Estado Actual

```
✅ Backend:        100% listo (17 endpoints)
✅ Estructura:     3 carpetas de páginas existen
⏳ Páginas:        Existen pero necesitan verificación
⏳ Rutas:          Configuradas en Inertia
⏳ npm build:      Necesita compilar
```

---

## 🎯 Tareas Inmediatas

### 1️⃣ Compilar Frontend (5 minutos)

```bash
cd /workspaces/talentia/src

# Instalar dependencias si no están
npm install

# Compilar assets
npm run build
```

**Checkpoint:** Sin errores de compilación

### 2️⃣ Iniciar Servidor (2 minutos)

**Terminal 1:**

```bash
cd /workspaces/talentia/src
php artisan serve --port=8000
```

Espera que veas: `Server running on http://127.0.0.1:8000`

### 3️⃣ Iniciar Watch Mode (En otra terminal)

**Terminal 2:**

```bash
cd /workspaces/talentia/src
npm run dev
```

Ahora los cambios en Vue se verán automáticamente.

### 4️⃣ Prueba en Navegador

1. Abre: http://127.0.0.1:8000
2. Inicia sesión (si hay un usuario de demo)
3. Navega a `/People`
4. Deberías ver la tabla de peopleas

---

## 🔍 Si No Funciona

### Error: "Cannot GET /People"

- ✅ Verifica que Backend está corriendo
- ✅ Verifica que npm run dev está corriendo
- ✅ Revisa browser console (F12)

### Error: "API call failed"

- ✅ Verifica que `curl http://localhost:8000/api/People` funciona
- ✅ Revisa headers en axios (Authorization header)

### Errores de compilación

- ✅ `npm run lint` para ver problemas
- ✅ Revisa el archivo compilado

---

## 📋 Estructura de Carpetas

```
src/resources/js/pages/
├── People/
│   ├── Index.vue  ✅ Lista de peopleas
│   └── Show.vue   ✅ Detalle de peoplea
├── Roles/
│   └── Index.vue  ✅ Lista de roles
├── Skills/
│   └── Index.vue  ✅ Catálogo de skills
├── GapAnalysis/
│   ├── Index.vue  ⏳ Crear
│   └── Show.vue   ⏳ Crear
├── LearningPaths/
│   ├── Index.vue  ⏳ Crear
│   └── Show.vue   ⏳ Crear
└── Dashboard/
    └── (actualizar)
```

---

## 🧪 Testing Rápido de Endpoints

```bash
# Prueba /api/People
curl http://localhost:8000/api/People | jq

# Prueba /api/roles
curl http://localhost:8000/api/roles | jq

# Prueba /api/skills
curl http://localhost:8000/api/skills | jq

# Prueba /api/gap-analysis
curl -X POST http://localhost:8000/api/gap-analysis \
  -H "Content-Type: application/json" \
  -d '{"people_id": 1, "role_id": 1}' | jq
```

---

## 📊 Checklist Prioridad 1

- [ ] npm run build sin errores
- [ ] php artisan serve corriendo
- [ ] npm run dev corriendo
- [ ] http://127.0.0.1:8000 accesible
- [ ] `/People` muestra tabla de peopleas
- [ ] Click en peoplea abre detalle
- [ ] `/roles` muestra tabla de roles
- [ ] `/skills` muestra catálogo de skills
- [ ] Filtros funcionan en /People

---

## ⏱️ Timing

```
09:30-09:35  Build + Servidor
09:35-10:30  Verificar/arreglar /People
10:30-11:15  Verificar /roles + /skills
11:15-11:45  Buffer/ajustes
11:45        ✅ Checkpoint: P1 completa
```

---

## 📞 Si Necesitas Ayuda

1. Revisa: [DIA6_GUIA_INICIO_FRONTEND.md](docs/DIA6_GUIA_INICIO_FRONTEND.md)
2. Revisa: [dia5_api_endpoints.md](docs/dia5_api_endpoints.md)
3. Terminal: `npm run lint` para errores
4. F12 en navegador para ver errores de JS

---

## 🚀 Próximo Paso (después de P1)

Una vez que `/People`, `/roles` y `/skills` funcionen:

1. Empezar `/gap-analysis` (13:00)
2. Crear `/development-paths`
3. Crear `/job-openings`, `/applications`, `/marketplace`

**Tiempo disponible: 4-5 horas (13:00-17:00)**

---

¡Vamos! 🚀
