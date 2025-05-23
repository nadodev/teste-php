<?php 

switch ($route) {
    case 'produtos':
        require_once __DIR__ . '/src/Presentation/Controllers/ProdutoController.php';
        $controller = new Presentation\Controllers\ProdutoController();
        $controller->index();
        break;
    
    case 'produto/novo':
        require_once __DIR__ . '/src/Presentation/Controllers/ProdutoController.php';
        $controller = new Presentation\Controllers\ProdutoController();
        $controller->create();
        break;
    
    case 'produto/editar':
        require_once __DIR__ . '/src/Presentation/Controllers/ProdutoController.php';
        $controller = new Presentation\Controllers\ProdutoController();
        $controller->edit();
        break;

    case 'produto/excluir':
        require_once __DIR__ . '/src/Presentation/Controllers/ProdutoController.php';
        $controller = new Presentation\Controllers\ProdutoController();
        $controller->delete();
        break;
    
    case 'carrinho':
        require_once __DIR__ . '/src/Presentation/Controllers/CarrinhoController.php';
        $controller = new Presentation\Controllers\CarrinhoController();
        $controller->index();
        break;

    case 'carrinho/adicionar':
        require_once __DIR__ . '/src/Presentation/Controllers/CarrinhoController.php';
        $controller = new Presentation\Controllers\CarrinhoController();
        $controller->adicionar();
        break;

    case 'carrinho/remover':
        require_once __DIR__ . '/src/Presentation/Controllers/CarrinhoController.php';
        $controller = new Presentation\Controllers\CarrinhoController();
        $controller->remover();
        break;

    case 'carrinho/atualizar':
        require_once __DIR__ . '/src/Presentation/Controllers/CarrinhoController.php';
        $controller = new Presentation\Controllers\CarrinhoController();
        $controller->atualizar();
        break;

    case 'carrinho/aplicar-cupom':
        require_once __DIR__ . '/src/Presentation/Controllers/CarrinhoController.php';
        $controller = new Presentation\Controllers\CarrinhoController();
        $controller->aplicarCupom();
        break;

    case 'carrinho/remover-cupom':
        require_once __DIR__ . '/src/Presentation/Controllers/CarrinhoController.php';
        $controller = new Presentation\Controllers\CarrinhoController();
        $controller->removerCupom();
        break;

    case 'carrinho/finalizar':
        require_once __DIR__ . '/src/Presentation/Controllers/CarrinhoController.php';
        $controller = new Presentation\Controllers\CarrinhoController();
        $controller->finalizar();
        break;

    case 'carrinho/sucesso':
        require_once __DIR__ . '/src/Presentation/Controllers/CarrinhoController.php';
        $controller = new Presentation\Controllers\CarrinhoController();
        $controller->sucesso();
        break;

    case 'cupons':
        require_once __DIR__ . '/src/Presentation/Controllers/CupomController.php';
        $controller = new Presentation\Controllers\CupomController();
        $controller->index();
        break;

    case 'cupom/novo':
        require_once __DIR__ . '/src/Presentation/Controllers/CupomController.php';
        $controller = new Presentation\Controllers\CupomController();
        $controller->create();
        break;

    case 'cupom/editar':
        require_once __DIR__ . '/src/Presentation/Controllers/CupomController.php';
        $controller = new Presentation\Controllers\CupomController();
        $controller->edit();
        break;

    case 'cupom/excluir':
        require_once __DIR__ . '/src/Presentation/Controllers/CupomController.php';
        $controller = new Presentation\Controllers\CupomController();
        $controller->delete();
        break;

    case 'webhook':
        require_once __DIR__ . '/src/Presentation/Controllers/WebhookController.php';
        $controller = new Presentation\Controllers\WebhookController();
        $controller->handleOrderStatus();
        break;

    case 'pedidos':
        require_once __DIR__ . '/src/Presentation/Controllers/PedidosController.php';
        $controller = new Presentation\Controllers\PedidosController();
        $controller->index();
        break;

    case 'pedidos/detalhes':
        require_once __DIR__ . '/src/Presentation/Controllers/PedidosController.php';
        $controller = new Presentation\Controllers\PedidosController();
        $controller->detalhes();
        break;
    
    default:
        header('Location: /produtos');
        exit;
} 