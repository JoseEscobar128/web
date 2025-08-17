<?php

// --- INICIO DEL ARREGLO DEFINITIVO PARA CLOUDFLARE ---
// Forzamos a PHP a creer que la conexión es HTTPS desde el primer momento.
// Esto anula cualquier configuración incorrecta que venga de Nginx.
$_SERVER['HTTPS'] = 'on';
$_SERVER['HTTP_X_FORWARDED_PROTO'] = 'https';
// --- FIN DEL ARREGLO ---


use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
