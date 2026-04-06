#!/usr/bin/env bash
set -euo pipefail

if [[ $# -ne 3 ]]; then
  echo "Usage: $0 <source_dir> <plugin_slug> <zip_name>" >&2
  exit 1
fi

SOURCE_DIR="$1"
PLUGIN_SLUG="$2"
ZIP_NAME="$3"

REPO_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
SOURCE_PATH="${REPO_ROOT}/${SOURCE_DIR}"
DIST_DIR="${REPO_ROOT}/dist"
STAGE_ROOT="$(mktemp -d)"
STAGE_DIR="${STAGE_ROOT}/${PLUGIN_SLUG}"
ZIP_PATH="${DIST_DIR}/${ZIP_NAME}"

if [[ ! -d "${SOURCE_PATH}" ]]; then
  echo "Source directory not found: ${SOURCE_DIR}" >&2
  exit 1
fi

mkdir -p "${DIST_DIR}" "${STAGE_DIR}"

rsync -a \
  --exclude='.git/' \
  --exclude='.github/' \
  --exclude='tests/' \
  --exclude='docs/' \
  --exclude='node_modules/' \
  --exclude='dist/' \
  --exclude='scripts/' \
  --exclude='.phpunit.result.cache' \
  --exclude='.phpstan-cache/' \
  --exclude='phpunit.xml*' \
  --exclude='phpstan*.neon*' \
  --exclude='phpcs.xml*' \
  --exclude='composer.json' \
  --exclude='composer.lock' \
  --exclude='README.md' \
  --exclude='CONTRIBUTING.md' \
  --exclude='AGENTS.md' \
  --exclude='*.log' \
  --exclude='.DS_Store' \
  "${SOURCE_PATH}/" "${STAGE_DIR}/"

rm -f "${ZIP_PATH}"
(
  cd "${STAGE_ROOT}"
  find "${PLUGIN_SLUG}" -type f -print | LC_ALL=C sort | zip -X -q "${ZIP_PATH}" -@
)

zip_size="$(wc -c < "${ZIP_PATH}" | tr -d ' ')"
echo "Created ${ZIP_PATH} (${zip_size} bytes)"

rm -rf "${STAGE_ROOT}"
