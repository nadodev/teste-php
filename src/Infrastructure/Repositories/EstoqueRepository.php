<?php

namespace Infrastructure\Repositories;

use Domain\Entities\Estoque;
use Domain\Interfaces\EstoqueRepositoryInterface;
use Infrastructure\Database\Connection;
use PDO;

class EstoqueRepository implements EstoqueRepositoryInterface
{
    private PDO $connection;

    public function __construct()
    {
        $this->connection = Connection::getInstance();
    }

    public function beginTransaction(): void
    {
        $this->connection->beginTransaction();
    }

    public function commit(): void
    {
        $this->connection->commit();
    }

    public function rollback(): void
    {
        $this->connection->rollBack();
    }

    public function findByProdutoId(int $produto_id): array
    {
        $stmt = $this->connection->prepare("SELECT * FROM estoque WHERE produto_id = ?");
        $stmt->execute([$produto_id]);
        $results = $stmt->fetchAll();
        $estoques = [];

        foreach ($results as $result) {
            $estoques[] = new Estoque(
                $result['id'],
                $result['produto_id'],
                $result['variacao'],
                $result['quantidade']
            );
        }

        return $estoques;
    }

    public function save(Estoque $estoque): Estoque
    {
        $stmt = $this->connection->prepare(
            "INSERT INTO estoque (produto_id, variacao, quantidade) VALUES (?, ?, ?)"
        );
        $stmt->execute([
            $estoque->getProdutoId(),
            $estoque->getVariacao(),
            $estoque->getQuantidade()
        ]);
        
        return new Estoque(
            $this->connection->lastInsertId(),
            $estoque->getProdutoId(),
            $estoque->getVariacao(),
            $estoque->getQuantidade()
        );
    }

    public function update(Estoque $estoque): bool
    {
        $stmt = $this->connection->prepare(
            "UPDATE estoque SET variacao = ?, quantidade = ? WHERE id = ? AND quantidade >= 0"
        );
        $result = $stmt->execute([
            $estoque->getVariacao(),
            $estoque->getQuantidade(),
            $estoque->getId()
        ]);

        if ($stmt->rowCount() === 0) {
            throw new \RuntimeException("Não foi possível atualizar o estoque. Quantidade insuficiente.");
        }

        return $result;
    }

    public function updateQuantidade(int $id, int $quantidade): bool
    {
        if ($quantidade < 0) {
            throw new \RuntimeException("A quantidade não pode ser negativa.");
        }

        $stmt = $this->connection->prepare("UPDATE estoque SET quantidade = ? WHERE id = ? AND quantidade >= 0");
        $result = $stmt->execute([$quantidade, $id]);

        if ($stmt->rowCount() === 0) {
            throw new \RuntimeException("Não foi possível atualizar o estoque. Quantidade insuficiente.");
        }

        return $result;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->connection->prepare("DELETE FROM estoque WHERE id = ?");
        return $stmt->execute([$id]);
    }
} 