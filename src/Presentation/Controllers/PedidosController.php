<?php

namespace Presentation\Controllers;

use Infrastructure\Repositories\PedidoRepository;

class PedidosController
{
    private PedidoRepository $pedidoRepository;

    public function __construct()
    {
        $this->pedidoRepository = new PedidoRepository();
    }

    public function index(): void
    {
        $pedidos = $this->pedidoRepository->findAll();
        require_once __DIR__ . '/../Views/pedidos/index.php';
    }

    public function detalhes(): void
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        
        if (!$id) {
            $_SESSION['message'] = [
                'type' => 'danger',
                'text' => 'Pedido não encontrado.'
            ];
            header('Location: ?route=pedidos');
            exit;
        }

        $pedido = $this->pedidoRepository->findById($id);
        
        if (!$pedido) {
            $_SESSION['message'] = [
                'type' => 'danger',
                'text' => 'Pedido não encontrado.'
            ];
            header('Location: ?route=pedidos');
            exit;
        }

        require_once __DIR__ . '/../Views/pedidos/detalhes.php';
    }
} 