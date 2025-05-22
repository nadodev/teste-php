<?php

namespace Infrastructure\Repositories;

use Domain\Entities\Produto;
use Domain\Interfaces\ProdutoRepositoryInterface;
use Infrastructure\Database\Connection;
use PDO;

class ProdutoRepository implements ProdutoRepositoryInterface
{
    private PDO $connection;

    public function __construct()
    {
        $this->connection = Connection::getInstance();
    }

    public function findById(int $id): ?Produto
    {
        $stmt = $this->connection->prepare("SELECT * FROM produtos WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch();

        if (!$result) {
            return null;
        }

        return new Produto(
            (int) $result['id'],
            $result['nome'],
            (float) $result['preco']
        );
    }

    public function findAll(): array
    {
        $stmt = $this->connection->query("SELECT * FROM produtos");
        $results = $stmt->fetchAll();
        $produtos = [];

        foreach ($results as $result) {
            $produtos[] = new Produto(
                (int) $result['id'],
                $result['nome'],
                (float) $result['preco']
            );
        }

        return $produtos;
    }

    public function save(Produto $produto): Produto
    {
        $stmt = $this->connection->prepare("INSERT INTO produtos (nome, preco) VALUES (?, ?)");
        $stmt->execute([$produto->getNome(), $produto->getPreco()]);
        
        $id = (int) $this->connection->lastInsertId();
        $produto->setId($id);
        
        return $produto;
    }

    public function update(Produto $produto): bool
    {
        if ($produto->getId() === null) {
            return false;
        }

        $stmt = $this->connection->prepare("UPDATE produtos SET nome = ?, preco = ? WHERE id = ?");
        return $stmt->execute([
            $produto->getNome(),
            $produto->getPreco(),
            $produto->getId()
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->connection->prepare("DELETE FROM produtos WHERE id = ?");
        return $stmt->execute([$id]);
    }
} 