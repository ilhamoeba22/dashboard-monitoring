<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $export = new \App\Exports\TargetTemplateExport();
    $result = \Maatwebsite\Excel\Facades\Excel::download($export, 'Template_Target_RBB.xlsx');
    echo "Success: " . get_class($result);
} catch (\Throwable $e) {
    echo "Exception: " . $e->getMessage() . "\n" . $e->getTraceAsString();
}
