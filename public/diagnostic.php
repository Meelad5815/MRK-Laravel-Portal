<?php

declare(strict_types=1);

header('Content-Type: text/plain; charset=UTF-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

echo "DIAGNOSTIC=OK\n";
echo "PHP_MAJOR=" . PHP_MAJOR_VERSION . "\n";
echo "PHP_MINOR=" . PHP_MINOR_VERSION . "\n";
echo "SERVER_SOFTWARE=" . ($_SERVER['SERVER_SOFTWARE'] ?? 'unknown') . "\n";
