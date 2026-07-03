<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    $req = Illuminate\Http\Request::create('/api/assignment/candidates', 'GET', [
        'tanggal' => '2026-07-02',
        'factory' => 'Factory 3 & 4',
        'shift' => 'A',
        'machine' => '#01-350T',
        'absentName' => 'Dadi'
    ]);
    
    $c = app('App\Http\Controllers\Admin\AssignmentController');
    $res = $c->candidates($req);
    echo "SUCCESS\n";
} catch (\Throwable $e) {
    echo "ERROR:\n" . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n";
}
