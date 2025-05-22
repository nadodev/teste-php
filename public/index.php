<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../vendor/autoload.php';

Infrastructure\Config\Env::load();
Infrastructure\Config\Config::load();
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
