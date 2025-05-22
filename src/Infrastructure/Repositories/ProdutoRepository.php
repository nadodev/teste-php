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
            throw new \RuntimeException("ID do produto não pode ser nulo.");
        }

        $stmt = $this->connection->prepare("UPDATE produtos SET nome = ?, preco = ? WHERE id = ?");
        $result = $stmt->execute([
            $produto->getNome(),
            $produto->getPreco(),
            $produto->getId()
        ]);


        if (!$result) {
            throw new \RuntimeException("Erro ao atualizar o produto: " . implode(", ", $stmt->errorInfo()));
        }

        return true;
    }

    public function delete(int $id): bool
    {
        try {
            $this->connection->beginTransaction();

            $stmtPedidoItens = $this->connection->prepare("DELETE FROM pedido_itens WHERE produto_id = ?");
            $stmtPedidoItens->execute([$id]);
            $stmtEstoque = $this->connection->prepare("DELETE FROM estoque WHERE produto_id = ?");
            $stmtEstoque->execute([$id]);
            $stmtProduto = $this->connection->prepare("DELETE FROM produtos WHERE id = ?");
            $stmtProduto->execute([$id]);

            $this->connection->commit();
            return true;
        } catch (\Exception $e) {
            $this->connection->rollBack();
            throw new \RuntimeException("Erro ao excluir o produto: " . $e->getMessage());
        }
    }
} 