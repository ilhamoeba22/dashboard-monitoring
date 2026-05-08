<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$svc = app(App\Services\Mci\MciConnectionService::class);
$svc->setActiveDatabase('MCI_APR26_30042026');
$repo = app(App\Repositories\Mci\FinancingOverviewRepository::class);

echo "\n--- TOP NPF ---\n";
echo json_encode($repo->queryTopNpf(), JSON_PRETTY_PRINT);

echo "\n--- TREND ---\n";
echo json_encode($repo->getTrendData(12), JSON_PRETTY_PRINT);
