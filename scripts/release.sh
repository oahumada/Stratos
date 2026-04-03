#!/bin/bash

# Script para crear releases con versionado semántico y changelog automático
# Uso: ./scripts/release.sh [major|minor|patch|alpha|beta|rc|auto] [--allow-empty] [--no-sync] [--yes]

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
echo -e "${BLUE}  🚀 Asistente de Releases - Stratos${NC}"
echo -e "${BLUE}═══════════════════════════════════════════════════════════════${NC}\n"

ALLOW_EMPTY_RELEASE="false"
AUTO_SYNC="true"
NON_INTERACTIVE="false"
AUTO_PUSH="false"

for arg in "$@"; do
    if [ "$arg" = "--allow-empty" ]; then
        ALLOW_EMPTY_RELEASE="true"
    fi

    if [ "$arg" = "--no-sync" ]; then
        AUTO_SYNC="false"
    fi

    if [ "$arg" = "--yes" ]; then
        NON_INTERACTIVE="true"
        AUTO_PUSH="true"
    fi
done

# Sincronizar referencias remotas/tags para evitar releases sobre estado desactualizado
git fetch origin --tags --quiet

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
    if [ "$NON_INTERACTIVE" = "true" ]; then
        echo -e "${RED}❌ Modo no interactivo bloqueado fuera de main/MVP${NC}\n"
        exit 1
    else
        read -p "¿Continuar? (y/n): " confirm
        if [ "$confirm" != "y" ]; then
            exit 0
        fi
    fi
fi

if [ "$AUTO_SYNC" = "true" ]; then
    echo -e "${CYAN}🔄 Sincronizando con remoto (git pull --rebase origin ${CURRENT_BRANCH})...${NC}"
    if ! git pull --rebase origin "$CURRENT_BRANCH"; then
        echo -e "${RED}❌ Error: no se pudo sincronizar con origin/${CURRENT_BRANCH}${NC}"
        echo -e "${YELLOW}Resuelve conflictos y vuelve a ejecutar release.${NC}\n"
        exit 1
    fi
    echo -e "${GREEN}✓ Rama sincronizada${NC}\n"
fi

REMOTE_REF="origin/${CURRENT_BRANCH}"
if git show-ref --verify --quiet "refs/remotes/${REMOTE_REF}"; then
    LOCAL_HEAD=$(git rev-parse HEAD)
    REMOTE_HEAD=$(git rev-parse "${REMOTE_REF}")
    BASE_HEAD=$(git merge-base HEAD "${REMOTE_REF}")

    if [ "$BASE_HEAD" != "$REMOTE_HEAD" ]; then
        echo -e "${RED}❌ Error: Tu rama local no está alineada con ${REMOTE_REF}${NC}"
        echo -e "${YELLOW}Haz primero: git pull --rebase origin ${CURRENT_BRANCH}${NC}\n"
        exit 1
    fi
fi

# Obtener versión actual
CURRENT_VERSION=$(node -p "require('./package.json').version")
echo -e "${CYAN}📌 Versión actual: ${GREEN}${CURRENT_VERSION}${NC}\n"

# Evitar downgrades de versión respecto de remoto
REMOTE_VERSION=""
if git show "${REMOTE_REF}:package.json" >/dev/null 2>&1; then
    REMOTE_VERSION=$(git show "${REMOTE_REF}:package.json" | node -e 'let data=""; process.stdin.on("data", d => data += d); process.stdin.on("end", () => console.log(JSON.parse(data).version));')
fi

if [ -n "$REMOTE_VERSION" ]; then
    VERSION_CHECK=$(node -e '
const localV = process.argv[1];
const remoteV = process.argv[2];
const parse = (v) => v.split("-")[0].split(".").map((n) => Number(n));
const [lMaj,lMin,lPatch] = parse(localV);
const [rMaj,rMin,rPatch] = parse(remoteV);
const localTuple = [lMaj || 0, lMin || 0, lPatch || 0];
const remoteTuple = [rMaj || 0, rMin || 0, rPatch || 0];
for (let i = 0; i < 3; i++) {
  if (localTuple[i] < remoteTuple[i]) { console.log("behind"); process.exit(0); }
  if (localTuple[i] > remoteTuple[i]) { console.log("ahead"); process.exit(0); }
}
console.log("equal");
' "$CURRENT_VERSION" "$REMOTE_VERSION")

    if [ "$VERSION_CHECK" = "behind" ]; then
        echo -e "${RED}❌ Error: package.json local (${CURRENT_VERSION}) es menor que remoto (${REMOTE_VERSION})${NC}"
        echo -e "${YELLOW}Sincroniza rama/versiones antes de crear release para evitar bucles.${NC}\n"
        exit 1
    fi
fi

# Evitar releases con package.json por detrás del último tag semver estable
LAST_STABLE_TAG=$(git tag --sort=-v:refname | grep -E '^v[0-9]+\.[0-9]+\.[0-9]+$' | head -n 1 || true)
if [ -n "$LAST_STABLE_TAG" ]; then
    LAST_STABLE_VERSION=${LAST_STABLE_TAG#v}
    TAG_VERSION_CHECK=$(node -e '
const localV = process.argv[1];
const tagV = process.argv[2];
const parse = (v) => v.split("-")[0].split(".").map((n) => Number(n));
const [lMaj,lMin,lPatch] = parse(localV);
const [tMaj,tMin,tPatch] = parse(tagV);
const localTuple = [lMaj || 0, lMin || 0, lPatch || 0];
const tagTuple = [tMaj || 0, tMin || 0, tPatch || 0];
for (let i = 0; i < 3; i++) {
  if (localTuple[i] < tagTuple[i]) { console.log("behind"); process.exit(0); }
  if (localTuple[i] > tagTuple[i]) { console.log("ahead"); process.exit(0); }
}
console.log("equal");
' "$CURRENT_VERSION" "$LAST_STABLE_VERSION")

    if [ "$TAG_VERSION_CHECK" = "behind" ]; then
        echo -e "${RED}❌ Error: package.json (${CURRENT_VERSION}) está por detrás del último tag (${LAST_STABLE_TAG})${NC}"
        echo -e "${YELLOW}Corrige versión/base antes de ejecutar release.${NC}\n"
        exit 1
    fi
fi

# Determinar nuevo tipo de versión
if [ -z "$1" ] || [[ "$1" == --* ]]; then
    if [ "$NON_INTERACTIVE" = "true" ]; then
        RELEASE_TYPE="auto"
    else
    echo -e "${YELLOW}1. ¿Qué tipo de release?${NC}"
    echo "   1) patch  - Fixes (1.0.0 → 1.0.1)"
    echo "   2) minor  - Nuevas features (1.0.0 → 1.1.0)"
    echo "   3) major  - Breaking changes (1.0.0 → 2.0.0)"
    echo "   4) alpha  - Pre-release alpha (ej: 1.2.0-alpha.0)"
    echo "   5) beta   - Pre-release beta (ej: 1.2.0-beta.0)"
    echo "   6) rc     - Release candidate (ej: 1.2.0-rc.0)"
    echo "   7) auto   - Detectar automáticamente"
    read -p "Elige (1-7): " type_choice
    
        case $type_choice in
            1) RELEASE_TYPE="patch" ;;
            2) RELEASE_TYPE="minor" ;;
            3) RELEASE_TYPE="major" ;;
            4) RELEASE_TYPE="alpha" ;;
            5) RELEASE_TYPE="beta" ;;
            6) RELEASE_TYPE="rc" ;;
            7) RELEASE_TYPE="auto" ;;
            *) echo -e "${RED}Opción inválida${NC}"; exit 1 ;;
        esac
    fi
else
    RELEASE_TYPE=$1
fi

echo -e "${GREEN}✓ Tipo de release: ${RELEASE_TYPE}${NC}\n"

SUGGESTED_RELEASE_TYPE="$(./scripts/suggest-release-type.sh)"

if [ "$RELEASE_TYPE" != "auto" ] && [ "$ALLOW_EMPTY_RELEASE" != "true" ] && [ "$SUGGESTED_RELEASE_TYPE" = "none" ]; then
    echo -e "${YELLOW}⚠️  No hay commits versionables desde el último tag.${NC}"
    echo -e "${YELLOW}Se evita crear una release vacía para no ensuciar el changelog.${NC}\n"
    echo -e "${CYAN}Sugerencia actual: ${SUGGESTED_RELEASE_TYPE}${NC}"
    echo -e "${CYAN}Si realmente quieres forzarla, usa:${NC} ./scripts/release.sh ${RELEASE_TYPE} --allow-empty\n"
    exit 1
fi

# Generar cambios y calcular nueva versión
echo -e "${CYAN}📊 Analizando commits...${NC}\n"

if [ "$RELEASE_TYPE" = "auto" ]; then
    # Detectar automáticamente
    npx standard-version --dry-run 2>/dev/null | grep "bumping version" || true
    echo -e "\nEjecutando: npx standard-version\n"
    npx standard-version
elif [ "$RELEASE_TYPE" = "alpha" ] || [ "$RELEASE_TYPE" = "beta" ] || [ "$RELEASE_TYPE" = "rc" ]; then
    npx standard-version --prerelease "$RELEASE_TYPE"
else
    npx standard-version --release-as "$RELEASE_TYPE"
fi

# Obtener nueva versión
NEW_VERSION=$(node -p "require('./package.json').version")
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
push_confirm="n"
if [ "$AUTO_PUSH" = "true" ]; then
    push_confirm="y"
else
    read -p "¿Hacer push de los cambios y tags? (y/n): " push_confirm
fi

if [ "$push_confirm" = "y" ] || [ "$push_confirm" = "Y" ]; then
    echo -e "${CYAN}📤 Haciendo push...${NC}\n"
    
    git push --follow-tags origin "$CURRENT_BRANCH"
    
    echo -e "\n${GREEN}✅ Release completado exitosamente!${NC}\n"
    echo -e "${CYAN}Git tag creado: ${TAG}${NC}"
    echo -e "${CYAN}Rama: ${CURRENT_BRANCH}${NC}"
    echo -e "${CYAN}CHANGELOG actualizado: CHANGELOG.md${NC}\n"
    
    # Mostrar link de GitHub
    echo -e "${YELLOW}📎 Enlace del release:${NC}"
    echo -e "${CYAN}https://github.com/oahumada/Stratos/releases/tag/${TAG}${NC}\n"
else
    echo -e "${YELLOW}⚠️  Push cancelado${NC}"
    echo -e "${CYAN}Los cambios están locales. Haz push manualmente:${NC}"
    echo -e "   git push --follow-tags\n"
fi

echo -e "${GREEN}🎉 ¡Release listo!${NC}\n"
