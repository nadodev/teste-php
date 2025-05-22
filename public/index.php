<?php

// Configuração de ambiente
define('ENVIRONMENT', 'development');
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../vendor/autoload.php';

// Load configuration
Infrastructure\Config\Config::load();

// Pega a rota da URL amigável
$requestUri = $_SERVER['REQUEST_URI'];
$scriptName = dirname($_SERVER['SCRIPT_NAME']);

// Remove o path base (caso esteja em subdiretório)
if (strpos($requestUri, $scriptName) === 0) {
    $route = substr($requestUri, strlen($scriptName));
} else {
    $route = $requestUri;
}

// Remove query string
$route = explode('?', $route, 2)[0];

// Remove barras no início/fim
$route = trim($route, '/');

// Se vazio, define rota padrão
if ($route === '') {
    $route = 'produtos';
}

// Basic routing
switch ($route) {
    case 'produtos':
        require_once __DIR__ . '/../src/Presentation/Controllers/ProdutoController.php';
        $controller = new Presentation\Controllers\ProdutoController();
        $controller->index();
        break;
    
    case 'produto/novo':
        require_once __DIR__ . '/../src/Presentation/Controllers/ProdutoController.php';
        $controller = new Presentation\Controllers\ProdutoController();
        $controller->create();
        break;
    
    case 'produto/editar':
        require_once __DIR__ . '/../src/Presentation/Controllers/ProdutoController.php';
        $controller = new Presentation\Controllers\ProdutoController();
        $controller->edit();
        break;

    case 'produto/excluir':
        require_once __DIR__ . '/../src/Presentation/Controllers/ProdutoController.php';
        $controller = new Presentation\Controllers\ProdutoController();
        $controller->delete();
        break;
    
    case 'carrinho':
        require_once __DIR__ . '/../src/Presentation/Controllers/CarrinhoController.php';
        $controller = new Presentation\Controllers\CarrinhoController();
        $controller->index();
        break;

    case 'carrinho/adicionar':
        require_once __DIR__ . '/../src/Presentation/Controllers/CarrinhoController.php';
        $controller = new Presentation\Controllers\CarrinhoController();
        $controller->adicionar();
        break;

    case 'carrinho/remover':
        require_once __DIR__ . '/../src/Presentation/Controllers/CarrinhoController.php';
        $controller = new Presentation\Controllers\CarrinhoController();
        $controller->remover();
        break;

    case 'carrinho/atualizar':
        require_once __DIR__ . '/../src/Presentation/Controllers/CarrinhoController.php';
        $controller = new Presentation\Controllers\CarrinhoController();
        $controller->atualizar();
        break;

    case 'carrinho/aplicar-cupom':
        require_once __DIR__ . '/../src/Presentation/Controllers/CarrinhoController.php';
        $controller = new Presentation\Controllers\CarrinhoController();
        $controller->aplicarCupom();
        break;

    case 'carrinho/remover-cupom':
        require_once __DIR__ . '/../src/Presentation/Controllers/CarrinhoController.php';
        $controller = new Presentation\Controllers\CarrinhoController();
        $controller->removerCupom();
        break;

    case 'carrinho/finalizar':
        require_once __DIR__ . '/../src/Presentation/Controllers/CarrinhoController.php';
        $controller = new Presentation\Controllers\CarrinhoController();
        $controller->finalizar();
        break;

    case 'carrinho/sucesso':
        require_once __DIR__ . '/../src/Presentation/Controllers/CarrinhoController.php';
        $controller = new Presentation\Controllers\CarrinhoController();
        $controller->sucesso();
        break;

    case 'cupons':
        require_once __DIR__ . '/../src/Presentation/Controllers/CupomController.php';
        $controller = new Presentation\Controllers\CupomController();
        $controller->index();
        break;

    case 'cupom/novo':
        require_once __DIR__ . '/../src/Presentation/Controllers/CupomController.php';
        $controller = new Presentation\Controllers\CupomController();
        $controller->create();
        break;

    case 'cupom/editar':
        require_once __DIR__ . '/../src/Presentation/Controllers/CupomController.php';
        $controller = new Presentation\Controllers\CupomController();
        $controller->edit();
        break;

    case 'cupom/excluir':
        require_once __DIR__ . '/../src/Presentation/Controllers/CupomController.php';
        $controller = new Presentation\Controllers\CupomController();
        $controller->delete();
        break;

    case 'webhook':
        require_once __DIR__ . '/../src/Presentation/Controllers/WebhookController.php';
        $controller = new Presentation\Controllers\WebhookController();
        $controller->handleOrderStatus();
        break;

    case 'pedidos':
        require_once __DIR__ . '/../src/Presentation/Controllers/PedidosController.php';
        $controller = new Presentation\Controllers\PedidosController();
        $controller->index();
        break;

    case 'pedidos/detalhes':
        require_once __DIR__ . '/../src/Presentation/Controllers/PedidosController.php';
        $controller = new Presentation\Controllers\PedidosController();
        $controller->detalhes();
        break;
    
    default:
        header('Location: /produtos');
        exit;
} 