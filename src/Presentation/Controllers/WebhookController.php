<?php

namespace Presentation\Controllers;

use Infrastructure\Database\Connection;

class WebhookController
{
    private $connection;

    public function __construct()
    {
        $this->connection = Connection::getInstance();
    }

    public function atualizarPedido(): void
    {
        // Only accept POST requests
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        // Get JSON data
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        // Validate input
        if (!isset($data['id']) || !isset($data['status'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid input']);
            return;
        }

        $id = (int) $data['id'];
        $status = $data['status'];

        try {
            if ($status === 'cancelado') {
                $stmt = $this->connection->prepare("DELETE FROM pedidos WHERE id = ?");
                $success = $stmt->execute([$id]);
            } else {
                $stmt = $this->connection->prepare("UPDATE pedidos SET status = ? WHERE id = ?");
                $success = $stmt->execute([$status, $id]);
            }

            if ($success) {
                http_response_code(200);
                echo json_encode(['message' => 'Order updated successfully']);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Order not found']);
            }
        } catch (\PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database error']);
        }
    }
} 