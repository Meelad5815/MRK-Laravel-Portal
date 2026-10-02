<?php
header('Content-Type: text/plain; charset=utf-8');

echo "PHP_VERSION=" . PHP_VERSION . "\n";

try {
    $required = version_compare(PHP_VERSION, '8.2.0', '>=');
    echo "PHP_REQUIREMENT=" . ($required ? "PASS" : "FAIL") . "\n";

    foreach (['ctype', 'fileinfo', 'mbstring', 'openssl', 'pdo', 'session', 'tokenizer', 'xml'] as $ext) {
        echo "EXT_" . strtoupper($ext) . "=" . (extension_loaded($ext) ? "OK" : "MISSING") . "\n";
    }

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
}
