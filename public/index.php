<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// When the app lives in a subdirectory, tell Symfony to strip that prefix
// from REQUEST_URI before Laravel resolves the route.
$subdirectory = '';
$envFile = __DIR__.'/../.env';

if (is_file($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES) as $line) {
        if (preg_match('/^APP_SUBDIRECTORY\s*=\s*(.*)$/', $line, $matches) === 1) {
            $subdirectory = trim($matches[1], " \t\r\n\"'");

            break;
        }
    }
}

if ($subdirectory !== '') {
    $scriptName = rtrim($subdirectory, '/').'/index.php';

    $_SERVER['SCRIPT_NAME'] = $scriptName;
    $_SERVER['PHP_SELF'] = $scriptName;
}

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
