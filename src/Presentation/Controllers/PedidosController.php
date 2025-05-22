<?php

namespace Presentation\Controllers;

use Infrastructure\Repositories\PedidoRepository;
use Presentation\View;

class PedidosController
{
    private PedidoRepository $pedidoRepository;
    private View $view;

    public function __construct()
    {
        $this->pedidoRepository = new PedidoRepository();
        $this->view = new View();
    }

    public function index(): void
    {
        $pedidos = $this->pedidoRepository->findAll();
        $this->view->render('pedidos/index', [
            'pedidos' => $pedidos
        ]);
    }

    public function detalhes(): void
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        
        if (!$id) {
            $_SESSION['message'] = [
                'type' => 'danger',
                'text' => 'Pedido não encontrado.'
            ];
            header('Location: /pedidos');
            exit;
        }

        $pedido = $this->pedidoRepository->findById($id);
        
        if (!$pedido) {
            $_SESSION['message'] = [
                'type' => 'danger',
                'text' => 'Pedido não encontrado.'
            ];
            header('Location: /pedidos');
            exit;
        }

        $this->view->render('pedidos/detalhes', [
            'pedido' => $pedido
        ]);
    }
} 