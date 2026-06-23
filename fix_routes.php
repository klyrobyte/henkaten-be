<?php
$c = file_get_contents('routes/web.php');
$c = preg_replace('/(?s)\/\/ â”€â”€â”€ API Documentation.*/', '', $c);
$c = preg_replace('/(?s)\/\/  ─ API Documentation.*/', '', $c);
file_put_contents('routes/web.php', trim($c) . PHP_EOL);
echo "Done";
