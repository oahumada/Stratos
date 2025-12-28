#!/bin/bash

# Script para crear commits semánticos interactivamente
# Uso: ./scripts/commit.sh

set -e

# Colores
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}═══════════════════════════════════════════════════════════════${NC}"
echo -e "${BLUE}  Asistente de Commits Semánticos - TalentIA${NC}"
echo -e "${BLUE}═══════════════════════════════════════════════════════════════${NC}\n"

# 1. Seleccionar tipo
echo -e "${YELLOW}1. Selecciona el tipo de cambio:${NC}"
echo "   1) feat      - Nueva funcionalidad"
echo "   2) fix       - Corrección de bugs"
echo "   3) docs      - Cambios en documentación"
echo "   4) style     - Cambios de estilo (CSS, formato)"
echo "   5) refactor  - Refactoring de código"
echo "   6) perf      - Mejora de rendimiento"
echo "   7) test      - Agregar o actualizar tests"
echo "   8) chore     - Cambios en build, dependencias"
echo "   9) ci        - Cambios en CI/CD"
echo "   10) revert   - Revertir un commit anterior"

read -p "Elige una opción (1-10): " type_choice

case $type_choice in
    1) COMMIT_TYPE="feat" ;;
    2) COMMIT_TYPE="fix" ;;
    3) COMMIT_TYPE="docs" ;;
    4) COMMIT_TYPE="style" ;;
    5) COMMIT_TYPE="refactor" ;;
    6) COMMIT_TYPE="perf" ;;
    7) COMMIT_TYPE="test" ;;
    8) COMMIT_TYPE="chore" ;;
    9) COMMIT_TYPE="ci" ;;
    10) COMMIT_TYPE="revert" ;;
    *) echo -e "${RED}Opción inválida${NC}"; exit 1 ;;
esac

echo -e "${GREEN}✓ Tipo: ${COMMIT_TYPE}${NC}\n"

# 2. Ingresar scope (opcional)
echo -e "${YELLOW}2. Ingresa el scope (opcional, ej: auth, forms, api):${NC}"
read -p "Scope [presionar Enter para omitir]: " COMMIT_SCOPE

if [ -n "$COMMIT_SCOPE" ]; then
    COMMIT_SCOPE="($COMMIT_SCOPE)"
    echo -e "${GREEN}✓ Scope: ${COMMIT_SCOPE}${NC}\n"
else
    COMMIT_SCOPE=""
    echo -e "${GREEN}✓ Sin scope${NC}\n"
fi

# 3. Ingresar subject
echo -e "${YELLOW}3. Describe el cambio (máximo 100 caracteres, modo imperativo):${NC}"
echo "   Ej: agregar validación de email, corregir filtro de búsqueda"
read -p "Subject: " COMMIT_SUBJECT

if [ -z "$COMMIT_SUBJECT" ]; then
    echo -e "${RED}El subject es requerido${NC}"
    exit 1
fi

# Validar largo
if [ ${#COMMIT_SUBJECT} -gt 100 ]; then
    echo -e "${RED}El subject es muy largo (${#COMMIT_SUBJECT} caracteres, máximo 100)${NC}"
    exit 1
fi

echo -e "${GREEN}✓ Subject: ${COMMIT_SUBJECT}${NC}\n"

# 4. Ingresar body (opcional)
echo -e "${YELLOW}4. Ingresa descripción detallada (opcional, máximo 100 caracteres por línea):${NC}"
echo "   Presiona Ctrl+D cuando termines de escribir"
read -d '' COMMIT_BODY << 'EOF' || true
EOF

if [ -n "$COMMIT_BODY" ]; then
    echo -e "${GREEN}✓ Body agregado${NC}\n"
fi

# 5. Ingresar footer (opcional)
echo -e "${YELLOW}5. Ingresa referencias a issues (opcional, ej: Fixes #123, Closes #456):${NC}"
read -p "Footer [presionar Enter para omitir]: " COMMIT_FOOTER

if [ -n "$COMMIT_FOOTER" ]; then
    echo -e "${GREEN}✓ Footer: ${COMMIT_FOOTER}${NC}\n"
fi

# 6. Mostrar resumen
echo -e "${BLUE}═══════════════════════════════════════════════════════════════${NC}"
echo -e "${YELLOW}RESUMEN DEL COMMIT:${NC}"
echo -e "${BLUE}═══════════════════════════════════════════════════════════════${NC}\n"

COMMIT_HEADER="${COMMIT_TYPE}${COMMIT_SCOPE}: ${COMMIT_SUBJECT}"
echo -e "Header: ${GREEN}${COMMIT_HEADER}${NC}"

if [ -n "$COMMIT_BODY" ]; then
    echo -e "Body: ${GREEN}${COMMIT_BODY}${NC}"
fi

if [ -n "$COMMIT_FOOTER" ]; then
    echo -e "Footer: ${GREEN}${COMMIT_FOOTER}${NC}"
fi

echo -e "\n${BLUE}═══════════════════════════════════════════════════════════════${NC}\n"

# 7. Confirmar
read -p "¿Confirmar commit? (y/n): " confirm

if [ "$confirm" != "y" ] && [ "$confirm" != "Y" ]; then
    echo -e "${YELLOW}Commit cancelado${NC}"
    exit 0
fi

# 8. Hacer el commit
COMMIT_MESSAGE="${COMMIT_HEADER}"

if [ -n "$COMMIT_BODY" ]; then
    COMMIT_MESSAGE="${COMMIT_MESSAGE}\n\n${COMMIT_BODY}"
fi

if [ -n "$COMMIT_FOOTER" ]; then
    COMMIT_MESSAGE="${COMMIT_MESSAGE}\n\n${COMMIT_FOOTER}"
fi

git commit -m "$(echo -e "$COMMIT_MESSAGE")"

echo -e "\n${GREEN}✓ Commit realizado exitosamente${NC}\n"
