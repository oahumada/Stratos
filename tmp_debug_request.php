<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::create('/api/scenarios/1/analytics', 'GET');
$response = $kernel->handle($request);
echo "Status: " . $response->getStatusCode() . "\n";
echo "Headers: \n";
foreach ($response->headers->all() as $k => $v) {
    echo $k . ': ' . implode(', ', $v) . "\n";
}
echo "Body:\n";
echo (string)$response->getContent() . "\n";
$kernel->terminate($request, $response);
