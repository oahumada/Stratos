# 🎯 Patrón: Actualización de UI después de crear nodos jerárquicos

**Problema identificado:** Después de crear un nodo, no se refleja en la UI de inmediato.

## Análisis del Patrón Actual

### Capabilities - ✅ FUNCIONA (línea ~1780)

```typescript
async function saveNewCapability() {
    const created = await api.post(...);
    showSuccess('Capacidad creada');

    // Optimistic update: agregar a local nodes
    nodes.value = [...nodes.value, newNode];
    buildEdgesFromItems(...);
    positionsDirty.value = true;

    // Refresh canonical tree from API
    await loadTreeFromApi(props.scenario.id);  // ✅ KEY
}
```

**Flujo:**

1. POST crea capability
2. Optimistic update: agrega a `nodes.value`
3. Refresh: `loadTreeFromApi()` recarga todo
4. Resultado: Capability visible inmediatamente + actualizado desde API

### Competencies - ✅ FUNCIONA (línea ~3545)

```typescript
async function createAndAttachComp() {
    const result = await api.post(...);
    showSuccess('Competencia creada');

    // Si hay skills, crearlas también
    if (newCompSkills.value) { ... }

    // Reset form
    resetCompetencyForm();

    // Expand parent para mostrar nueva competencia
    if (parent) {
        expandCompetencies(parent, { x: parent.x, y: parent.y });  // ✅ KEY
    }
    showSuccess('Competencia creada y asociada');
}
```

**Flujo:**

1. POST crea competency
2. Crea skills asociados si aplica
3. `expandCompetencies()` reabre la vista de parent
4. Resultado: Nueva competencia visible (porque se reexpande parent)

### Skills - ❌ FALLA (línea ~580)

```typescript
async function createAndAttachSkill() {
  const created = await createAndAttachSkillForComp(compId, payload);

  // Agrega a selectedChild.skills
  if (created) {
    if (!Array.isArray((selectedChild.value as any).skills))
      (selectedChild.value as any).skills = [];
    (selectedChild.value as any).skills.push(created);
  }

  createSkillDialogVisible.value = false;
  showSuccess("Skill creada y asociada");
  // ❌ FALTA: No expande skills, no refresca UI
}
```

**Flujo:**

1. POST crea skill
2. Intenta agregar a `selectedChild.value.skills`
3. Cierra diálogo
4. ❌ NO actualiza la visualización de skills
5. Resultado: Skill existe en BD pero no se ve en UI

## Solución

### Opción A: Expandir Skills (Similar a Competencies)

```typescript
async function createAndAttachSkill() {
  const created = await createAndAttachSkillForComp(compId, payload);

  if (created) {
    if (!Array.isArray((selectedChild.value as any).skills))
      (selectedChild.value as any).skills = [];
    (selectedChild.value as any).skills.push(created);
  }

  createSkillDialogVisible.value = false;
  newSkillName.value = "";
  newSkillCategory.value = "";
  newSkillDescription.value = "";
  showSuccess("Skill creada y asociada");

  // ✅ AGREGAR: Expand skills como se hace con competencies
  if (selectedChild.value) {
    expandSkills(selectedChild.value, undefined, { layout: "auto" });
  }
  savingSkill.value = false;
}
```

### Opción B: Refresh Completo (Similar a Capabilities)

```typescript
async function createAndAttachSkill() {
  const created = await createAndAttachSkillForComp(compId, payload);

  showSuccess("Skill creada y asociada");

  // ✅ AGREGAR: Refrescar árbol completo
  await loadTreeFromApi(props.scenario.id);

  createSkillDialogVisible.value = false;
  newSkillName.value = "";
  newSkillCategory.value = "";
  newSkillDescription.value = "";
  savingSkill.value = false;
}
```

### Opción C: Hybrid (Mejor)

```typescript
async function createAndAttachSkill() {
  const created = await createAndAttachSkillForComp(compId, payload);

  if (created) {
    // Optimistic update
    if (!Array.isArray((selectedChild.value as any).skills))
      (selectedChild.value as any).skills = [];
    (selectedChild.value as any).skills.push(created);
  }

  createSkillDialogVisible.value = false;
  newSkillName.value = "";
  newSkillCategory.value = "";
  newSkillDescription.value = "";
  showSuccess("Skill creada y asociada");

  // ✅ AGREGAR AMBAS:
  // 1. Expandir para mostrar nueva skill inmediatamente
  if (selectedChild.value) {
    expandSkills(selectedChild.value, undefined, { layout: "auto" });
  }

  // 2. Refresh en background para sincronizar con API
  if (props.scenario?.id) {
    loadTreeFromApi(props.scenario.id).catch((err) => {
      console.error("[createAndAttachSkill] error refreshing tree:", err);
    });
  }

  savingSkill.value = false;
}
```

## Recomendación

**Usar Opción C (Hybrid):**

- ✅ Muestra skill inmediatamente (expandSkills)
- ✅ Sincroniza con API en background (loadTreeFromApi)
- ✅ No bloquea UI (no await en refresh)
- ✅ Consistente con patrón de competencies + capabilities

## Implementación

El fix es una línea:

```typescript
// Después de crear skill, antes del finally:
if (selectedChild.value) {
  expandSkills(selectedChild.value, undefined, { layout: "auto" });
}

// Opcional: refresh en background
loadTreeFromApi(props.scenario.id).catch(() => {});
```
