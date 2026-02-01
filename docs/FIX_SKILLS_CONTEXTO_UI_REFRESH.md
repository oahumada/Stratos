# Fix Completo: Skills - Contexto y UI Refresh

**Fecha:** 2026-02-01  
**Componente:** `ScenarioPlanning/Index.vue`  
**Tipo:** Bug fixes relacionados (contexto + visualización)

---

## Contexto General

Durante la implementación de la jerarquía de planificación de escenarios (Scenarios → Capabilities → Competencies → Skills), se identificaron 3 problemas relacionados en el manejo de skills:

1. **Bug crítico:** Foreign key constraint al guardar competencies con skills
2. **Bug de contexto:** Crear skills repetidas fallaba por contexto incorrecto
3. **Bug de UI:** Skills creadas no se mostraban inmediatamente

Todos estos bugs compartían una raíz común: **falta de robustez en el manejo del contexto jerárquico**.

---

## 🐛 Problema 1: Foreign Key Constraint Failed

### Síntoma

```
SQLSTATE[23000]: Integrity constraint violation: 19 FOREIGN KEY constraint failed
```

### Causa Raíz

En `saveSelectedChild()`, la lógica extraía **nombres de skills** en lugar de **IDs**:

```typescript
// ❌ ANTES (línea ~3599)
const skillIds = formData.skills
  ?.filter((s: any) => s.id)
  .map((s: any) => s.id); // BIEN, pero...

// Si formData.skills era ['Skill Name'] en lugar de [{ id: 1, name: 'Skill Name' }]
// → skillIds = [undefined]
// → Backend recibe null
// → FK constraint failed
```

### Solución

Extracción robusta de IDs desde objetos:

```typescript
// ✅ DESPUÉS
const skillIds = formData.skills
  ?.filter((s: any) => s && (s.id || typeof s === "object"))
  .map((s: any) => (typeof s === "object" ? s.id : s))
  .filter((id: any) => id !== undefined && id !== null);
```

**Resultado:** Backend recibe `[1, 2, 3]` en lugar de `['Skill A', 'Skill B']`

---

## 🐛 Problema 2: Contexto Incorrecto al Crear Skills Repetidas

### Síntoma

- Primera creación de skill: ✅ OK
- Segunda creación de skill: ❌ Falla o usa padre incorrecto

### Causa Raíz

`showCreateSkillDialog()` no validaba ni limpiaba el contexto:

```typescript
// ❌ ANTES (línea ~1660)
const showCreateSkillDialog = (displayNode: any) => {
  // Solo seteaba selectedChild si displayNode era competency
  if (dn.compId || (typeof dn.id === "number" && dn.id < 0)) {
    selectedChild.value = dn as any;
  }
  // Si displayNode era skill → selectedChild quedaba como skill
  // → createAndAttachSkill() usaba skill como padre → ERROR
};
```

### Solución

Robusta resolución de contexto en 5 pasos:

```typescript
// ✅ DESPUÉS (líneas 1660-1710)
const showCreateSkillDialog = (displayNode: any) => {
  const dn = displayNode || selectedNode.value;

  // PASO 1: Si displayNode es competency → usar directamente
  if ((dn.compId || (typeof dn.id === "number" && dn.id < 0)) && !dn.skillId) {
    selectedChild.value = dn as any;
  }

  // PASO 2: Si displayNode es capability con competencies → usar primera
  else if (dn.capabilityId && dn.competencies?.length) {
    selectedChild.value = dn.competencies[0];
  }

  // PASO 3: Si displayNode es skill → buscar competencia padre vía edges
  else if (dn.skillId) {
    const skillId = typeof dn.skillId === "number" ? dn.skillId : dn.id;
    const edgeToParent = internalEdges.value.find(
      (e: any) => e.target === `skill-${skillId}`,
    );
    if (edgeToParent) {
      const compId = parseInt(edgeToParent.source.replace("comp-", ""));
      const foundComp = allComps.value.find((c: any) => c.id === compId);
      if (foundComp) selectedChild.value = foundComp;
    }
  }

  // PASO 4: Si selectedChild actual es skill → buscar su competencia padre
  else if (selectedChild.value && (selectedChild.value as any).skillId) {
    const currentSkillId =
      (selectedChild.value as any).skillId || (selectedChild.value as any).id;
    const edgeToParent = internalEdges.value.find(
      (e: any) => e.target === `skill-${currentSkillId}`,
    );
    if (edgeToParent) {
      const compId = parseInt(edgeToParent.source.replace("comp-", ""));
      const foundComp = allComps.value.find((c: any) => c.id === compId);
      if (foundComp) selectedChild.value = foundComp;
    }
  }

  // PASO 5: Validación final - si sigue siendo skill, limpiar
  if (selectedChild.value && (selectedChild.value as any).skillId) {
    console.warn("⚠️ selectedChild es skill, limpiando contexto");
    selectedChild.value = null;
  }

  formData.name = "";
  formData.description = "";
  showSkillDialog.value = true;
};
```

### Casos Manejados

- ✅ Crear skill desde competencia seleccionada → usa competencia
- ✅ Crear skill desde capability → usa primera competency
- ✅ Crear skill estando en otra skill → busca competency padre
- ✅ Crear múltiples skills sucesivamente → limpia contexto skill
- ✅ Previene usar skill como padre → validación final

---

## 🐛 Problema 3: Skills Creadas No Se Muestran Inmediatamente

### Síntoma

Al crear o adjuntar una skill:

- ✅ Se guarda correctamente en backend
- ❌ NO aparece visualmente en el mapa
- ✅ Aparece después de refresh manual

### Causa Raíz

Faltaba llamar a `expandSkills()` después de crear/adjuntar.

**Comparación de patrones:**

```typescript
// ✅ Capabilities (línea ~1780)
await createCapability(...);
await loadTreeFromApi(props.scenario.id);  // Refresh completo

// ✅ Competencies (línea ~3563)
await createCompetency(...);
expandCompetencies(parent, { x: parent.x, y: parent.y });  // Expand para mostrar

// ❌ Skills (línea ~580) - FALTABA
await createSkill(...);
// NO había expand → skill creada pero invisible
```

### Solución

Agregado `expandSkills()` en 2 funciones:

#### 1. `createAndAttachSkill()` (línea ~588)

```typescript
// ✅ DESPUÉS
const created = await createAndAttachSkillForComp(compId, payload);
if (created) {
  if (!Array.isArray((selectedChild.value as any).skills)) {
    (selectedChild.value as any).skills = [];
  }
  (selectedChild.value as any).skills.push(created);
}
showSuccess("Skill creada y asociada");

// ✅ AGREGADO: Expand para mostrar inmediatamente
if (selectedChild.value) {
  expandSkills(selectedChild.value, undefined, { layout: "auto" });
}
```

#### 2. `attachExistingSkill()` (línea ~617)

```typescript
// ✅ DESPUÉS
await api.post(`/api/competencies/${compId}/skills`, {
  skill_id: selectedSkillId.value,
});
showSuccess("Skill asociada");

// ✅ AGREGADO: Expand para mostrar inmediatamente
if (selectedChild.value) {
  expandSkills(selectedChild.value, undefined, { layout: "auto" });
}
```

### Comportamiento Ahora

- ✅ Crear skill → aparece inmediatamente en el mapa
- ✅ Adjuntar skill existente → aparece inmediatamente en el mapa
- ✅ Consistente con capabilities y competencies

---

## 📋 Resumen de Cambios

| Función                   | Línea     | Cambio                            | Propósito                    |
| ------------------------- | --------- | --------------------------------- | ---------------------------- |
| `saveSelectedChild()`     | ~3599     | Extracción robusta de skill IDs   | Prevenir FK constraint       |
| `showCreateSkillDialog()` | 1660-1710 | Resolución de contexto en 5 pasos | Validar padre competency     |
| `createAndAttachSkill()`  | ~588      | Agregado `expandSkills()`         | Mostrar skill inmediatamente |
| `attachExistingSkill()`   | ~617      | Agregado `expandSkills()`         | Mostrar skill inmediatamente |

---

## 🎯 Lecciones Aprendidas

### 1. Separar UI Data vs API Data

- UI muestra **nombres** para el usuario
- API requiere **IDs** para foreign keys
- Mantener referencias a objetos completos: `{ id: 1, name: 'Skill' }`
- Extraer IDs solo al enviar al backend

### 2. Validación de Contexto Jerárquico

En estructuras padre-hijo-nieto:

- **Validar tipo** antes de usar como contexto
- **Buscar padre** si el nodo actual es incorrecto
- **Limpiar contexto** como último recurso
- **Multiple fallbacks** para robustez

### 3. Actualización de UI Después de Mutaciones

Patrón consistente en las 3 jerarquías:

```typescript
// Crear nodo
await createNode(...);

// Actualizar datos locales
parent.children.push(created);

// 🎯 CRÍTICO: Actualizar visualización
expandChildren(parent, ...);  // o loadTreeFromApi()
```

### 4. Testing de Casos Edge

Casos que deben probarse:

- ✅ Primera creación
- ✅ Creación repetida (2da, 3ra vez)
- ✅ Creación desde diferentes contextos (padre correcto vs incorrecto)
- ✅ Creación + edición inmediata
- ✅ Refresh manual vs automático

---

## 🧪 Pruebas Recomendadas

### Manual Testing

1. Crear capability → crear competency → crear skill
2. Seleccionar skill → intentar crear otra skill (debe usar comp padre)
3. Crear skill → verificar aparece sin refresh
4. Adjuntar skill existente → verificar aparece sin refresh
5. Crear múltiples skills seguidas → todas deben aparecer

### Integration Testing (Futuro)

```typescript
describe("Skills - Contexto y UI", () => {
  it("extrae skill IDs correctamente para API", () => {
    // Test saveSelectedChild skill ID extraction
  });

  it("resuelve contexto competency desde skill", () => {
    // Test showCreateSkillDialog context resolution
  });

  it("muestra skill inmediatamente después de crear", () => {
    // Test expandSkills call after create
  });
});
```

---

## 🔗 Referencias

- **Documentación relacionada:**
  - `PATRON_BUGS_CONTEXTO_DIALOGO.md` - Patrón general de bugs de contexto
  - `DRY_REFACTOR_SCENARIO_PLANNING.md` - Plan para eliminar duplicación
  - `openmemory.md` - Registro completo de fixes

- **Archivos modificados:**
  - `src/resources/js/pages/ScenarioPlanning/Index.vue`

- **Issue tracking:**
  - Bug original: Foreign key constraint failed (skills)
  - Bug relacionado: Competencies tenían mismo problema de contexto
  - Mejora: DRY refactoring pendiente (eliminar ~650 líneas duplicadas)

---

**Próximos pasos:**

1. ✅ Bugs críticos resueltos
2. ⏳ Aplicar DRY refactoring usando composables creados
3. ⏳ Crear tests de integración
4. ⏳ Documentar patrón para futuros módulos jerárquicos
