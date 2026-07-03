<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$req = Illuminate\Http\Request::create('/api/skills/save-batch', 'POST', [], [], [], [], json_encode([
    'skills' => [
        [
            'member_id' => App\Models\Member::first()->id,
            'machine_name' => '#01-1300T',
            'process_name' => '',
            'factory' => 'Factory 3 & 4',
            'skill_pct' => 50
        ]
    ]
]));
$req->headers->set('Accept', 'application/json');
$req->headers->set('Content-Type', 'application/json');

$response = $kernel->handle($req);

echo "Status: " . $response->getStatusCode() . "\n";
echo "Content: " . $response->getContent() . "\n";
