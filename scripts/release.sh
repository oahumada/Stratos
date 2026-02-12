#!/bin/bash

# Script para crear releases con versionado semántico y changelog automático
# Uso: ./scripts/release.sh [major|minor|patch]

set -e

# Colores
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
MAGENTA='\033[0;35m'
NC='\033[0m' # No Color

echo -e "${BLUE}═══════════════════════════════════════════════════════════════${NC}"
echo -e "${BLUE}  🚀 Asistente de Releases - Strato${NC}"
echo -e "${BLUE}═══════════════════════════════════════════════════════════════${NC}\n"

# Verificar que no hay cambios sin commitear
if ! git diff-index --quiet HEAD --; then
    echo -e "${RED}❌ Error: Hay cambios sin commitear${NC}"
    echo -e "${YELLOW}Por favor, haz commit de todos los cambios antes de hacer release${NC}\n"
    exit 1
fi

# Verificar rama
CURRENT_BRANCH=$(git rev-parse --abbrev-ref HEAD)
if [ "$CURRENT_BRANCH" != "MVP" ] && [ "$CURRENT_BRANCH" != "main" ]; then
    echo -e "${YELLOW}⚠️  Estás en rama: ${CURRENT_BRANCH}${NC}"
    read -p "¿Continuar? (y/n): " confirm
    if [ "$confirm" != "y" ]; then
        exit 0
    fi
fi

# Obtener versión actual
CURRENT_VERSION=$(cat package.json | grep '"version"' | head -1 | sed 's/.*"version": "\(.*\)".*/\1/')
echo -e "${CYAN}📌 Versión actual: ${GREEN}${CURRENT_VERSION}${NC}\n"

# Determinar nuevo tipo de versión
if [ -z "$1" ]; then
    echo -e "${YELLOW}1. ¿Qué tipo de release?${NC}"
    echo "   1) patch  - Fixes (1.0.0 → 1.0.1)"
    echo "   2) minor  - Nuevas features (1.0.0 → 1.1.0)"
    echo "   3) major  - Breaking changes (1.0.0 → 2.0.0)"
    echo "   4) auto   - Detectar automáticamente"
    read -p "Elige (1-4): " type_choice
    
    case $type_choice in
        1) RELEASE_TYPE="patch" ;;
        2) RELEASE_TYPE="minor" ;;
        3) RELEASE_TYPE="major" ;;
        4) RELEASE_TYPE="auto" ;;
        *) echo -e "${RED}Opción inválida${NC}"; exit 1 ;;
    esac
else
    RELEASE_TYPE=$1
fi

echo -e "${GREEN}✓ Tipo de release: ${RELEASE_TYPE}${NC}\n"

# Generar cambios y calcular nueva versión
echo -e "${CYAN}📊 Analizando commits...${NC}\n"

if [ "$RELEASE_TYPE" = "auto" ]; then
    # Detectar automáticamente
    npx standard-version --dry-run 2>/dev/null | grep "bumping version" || true
    echo -e "\nEjecutando: npx standard-version\n"
    npx standard-version
else
    npx standard-version --release-as "$RELEASE_TYPE"
fi

# Obtener nueva versión
NEW_VERSION=$(cat package.json | grep '"version"' | head -1 | sed 's/.*"version": "\(.*\)".*/\1/')
TAG="v${NEW_VERSION}"

echo -e "\n${BLUE}═══════════════════════════════════════════════════════════════${NC}"
echo -e "${MAGENTA}✨ RESUMEN DEL RELEASE:${NC}"
echo -e "${BLUE}═══════════════════════════════════════════════════════════════${NC}\n"

echo -e "Versión anterior:    ${RED}${CURRENT_VERSION}${NC}"
echo -e "Nueva versión:       ${GREEN}${NEW_VERSION}${NC}"
echo -e "Tag:                 ${CYAN}${TAG}${NC}"
echo -e "Changelog:           ${CYAN}CHANGELOG.md${NC}\n"

# Mostrar cambios del changelog
if [ -f CHANGELOG.md ]; then
    echo -e "${YELLOW}📝 Últimos cambios en CHANGELOG.md:${NC}\n"
    head -30 CHANGELOG.md
    echo -e "\n${CYAN}... (ver CHANGELOG.md para más detalles)${NC}\n"
fi

echo -e "${BLUE}═══════════════════════════════════════════════════════════════${NC}\n"

# Confirmar push
read -p "¿Hacer push de los cambios y tags? (y/n): " push_confirm

if [ "$push_confirm" = "y" ] || [ "$push_confirm" = "Y" ]; then
    echo -e "${CYAN}📤 Haciendo push...${NC}\n"
    
    git push --follow-tags origin "$CURRENT_BRANCH"
    
    echo -e "\n${GREEN}✅ Release completado exitosamente!${NC}\n"
    echo -e "${CYAN}Git tag creado: ${TAG}${NC}"
    echo -e "${CYAN}Rama: ${CURRENT_BRANCH}${NC}"
    echo -e "${CYAN}CHANGELOG actualizado: CHANGELOG.md${NC}\n"
    
    # Mostrar link de GitHub
    echo -e "${YELLOW}📎 Enlace del release:${NC}"
    echo -e "${CYAN}https://github.com/oahumada/Strato/releases/tag/${TAG}${NC}\n"
else
    echo -e "${YELLOW}⚠️  Push cancelado${NC}"
    echo -e "${CYAN}Los cambios están locales. Haz push manualmente:${NC}"
    echo -e "   git push --follow-tags\n"
fi

echo -e "${GREEN}🎉 ¡Release listo!${NC}\n"
