#!/bin/bash

echo "=== Testing Skill Level Definitions System ==="
echo ""

echo "1. Verificando migración..."
php src/artisan migrate:status | grep skill_level_definitions
echo ""

echo "2. Contando niveles en DB..."
cd src && php artisan tinker --execute="echo 'Total skill levels: ' . \App\Models\SkillLevelDefinition::count();"
echo ""

echo "3. Probando endpoint de catálogos..."
echo "GET /api/catalogs?catalogs[]=skill_levels"
echo ""

echo "4. Verificando modelo SkillLevelDefinition..."
cd .. && cd src && php artisan tinker --execute="\$level = \App\Models\SkillLevelDefinition::find(3); echo 'Level 3: ' . \$level->display_label . PHP_EOL; echo 'Description: ' . \$level->description . PHP_EOL;"
echo ""

echo "5. Verificando estructura de Skills con niveles..."
cd .. && cd src && php artisan tinker --execute="\$skill = \App\Models\Skills::with(['roles' => function(\$q) { \$q->wherePivot('required_level', '>=', 1); }])->first(); if(\$skill && \$skill->roles->count() > 0) { echo 'Skill: ' . \$skill->name . PHP_EOL; echo 'Primer rol requiere nivel: ' . \$skill->roles->first()->pivot->required_level . PHP_EOL; } else { echo 'No hay skills con roles que requieran niveles' . PHP_EOL; }"
echo ""

echo "=== Test Completado ==="
