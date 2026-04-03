#!/usr/bin/env bash

set -euo pipefail

BASE_BRANCH="${1:-main}"
REMOTE="${2:-origin}"

if [ ! -f package.json ]; then
  echo "[version-check] package.json not found; skipping"
  exit 0
fi

if ! command -v node >/dev/null 2>&1; then
  echo "[version-check] node is required to validate versions"
  exit 1
fi

git fetch "${REMOTE}" "${BASE_BRANCH}" --depth=1 --quiet

if ! git show "${REMOTE}/${BASE_BRANCH}:package.json" >/dev/null 2>&1; then
  echo "[version-check] cannot read ${REMOTE}/${BASE_BRANCH}:package.json; skipping"
  exit 0
fi

BASE_VERSION=$(git show "${REMOTE}/${BASE_BRANCH}:package.json" | node -e 'let data=""; process.stdin.on("data", d => data += d); process.stdin.on("end", () => console.log(JSON.parse(data).version));')
LOCAL_VERSION=$(node -p "require('./package.json').version")

RESULT=$(node -e '
const base = process.argv[1];
const local = process.argv[2];

const parse = (value) => {
  const [core] = value.split("-");
  const [major = "0", minor = "0", patch = "0"] = core.split(".");
  return [Number(major), Number(minor), Number(patch)];
};

const compare = (left, right) => {
  for (let index = 0; index < 3; index += 1) {
    if (left[index] > right[index]) return 1;
    if (left[index] < right[index]) return -1;
  }
  return 0;
};

const relation = compare(parse(local), parse(base));
if (relation < 0) {
  console.log("behind");
  process.exit(0);
}
if (relation > 0) {
  console.log("ahead");
  process.exit(0);
}
console.log("equal");
' "${BASE_VERSION}" "${LOCAL_VERSION}")

if [ "${RESULT}" = "behind" ]; then
  echo "[version-check] ❌ package.json version downgrade detected: ${LOCAL_VERSION} < ${BASE_VERSION} (${REMOTE}/${BASE_BRANCH})"
  exit 1
fi

echo "[version-check] ✅ Version monotonicity passed: ${LOCAL_VERSION} >= ${BASE_VERSION} (${REMOTE}/${BASE_BRANCH})"
