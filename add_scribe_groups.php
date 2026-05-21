<?php

/**
 * add_scribe_groups.php
 * Adds @group annotations to API controllers for Scribe documentation.
 */

$dir = __DIR__ . '/app/Http/Controllers/Admin';
$files = glob($dir . '/*.php');

foreach ($files as $file) {
    $content = file_get_contents($file);
    $basename = basename($file, '.php');
    
    // Group name is the controller name without "Controller"
    $groupName = str_replace('Controller', '', $basename);
    // Add spaces before capital letters (e.g. MesinManagement -> Mesin Management)
    $groupName = preg_replace('/(?<!^)([A-Z])/', ' $1', $groupName);

    // Skip if already has @group
    if (strpos($content, '@group') !== false) {
        echo "SKIP (already has @group): $basename\n";
        continue;
    }

    // Find class declaration and insert docblock before it
    $pattern = '/(class\s+' . $basename . '\s+extends)/';
    if (preg_match($pattern, $content)) {
        $docblock = <<<DOC
/**
 * @group $groupName
 * 
 * APIs for managing $groupName.
 */
class $basename extends
DOC;
        $content = preg_replace($pattern, $docblock, $content);
        file_put_contents($file, $content);
        echo "UPDATED: $basename\n";
    }
}

echo "Done.\n";
