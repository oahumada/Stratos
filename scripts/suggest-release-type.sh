#!/usr/bin/env bash

set -euo pipefail

LAST_TAG=""
if LAST_TAG=$(git describe --tags --abbrev=0 2>/dev/null); then
  RANGE="${LAST_TAG}..HEAD"
else
  RANGE="HEAD"
fi

COMMITS="$(git log --format='%H|%s|%b' ${RANGE})"

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

if echo "${COMMITS}" | grep -Eq '^[^|]+\|(fix|perf|refactor)(\(|:)' ; then
  echo "patch"
  exit 0
fi

# No-bump commits only (docs/test/chore/style/ci/revert) -> none
if echo "${COMMITS}" | grep -Eq '^[^|]+\|(docs|test|chore|style|ci|revert)(\(|:)' ; then
  # If there are unknown/non-conventional commits mixed in, default to patch for safety
  if echo "${COMMITS}" | grep -Ev '^[^|]+\|(docs|test|chore|style|ci|revert)(\(|:)|^[^|]+\|$' | grep -q '.'; then
    echo "patch"
  else
    echo "none"
  fi
  exit 0
fi

echo "patch"
