# Workflow de Desarrollo TalentIA

## Guía para Uso Eficiente de memories.md

Este documento describe cómo usar `docs/memories.md` como fuente de verdad durante todo el ciclo de desarrollo para asegurar consistencia, calidad y velocidad.

---

## 1. Workflow General

### 1.1 Antes de Cada Tarea/Feature

Siempre consulta la sección relevante de `memories.md` antes de empezar a codear.

**Ejemplo:** Vas a implementar el endpoint de gap-analysis

En el chat con GitHub Copilot, empieza con:

```
"Según memories.md sección X, necesito implementar [feature].
¿Qué endpoints/modelos/validaciones debo crear?"
```

Esto fuerza a Copilot a revisar el spec antes de generar código.

### 1.2 Checklist Pre-Implementación (30 segundos)

Antes de escribir una línea de código, verifica:

- [ ] **¿Está en el alcance MVP?** → Revisar sección 2.1
- [ ] **¿Qué endpoints necesito?** → Sección 6.2 (revisar leyenda ✅/🔴/🟡)
- [ ] **¿Qué modelos/tablas afecta?** → Sección 7 (Modelo de Datos)
- [ ] **¿Hay algoritmo específico?** → Sección 16 (Algoritmos Clave)
- [ ] **¿Hay reglas de negocio?** → Sección 5 (Reglas de Negocio)

### 1.3 Durante el Desarrollo

Mantén `docs/memories.md` abierto en un tab de VS Code.

**Búsqueda rápida con Ctrl+F:**

- Nombres de modelos: `People`, `Role`, `Skill`
- Endpoints: `/gap-analysis`, `/People`
- Validaciones: `level (1-5)`, `organization_id`
- Algoritmos: `calculateGap`, `generateDevelopmentPath`

### 1.4 Validación Post-Implementación

Después de implementar algo, pide validación a Copilot:

```
"Acabo de implementar [X]. Revisa contra memories.md sección Y
si cumple con: validaciones, multi-tenant, convenciones de nombres"
```

### 1.5 Actualización del Documento

Cuando descubras algo durante el desarrollo que no esté documentado:

```
"Agregué [X] al código. ¿Debemos actualizar memories.md sección Y?"
```

Copilot hará el update inmediatamente para mantener sincronía.

---

## 2. Comandos Rápidos para Copilot

### Consultas Específicas

```
"Según memories.md, ¿cuál es la estructura exacta del endpoint POST /gap-analysis?"

"Dame el algoritmo de calculateGap de la sección 16.1"

"¿Qué campos tiene la tabla people_skills según el modelo de datos?"

"¿Cuáles son los datos de demo de TechCorp (sección 11)?"
```

### Validar Código

```
"Revisa este código contra memories.md - ¿cumple multi-tenant y validaciones?"

"¿Este endpoint está marcado como MVP en memories.md sección 6.2?"

"Verifica que estas migraciones cumplan con los constraints de la sección 7"
```

### Generar Código desde Spec

```
"Implementa el GapAnalysisService siguiendo el pseudocódigo de memories.md sección 16.1"

"Crea el seeder de TechCorp según los datos de la sección 11.2"

"Genera el endpoint GET /dashboard/metrics según sección 6.2 (marcado MVP)"
```

---

## 3. Workflow Semana 1: Modelo de Datos + Core

### Día 1-2: Setup + Migraciones

**Input:**

```
"Vamos a crear las migraciones según sección 7 de memories.md.
Empecemos con organizations, users, skills, roles"
```

**Copilot genera:** Migraciones respetando constraints, índices, multi-tenant

**Validación:**

```bash
php artisan migrate
# Revisar estructura en DB (pgAdmin o TablePlus)
```

### Día 3-4: Modelos + Seeders

**Input:**

```
"Crea los modelos Eloquent según sección 7 con relaciones y scopes multi-tenant"
```

**Copilot genera:** Modelos con Global Scopes para `organization_id`

**Input:**

```
"Ahora el seeder de TechCorp según sección 11"
```

**Validación:**

```bash
php artisan db:seed --class=DemoSeeder
# Verificar 20 peopleas, 8 roles, 30 skills en DB
```

### Día 5-7: Gap Analysis (Core Business Logic)

**Input:**

```
"Implementa GapAnalysisService según algoritmo 16.1"
```

**Copilot genera:** Service con lógica de cálculo de brechas

**Input:**

```
"Crea el endpoint POST /gap-analysis según sección 6.2 (MVP)"
```

**Validación:**

```bash
# Test con Postman/Insomnia usando datos de TechCorp
POST /api/gap-analysis
Body: {
  "people_id": 8,  // Ana García
  "role_id": 2     // Senior Frontend Developer
}

# Debe retornar ~88.5% match según memories.md 11.2
```

---

## 4. Workflow Semana 2: Features Avanzadas

### Día 8-10: Rutas de Desarrollo

**Input:**

```
"Implementa DevelopmentPathService según algoritmo 16.2"
"Crea endpoint POST /development-paths/generate (sección 6.2 - MVP simulado)"
```

**Validación:**

- Generar ruta para Ana García → Senior Frontend
- Verificar pasos sugeridos (cursos de Kubernetes, GraphQL)
- Estimar duración en meses

### Día 11-12: Marketplace + Matching

**Input:**

```
"Implementa matching de candidatos según algoritmo 16.3"
"Crea endpoint GET /job-openings/{id}/candidates (sección 6.2 - MVP)"
```

**Validación:**

- Abrir vacante "Senior Frontend Developer"
- Verificar que Ana García (88.5%) aparece primera
- Carlos López (75%) aparece segundo

### Día 13-14: Dashboard + Frontend

**Input:**

```
"Implementa endpoints de dashboard según sección 6.2 (todos MVP)"
"Crea componentes Vue para dashboard según sección 10"
```

**Validación:**

- Dashboard muestra KPIs: coverage, roles at risk, gaps
- Gráficos renderizan correctamente con Vuetify

---

## 5. Patrón de Conversación Eficiente

### Ejemplo de Conversación Ideal

**TÚ:**

```
Siguiente tarea: Dashboard metrics (sección 4.3 + 6.2)
```

**COPILOT:**

```
✅ Revisado memories.md sección 4.3 (flujo dashboard)
✅ Endpoint GET /dashboard/metrics marcado como MVP
✅ Necesita calcular: coverage, roles_at_risk, total_gaps, ready_for_promotion

¿Creo el Controller + Service + Resource?
```

**TÚ:**

```
Sí, hazlo
```

**COPILOT:**

```
[Genera código completo con validaciones según spec]
```

### Evitar Desviaciones del MVP

Si pides algo fuera del MVP, Copilot te recordará:

**COPILOT:**

```
⚠️ Autenticación completa está marcada como POST-MVP en sección 2.2.
Para el MVP usamos user hardcoded según sección 4.1.
¿Procedemos así o cambiamos el alcance?
```

---

## 6. Estructura de Commits Sugerida

Referencia siempre a la sección de `memories.md`:

```bash
git commit -m "feat: implement gap analysis algorithm (memories.md 16.1 + 5.2)"
git commit -m "feat: add dashboard metrics endpoint (memories.md 6.2 MVP)"
git commit -m "chore: seed TechCorp demo data (memories.md 11.2)"
git commit -m "refactor: apply multi-tenant scope (memories.md 3.2)"
git commit -m "test: gap analysis with TechCorp data (memories.md 16.1)"
```

---

## 7. Estrategia de Desarrollo

### Opción A: Full MVP en Orden Secuencial (Recomendado)

**Ventajas:** Construcción sólida de la base, menos refactoring

**Pasos:**

1. **Migraciones** (sección 7) - Día 1-2
2. **Modelos + Scopes multi-tenant** - Día 2-3
3. **Seeder TechCorp** (sección 11) - Día 3-4
4. **Endpoints de lectura** (People, Skills, Roles) - Día 4-5
5. **Gap Analysis** (algoritmo 16.1 + endpoint 6.2) - Día 5-7
6. **Development Paths** (algoritmo 16.2) - Día 8-10
7. **Marketplace + Matching** (algoritmo 16.3) - Día 11-12
8. **Dashboard** (sección 4.3) - Día 13-14

### Opción B: Slice Vertical (Feature End-to-End)

**Ventajas:** Feedback rápido, demo funcional desde día 3

**Pasos:**

1. **Setup básico** (migrations + models mínimos) - Día 1
2. **Seeder reducido** (3 peopleas + 1 rol) - Día 2
3. **Gap Analysis completo** (backend + frontend) - Día 3-5
4. **Demo visual + validación** - Día 5
5. **Iterar con Development Paths** - Día 6-8
6. **Iterar con Marketplace** - Día 9-11
7. **Dashboard + pulido** - Día 12-14

---

## 8. Checklist de Calidad por Feature

Antes de marcar una feature como "done", validar:

### Backend

- [ ] Migraciones ejecutan sin errores
- [ ] Modelos tienen Global Scope `organization_id`
- [ ] Validaciones en FormRequest (sección 5)
- [ ] API Resources retornan estructura documentada (sección 6)
- [ ] Algoritmo implementado según pseudocódigo (sección 16)
- [ ] Endpoints marcados MVP están funcionales
- [ ] Tests básicos (opcional en MVP, crítico post-MVP)

### Frontend

- [ ] Componentes Vuetify según sistema de diseño (sección 8)
- [ ] TypeScript strict mode sin errores
- [ ] Interfaces definidas para modelos (sección 10)
- [ ] Responsive en móvil/tablet/desktop
- [ ] Datos de demo renderizan correctamente

### Multi-Tenant

- [ ] Todas las queries filtran por `organization_id`
- [ ] Middleware valida tenant context
- [ ] Subdomain detecta organización correctamente

---

## 9. Testing Rápido con Datos de Demo

### Casos de Prueba Pre-definidos (Sección 11)

**Caso 1: Ana García (match alto)**

```bash
POST /api/gap-analysis
Body: { people_id: 8, role_id: 2 }
# Expected: ~88.5% match, gaps en Kubernetes/GraphQL
```

**Caso 2: Carlos López (match medio)**

```bash
POST /api/gap-analysis
Body: { people_id: 12, role_id: 2 }
# Expected: ~75% match, gaps en System Design/Microservices/Docker
```

**Caso 3: María Rodríguez (lista para promoción)**

```bash
POST /api/gap-analysis
Body: { people_id: 5, role_id: 7 }
# Expected: ~95% match, candidata ideal para Tech Lead
```

**Caso 4: Dashboard Metrics**

```bash
GET /api/dashboard/metrics
# Expected:
# - coverage: ~75%
# - roles_at_risk: 2-3 roles
# - total_gaps: calculado según todos los empleados
# - ready_for_promotion: María + 1-2 más
```

---

## 10. Troubleshooting Común

### Problema: Query devuelve datos de otra organización

**Causa:** Falta aplicar Global Scope

**Solución:**

```
"Copilot, revisa si el modelo X tiene el Global Scope de organization_id
según memories.md sección 3.2"
```

### Problema: Endpoint devuelve estructura diferente a la documentada

**Causa:** API Resource no sigue el spec

**Solución:**

```
"El endpoint GET /People devuelve [estructura actual].
Según memories.md sección 6.2 debería retornar [estructura esperada].
Corrige el Resource"
```

### Problema: Validación falla con datos válidos

**Causa:** Reglas de validación no coinciden con reglas de negocio

**Solución:**

```
"La validación de people_skills.level rechaza nivel 3.
Según memories.md sección 5.1, niveles válidos son 1-5.
Corrige el FormRequest"
```

---

## 11. Recursos Rápidos

### Navegación Rápida en memories.md

| Necesitas...                  | Ve a Sección             |
| ----------------------------- | ------------------------ |
| ¿Está en MVP?                 | 2.1                      |
| ¿Qué endpoint usar?           | 6.2 (con leyenda ✅🔴🟡) |
| ¿Cómo se calcula X?           | 16 (Algoritmos)          |
| ¿Qué campos tiene tabla Y?    | 7 (Modelo de Datos)      |
| ¿Cuáles son las validaciones? | 5 (Reglas de Negocio)    |
| ¿Qué datos de prueba usar?    | 11 (TechCorp Demo)       |
| ¿Cómo se ve el flujo?         | 4 (Flujos Principales)   |
| ¿Qué stack usar?              | 3 (Arquitectura)         |
| ¿Comandos disponibles?        | 14 (Comandos Útiles)     |

### Glosario Rápido (Sección 15)

- **Skill:** Capacidad/conocimiento (ej: React, Leadership)
- **Nivel:** Escala 1-5 de maestría
- **Rol:** Perfil de cargo con skills requeridas
- **Brecha (Gap):** Diferencia entre nivel actual y requerido
- **Match %:** Alineación peoplea ↔ rol
- **Ruta de Desarrollo:** Plan para cerrar brechas

---

## 12. Próximos Pasos

### Para Empezar AHORA

**Opción Recomendada:** Opción A (Full MVP secuencial)

**Primer comando:**

```
"Empecemos con las migraciones según memories.md sección 7.
Crea migrations para organizations, users, skills, roles, People,
con todos los constraints e índices documentados"
```

**Validación inicial:**

```bash
php artisan migrate
php artisan db:seed --class=DemoSeeder
# Verificar que TechCorp se crea correctamente
```

---

**Última actualización:** 2025-12-27  
**Versión:** 1.0  
**Autor:** Equipo TalentIA
