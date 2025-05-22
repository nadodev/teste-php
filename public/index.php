<?php

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../vendor/autoload.php';

// Load configuration
Infrastructure\Config\Config::load();

// Basic routing
$route = $_GET['route'] ?? 'home';

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

    case 'webhook':
        require_once __DIR__ . '/../src/Presentation/Controllers/WebhookController.php';
        $controller = new Presentation\Controllers\WebhookController();
        $controller->atualizarPedido();
        break;
    
    default:
        header('Location: ?route=produtos');
        exit;
} 