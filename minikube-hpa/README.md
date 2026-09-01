# PSI5120 — implantação local com Minikube e HPA

Esta pasta implementa a parte local do Trabalho Avaliativo 1: um servidor web em Kubernetes com autoescalamento horizontal de Pods (HPA).

## Arquitetura

`load-generator Job` → `Service web-hpa` → `Deployment web-hpa` (1–5 Pods) → `HPA`.

A aplicação PHP recebe `?work=N`; o cálculo determinístico produz carga de CPU. O HPA compara a utilização média de CPU com a meta de 50% dos `requests` de 100m. O Metrics Server fornece a métrica usada nessa decisão.

## Execução

```sh
minikube start --driver=docker --cpus=4 --memory=6144
minikube addons enable metrics-server
docker build -t psi5120-hpa-web:1.0 .
minikube image load psi5120-hpa-web:1.0
kubectl apply -f manifests/namespace.yaml
kubectl apply -f manifests/deployment.yaml -f manifests/service.yaml -f manifests/hpa.yaml
kubectl -n psi5120-hpa rollout status deployment/web-hpa
kubectl -n psi5120-hpa get pods,hpa
kubectl -n psi5120-hpa top pods
```

Inicie a carga e observe o HPA em terminais separados:

```sh
kubectl apply -f manifests/load-generator.yaml
kubectl -n psi5120-hpa get hpa web-hpa --watch
kubectl -n psi5120-hpa get pods --watch
kubectl -n psi5120-hpa top pods
```

## Evidências para o artigo

As evidências principais do Minikube são o ciclo contínuo em `evidence/04-ciclo-continuo-antes.png`, `05-ciclo-continuo-durante.png` e `06-ciclo-continuo-pos.png`. Os arquivos 01--03 foram preservados como validações anteriores separadas e não devem ser tratados como uma única sequência temporal.

1. `kubectl get deploy,pods,svc,hpa -n psi5120-hpa` antes da carga.
2. `kubectl top pods -n psi5120-hpa` antes e durante a carga.
3. HPA com métrica e réplicas desejadas acima de 1 durante a carga.
4. Pods adicionais `Running` e distribuição de respostas por hostname.
5. HPA e Pods após o fim da carga e a janela de estabilização.

## Variante EKS

`eksctl-cluster.yaml` descreve o cluster temporário em `sa-east-1`, com um único nó gerenciado e sem NAT Gateway. Após o control plane ficar ativo, use `deployment-eks.yaml` no lugar de `deployment.yaml`; a imagem correspondente está no Amazon ECR. Execute `cleanup-eks.sh` após capturar as evidências, pois o control plane e o nó gerenciado têm cobrança por tempo de uso.
