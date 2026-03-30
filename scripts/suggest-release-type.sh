#!/usr/bin/env bash

set -euo pipefail

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
