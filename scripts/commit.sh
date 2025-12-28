#!/bin/bash

# Script para crear commits semánticos interactivamente
# Analiza git diff y sugiere tipo de commit automáticamente
# Uso: ./scripts/commit.sh

set -e

# Colores
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

# Función para detectar cambios
detect_changes() {
    # Verificar si hay cambios preparados
    if ! git diff --cached --quiet; then
        return 0  # Hay cambios
    else
        return 1  # No hay cambios
    fi
}

# Función para obtener archivos modificados
get_modified_files() {
    git diff --cached --name-only
}

# Función para mostrar preview de cambios
show_diff_preview() {
    echo -e "\n${CYAN}📝 CAMBIOS PREPARADOS (git diff):${NC}\n"
    git diff --cached --stat
    echo ""
    
    # Mostrar primeros cambios
    local lines=$(git diff --cached | wc -l)
    if [ $lines -lt 100 ]; then
        git diff --cached
    else
        echo -e "${YELLOW}Mostrando primeros 50 cambios (hay $lines líneas totales):${NC}"
        git diff --cached | head -50
        echo -e "\n${YELLOW}... (más cambios)${NC}"
    fi
}

# Función para sugerir tipo de commit
suggest_commit_type() {
    local files=$(get_modified_files)
    local has_test=false
    local has_config=false
    local has_docs=false
    
    # Analizar archivos
    while IFS= read -r file; do
        if [[ $file == *test* ]] || [[ $file == *spec* ]]; then
            has_test=true
        fi
        if [[ $file == *config* ]] || [[ $file == *package.json* ]] || [[ $file == *.yml ]] || [[ $file == *.yaml* ]]; then
            has_config=true
        fi
        if [[ $file == *README* ]] || [[ $file == *docs* ]] || [[ $file == *.md ]]; then
            has_docs=true
        fi
    done <<< "$files"
    
    # Sugerir tipo basado en archivos
    if [ "$has_test" = true ]; then
        echo "test"
    elif [ "$has_config" = true ] && [ "$has_docs" = false ] && [ "$has_test" = false ]; then
        echo "chore"
    elif [ "$has_docs" = true ]; then
        echo "docs"
    else
        echo "feat"  # Default
    fi
}

echo -e "${BLUE}═══════════════════════════════════════════════════════════════${NC}"
echo -e "${BLUE}  Asistente de Commits Semánticos - TalentIA${NC}"
echo -e "${BLUE}═══════════════════════════════════════════════════════════════${NC}\n"

# Verificar si hay cambios preparados
if ! detect_changes; then
    echo -e "${RED}❌ No hay cambios preparados para hacer commit${NC}"
    echo -e "${YELLOW}Usa primero: git add <archivos>${NC}\n"
    exit 1
fi

# Mostrar cambios
show_diff_preview

# Sugerir tipo
SUGGESTED_TYPE=$(suggest_commit_type)
echo -e "${CYAN}💡 Tipo sugerido basado en cambios: ${GREEN}${SUGGESTED_TYPE}${NC}\n"

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
echo "   (presionar Enter para usar sugerencia: ${GREEN}${SUGGESTED_TYPE}${NC})"

read -p "Elige una opción (1-10) o Enter para sugerencia: " type_choice

# Si está vacío, usar sugerencia
if [ -z "$type_choice" ]; then
    case $SUGGESTED_TYPE in
        feat) type_choice=1 ;;
        fix) type_choice=2 ;;
        docs) type_choice=3 ;;
        style) type_choice=4 ;;
        refactor) type_choice=5 ;;
        perf) type_choice=6 ;;
        test) type_choice=7 ;;
        chore) type_choice=8 ;;
        ci) type_choice=9 ;;
        revert) type_choice=10 ;;
    esac
    echo -e "${GREEN}✓ Usando sugerencia: ${SUGGESTED_TYPE}${NC}\n"
fi

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
echo -e "${YELLOW}2. Ingresa el scope (opcional, ej: auth, forms, api, models):${NC}"

# Sugerir scopes basado en archivos
SUGGESTED_SCOPES=""
if echo "$files" | grep -q "form"; then
    SUGGESTED_SCOPES="$SUGGESTED_SCOPES forms"
fi
if echo "$files" | grep -q "auth"; then
    SUGGESTED_SCOPES="$SUGGESTED_SCOPES auth"
fi
if echo "$files" | grep -q "api"; then
    SUGGESTED_SCOPES="$SUGGESTED_SCOPES api"
fi
if echo "$files" | grep -q "model"; then
    SUGGESTED_SCOPES="$SUGGESTED_SCOPES models"
fi

if [ -n "$SUGGESTED_SCOPES" ]; then
    echo -e "   ${CYAN}Scopes sugeridos: ${GREEN}$SUGGESTED_SCOPES${NC}"
fi

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

# Sugerir basado en archivos
MODIFIED_COUNT=$(echo "$files" | wc -l)
if [ $MODIFIED_COUNT -eq 1 ]; then
    FILENAME=$(echo "$files" | xargs basename)
    echo -e "   ${CYAN}Archivo modificado: ${GREEN}${FILENAME}${NC}"
fi
echo -e "   ${CYAN}Archivos afectados: ${GREEN}${MODIFIED_COUNT}${NC}\n"

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
