# 🚫 Reglas de Exclusión en el Marketplace

## 📋 Reglas de Negocio

El sistema aplica **dos reglas de exclusión automática** para mantener el marketplace enfocado en oportunidades viables:

### 1. Exclusión por Mismo Rol

**Las personas que actualmente ocupan el mismo rol que una vacante abierta NO son consideradas candidatas para esa posición.**

### 2. Exclusión por Match Muy Bajo (<40%)

**Las personas con menos del 40% de match NO aparecen como candidatos, independientemente de su rol.**

---

## ❓ ¿Por Qué Estas Reglas?

### Regla 1: Mismo Rol

1. **No tiene sentido lateral al mismo puesto**
   - Si eres "Senior Backend Developer", no aplicas a otra vacante de "Senior Backend Developer"
   - Sería simplemente cambiarte de equipo con el mismo rol (no es promoción ni desarrollo)

2. **Evita ruido en el marketplace**
   - Reduce candidatos irrelevantes
   - Enfoca en verdaderas oportunidades de crecimiento/cambio

3. **Casos válidos siguen funcionando**
   - Promociones (Junior → Senior) ✅
   - Cambios de especialización (Backend → Frontend) ✅
   - Movilidad a diferentes áreas (Sales → Marketing con mismo nivel) ✅

### Regla 2: Match Muy Bajo

1. **Viabilidad práctica**
   - Match <40% indica brechas de habilidades extremadamente grandes
   - Tiempo de desarrollo interno sería prohibitivo (típicamente >6 meses)
   - Costo/beneficio desfavorable comparado con reclutamiento externo

2. **Enfoque del marketplace**
   - Mostrar solo oportunidades realistas
   - Evitar falsas expectativas para empleados
   - Mantener utilidad del sistema para reclutadores

3. **Optimización de recursos**
   - Reclutadores no pierden tiempo evaluando candidatos no viables
   - Sistema recomienda búsqueda externa automáticamente
   - Métricas más precisas y accionables

---

## 🎯 Casos de Uso

### ✅ Caso Incluido: Match Suficiente con Rol Diferente

```
Empleado:
  - Nombre: Ana Torres
  - Rol Actual: Junior Data Analyst (ID: 30)
  - Skills: SQL, Excel, Python básico

Vacante:
  - Título: Data Scientist
  - Rol: Data Scientist (ID: 35)
  - Skills requeridas: Python avanzado, ML, estadística

Match: 52% ⚠️

Resultado: ✅ Ana aparece como candidata
Razón: 
  - Rol diferente (30 ≠ 35) ✅
  - Match ≥40% (52% > 40%) ✅
Categoría: Match Moderado - Estrategia dual
```

---

### ❌ Caso Excluido: Match Muy Bajo

```
Empleado:
  - Nombre: Carlos Ruiz
  - Rol Actual: Frontend Developer (ID: 26)
  - Skills: React, CSS, JavaScript

Vacante:
  - Título: DevOps Engineer
  - Rol: DevOps Engineer (ID: 40)
  - Skills: Docker, K8s, CI/CD, AWS, Linux

Match: 18% 🔴

Resultado: ❌ Carlos NO aparece como candidato
Razón: Match <40% (18% < 40%) ❌
Justificación: Brechas demasiado grandes, no viable
```

---

### ❌ Caso Excluido: Mismo Rol Exacto

```
Empleado:
  - Nombre: Juan Pérez
  - Rol Actual: Senior Backend Developer (ID: 25)
  - Departamento: Engineering Team A

Vacante Abierta:
  - Título: Senior Backend Developer
  - Rol: Senior Backend Developer (ID: 25)
  - Departamento: Engineering Team B

Resultado: ❌ Juan NO aparece como candidato
Razón: Mismo rol (ID: 25)
```

**Nota:** Si Juan quiere cambiar de Team A a Team B con el mismo rol, eso se maneja por otro proceso (transferencia interna, no vacante competitiva).

---

### ✅ Caso Incluido: Promoción

```
Empleado:
  - Nombre: María López
  - Rol Actual: Junior Backend Developer (ID: 23)
  - Departamento: Engineering

Vacante Abierta:
  - Título: Senior Backend Developer
  - Rol: Senior Backend Developer (ID: 25)
  - Departamento: Engineering

Resultado: ✅ María SÍ aparece como candidata
Razón: Roles diferentes (23 ≠ 25) - Es una promoción
Match: Dependiendo de sus skills, podría tener 60-80%
```

---

### ✅ Caso Incluido: Cambio de Especialización

```
Empleado:
  - Nombre: Carlos Ruiz
  - Rol Actual: Senior Frontend Developer (ID: 26)
  - Departamento: Engineering

Vacante Abierta:
  - Título: Senior Backend Developer
  - Rol: Senior Backend Developer (ID: 25)
  - Departamento: Engineering

Resultado: ✅ Carlos SÍ aparece como candidato
Razón: Roles diferentes (26 ≠ 25) - Cambio de especialización
Match: Dependiendo de skills de backend, podría tener 40-70%
```

---

### ✅ Caso Incluido: Movilidad Lateral a Otra Área

```
Empleado:
  - Nombre: Ana Torres
  - Rol Actual: Product Manager (ID: 42)
  - Departamento: Product Team A

Vacante Abierta:
  - Título: Product Manager - New Markets
  - Rol: Product Manager (ID: 42)
  - Departamento: Product Team B

Resultado: ❌ Ana NO aparece como candidata
Razón: Mismo rol (ID: 42)

Nota: Si realmente son roles diferentes (ej. "Product Manager" vs 
"Senior Product Manager"), tendrían IDs diferentes y Ana SÍ sería candidata.
```

**Importante:** Si tu sistema tiene roles muy granulares (ej. "Product Manager Team A" vs "Product Manager Team B" son roles separados), entonces SÍ serían candidatos. La exclusión es por **role_id**, no por título.

---

## 🔧 Implementación Técnica

### Backend Filter (Laravel)

**Archivo:** `MarketplaceController.php`

**Constante de Configuración:**

```php
class MarketplaceController extends Controller
{
    /**
     * Umbral mínimo de match para considerar a alguien como candidato viable
     * Candidatos con match < MINIMUM_MATCH_THRESHOLD no aparecen en el marketplace
     * 
     * TODO: Mover esto a configuración de organización cuando se implemente settings
     */
    private const MINIMUM_MATCH_THRESHOLD = 40;
}
```

#### Vista de Reclutador

```php
public function recruiterView(): JsonResponse
{
    $openings = JobOpening::where('organization_id', $user->organization_id)
        ->where('status', 'open')
        ->with('role')
        ->get();

    $positionsWithCandidates = $openings->map(function ($opening) use ($user, $gapService) {
        // 🚫 EXCLUSIÓN 1: Personas con el mismo role_id que la vacante
        $people = People::where('organization_id', $user->organization_id)
            ->where('deleted_at', null)
            ->where('role_id', '!=', $opening->role_id) // ← Filtro por rol
            ->get();
        
        // Calcular match para cada persona
        $candidates = $people->map(function ($person) use ($opening, $gapService) {
            $analysis = $gapService->calculate($person, $opening->role);
            
            // 🚫 EXCLUSIÓN 2: Match muy bajo
            if ($analysis['match_percentage'] < self::MINIMUM_MATCH_THRESHOLD) {
                return null; // Se filtrará después
            }
            
            // ... construir candidato
        })
        ->filter() // Eliminar nulls (match <40%)
        ->sortByDesc('match_percentage')
        ->values();
    });
}
```

#### Vista de Empleado

```php
public function opportunities(int $peopleId): JsonResponse
{
    $people = People::find($peopleId);
    
    // 🚫 EXCLUSIÓN 1: Vacantes con el mismo role_id que el empleado
    $openings = JobOpening::where('organization_id', $people->organization_id)
        ->where('status', 'open')
        ->where('role_id', '!=', $people->role_id) // ← Filtro por rol
        ->with('role')
        ->get();
    
    $opportunities = $openings->map(function ($opening) use ($people, $gapService) {
        $analysis = $gapService->calculate($people, $opening->role);
        
        // 🚫 EXCLUSIÓN 2: Match muy bajo
        if ($analysis['match_percentage'] < self::MINIMUM_MATCH_THRESHOLD) {
            return null; // Se filtrará después
        }
        
        // ... construir oportunidad
    })
    ->filter() // Eliminar nulls
    ->sortByDesc('match_percentage')
    ->values();
}
```

---

## 🧪 Ejemplos de Testing

### Test Case 1: Mismo Rol - Debe Excluir

```php
// Arrange
$role = Role::create(['name' => 'Senior Developer', 'organization_id' => 1]);
$person = People::create(['role_id' => $role->id, 'organization_id' => 1]);
$opening = JobOpening::create(['role_id' => $role->id, 'organization_id' => 1]);

// Act
$response = $this->get("/api/marketplace/recruiter");

// Assert
$candidates = $response->json('data.positions.0.candidates');
$this->assertEmpty($candidates); // ❌ No debe aparecer como candidato
```

### Test Case 2: Rol Diferente + Match Suficiente - Debe Incluir

```php
// Arrange
$roleDeveloper = Role::create(['name' => 'Developer', 'organization_id' => 1]);
$roleSenior = Role::create(['name' => 'Senior Developer', 'organization_id' => 1]);
$person = People::create(['role_id' => $roleDeveloper->id, 'organization_id' => 1]);
// Mock: person tiene 60% match con roleSenior
$opening = JobOpening::create(['role_id' => $roleSenior->id, 'organization_id' => 1]);

// Act
$response = $this->get("/api/marketplace/recruiter");

// Assert
$candidates = $response->json('data.positions.0.candidates');
$this->assertNotEmpty($candidates); // ✅ Debe aparecer como candidato
$this->assertGreaterThanOrEqual(40, $candidates[0]['match_percentage']);
```

### Test Case 3: Match Muy Bajo - Debe Excluir

```php
// Arrange
$roleA = Role::create(['name' => 'Frontend Developer', 'organization_id' => 1]);
$roleB = Role::create(['name' => 'DevOps Engineer', 'organization_id' => 1]);
$person = People::create(['role_id' => $roleA->id, 'organization_id' => 1]);
// Mock: person tiene 25% match con roleB
$opening = JobOpening::create(['role_id' => $roleB->id, 'organization_id' => 1]);

// Act
$response = $this->get("/api/marketplace/recruiter");

// Assert
$candidates = $response->json('data.positions.0.candidates');
$this->assertEmpty($candidates); // ❌ No debe aparecer (match <40%)
```

---

## 📊 Impacto en Métricas

### Antes de las Exclusiones

```
Posición: Senior Backend Developer
Total de empleados en organización: 100

Evaluados: 100
  - 5 personas que YA son Senior Backend Developers (mismo rol)
  - 30 personas con match <40% (no viables)
  - 65 personas con match ≥40% y rol diferente

Candidatos mostrados: 100
```

**Problemas:** 
- 35 candidatos irrelevantes contaminan el análisis
- Reclutador pierde tiempo revisando opciones no viables
- Métricas infladas e inexactas

### Después de las Exclusiones

```
Posición: Senior Backend Developer
Total de empleados en organización: 100

Evaluados: 95 (excluidos 5 con mismo rol)
  - Filtrados: 30 con match <40%
  - Candidatos viables: 65

Candidatos mostrados: 65
```

**Beneficios:**
- Solo candidatos verdaderamente relevantes
- Métricas precisas y accionables
- Mejor experiencia para reclutador y candidatos

---

## 🤔 Preguntas Frecuentes

### Q: ¿Por qué 40% y no otro número?

**A:** El 40% es un **balance entre inclusión y viabilidad**:

- **<40%**: Típicamente requiere >6 meses de desarrollo
- **40-50%**: ~3-6 meses, viable con plan de capacitación
- **≥50%**: <3 meses, candidatos más realistas

**Configurable:** En el futuro, este umbral será configurable por organización en settings.

### Q: ¿Qué pasa si NINGÚN candidato tiene ≥40%?

**A:** El sistema muestra:
```
🚨 Sin candidatos viables
No hay candidatos internos con ≥40% de match
Acción: Iniciar búsqueda externa inmediata
```

Esto es **correcto**: indica claramente que no hay talento interno viable.

### Q: ¿Puedo ver candidatos con <40% en algún lugar?

**A:** No por diseño. Razones:

1. **No son viables** para la vacante
2. **Experiencia del empleado**: No crear falsas expectativas
3. **Eficiencia del reclutador**: Enfocarse solo en opciones reales

Si necesitas ver análisis completo, usa el módulo de Gap Analysis directamente.

### Q: ¿Qué pasa si alguien quiere cambiar de equipo con el mismo rol?

**A:** Eso no se maneja como una "vacante competitiva" en el marketplace. Debería ser:
- Proceso de transferencia interna directa
- Conversación manager-to-manager
- No requiere análisis de gap de skills (ya tiene el rol)

### Q: ¿La exclusión por rol aplica a roles similares?

**A:** Solo aplica a **mismo role_id**. Ejemplos:

```
✅ CANDIDATO (roles diferentes):
  - Backend Developer Junior (ID: 23) → Backend Developer Senior (ID: 25)
  - Frontend Developer (ID: 26) → Backend Developer (ID: 25)

❌ NO CANDIDATO (mismo rol):
  - Backend Developer (ID: 25) → Backend Developer (ID: 25)
```

---

## ✅ Beneficios de Estas Reglas

### 1. Limpieza del Marketplace
- Solo oportunidades de verdadero crecimiento/cambio
- Solo candidatos con viabilidad realista
- Sin ruido innecesario

### 2. Mejor Experiencia de Usuario
- **Empleados**: Ven solo vacantes relevantes y alcanzables
- **Reclutadores**: Ven solo candidatos que vale la pena evaluar
- **Managers**: Recomendaciones más acertadas

### 3. Análisis de Match Más Preciso
- Métricas no infladas por candidatos irrelevantes
- Recomendaciones de búsqueda externa más acertadas
- Dashboard refleja realidad del talento disponible

### 4. Optimización de Recursos
- Menos tiempo perdido en evaluaciones no viables
- Enfoque en desarrollar candidatos con potencial real
- Búsqueda externa se activa cuando realmente necesaria

### 5. Lógica de Negocio Clara
- **Vacantes** = promoción, cambio significativo, o movilidad viable
- **Transferencias laterales** = proceso separado
- **Desarrollo extremo** (<40% match) = no es rol del marketplace

---

## 🚀 Configuración Futura

### Umbral Personalizable por Organización

**Próxima implementación:**

```php
// En Organization model o settings
'minimum_match_threshold' => 40, // Default

// Casos de uso:
// - Startup en crecimiento: 30% (más tolerante)
// - Empresa grande con urgencias: 50% (más exigente)
// - Organización con pool extenso: 40% (balanceado)
```

**Interfaz de configuración:**
```
Settings > Marketplace > Umbral Mínimo de Match
  
  [====|====] 40%
  
  Más inclusivo (30%)  ←→  Más exigente (60%)
  
  Descripción: Candidatos con match inferior a este umbral
  no aparecerán en el marketplace.
```

---

## 📁 Archivos Afectados

- ✅ `/src/app/Http/Controllers/Api/MarketplaceController.php` - Ambos filtros implementados
- ✅ `/src/resources/js/pages/Marketplace/Index.vue` - UI actualizada con nota de umbral
- ✅ `/docs/ESTRATEGIA_MATCHING_CANDIDATOS.md` - Documentado
- ✅ `/docs/MATCHING_CANDIDATOS_RESUMEN.md` - Documentado
- ✅ `/docs/EXCLUSION_MISMO_ROL.md` - Este archivo (renombrado a REGLAS_EXCLUSION.md)

---

**Implementado:** 3 de enero de 2026  
**Status:** ✅ Activo en producción  
**Umbral actual:** 40% (configurable en el futuro)
