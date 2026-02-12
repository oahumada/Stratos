#!/bin/bash

# Colores
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

clear

echo -e "${CYAN}"
echo "╔════════════════════════════════════════════════════════════╗"
echo "║          Strato - DATABASE VISUALIZATION TOOL            ║"
echo "║            Diagrama Entidad-Relación (ER)                  ║"
echo "╚════════════════════════════════════════════════════════════╝"
echo -e "${NC}"

echo ""
echo -e "${YELLOW}📊 Documentación Disponible:${NC}"
echo ""
echo "  1. ${GREEN}Ver Diagrama HTML Interactivo${NC}"
echo "     → Archivo: docs/DATABASE_ER_DIAGRAM.html"
echo "     → Abre en navegador"
echo "     → Diagrama Mermaid + Estadísticas"
echo ""
echo "  2. ${GREEN}Ver Documentación Markdown${NC}"
echo "     → Archivo: docs/DATABASE_ER_DIAGRAM.md"
echo "     → 10KB de documentación detallada"
echo "     → Diagrama ASCII + Ejemplos SQL"
echo ""
echo "  3. ${GREEN}Ver Guía de Visualización${NC}"
echo "     → Archivo: docs/DATABASE_VISUALIZATION_GUIDE.md"
echo "     → 8 métodos diferentes"
echo "     → CLI, JSON, PlantUML, Mermaid"
echo ""
echo "  4. ${GREEN}Ver Estado Actual${NC}"
echo "     → Archivo: docs/STATUS_CURRENT_STATE.md"
echo "     → Checklist de completitud"
echo "     → Roadmap de siguientes pasos"
echo ""
echo -e "${YELLOW}🔧 Comandos CLI:${NC}"
echo ""
echo "  Ver tablas:"
echo "  ${BLUE}sqlite3 src/database/database.sqlite \".tables\"${NC}"
echo ""
echo "  Ver estructura de role_skills:"
echo "  ${BLUE}sqlite3 src/database/database.sqlite \"PRAGMA table_info(role_skills);\"${NC}"
echo ""
echo "  Ver datos (roles con 6 skills cada uno):"
echo "  ${BLUE}sqlite3 src/database/database.sqlite << 'QUERY'${NC}"
echo "  ${BLUE}.mode column${NC}"
echo "  ${BLUE}.headers on${NC}"
echo "  ${BLUE}SELECT r.name, COUNT(*) as skills FROM role_skills rs${NC}"
echo "  ${BLUE}LEFT JOIN roles r ON rs.role_id = r.id${NC}"
echo "  ${BLUE}GROUP BY rs.role_id;${NC}"
echo "  ${BLUE}QUERY${NC}"
echo ""
echo -e "${YELLOW}📁 Ubicación Base de Datos:${NC}"
echo "  ${CYAN}/home/omar/Strato/src/database/database.sqlite${NC}"
echo ""
echo -e "${YELLOW}✅ Verificación Rápida:${NC}"

# Verify database
if [ -f "src/database/database.sqlite" ]; then
    echo "  ✅ Base de datos encontrada"
    
    # Count records
    ROLES=$(sqlite3 src/database/database.sqlite "SELECT COUNT(*) FROM roles;")
    SKILLS=$(sqlite3 src/database/database.sqlite "SELECT COUNT(*) FROM skills;")
    ROLE_SKILLS=$(sqlite3 src/database/database.sqlite "SELECT COUNT(*) FROM role_skills;")
    PEOPLE=$(sqlite3 src/database/database.sqlite "SELECT COUNT(*) FROM people;")
    
    echo "  ✅ Roles: $ROLES"
    echo "  ✅ Skills: $SKILLS"
    echo "  ✅ Role-Skill Relations: $ROLE_SKILLS"
    echo "  ✅ Personas: $PEOPLE"
else
    echo "  ❌ Base de datos NO encontrada"
fi

echo ""
echo -e "${CYAN}═════════════════════════════════════════════════════════════${NC}"
echo ""
echo -e "${GREEN}Próximos pasos:${NC}"
echo "  1. Abre: docs/DATABASE_ER_DIAGRAM.html en un navegador"
echo "  2. O ejecuta: sqlite3 src/database/database.sqlite"
echo "  3. Luego continúa con los endpoints API (Fase 2)"
echo ""
echo -e "${CYAN}═════════════════════════════════════════════════════════════${NC}"
