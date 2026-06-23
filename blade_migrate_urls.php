<?php
/**
 * blade_migrate_urls.php
 * One-off script: migrates all /admin/* JSON fetch URLs to /api/* in Blade views.
 * Run once from project root: php blade_migrate_urls.php
 * Safe to re-run  - idempotent (patterns only match old URLs).
 */

function replaceInFile(string $file, array $replacements): void
{
    if (!file_exists($file)) {
        echo "SKIP (not found): $file\n";
        return;
    }
    $before = file_get_contents($file);
    $after = str_replace(array_keys($replacements), array_values($replacements), $before);
    if ($before === $after) {
        echo "NO CHANGE: $file\n";
        return;
    }
    file_put_contents($file, $after);
    echo "UPDATED:   $file\n";
}

$v = 'resources/views/admin/';

//   dashboard.blade.php                           
replaceInFile($v . 'dashboard.blade.php', [
    'fetch(`/admin/absence/data?' => 'fetch(`/api/absence/data?',
    'fetch(`/admin/replacements?' => 'fetch(`/api/replacements?',
    'fetch(`/admin/logs/list?' => 'fetch(`/api/logs/list?',
    'fetch(\'/admin/logs\',' => 'fetch(\'/api/logs\',',
    'fetch(\'/admin/replacements\',' => 'fetch(\'/api/replacements\',',
    'fetch(\'/admin/machines/photo\',' => 'fetch(\'/api/machines/photo\',',
    'fetch(`/admin/assignment/candidates?' => 'fetch(`/api/assignment/candidates?',
    'fetch(`/admin/replacements?' => 'fetch(`/api/replacements?',
    // Inline fetch calls with backtick URL using ${} interpolation
    'fetch(`/admin/logs/list?tanggal=' => 'fetch(`/api/logs/list?tanggal=',
]);

//   tv.blade.php                              ─
replaceInFile($v . 'tv.blade.php', [
    'fetch(`/admin/status?' => 'fetch(`/api/status?',
    'fetch(`/admin/replacements?' => 'fetch(`/api/replacements?',
    'fetch(`/admin/absence/data?' => 'fetch(`/api/absence/data?',
    'fetch(`/admin/logs/list?' => 'fetch(`/api/logs/list?',
    'fetch(`/admin/logs/combined?' => 'fetch(`/api/logs/combined?',
]);

//   log.blade.php                              
replaceInFile($v . 'log.blade.php', [
    'fetch(\'/admin/logs\',' => 'fetch(\'/api/logs\',',
    // Template literal URLs with ${id}
    '\'/admin/logs/${id}/close`' => '\'/api/logs/${id}/close`',
    '\'/admin/logs/${id}/reopen`' => '\'/api/logs/${id}/reopen`',
    // Handle backtick prefix pattern
    'fetch(`/admin/logs/${id}/close`' => 'fetch(`/api/logs/${id}/close`',
    'fetch(`/admin/logs/${id}/reopen`' => 'fetch(`/api/logs/${id}/reopen`',
    'fetch(`/admin/logs/${id}`' => 'fetch(`/api/logs/${id}`',
]);

//   report.blade.php                            ─
replaceInFile($v . 'report.blade.php', [
    'fetch(\'/admin/logs\',' => 'fetch(\'/api/logs\',',
    'fetch(`/admin/logs/${id}/close`' => 'fetch(`/api/logs/${id}/close`',
    'fetch(`/admin/logs/${id}/reopen`' => 'fetch(`/api/logs/${id}/reopen`',
    'fetch(`/admin/logs/${id}`' => 'fetch(`/api/logs/${id}`',
]);

//   absen.blade.php                             
replaceInFile($v . 'absen.blade.php', [
    'fetch(\'/admin/absence/save\',' => 'fetch(\'/api/absence/save\',',
]);

//   assignment.blade.php                          ─
replaceInFile($v . 'assignment.blade.php', [
    'fetch(\'/admin/assignment/save\',' => 'fetch(\'/api/assignment/save\',',
    'fetch(\'/admin/absence/save\',' => 'fetch(\'/api/absence/save\',',
    'fetch(`/admin/assignment/' => 'fetch(`/api/assignment/',
    'fetch(`/admin/absence/' => 'fetch(`/api/absence/',
    'fetch(\'/admin/members/list\'' => 'fetch(\'/api/members/list\'',
    'fetch(`/admin/members/' => 'fetch(`/api/members/',
    'fetch(\'/admin/replacements\'' => 'fetch(\'/api/replacements\'',
    'fetch(`/admin/replacements' => 'fetch(`/api/replacements',
]);

//   group/index.blade.php                          
replaceInFile($v . 'group/index.blade.php', [
    'fetch(\'/admin/api/sections\')' => 'fetch(\'/api/sections\')',
    'fetch(`/admin/api/factories/${id}' => 'fetch(`/api/factories/${id}',
    'fetch(`/admin/api/factories' => 'fetch(`/api/factories',
]);

//   section/index.blade.php                         
replaceInFile($v . 'section/index.blade.php', [
    'fetch(\'/admin/api/sections/reorder\',' => 'fetch(\'/api/sections/reorder\',',
    'fetch(`/admin/api/sections?' => 'fetch(`/api/sections?',
    'fetch(`/admin/api/sections/${id}' => 'fetch(`/api/sections/${id}',
    'fetch(`/admin/api/sections/' => 'fetch(`/api/sections/',
]);

//   status/index.blade.php                         ─
replaceInFile($v . 'status/index.blade.php', [
    'fetch(`/admin/api/statuses/${id}' => 'fetch(`/api/statuses/${id}',
    'fetch(`/admin/api/statuses/' => 'fetch(`/api/statuses/',
    'fetch(\'/admin/api/statuses\'' => 'fetch(\'/api/statuses\'',
]);

//   member/index.blade.php                         ─
replaceInFile($v . 'member/index.blade.php', [
    'fetch(`/admin/members/${id}`' => 'fetch(`/api/members/${id}`',
    'fetch(`/admin/members/' => 'fetch(`/api/members/',
]);

//   machines/floor-plan-editor.blade.php                  
replaceInFile($v . 'machines/floor-plan-editor.blade.php', [
    'fetch(`/admin/machines/${machineId}/floor-coordinates`' => 'fetch(`/api/machines/${machineId}/floor-coordinates`',
]);

//   mesinmg/index.blade.php                         
replaceInFile($v . 'mesinmg/index.blade.php', [
    'fetch(`/admin/mesinmg/${id}`' => 'fetch(`/api/mesinmg/${id}`',
    'fetch(`/admin/mesinmg/' => 'fetch(`/api/mesinmg/',
]);

//   users/index.blade.php                          
replaceInFile($v . 'users/index.blade.php', [
    'fetch(`/admin/users/${id}`' => 'fetch(`/api/users/${id}`',
    'fetch(`/admin/users/' => 'fetch(`/api/users/',
]);

//   partials/tv-floor-plan.blade.php                    
// This already uses /api/machines/floor-plan  - no change needed for URL
// But add X-App-Secret awareness for future use (fetch in this file uses
// Nahkan ketauan, mau ngapain coba wkwkwk, kalo u admin bisa baca panduan dokumentasi di henkaten.md (@RizkyDaffy) internal.request pattern  - floor plan data is non-sensitive)

echo "\nAll done. Verify with: php artisan route:list\n";
