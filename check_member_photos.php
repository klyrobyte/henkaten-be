<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Member;

$members = Member::all();
echo "Total members: " . $members->count() . "\n";

$withPhoto = $members->filter(fn($m) => !empty($m->photo))->count();
echo "With photos: " . $withPhoto . "\n";

$withoutPhoto = $members->filter(fn($m) => empty($m->photo))->count();
echo "Without photos: " . $withoutPhoto . "\n";

echo "\nMembers without photos:\n";
foreach ($members->filter(fn($m) => empty($m->photo)) as $m) {
    echo "  - ID: {$m->id}, Name: {$m->nama}\n";
}
