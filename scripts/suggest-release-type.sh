#!/usr/bin/env bash

set -euo pipefail

recommended_from_standard_version() {
  if ! command -v npx >/dev/null 2>&1; then
    return 1
  fi

  local bump_line
  bump_line="$(npx standard-version --dry-run 2>/dev/null | grep -m1 'bumping version' || true)"

  if [ -z "${bump_line}" ]; then
    return 1
  fi

  local from_version
  local to_version

  from_version="$(echo "${bump_line}" | sed -E 's/.*from ([^ ]+) to ([^ ]+).*/\1/')"
  to_version="$(echo "${bump_line}" | sed -E 's/.*from ([^ ]+) to ([^ ]+).*/\2/')"

  if [ -z "${from_version}" ] || [ -z "${to_version}" ]; then
    return 1
  fi

  local from_base
  local to_base
  from_base="${from_version%%-*}"
  to_base="${to_version%%-*}"

  local from_major from_minor from_patch
  local to_major to_minor to_patch
  IFS='.' read -r from_major from_minor from_patch <<<"${from_base}"
  IFS='.' read -r to_major to_minor to_patch <<<"${to_base}"

  from_major="${from_major:-0}"
  from_minor="${from_minor:-0}"
  from_patch="${from_patch:-0}"
  to_major="${to_major:-0}"
  to_minor="${to_minor:-0}"
  to_patch="${to_patch:-0}"

  if [ "${to_major}" -gt "${from_major}" ]; then
    echo "major"
    return 0
  fi

  if [ "${to_minor}" -gt "${from_minor}" ]; then
    echo "minor"
    return 0
  fi

  if [ "${to_patch}" -gt "${from_patch}" ]; then
    echo "patch"
    return 0
  fi

  echo "none"
  return 0
}

if suggested_bump="$(recommended_from_standard_version)"; then
  echo "${suggested_bump}"
  exit 0
fi

LAST_TAG=""
if LAST_TAG=$(git describe --tags --abbrev=0 2>/dev/null); then
  RANGE="${LAST_TAG}..HEAD"
else
  RANGE="HEAD"
fi

COMMITS="$(git log --format='%H|%s|%b' ${RANGE})"

is_refactor_core_change() {
  local refactor_commits
  refactor_commits="$(git log --format='%H|%s' ${RANGE} | grep -E '^[^|]+\|refactor(\(|:)' | cut -d'|' -f1 || true)"

  if [ -z "${refactor_commits}" ]; then
    return 1
  fi

  local core_paths
  core_paths='^(app/Services/|app/Http/Controllers/|app/Models/|routes/|config/|bootstrap/app\.php$)'

  for commit_hash in ${refactor_commits}; do
    local changed_files
    changed_files="$(git show --name-only --pretty='' "${commit_hash}" || true)"

    if echo "${changed_files}" | grep -Eq "${core_paths}"; then
      return 0
    fi
  done

  return 1
}

if [ -z "${COMMITS}" ]; then
  echo "none"
  exit 0
fi

if echo "${COMMITS}" | grep -Eq 'BREAKING CHANGE|^[^|]+\|[^|]*!\(|^[^|]+\|[^|]*!:'; then
  echo "major"
  exit 0
fi

if echo "${COMMITS}" | grep -Eq '^[^|]+\|feat(\(|:)' ; then
  echo "minor"
  exit 0
fi

if echo "${COMMITS}" | grep -Eq '^[^|]+\|(fix|perf)(\(|:)' ; then
  echo "patch"
  exit 0
fi

if echo "${COMMITS}" | grep -Eq '^[^|]+\|refactor(\(|:)' ; then
  if is_refactor_core_change; then
    echo "patch"
  else
    echo "none"
  fi
  exit 0
fi

# No-bump commits only (docs/test/chore/style/ci/revert/refactor non-core) -> none
if echo "${COMMITS}" | grep -Eq '^[^|]+\|(docs|test|chore|style|ci|revert|refactor)(\(|:)' ; then
  # If there are unknown/non-conventional commits mixed in, default to patch for safety
  if echo "${COMMITS}" | grep -Ev '^[^|]+\|(docs|test|chore|style|ci|revert|refactor)(\(|:)|^[^|]+\|$' | grep -q '.'; then
    echo "patch"
  else
    echo "none"
  fi
  exit 0
fi

echo "patch"
