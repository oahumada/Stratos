#!/usr/bin/env bash
set -euo pipefail

OWNER="oahumada"
REPO="Stratos"
BRANCH="main"
API="https://api.github.com/repos/${OWNER}/${REPO}/branches/${BRANCH}/protection"
PAYLOAD_FILE=".github/branch-protection-main-release-governance.json"

if [[ -z "${GITHUB_TOKEN:-}" ]]; then
  echo "❌ GITHUB_TOKEN no está definido."
  echo "Define un token con permiso 'repo' (classic) o 'Administration: Read and write' (fine-grained)."
  exit 1
fi

if [[ ! -f "${PAYLOAD_FILE}" ]]; then
  echo "❌ No existe ${PAYLOAD_FILE}"
  exit 1
fi

echo "Aplicando branch protection en ${OWNER}/${REPO}:${BRANCH}..."

curl --fail --silent --show-error \
  -X PUT \
  -H "Accept: application/vnd.github+json" \
  -H "Authorization: Bearer ${GITHUB_TOKEN}" \
  -H "X-GitHub-Api-Version: 2022-11-28" \
  "${API}" \
  -d "@${PAYLOAD_FILE}" >/dev/null

echo "✅ Branch protection aplicada correctamente."
