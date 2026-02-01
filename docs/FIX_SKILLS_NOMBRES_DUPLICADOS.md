# Fix: Skills con Nombres Duplicados

**Fecha:** 2026-02-01  
**Problema reportado:** No se podían crear skills nuevas porque usaba nombres duplicados  
**Tipo:** Constraint de base de datos + lógica de creación

---

## 🐛 Problema

Al intentar crear una skill con un nombre que ya existe en la organización:

```
SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry 'X-Nombre Skill' for key 'skills.skills_organization_id_name_unique'
```

**Causa raíz:**
1. Migración tiene constraint único: `$table->unique(['organization_id', 'name']);`
2. Endpoint `/api/competencies/{id}/skills` creaba directamente sin verificar si ya existe

---

## 🤔 Análisis: ¿Es Correcto No Permitir Duplicados?

### Opción A: NO permitir duplicados (implementado)

**Ventajas:**
- ✅ Evita redundancia de datos
- ✅ Facilita búsqueda y autocomplete
- ✅ Mantiene catálogo limpio y consistente
- ✅ Previene confusión ("Liderazgo" vs "liderazgo" vs "Liderazgo ")

**Desventajas:**
- ❌ Diferentes competencias podrían necesitar skills con mismo nombre pero contexto diferente
  - Ejemplo: "Análisis" (técnico) vs "Análisis" (financiero)
  - Ejemplo: "Comunicación" (escrita) vs "Comunicación" (verbal)

### Opción B: Permitir duplicados

**Ventajas:**
- ✅ Máxima flexibilidad
- ✅ Cada competencia puede tener su propio set de skills independiente

**Desventajas:**
- ❌ Difícil de mantener (muchas skills casi idénticas)
- ❌ Confusión en reportes y métricas
- ❌ Complicado de buscar y filtrar
- ❌ Inconsistencias (mayúsculas, tildes, espacios)

---

## ✅ Decisión Implementada

**Estrategia: Reutilizar Skills Existentes**

En lugar de fallar al encontrar duplicado, el sistema ahora:
1. Busca si la skill ya existe (mismo nombre + organización)
2. Si existe → la adjunta a la competencia
3. Si no existe → la crea y adjunta

### Beneficios:
- ✅ No falla al usuario con error críptico
- ✅ Mantiene catálogo limpio (sin duplicados)
- ✅ Permite asociar misma skill a múltiples competencias
- ✅ UX mejorada (transparente para el usuario)

---

## 🔧 Solución Implementada

### Antes (líneas 627-638 en api.php):

```php
} else {
    $payload = $request->input('skill', []);
    $name = trim($payload['name'] ?? '');
    if (empty($name))
        throw new \Exception('Skill name is required');
    
    // ❌ Creaba directamente → error si duplicado
    $createdSkill = App\Models\Skill::create([
        'organization_id' => $user->organization_id ?? null,
        'name' => $name,
        'description' => $payload['description'] ?? null,
        'category' => $payload['category'] ?? null,
    ]);
    $skillToAttach = $createdSkill;
}
```

### Después (líneas 627-648 en api.php):

```php
} else {
    $payload = $request->input('skill', []);
    $name = trim($payload['name'] ?? '');
    if (empty($name))
        throw new \Exception('Skill name is required');
    
    // ✅ Buscar skill existente con el mismo nombre en la organización
    $existingSkill = App\Models\Skill::where('organization_id', $user->organization_id ?? null)
        ->where('name', $name)
        ->first();
    
    if ($existingSkill) {
        // ✅ Reutilizar skill existente
        $skillToAttach = $existingSkill;
    } else {
        // ✅ Crear nueva skill solo si no existe
        $createdSkill = App\Models\Skill::create([
            'organization_id' => $user->organization_id ?? null,
            'name' => $name,
            'description' => $payload['description'] ?? null,
            'category' => $payload['category'] ?? null,
        ]);
        $skillToAttach = $createdSkill;
    }
}
```

---

## 🎯 Comportamiento Ahora

### Escenario 1: Skill Nueva
```
Usuario crea skill "TypeScript"
→ No existe en la BD
→ Se crea y adjunta a competencia
✅ Resultado: Skill creada y asociada
```

### Escenario 2: Skill Duplicada
```
Usuario crea skill "TypeScript" (ya existe)
→ Existe en la BD
→ Se reutiliza y adjunta a competencia
✅ Resultado: Skill existente asociada (sin error)
```

### Escenario 3: Múltiples Competencias
```
Competencia "Frontend" tiene skill "JavaScript"
Competencia "Backend" necesita skill "JavaScript"
→ Se reutiliza la misma skill
→ Ambas competencias apuntan a la misma skill
✅ Resultado: Consistencia y no duplicación
```

---

## 📊 Implicaciones

### Modelo de Datos:
- Skills se comportan como **catálogo compartido** de la organización
- Múltiples competencias pueden compartir las mismas skills
- Tabla pivot `competency_skills` maneja las relaciones M:M

### UX:
- Usuario NO ve error al "duplicar" nombre
- Sistema es inteligente y reutiliza
- Futuro: podría mostrar mensaje "Skill existente asociada" vs "Skill creada"

### Consistencia:
- ✅ Mantiene nombres únicos
- ✅ Facilita reportes (no contar duplicados)
- ✅ Facilita búsqueda y autocomplete

---

## 🧪 Casos de Prueba

```typescript
describe('Skills - Nombres Duplicados', () => {
  it('crea skill si no existe', async () => {
    const response = await api.post('/competencies/1/skills', {
      skill: { name: 'Nueva Skill', description: 'Test' }
    });
    expect(response.status).toBe(201);
    expect(response.data.data.name).toBe('Nueva Skill');
  });
  
  it('reutiliza skill si ya existe', async () => {
    // Primera creación
    await api.post('/competencies/1/skills', {
      skill: { name: 'TypeScript' }
    });
    
    // Segunda "creación" con mismo nombre
    const response = await api.post('/competencies/2/skills', {
      skill: { name: 'TypeScript' }
    });
    
    expect(response.status).toBe(201);
    // Debería ser la misma skill (mismo ID)
    expect(await Skill.where('name', 'TypeScript').count()).toBe(1);
  });
  
  it('asocia correctamente a múltiples competencias', async () => {
    await api.post('/competencies/1/skills', { skill: { name: 'SQL' } });
    await api.post('/competencies/2/skills', { skill: { name: 'SQL' } });
    
    const comp1 = await Competency.with('skills').find(1);
    const comp2 = await Competency.with('skills').find(2);
    
    expect(comp1.skills.some(s => s.name === 'SQL')).toBe(true);
    expect(comp2.skills.some(s => s.name === 'SQL')).toBe(true);
  });
});
```

---

## 🔮 Consideraciones Futuras

### Posibles Mejoras:

1. **Feedback al Usuario:**
   ```typescript
   // En el frontend, mostrar mensaje diferente
   if (skillWasCreated) {
     showSuccess('Skill creada y asociada');
   } else {
     showSuccess('Skill existente asociada');
   }
   ```

2. **Búsqueda Fuzzy:**
   ```php
   // Detectar similares antes de crear
   $similar = Skill::where('name', 'LIKE', "%{$name}%")->get();
   if ($similar->count() > 0) {
       // Sugerir al usuario usar existente
   }
   ```

3. **Categorización:**
   ```php
   // Si necesitas "Análisis" técnico vs financiero
   // Usar campo 'category' o crear taxonomía
   unique(['organization_id', 'name', 'category'])
   ```

4. **Skill Hierarchy:**
   ```php
   // Usar parent_skill_id para jerarquías
   "Comunicación" (padre)
     ├─ "Comunicación Escrita"
     └─ "Comunicación Verbal"
   ```

---

## 📝 Archivos Modificados

- **Archivo:** `src/routes/api.php`
- **Líneas:** 627-648
- **Función:** `POST /api/competencies/{id}/skills`
- **Migración:** `2025_12_27_162333_create_skills_table.php` (línea 27: unique constraint)

---

## 🔗 Referencias

- **Modelo:** `app/Models/Skill.php`
- **Migración:** `database/migrations/2025_12_27_162333_create_skills_table.php`
- **Tabla Pivot:** `competency_skills` (relación M:M)
- **Documentación relacionada:**
  - `FIX_SKILLS_CONTEXTO_UI_REFRESH.md` - Fix de contexto y UI
  - `PATRON_REFRESH_UI_DESPUES_CREAR.md` - Patrón de actualización UI

---

## ✅ Conclusión

**Decisión:** Mantener constraint único y reutilizar skills existentes

**Razón:** 
- Balance entre flexibilidad y consistencia
- UX mejorada (no errores crípticos)
- Catálogo limpio y mantenible
- Permite compartir skills entre competencias

**Próximos pasos:**
- ✅ Fix implementado y probado
- ⏳ Agregar tests de integración
- ⏳ Mejorar feedback al usuario (mostrar si es nueva o reutilizada)
- ⏳ Considerar autocomplete con skills existentes en el frontend
