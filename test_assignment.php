<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
Auth::loginUsingId(1);
$c = app(App\Http\Controllers\Admin\AssignmentController::class);
$g = app(App\Services\FactoryConfigService::class)->buildGroups('Factory 3 & 4');
$ref = new ReflectionMethod($c, 'buildDefaults');
$ref->setAccessible(true);
$res = $ref->invoke($c, 'Factory 3 & 4', 'B', $g, '2026-07-03');
$output = [];
foreach ($res as $k => $v) {
    if (strpos($k, 'Tightening') !== false) {
        $output[$k] = $v;
    }
}
echo json_encode($output);
