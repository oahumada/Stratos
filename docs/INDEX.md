# 📚 Documentación TalentIA

Índice centralizado de toda la documentación del proyecto.

---

## 🚀 Inicio Rápido

- **[QUICK_START.md](QUICK_START.md)** - Cómo empezar (5 minutos)
- **[CHEATSHEET_COMANDOS.md](CHEATSHEET_COMANDOS.md)** - Comandos útiles
- **[DIA6_INICIO_RESUMEN.md](DIA6_INICIO_RESUMEN.md)** - ⭐ Resumen de Día 6 (leer hoy)

---

## 🎯 Patrones y Arquitectura Frontend (NUEVO)

- **[GUIA_RAPIDA_CRUD_GENERICO.md](GUIA_RAPIDA_CRUD_GENERICO.md)** - ⭐⭐ LEER PRIMERO - Guía rápida en 2 minutos
  - 5 pasos simples para crear nuevo CRUD
  - Sin escribir controladores
  - Sin duplicar rutas
- **[PATRON_JSON_DRIVEN_CRUD.md](PATRON_JSON_DRIVEN_CRUD.md)** - ⭐⭐⭐ Patrón JSON-Driven CRUD completo
  - Explicación de FormSchemaController automático
  - Cómo form-schema-complete.php genera rutas sin duplicación
  - Arquitectura técnica completa
- **[GUIA_CREAR_NUEVO_CRUD_GENERICO.md](GUIA_CREAR_NUEVO_CRUD_GENERICO.md)** - 📖 Guía paso-a-paso detallada
  - 6 pasos con ejemplos concretos
  - Ejemplo con "Certifications"
  - Checklist completo
- **[CHECKLIST_NUEVO_CRUD.md](CHECKLIST_NUEVO_CRUD.md)** - ✅ Checklist operativo con verificaciones
  - Paso 0: Registrar en form-schema-complete.php (IMPORTANTE)
  - Pasos 1-9: Llenar archivos, verificar
  - Troubleshooting
- **[PROGRESO_PRIORITY1_COMPLETO.md](PROGRESO_PRIORITY1_COMPLETO.md)** - ✅ Status Priority 1 - COMPLETADO
  - People/Index.vue ✅, Roles/Index.vue ✅, Skills/Index.vue ✅
  - Dashboard ✅, Marketplace ✅, GapAnalysis ✅, LearningPaths ✅
  - 100% reutilización, sin duplicación

---

## 🛠️ Setup y Configuración

### Desarrollo

- **[COMMITS_SETUP.md](COMMITS_SETUP.md)** - Setup de commits semánticos
- **[GUIA_COMMITS_SEMANTICOS.md](GUIA_COMMITS_SEMANTICOS.md)** - Guía completa de commits
- **[SCRIPT_COMMIT_MEJORADO.md](SCRIPT_COMMIT_MEJORADO.md)** - Explicación del script mejorado

### Autenticación (Sanctum)

- **[AUTH_SANCTUM_COMPLETA.md](AUTH_SANCTUM_COMPLETA.md)** - ⭐ Guía completa Sanctum en TalentIA
  - Estado actual de la configuración
  - Cómo funciona el flujo de autenticación
  - Solución de problemas
- **[auth_sanctum_api.md](auth_sanctum_api.md)** - Explicación técnica de `auth:sanctum`
- **[auth_sanctum_laravel12.md](auth_sanctum_laravel12.md)** - Configuración específica en Laravel 12

### Versionado y Releases

- **[VERSIONADO_SETUP.md](VERSIONADO_SETUP.md)** - Setup de versionado
- **[GUIA_VERSIONADO_CHANGELOG.md](GUIA_VERSIONADO_CHANGELOG.md)** - Guía completa de versionado

---

## 📖 Documentación Técnica

### Backend

- **[dia5_api_endpoints.md](dia5_api_endpoints.md)** - 17 endpoints del MVP
- **[dia3_services_logica_negocio.md](dia3_services_logica_negocio.md)** - Lógica de negocio
- **[DIAGRAMA_FLUJO.md](DIAGRAMA_FLUJO.md)** - Diagramas de flujo
- **[FormSchemaController-Complete-Documentation.md](FormSchemaController-Complete-Documentation.md)** - Controller CRUD

### Frontend

- **[DIA6_GUIA_INICIO_FRONTEND.md](DIA6_GUIA_INICIO_FRONTEND.md)** - Inicio con frontend
- **[DIA6_ARQUITECTURA_COMPLETA_FRONTEND_BACKEND.md](DIA6_ARQUITECTURA_COMPLETA_FRONTEND_BACKEND.md)** - Arquitectura full-stack
- **[DIA6_ANALISIS_ARQUITECTURA_FRONTEND.md](DIA6_ANALISIS_ARQUITECTURA_FRONTEND.md)** - Análisis detallado frontend
- **[DIA6_PLAN_ACCION.md](DIA6_PLAN_ACCION.md)** - Plan de acción Día 6 (actualizado)
- **[ACCION_DIA_6.md](ACCION_DIA_6.md)** - Checklist diario Día 6 (actualizado)

### Database

- **[dia1_migraciones_modelos_completados.md](dia1_migraciones_modelos_completados.md)** - Migraciones completadas
- **[dia2_seeders_completados.md](dia2_seeders_completados.md)** - Seeders de datos
- **[DATABASE_ER_DIAGRAM.html](DATABASE_ER_DIAGRAM.html)** - Diagrama ER interactivo (Mermaid)
- **[DATABASE_ER_DIAGRAM.md](DATABASE_ER_DIAGRAM.md)** - Diagrama ER (Markdown + ASCII)
- **[DATABASE_VISUALIZATION_GUIDE.md](DATABASE_VISUALIZATION_GUIDE.md)** - Guía de visualización (8 métodos)
- **[DATABASE_DIAGRAM_README.md](DATABASE_DIAGRAM_README.md)** - Cómo acceder a diagramas

### People Role Skills (Sistema de Skills con Historial) 🆕

- **[PEOPLE_ROLE_SKILLS_RESUMEN_FINAL.md](PEOPLE_ROLE_SKILLS_RESUMEN_FINAL.md)** - ⭐⭐⭐ LEER PRIMERO - Resumen ejecutivo
  - Problema identificado: inconsistencia people_skills vs role_skills
  - Solución: tabla `people_role_skills` con contexto de rol
  - Estado actual: 129 skills migradas, 74 expiradas, 75 gaps
  - Próximos pasos: Observer, API, Frontend
- **[PEOPLE_ROLE_SKILLS_IMPLEMENTACION.md](PEOPLE_ROLE_SKILLS_IMPLEMENTACION.md)** - 📖 Documentación técnica completa
  - Schema de tabla (13 columnas)
  - API del Repository (10+ métodos)
  - Scopes y helpers del modelo
  - Casos de uso reales
  - Comandos útiles
- **[PEOPLE_ROLE_SKILLS_FLUJO.md](PEOPLE_ROLE_SKILLS_FLUJO.md)** - 📊 Diagramas y flujos (Mermaid)
  - Flujo de asignación de rol
  - Diagrama de estados (skill lifecycle)
  - Diagrama de componentes (arquitectura)
  - Diagrama de secuencia (cambio de rol)
  - Diagrama ER (relaciones)
  - Casos de uso con ejemplos SQL

**Script de Verificación:**
```bash
./verify-people-role-skills.sh  # Verifica implementación completa
```

---

## 📊 Estado y Tracking

### MVP

- **[memories.md](memories.md)** - Memoria de contexto (actualizada)
- **[estado_actual_mvp.md](estado_actual_mvp.md)** - Estado actual del MVP
- **[CHECKLIST_MVP_COMPLETION.md](CHECKLIST_MVP_COMPLETION.md)** - Checklist de completitud

### Releases

- **[CHANGELOG_SISTEMA_OPERACION.md](CHANGELOG_SISTEMA_OPERACION.md)** - Cambios del sistema

---

## 🎯 Guías Especializadas

### Arquitectura

- **[GUIA_NAVEGACION_ARQUITECTURA.md](GUIA_NAVEGACION_ARQUITECTURA.md)** - Navegación de la arquitectura
- **[GUIA_DESARROLLO_ESTRUCTURADO.md](GUIA_DESARROLLO_ESTRUCTURADO.md)** - Guía de desarrollo estructurado
- **[PANORAMA_COMPLETO_ARQUITECTURA.md](PANORAMA_COMPLETO_ARQUITECTURA.md)** - Visión completa

### Índices y Referencias

- **[INDICE_DOCUMENTACION_ARQUITECTURA.md](INDICE_DOCUMENTACION_ARQUITECTURA.md)** - Índice de arquitectura
- **[INDICE_FINAL_ENTREGABLES.md](INDICE_FINAL_ENTREGABLES.md)** - Índice de entregables
- **[DIA6_TABLA_REFERENCIA_RAPIDA.md](DIA6_TABLA_REFERENCIA_RAPIDA.md)** - Tabla de referencia rápida

---

## 🆕 Módulos Nuevos (Día 6+)

### Workforce Planning - Planificación Dotacional Estratégica

- **[MODULE_TASKFORCE.md](MODULE_TASKFORCE.md)** - Análisis completo del módulo (referencia)
- **[WORKFORCE_PLANNING_GUIA.md](WORKFORCE_PLANNING_GUIA.md)** - Guía rápida de implementación ⭐ COMIENZA AQUÍ

---

## 📝 Resúmenes Ejecutivos

- **[DIA6_RESUMEN_5_MINUTOS.md](DIA6_RESUMEN_5_MINUTOS.md)** - Resumen en 5 minutos
- **[RESUMEN_EJECUTIVO.md](RESUMEN_EJECUTIVO.md)** - Resumen ejecutivo completo
- **[RESUMEN_FINAL_ARQUITECTURA.md](RESUMEN_FINAL_ARQUITECTURA.md)** - Resumen de arquitectura
- **[ULTRA_RESUMEN.md](ULTRA_RESUMEN.md)** - Versión ultra-corta

---

## 🔧 Troubleshooting y Ayuda

- **[TROUBLESHOOTING.md](TROUBLESHOOTING.md)** - Problemas comunes y soluciones
- **[PROMPT_INICIAL_COPIAR_PEGAR.md](PROMPT_INICIAL_COPIAR_PEGAR.md)** - Prompt inicial para Copilot

---

## 📋 Planes y Roadmaps

### 🚀 **Roadmap Frontend MVP** ⭐ ACTUAL

- **[MVP_FRONTEND_ROADMAP.md](MVP_FRONTEND_ROADMAP.md)** - Plan detallado de Frontend para últimas 2 semanas (8-14 días)
  - Módulos: People, Skills, Roles, Gap Analysis, Learning Paths, Dashboard
  - Timeline por fase
  - Criterios de aceptación
  - **👉 COMIENZA AQUÍ para saber qué construir**

### 📅 Planes Pasados

- **[DIA6_PLAN_ACCION.md](DIA6_PLAN_ACCION.md)** - Plan de acción del día 6
- **[ACCION_DIA_6.md](ACCION_DIA_6.md)** - Acciones completadas día 6
- **[DIA6_EVALUACION_INTEGRAL.md](DIA6_EVALUACION_INTEGRAL.md)** - Evaluación integral
- **[DIA6_RESUMEN_DOCUMENTACION_GENERADA.md](DIA6_RESUMEN_DOCUMENTACION_GENERADA.md)** - Resumen de docs generadas

---

## 📚 Otras Guías

- **[ECHADA_DE_ANDAR.md](ECHADA_DE_ANDAR.md)** - Puesta en marcha
- **[ENTREGA_COMPLETA_DIA6.md](ENTREGA_COMPLETA_DIA6.md)** - Entrega completa
- **[LECCIONES_APRENDIDAS_DIA1_5.md](LECCIONES_APRENDIDAS_DIA1_5.md)** - Lecciones aprendidas
- **[STATUS_EJECUTIVO_DIA5.md](STATUS_EJECUTIVO_DIA5.md)** - Status ejecutivo día 5
- **[MAPA_NAVEGACION.md](MAPA_NAVEGACION.md)** - Mapa de navegación
- **[DIA6_COMENTARIOS_CODIGO.md](DIA6_COMENTARIOS_CODIGO.md)** - Comentarios sobre el código

---

## 🚀 Cómo Usar Esta Documentación

1. **Si es tu primer día:**

   - Lee [QUICK_START.md](QUICK_START.md)
   - Luego [memories.md](memories.md)

2. **Si necesitas hacer un commit:**

   - [GUIA_COMMITS_SEMANTICOS.md](GUIA_COMMITS_SEMANTICOS.md)

3. **Si necesitas hacer un release:**

   - [GUIA_VERSIONADO_CHANGELOG.md](GUIA_VERSIONADO_CHANGELOG.md)

4. **Si necesitas entender la arquitectura:**

   - [DIA6_ARQUITECTURA_COMPLETA_FRONTEND_BACKEND.md](DIA6_ARQUITECTURA_COMPLETA_FRONTEND_BACKEND.md)

5. **Si algo no funciona:**
   - [TROUBLESHOOTING.md](TROUBLESHOOTING.md)

---

## 📊 Estadísticas de Documentación

```
Total de archivos:    45+ documentos
Documentación:        ~500 KB
Últimas actualizaciones: 28 Dec 2025
Estado:               ✅ Centralizada en /docs
```

---

**Última actualización:** 28 de Diciembre, 2025
