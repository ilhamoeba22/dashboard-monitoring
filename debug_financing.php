<?php

declare(strict_types=1);

use App\Repositories\Interfaces\FinancingRepositoryInterface;
use Illuminate\Contracts\Http\Kernel;

define('LARAVEL_START', microtime(true));
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Kernel::class);

// Direct repository test — bypass controller
try {
    $repo = $app->make(FinancingRepositoryInterface::class);
    echo 'Repository instantiated OK'.PHP_EOL;

    $result = $repo->getNominative([], 5);
    echo 'getNominative OK, count: '.$result->count().PHP_EOL;
} catch (Throwable $e) {
    echo 'ERROR: '.$e->getMessage().PHP_EOL;
    echo 'CLASS: '.get_class($e).PHP_EOL;
    echo 'FILE: '.$e->getFile().':'.$e->getLine().PHP_EOL;
    echo 'TRACE (first 5):'.PHP_EOL;
    $trace = explode("\n", $e->getTraceAsString());
    echo implode("\n", array_slice($trace, 0, 10)).PHP_EOL;
}
