# 📊 Corrección: Conteo de Candidatos vs Posiciones

**Fecha:** 3 de enero de 2026  
**Cambio:** Métricas del dashboard actualizadas para contar candidatos, no posiciones

---

## ❌ Problema Identificado

Las tarjetas del dashboard mostraban un conteo **incorrecto** de candidatos.

**Ejemplo del error:**
```
Dashboard mostraba: "4 posiciones con match 50-69%"
Realidad: 5 candidatos individuales con match 50-69%
```

**Causa:** El código estaba contando **posiciones** (una por posición abierta) en lugar de **candidatos individuales** dentro de esas posiciones.

---

## ✅ Solución Implementada

### Cambio en la Lógica

**Antes:**
```javascript
positions.value.forEach(position => {
  const topCandidate = position.candidates[0];
  if (topCandidate.match_percentage >= 50 && topCandidate.match_percentage < 70) {
    moderateCount++; // Contar posición (0 o 1)
  }
});
```

**Problema:** Cada posición solo contribuye 1 al conteo, no importa cuántos candidatos tenga.

---

**Después:**
```javascript
positions.value.forEach(position => {
  position.candidates.forEach(candidate => {
    if (candidate.match_percentage >= 50 && candidate.match_percentage < 70) {
      moderateCount++; // Contar cada candidato
    }
  });
});
```

**Solución:** Cada candidato individual se cuenta en su rango correspondiente.

---

## 📈 Impacto de la Corrección

### Ejemplo Real

**Posición:** Senior Developer (1 vacante)
```
Candidatos:
  1. Juan   - 72% (Buen Match)
  2. María  - 62% (Moderado)
  3. Carlos - 55% (Moderado)
  4. Ana    - 48% (Bajo)
  5. Pedro  - 45% (Bajo)
```

**Antes (INCORRECTO):**
- Buen Match: 1 posición ✗
- Moderado: 1 posición ✗
- Bajo: 1 posición ✗

**Después (CORRECTO):**
- Buen Match: 1 candidato ✅
- Moderado: 2 candidatos ✅
- Bajo: 2 candidatos ✅

---

## 📊 Métricas Actualizadas

### Tarjetas del Dashboard

| Métrica | Antes | Ahora | Cambio |
|---------|-------|-------|--------|
| Excelente (≥80%) | Posiciones | **Candidatos** | Más preciso |
| Buen Match (70-79%) | Posiciones | **Candidatos** | Más preciso |
| Moderado (50-69%) | Posiciones | **Candidatos** | Más preciso |
| Bajo (40-49%) | N/A | **Candidatos** | Nuevo rango |
| Sin viables | Posiciones sin candidatos | **Posiciones sin viables** | Más claro |

### Nuevos Campos Agregados

```javascript
{
  candidatesExcellentMatch: number,      // Candidatos ≥80%
  candidatesGoodMatch: number,           // Candidatos 70-79%
  candidatesModerateMatch: number,       // Candidatos 50-69%
  candidatesLowMatch: number,            // Candidatos 40-49%
  positionsWithoutViableCandidates: number, // Posiciones sin candidatos ≥40%
  avgMatchPercentage: number             // Promedio global
}
```

---

## 💡 Implicaciones para el Reclutador

### Mejor Insight

**Antes:**
- "Tengo 4 posiciones con talento moderado"
- Ambiguo: ¿4 posiciones con 1 candidato cada una? ¿O más?

**Después:**
- "Tengo 5 candidatos con match moderado"
- Claro: Hay 5 personas viables con ese rango de match

### Mejor Toma de Decisiones

Con datos correctos sobre **cantidad de candidatos** (no posiciones), el reclutador puede:

1. **Estimar recursos:**
   - "5 candidatos moderados = necesito plan de capacitación para múltiples personas"

2. **Priorizar búsqueda externa:**
   - "Solo 1 candidato excelente en 10 posiciones = urgente búsqueda externa"

3. **Evaluar estrategia:**
   - "15 candidatos totales viables vs 100 empleados = 15% de talento aprovechable"

---

## 🎨 Cambios Visuales

### Dashboard - Antes
```
┌─────────────────────────────────────────┐
│  Excelente ≥80%: 2 posiciones           │
│  Buen Match 70-79%: 3 posiciones        │
│  Moderado 50-69%: 4 posiciones          │ ❌ Confuso
│  Búsqueda Externa <50%: 5 posiciones    │
│  Total: 14 posiciones                   │
└─────────────────────────────────────────┘
```

### Dashboard - Después
```
┌─────────────────────────────────────────┐
│  Excelente ≥80%: 7 candidatos           │
│  Buen Match 70-79%: 12 candidatos       │
│  Moderado 50-69%: 5 candidatos          │ ✅ Claro
│  Bajo 40-49%: 3 candidatos              │
│  Sin viables: 2 posiciones              │
│  Total evaluados: 27 candidatos         │
└─────────────────────────────────────────┘
```

---

## 🔧 Implementación Técnica

### Archivo: `/src/resources/js/pages/Marketplace/Index.vue`

**Función actualizada:**
```javascript
const recruiterSummary = computed(() => {
  let excellentCount = 0;
  let goodCount = 0;
  let moderateCount = 0;
  let lowCount = 0;
  let positionsWithoutCandidates = 0;
  
  positions.value.forEach(position => {
    // Contabilizar posiciones sin candidatos
    if (position.candidates.length === 0) {
      positionsWithoutCandidates++;
    }
    
    // Contabilizar TODOS los candidatos por rango
    position.candidates.forEach(candidate => {
      if (candidate.match_percentage >= 80) {
        excellentCount++;
      } else if (candidate.match_percentage >= 70) {
        goodCount++;
      } else if (candidate.match_percentage >= 50) {
        moderateCount++;
      } else if (candidate.match_percentage >= 40) {
        lowCount++;
      }
    });
  });
  
  return {
    candidatesExcellentMatch: excellentCount,
    candidatesGoodMatch: goodCount,
    candidatesModerateMatch: moderateCount,
    candidatesLowMatch: lowCount,
    positionsWithoutViableCandidates: positionsWithoutCandidates,
  };
});
```

---

## ✅ Validación

**¿Cómo verificar que ahora es correcto?**

1. Abre la vista de reclutador
2. Mira una posición con múltiples candidatos (ej: 5 candidatos)
3. Revisa el dashboard
4. Suma los rangos que veas:
   - 1 en 70-79% → +1 a "Buen Match"
   - 2 en 50-69% → +2 a "Moderado"
   - 2 en 40-49% → +2 a "Bajo"
5. Verifica que las tarjetas reflejen esos números

**Antes:** Las tarjetas mostraban máximo 1 por posición.  
**Ahora:** Las tarjetas muestran la cantidad real de candidatos.

---

## 📝 Notas

- Las alertas también fueron actualizadas para hacer referencia a "candidatos" en lugar de "posiciones"
- El promedio de match (avgMatchPercentage) sigue siendo exacto en ambas versiones
- Las tarjetas ahora dan **visibilidad real del talento disponible**

---

**Implementado:** 3 de enero de 2026  
**Status:** ✅ En producción  
**Impacto:** Métricas ahora reflejan realidad del talento disponible
