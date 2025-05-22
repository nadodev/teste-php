<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../vendor/autoload.php';

Infrastructure\Config\Env::load();
Infrastructure\Config\Config::load();

if(Env::get('ENVIRONMENT') === 'development' && Env::get('APP_DEBUG') === 'true'){
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

$requestUri = $_SERVER['REQUEST_URI'];
$scriptName = dirname($_SERVER['SCRIPT_NAME']);

if (strpos($requestUri, $scriptName) === 0) {
    $route = substr($requestUri, strlen($scriptName));
} else {
    $route = $requestUri;
}

$route = explode('?', $route, 2)[0];

$route = trim($route, '/');

if ($route === '') {
    $route = 'produtos';
}

require_once __DIR__ . '/../route.php';
