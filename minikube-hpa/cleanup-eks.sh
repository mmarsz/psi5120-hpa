#!/usr/bin/env sh
# Remove o cluster e o node group do laboratório assim que as evidências forem coletadas.
set -eu
AWS_PROFILE=admin-lab /tmp/psi5120-tools/eksctl delete cluster \
  --config-file "$(dirname "$0")/eksctl-cluster.yaml" \
  --wait
