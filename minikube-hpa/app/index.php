<?php
// Endpoint didático: ?work=N executa N iterações para produzir carga mensurável pelo HPA.
$work = filter_input(INPUT_GET, 'work', FILTER_VALIDATE_INT, [
    'options' => ['default' => 0, 'min_range' => 0, 'max_range' => 5000000],
]);

$value = 0.0;
for ($i = 0; $i < $work; $i++) {
    $value += sqrt(($i % 997) + 1);
}

header('Content-Type: application/json');
echo json_encode([
    'service' => 'psi5120-hpa-web',
    'pod' => gethostname(),
    'work' => $work,
    'result' => round($value, 2),
    'timestamp_utc' => gmdate('c'),
]);
