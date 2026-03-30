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

echo "patch"
