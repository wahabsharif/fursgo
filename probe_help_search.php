<?php

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$request = Illuminate\Http\Request::create('/support-and-assistance/search', 'GET');
$response = $kernel->handle($request);

echo 'status=' . $response->getStatusCode() . PHP_EOL;
echo 'exists=' . (app('livewire')->exists('help.search') ? 'yes' : 'no') . PHP_EOL;
echo 'path=' . (app('livewire.finder')->resolveSingleFileComponentPath('help.search') ?? 'null') . PHP_EOL;

$body = $response->getContent();
if ($response->getStatusCode() >= 400 || str_contains($body, 'not found') || str_contains($body, 'Component [')) {
    echo "ERROR BODY:\n" . substr(strip_tags($body), 0, 1500) . PHP_EOL;
    exit(1);
}

echo 'ok: page rendered without component error' . PHP_EOL;
