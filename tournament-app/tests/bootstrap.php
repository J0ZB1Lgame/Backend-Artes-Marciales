<?php

/*
|--------------------------------------------------------------------------
| Bootstrap de Tests — RF-AN-06
|--------------------------------------------------------------------------
| Carga el autoloader de Composer y configura el entorno de pruebas.
| Lee el .env ANTES de que cualquier DAO instancie el singleton Database.
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/../vendor/autoload.php';

// Leer .env y exportar variables de entorno para que Database.php las use
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $vars = parse_ini_file($envFile);
    foreach ($vars as $key => $value) {
        $_ENV[$key]    = $value;
        $_SERVER[$key] = $value;
        putenv("{$key}={$value}");
    }
}

// Definir constante de entorno de pruebas
if (!defined('APP_TESTING')) {
    define('APP_TESTING', true);
}
