<?php
header('Content-Type: text/plain; charset=utf-8');

echo "PHP_VERSION=" . PHP_VERSION . "\n";

try {
    require __DIR__ . '/vendor/autoload.php';
    echo "AUTOLOAD=OK\n";

    $app = require __DIR__ . '/bootstrap/app.php';
    echo "APP_BOOTSTRAP=OK\n";

    $app->boot();
    echo "APP_BOOT=OK\n";

    echo "DIAGNOSTIC=OK\n";
} catch (Throwable $e) {
    http_response_code(500);
    echo "DIAGNOSTIC=FAILED\n";
    echo "TYPE=" . get_class($e) . "\n";
    echo "MESSAGE=" . $e->getMessage() . "\n";
    echo "FILE=" . $e->getFile() . "\n";
    echo "LINE=" . $e->getLine() . "\n";
}
