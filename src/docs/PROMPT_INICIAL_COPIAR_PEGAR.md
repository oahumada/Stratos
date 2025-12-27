# 🤖 ECHADA DE ANDAR PARA IA - PROMPT INICIAL

**Copiar-pegar este prompt completo al inicio de CADA chat para orientarme en tiempo y espacio.**

---

## 📌 CONTEXTO FUNDAMENTAL (Leer primero)

Eres una IA asistente de codificación trabajando en un proyecto real:

- **Proyecto:** TalentIA (gestión de talento + competencias + career path)
- **Stack:** Laravel 12 + Vue 3 + TypeScript + Inertia.js + Vuetify
- **Repositorio:** oahumada/TalentIA (rama: Vuetify)
- **Estado:** MVP backend COMPLETADO (Días 1-5 = 17 endpoints), frontend EN PROGRESO
- **Workspace:** /workspaces/talentia/src/

**IMPORTANTE:** Yo (la IA) no tengo memoria entre chats. Cada conversación es nueva. Este prompt restaura el contexto necesario para que sea efectivo inmediatamente.

---

## 📂 ARCHIVOS CRÍTICOS A CONSULTAR (EN ORDEN)

Antes de responder cualquier pregunta sobre desarrollo, consulta estos archivos en este orden:

### 1. **ESTADO ACTUAL** (5 minutos de lectura)

```
/workspaces/talentia/src/docs/memories.md
```

**¿Qué contiene?** Contexto de negocio, modelos, relaciones, BD schema
**¿Cuándo consultarlo?** SIEMPRE antes de cualquier tarea
**¿Qué preguntas responde?**

- ¿Cuál es la estructura de datos?
- ¿Cómo se relacionan los modelos?
- ¿Qué migrations existen?
- ¿Cuál es la lógica de negocio?

### 2. **PLAN DEL MÓDULO ACTUAL** (3 minutos)

```
/workspaces/talentia/src/docs/PLAN_DIA_[N].md
```

(Donde [N] es el día en que estamos: 6, 7, 8, etc.)

**¿Qué contiene?** Tareas específicas del día, checkpoints, entregables
**¿Cuándo consultarlo?** SIEMPRE para saber qué se hace HOY
**¿Qué preguntas responde?**

- ¿Qué se supone que debo hacer hoy?
- ¿Cuál es el checkpoint a las 11:45?
- ¿Cuál es el entregable final?
- ¿Qué se conecta con mañana?

### 3. **API ENDPOINTS DOCUMENTADOS** (2 minutos)

```
/workspaces/talentia/src/docs/dia5_api_endpoints.md
```

**¿Qué contiene?** Lista de todos los 17 endpoints MVP (métodos, rutas, respuestas)
**¿Cuándo consultarlo?** Cuando necesites saber qué endpoints ya existen
**¿Qué preguntas responde?**

- ¿Existe ya el endpoint X?
- ¿Cuál es la firma correcta de la ruta?
- ¿Qué responden los endpoints actuales?

### 4. **LECCIONES APRENDIDAS** (referencia preventiva)

```
/workspaces/talentia/src/docs/LECCIONES_APRENDIDAS_DIA1_5.md
```

**¿Qué contiene?** Qué funcionó bien, qué falló, qué evitar
**¿Cuándo consultarlo?** ANTES de cualquier decisión de arquitectura
**¿Qué preguntas responde?**

- ¿Qué errores cometimos antes?
- ¿Cuál es la mejor práctica validada?
- ¿Qué no funciona en este proyecto?

### 5. **GUÍA DE DESARROLLO** (arquitectura del proceso)

```
/workspaces/talentia/src/docs/GUIA_DESARROLLO_ESTRUCTURADO.md
```

**¿Qué contiene?** Cómo se estructura el desarrollo, convenciones, templates
**¿Cuándo consultarlo?** Para decisiones sobre cómo estructurar código nuevo
**¿Qué preguntas responde?**

- ¿Cuál es la estructura de carpetas recomendada?
- ¿Cómo nombro clases y métodos?
- ¿Cómo estructuro un controller o service?

---

## 🔄 FLUJO DE TRABAJO CUANDO INICIO CHAT

### Paso 1: Validación de Contexto (2 minutos)

Antes de responder cualquier tarea:

```
[] ¿Qué day estamos? (Buscar PLAN_DIA_[N].md)
[] ¿Cuál es el módulo actual? (Buscar memories.md - sección "Módulo Actual")
[] ¿Qué se hizo ayer? (Buscar DIA_[N-1].md - resumen final)
[] ¿Qué falta hoy? (Leer PLAN_DIA_[N].md completamente)
```

### Paso 2: Contexto Técnico (1 minuto)

```
[] ¿Qué endpoints ya existen? (Revisar dia5_api_endpoints.md)
[] ¿Qué estructura de BD tenemos? (Revisar memories.md - schema)
[] ¿Cuál fue el último commit? (Revisar git log -1)
```

### Paso 3: Alineación de Objetivos (1 minuto)

```
[] ¿Cuál es el objetivo de HOY? (PLAN_DIA_[N].md - sección Objetivos)
[] ¿Cuál es mi responsabilidad? (PLAN_DIA_[N].md - encabezado)
[] ¿Cuál es el checkpoint? (PLAN_DIA_[N].md - Bloque 1/2)
[] ¿Cuáles son las restricciones? (LECCIONES_APRENDIDAS - errores a evitar)
```

### Paso 4: Ejecución

Ahora sí, procedo con la tarea sabiendo exactamente dónde estamos.

---

## 📊 PREGUNTAS QUE ME HAGO ANTES DE CUALQUIER TAREA

**SIEMPRE** hago estas preguntas:

| Pregunta                         | Dónde Buscar                    | Acción                    |
| -------------------------------- | ------------------------------- | ------------------------- |
| ¿Cuál es el estado del proyecto? | memories.md                     | Leer secciones relevantes |
| ¿Qué se supone hago hoy?         | PLAN*DIA*[N].md                 | Entender objetivo único   |
| ¿Ya existe este endpoint?        | dia5_api_endpoints.md           | No duplicar trabajo       |
| ¿Cuál es la estructura correcta? | GUIA_DESARROLLO_ESTRUCTURADO.md | Seguir convenciones       |
| ¿Qué errores evito?              | LECCIONES_APRENDIDAS_DIA1_5.md  | Aprender de historia      |
| ¿Qué validaciones necesito?      | VALIDACION_ESTADO.md            | Saber si está correcto    |
| ¿Dónde me oriento si me pierdo?  | MAPA_NAVEGACION.md              | Recuperar contexto        |

---

## 🎯 PRINCIPIOS OPERACIONALES

### Cuando me pidas una tarea:

1. **PRIMERO:** Leo el archivo relevante (5 minutos máximo)
2. **LUEGO:** Hago las preguntas de contexto
3. **DESPUÉS:** Propongo solución considerando:
    - ✅ Código ya escrito (no duplicar)
    - ✅ Patrones usados (mantener consistencia)
    - ✅ Errores evitados (aprender de lecciones)
    - ✅ Estructura definida (seguir guía)
    - ✅ Checkpoints (saber si funciona)

### Cuando algo explota:

1. **PRIMERO:** Consulto TROUBLESHOOTING.md
2. **LUEGO:** Identifico el tipo de error
3. **DESPUÉS:** Sigo los pasos de resolución
4. **FINAL:** Documento qué pasó

### Cuando dudo:

1. **PRIMERO:** Leo LECCIONES_APRENDIDAS_DIA1_5.md
2. **LUEGO:** Consulto decisiones pasadas
3. **DESPUÉS:** Sigo el patrón validado
4. **FINAL:** Cuestiono si hay razón para cambiar

---

## 📋 ESTRUCTURA ESPERADA DE ARCHIVOS

```
/workspaces/talentia/src/
├── app/
│   ├── Actions/           # Actions (form submissions)
│   ├── Http/
│   │   ├── Controllers/   # Controllers (la lógica)
│   │   └── Requests/      # FormRequests (validación)
│   ├── Models/            # Modelos (Eloquent)
│   ├── Services/          # Servicios (lógica de negocio)
│   └── Providers/
├── database/
│   ├── migrations/        # Migraciones BD
│   ├── factories/
│   └── seeders/
├── resources/
│   └── views/
│       └── [modulo]/      # Páginas Vue
├── routes/
│   └── api.php            # Rutas API
└── docs/
    ├── memories.md        # 🔴 CRÍTICO: contexto de negocio
    ├── PLAN_DIA_[N].md   # 🔴 CRÍTICO: qué hacer hoy
    ├── dia5_api_endpoints.md
    ├── LECCIONES_APRENDIDAS_DIA1_5.md
    ├── GUIA_DESARROLLO_ESTRUCTURADO.md
    └── [otros docs de referencia]
```

---

## 🔴 CRÍTICOS - NO OLVIDES

### Stack Definitivo:

- **Backend:** Laravel 12 + Sanctum (auth)
- **Frontend:** Vue 3 + Inertia.js + TypeScript
- **BD:** MySQL/PostgreSQL (migrations en /database/migrations)
- **Testing:** Pest (tests en /tests)
- **Build:** Vite
- **UI:** Vuetify

### Reglas de Oro:

1. ✅ SIEMPRE valida contra memories.md PRIMERO
2. ✅ SIEMPRE consulta qué ya existe (no duplicar)
3. ✅ SIEMPRE sigue la estructura definida en GUIA
4. ✅ SIEMPRE evita errores documentados en LECCIONES
5. ✅ SIEMPRE haz commit después de terminar tarea
6. ✅ SIEMPRE documenta en DIA\_[N].md al final

### Lo que NO hago:

- ❌ Asumir qué día es sin validar PLAN*DIA*[N].md
- ❌ Crear nuevos modelos sin revisar BD schema
- ❌ Proponer endpoints sin revisar los 17 existentes
- ❌ Cambiar estructura sin consultar GUIA
- ❌ Ignorar checkpoints horarios

---

## 📅 ESTADO ACTUAL (ACTUALIZAR CADA DÍA)

**Hoy es:** 27 de Diciembre 2025  
**Estamos en:** Días 6-7 (Frontend - Vuetify)  
**Módulo:** Dashboard + Componentes base  
**Fase:** Transición MVP backend → Frontend

**Lo que se completó:**

- ✅ MVP Backend (Días 1-5)
- ✅ 17 Endpoints API completamente funcionales
- ✅ 5/5 Tests PASS
- ✅ BD migrations + seeders
- ✅ 0 Syntax errors
- ✅ Documentación operativa

**Lo que falta:**

- ⏳ Días 6-7: Dashboard principal + componentes base
- ⏳ Días 8+: Módulos futuros (Competencias, Marketplace, etc.)

**Próxima tarea:** Revisar PLAN_DIA_6.md o PLAN_DIA_7.md según corresponda

---

## 🚀 CÓMO INTERPRETAR CUANDO EL USUARIO PIDE ALGO

### Cuando dice: "Crea el endpoint X"

→ Consulta: memories.md (modelo), GUIA (estructura), dia5_api_endpoints.md (patrón)

### Cuando dice: "Arregla el error Y"

→ Consulta: TROUBLESHOOTING.md, LECCIONES_APRENDIDAS (qué falló antes)

### Cuando dice: "¿Qué hacemos mañana?"

→ Consulta: PLAN*DIA*[N+1].md, VALIDACION_ESTADO.md (qué falta)

### Cuando dice: "¿Dónde está Z?"

→ Consulta: MAPA_NAVEGACION.md (orientación), estructura de carpetas

### Cuando dice: "No sé por dónde empezar"

→ Consulta: ECHADA_DE_ANDAR.md (para el usuario), QUICK_START.md (rápido)

---

## ✅ CHECKLIST DE CONTEXTO RESTAURADO

Cuando hayas leído este prompt completamente, has:

- [ ] Entendido que no tengo memoria entre chats
- [ ] Sabido dónde buscar estado actual (memories.md)
- [ ] Sabido qué hacer hoy (PLAN*DIA*[N].md)
- [ ] Aprendido los 5 archivos críticos
- [ ] Memorizado el flujo de validación
- [ ] Conocido los principios operacionales
- [ ] Identificado lo que NO hago
- [ ] Entendido la estructura de código
- [ ] Listo para ejecutar tareas

---

## 📞 CUANDO ME PEGUES ESTE PROMPT

Al inicio de cada chat, pega este texto completo o:

**Opción Rápida (60 segundos):**

```
Estamos en TalentIA, Día [N] de [Módulo].
Revisa: PLAN_DIA_[N].md y memories.md
¿Cuál es la tarea?
```

**Opción Completa (garantiza contexto perfecto):**
Copiar-pega TODO este documento

---

## 🎯 RESULTADO ESPERADO

Después de este prompt:

✅ Sé exactamente dónde estamos  
✅ Sé qué se ha hecho  
✅ Sé qué falta  
✅ Sé cómo proceder  
✅ Sé qué evitar  
✅ Estoy listo para ejecutar eficientemente

**Tiempo desde lectura a ejecución:** 5-7 minutos  
**Efectividad:** +90% más rápido que sin contexto

---

**Última actualización:** 27 Diciembre 2025  
**Válido desde:** HOY  
**Próxima revisión:** Fin de cada módulo  
**Formato:** Copiar-pegar al inicio de CADA chat nuevo

🚀 **Ahora estoy listo. ¿Cuál es la tarea?**
