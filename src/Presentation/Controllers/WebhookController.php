<?php

namespace Presentation\Controllers;

use Infrastructure\Repositories\PedidoRepository;

class WebhookController
{
    private PedidoRepository $pedidoRepository;

    public function __construct()
    {
        $this->pedidoRepository = new PedidoRepository();
    }

    public function handleOrderStatus(): void
    {
        try {
            // Verificar se é uma requisição POST
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                echo json_encode(['error' => 'Method not allowed']);
                return;
            }

            // Obter o corpo da requisição
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);

            // Validar dados recebidos
            if (!isset($data['pedido_id']) || !isset($data['status'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Missing required fields']);
                return;
            }

            $pedidoId = (int) $data['pedido_id'];
            $status = strtolower($data['status']);

            // Verificar se o pedido existe
            $pedido = $this->pedidoRepository->findById($pedidoId);
            if (!$pedido) {
                http_response_code(404);
                echo json_encode(['error' => 'Order not found']);
                return;
            }

            // Processar baseado no status
            if ($status === 'cancelado') {
                // Remover pedido
                $this->pedidoRepository->delete($pedidoId);
                $message = 'Order deleted successfully';
            } else {
                // Atualizar status do pedido
                $this->pedidoRepository->updateStatus($pedidoId, $status);
                $message = 'Order status updated successfully';
            }

            // Retornar sucesso
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => $message,
                'pedido_id' => $pedidoId,
                'status' => $status
            ]);

        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'error' => 'Internal server error',
                'message' => $e->getMessage()
            ]);
        }
    }
} 