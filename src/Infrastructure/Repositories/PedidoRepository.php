<?php

namespace Infrastructure\Repositories;

use Domain\Entities\Pedido;
use Infrastructure\Database\Connection;
use PDO;

class PedidoRepository
{
    private PDO $connection;

    public function __construct()
    {
        $this->connection = Connection::getInstance();
        $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $this->connection->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);
        $this->connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    }

    public function save(Pedido $pedido, array $itens, bool $useTransaction = true): Pedido
    {
        try {
            if ($useTransaction) {
                $this->connection->beginTransaction();
            }

            // Insere o pedido
            $stmt = $this->connection->prepare(
                "INSERT INTO pedidos (email, subtotal, frete, total, status) 
                 VALUES (?, ?, ?, ?, ?)"
            );
            
            $stmt->execute([
                $pedido->getEmail(),
                $pedido->getSubtotal(),
                $pedido->getFrete(),
                $pedido->getTotal(),
                $pedido->getStatus()
            ]);

            $pedidoId = $this->connection->lastInsertId();

            // Insere os itens do pedido
            $stmtItem = $this->connection->prepare(
                "INSERT INTO pedido_itens (pedido_id, produto_id, quantidade, preco_unitario, subtotal) 
                 VALUES (?, ?, ?, ?, ?)"
            );

            foreach ($itens as $item) {
                $stmtItem->execute([
                    $pedidoId,
                    $item['produto']->getId(),
                    $item['quantidade'],
                    $item['produto']->getPreco(),
                    $item['subtotal']
                ]);
            }

            if ($useTransaction) {
                $this->connection->commit();
            }

            return $this->findById($pedidoId);

        } catch (\Exception $e) {
            if ($useTransaction) {
                $this->connection->rollBack();
            }
            throw $e;
        }
    }

    public function findById(int $id): ?Pedido
    {
        $stmt = $this->connection->prepare(
            "SELECT id, email, subtotal, frete, total, status, data_criacao 
             FROM pedidos 
             WHERE id = ?"
        );
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            return null;
        }

        $pedido = new Pedido(
            (int)$result['id'],
            $result['email'],
            'N/A',
            'NA',
            (float)$result['subtotal'],
            0.00, 
            (float)$result['frete'],
            (float)$result['total'],
            $result['status'],
            $result['data_criacao']
        );

        // Carrega os itens do pedido
        $pedido->setItens($this->findItensByPedidoId($id));

        return $pedido;
    }

    public function findAll(): array
    {
        $stmt = $this->connection->prepare(
            "SELECT id, email, subtotal, frete, total, status, data_criacao 
             FROM pedidos 
             ORDER BY id DESC"
        );
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $pedidos = [];

        foreach ($results as $result) {
            $pedido = new Pedido(
                (int)$result['id'],
                $result['email'],
                'N/A',
                'NA',
                (float)$result['subtotal'],
                0.00,
                (float)$result['frete'],
                (float)$result['total'],
                $result['status'],
                $result['data_criacao']
            );

            $pedido->setItens($this->findItensByPedidoId($result['id']));
            $pedidos[] = $pedido;
        }

        return $pedidos;
    }

    private function findItensByPedidoId(int $pedidoId): array
    {
        $stmt = $this->connection->prepare(
            "SELECT pi.*, p.nome as produto_nome, p.preco as preco_atual
             FROM pedido_itens pi
             JOIN produtos p ON p.id = pi.produto_id
             WHERE pi.pedido_id = ?"
        );
        $stmt->execute([$pedidoId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} 