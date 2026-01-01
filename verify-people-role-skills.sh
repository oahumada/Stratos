#!/bin/bash

# =============================================================================
# SCRIPT DE VERIFICACIÓN - People Role Skills
# =============================================================================
# Verifica el estado de la implementación de people_role_skills
# Uso: ./verify-people-role-skills.sh
# =============================================================================

echo "═══════════════════════════════════════════════════════════════════════════"
echo "  VERIFICACIÓN PEOPLE_ROLE_SKILLS - $(date '+%Y-%m-%d %H:%M:%S')"
echo "═══════════════════════════════════════════════════════════════════════════"
echo ""

cd "$(dirname "$0")/src" || exit 1

# -----------------------------------------------------------------------------
# 1. VERIFICAR TABLA EN BASE DE DATOS
# -----------------------------------------------------------------------------
echo "📊 1. Verificando estructura de tabla..."
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

php artisan tinker --execute="
    \$exists = \Illuminate\Support\Facades\Schema::hasTable('people_role_skills');
    echo \$exists ? '✓ Tabla people_role_skills existe\n' : '✗ Tabla NO existe\n';
    
    if (\$exists) {
        \$columns = \Illuminate\Support\Facades\Schema::getColumnListing('people_role_skills');
        echo '  Columnas (' . count(\$columns) . '): ' . implode(', ', \$columns) . '\n';
    }
"

echo ""

# -----------------------------------------------------------------------------
# 2. ESTADÍSTICAS DE DATOS
# -----------------------------------------------------------------------------
echo "📈 2. Estadísticas de datos..."
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

php artisan tinker --execute="
    \$total = \App\Models\PeopleRoleSkill::count();
    \$active = \App\Models\PeopleRoleSkill::where('is_active', true)->count();
    \$inactive = \App\Models\PeopleRoleSkill::where('is_active', false)->count();
    \$expired = \App\Models\PeopleRoleSkill::where('expires_at', '<', now())->where('is_active', true)->count();
    \$expiringSoon = \App\Models\PeopleRoleSkill::whereBetween('expires_at', [now(), now()->addDays(30)])->where('is_active', true)->count();
    
    echo '  Total registros:              ' . str_pad(\$total, 5, ' ', STR_PAD_LEFT) . '\n';
    echo '  Skills activas (is_active=1): ' . str_pad(\$active, 5, ' ', STR_PAD_LEFT) . '\n';
    echo '  Skills históricas (=0):       ' . str_pad(\$inactive, 5, ' ', STR_PAD_LEFT) . '\n';
    echo '  Skills expiradas:             ' . str_pad(\$expired, 5, ' ', STR_PAD_LEFT) . ' ⚠️\n';
    echo '  Expiran en 30 días:           ' . str_pad(\$expiringSoon, 5, ' ', STR_PAD_LEFT) . ' ⚠️\n';
"

echo ""

# -----------------------------------------------------------------------------
# 3. VERIFICAR MODELO Y RELACIONES
# -----------------------------------------------------------------------------
echo "🔗 3. Verificando modelo y relaciones..."
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

php artisan tinker --execute="
    \$model = new \App\Models\PeopleRoleSkill();
    echo '✓ Modelo PeopleRoleSkill cargado\n';
    
    // Verificar relaciones
    \$sample = \App\Models\PeopleRoleSkill::with(['person', 'role', 'skill', 'evaluator'])->first();
    if (\$sample) {
        echo '✓ Relación person(): ' . (isset(\$sample->person) ? 'OK' : 'FALTA') . '\n';
        echo '✓ Relación role(): ' . (isset(\$sample->role) ? 'OK' : 'FALTA') . '\n';
        echo '✓ Relación skill(): ' . (isset(\$sample->skill) ? 'OK' : 'FALTA') . '\n';
        echo '✓ Relación evaluator(): ' . (isset(\$sample->evaluator) ? 'OK (null permitido)' : 'OK (null)') . '\n';
    } else {
        echo '⚠️ No hay registros para probar relaciones\n';
    }
"

echo ""

# -----------------------------------------------------------------------------
# 4. VERIFICAR SCOPES
# -----------------------------------------------------------------------------
echo "🔍 4. Verificando scopes del modelo..."
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

php artisan tinker --execute="
    try {
        \$active = \App\Models\PeopleRoleSkill::active()->count();
        echo '✓ Scope active(): ' . \$active . ' registros\n';
    } catch (\Exception \$e) {
        echo '✗ Scope active() ERROR: ' . \$e->getMessage() . '\n';
    }
    
    try {
        \$expired = \App\Models\PeopleRoleSkill::expired()->count();
        echo '✓ Scope expired(): ' . \$expired . ' registros\n';
    } catch (\Exception \$e) {
        echo '✗ Scope expired() ERROR: ' . \$e->getMessage() . '\n';
    }
    
    try {
        \$needsReevaluation = \App\Models\PeopleRoleSkill::needsReevaluation()->count();
        echo '✓ Scope needsReevaluation(): ' . \$needsReevaluation . ' registros\n';
    } catch (\Exception \$e) {
        echo '✗ Scope needsReevaluation() ERROR: ' . \$e->getMessage() . '\n';
    }
"

echo ""

# -----------------------------------------------------------------------------
# 5. VERIFICAR REPOSITORY
# -----------------------------------------------------------------------------
echo "📦 5. Verificando repository..."
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

php artisan tinker --execute="
    \$repo = app(\App\Repository\PeopleRoleSkillRepository::class);
    echo '✓ PeopleRoleSkillRepository instanciado\n';
    
    // Verificar método getActiveSkillsForPerson
    \$firstPerson = \App\Models\People::first();
    if (\$firstPerson) {
        \$activeSkills = \$repo->getActiveSkillsForPerson(\$firstPerson->id);
        echo '✓ getActiveSkillsForPerson(): ' . \$activeSkills->count() . ' skills activas para ' . \$firstPerson->name . '\n';
        
        \$stats = \$repo->getStatsForPerson(\$firstPerson->id);
        echo '✓ getStatsForPerson(): ' . json_encode(\$stats) . '\n';
    } else {
        echo '⚠️ No hay personas para probar repository\n';
    }
"

echo ""

# -----------------------------------------------------------------------------
# 6. GAPS DE SKILLS (CRÍTICO)
# -----------------------------------------------------------------------------
echo "⚠️  6. Gaps de Skills (current_level < required_level)..."
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

php artisan tinker --execute="
    \$gaps = \App\Models\PeopleRoleSkill::whereColumn('current_level', '<', 'required_level')
        ->where('is_active', true)
        ->with(['person', 'skill'])
        ->get();
    
    echo 'Total skills por debajo del nivel requerido: ' . \$gaps->count() . '\n\n';
    
    if (\$gaps->count() > 0) {
        echo 'Primeros 5 gaps:\n';
        \$gaps->take(5)->each(function(\$gap) {
            \$personName = \$gap->person ? \$gap->person->name : 'N/A';
            \$skillName = \$gap->skill ? \$gap->skill->name : 'N/A';
            echo '  • ' . \$personName . ' - ' . \$skillName . ': nivel ' . \$gap->current_level . '/' . \$gap->required_level . ' (gap: ' . (\$gap->required_level - \$gap->current_level) . ')\n';
        });
    }
"

echo ""

# -----------------------------------------------------------------------------
# 7. VERIFICAR ARCHIVOS DE IMPLEMENTACIÓN
# -----------------------------------------------------------------------------
echo "📄 7. Verificando archivos de implementación..."
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

FILES=(
    "database/migrations/2026_01_01_171617_create_people_role_skills_table.php"
    "app/Models/PeopleRoleSkill.php"
    "app/Repository/PeopleRoleSkillRepository.php"
    "database/seeders/PeopleRoleSkillSeeder.php"
)

for file in "${FILES[@]}"; do
    if [ -f "$file" ]; then
        size=$(wc -l < "$file")
        echo "  ✓ $file ($size líneas)"
    else
        echo "  ✗ $file (NO EXISTE)"
    fi
done

echo ""

# -----------------------------------------------------------------------------
# 8. VERIFICAR DOCUMENTACIÓN
# -----------------------------------------------------------------------------
echo "📚 8. Verificando documentación..."
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

DOCS=(
    "../docs/PEOPLE_ROLE_SKILLS_IMPLEMENTACION.md"
    "../docs/PEOPLE_ROLE_SKILLS_FLUJO.md"
)

for doc in "${DOCS[@]}"; do
    if [ -f "$doc" ]; then
        size=$(wc -l < "$doc")
        echo "  ✓ $(basename $doc) ($size líneas)"
    else
        echo "  ✗ $(basename $doc) (NO EXISTE)"
    fi
done

echo ""

# -----------------------------------------------------------------------------
# 9. RESUMEN Y RECOMENDACIONES
# -----------------------------------------------------------------------------
echo "═══════════════════════════════════════════════════════════════════════════"
echo "  RESUMEN"
echo "═══════════════════════════════════════════════════════════════════════════"

php artisan tinker --execute="
    \$total = \App\Models\PeopleRoleSkill::count();
    \$expired = \App\Models\PeopleRoleSkill::where('expires_at', '<', now())->where('is_active', true)->count();
    \$gaps = \App\Models\PeopleRoleSkill::whereColumn('current_level', '<', 'required_level')->where('is_active', true)->count();
    
    echo '\n✓ Sistema people_role_skills operativo\n';
    echo '✓ ' . \$total . ' skills registradas (activas + históricas)\n';
    
    if (\$expired > 0) {
        echo '⚠️ ' . \$expired . ' skills expiradas - ACCIÓN REQUERIDA: Reevaluar\n';
    }
    
    if (\$gaps > 0) {
        echo '⚠️ ' . \$gaps . ' skills por debajo del nivel requerido - Planificar capacitación\n';
    }
    
    echo '\n📋 PRÓXIMOS PASOS:\n';
    echo '  1. Implementar PeopleObserver para auto-sync en cambio de rol\n';
    echo '  2. Crear comando artisan para notificar reevaluaciones\n';
    echo '  3. Actualizar FormSchema para CRUD de people_role_skills\n';
    echo '  4. Crear endpoints API (/api/people/{id}/skills/active, /history, /gaps)\n';
    echo '  5. Implementar frontend (tabs: Actuales vs Historial)\n';
    echo '\n';
"

echo "═══════════════════════════════════════════════════════════════════════════"
echo "  Verificación completada - $(date '+%Y-%m-%d %H:%M:%S')"
echo "═══════════════════════════════════════════════════════════════════════════"
