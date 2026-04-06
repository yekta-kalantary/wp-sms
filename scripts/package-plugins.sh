#!/usr/bin/env bash
set -euo pipefail

REPO_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "${REPO_ROOT}"

mkdir -p dist

bash scripts/package-single-plugin.sh "core" "yekta-sms-core" "yekta-sms-core.zip"
bash scripts/package-single-plugin.sh "geateway/yekta-geateway-smsir" "yekta-geateway-smsir" "yekta-geateway-smsir.zip"
bash scripts/package-single-plugin.sh "integration/yekta-integration-woocomrce" "yekta-integration-woocomrce" "yekta-integration-woocomrce.zip"
bash scripts/package-single-plugin.sh "integration/yekta-integration-edd" "yekta-integration-edd" "yekta-integration-edd.zip"

echo "\nPackaged artifacts:"
find dist -maxdepth 1 -type f -name '*.zip' -print | LC_ALL=C sort
