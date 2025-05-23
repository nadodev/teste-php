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

    private function debug($message, $data = null): void
    {
        $log = date('Y-m-d H:i:s') . " - " . $message;
        if ($data !== null) {
            $log .= "\n" . print_r($data, true);
        }
        error_log($log);
    }

    public function findByProdutoId(int $produto_id): array
    {
        $checkProdutoStmt = $this->connection->prepare("SELECT id FROM produtos WHERE id = ?");
        $checkProdutoStmt->execute([$produto_id]);
        $produtoExists = $checkProdutoStmt->fetch(PDO::FETCH_ASSOC);
        
        $stmt = $this->connection->prepare("SELECT * FROM estoque WHERE produto_id = ?");
        $stmt->execute([$produto_id]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $estoques = [];
        foreach ($results as $result) {
            $estoque = new Estoque(
                (int) $result['id'],
                (int) $result['produto_id'],
                $result['variacao'],
                (int) $result['quantidade']
            );
                     
            $estoques[] = $estoque;
        }

        return $estoques;
    }

    public function save(Estoque $estoque): Estoque
    {
        try {
            $stmt = $this->connection->prepare(
                "INSERT INTO estoque (produto_id, variacao, quantidade) VALUES (?, ?, ?)"
            );
            
            $params = [
                $estoque->getProdutoId(),
                $estoque->getVariacao(),
                $estoque->getQuantidade()
            ];
            
            $result = $stmt->execute($params);

            if (!$result) {
                $error = $stmt->errorInfo();
                throw new \RuntimeException("Erro ao salvar o estoque: " . implode(", ", $error));
            }

            $id = $this->connection->lastInsertId();

            $estoqueSalvo = new Estoque(
                (int) $id,
                $estoque->getProdutoId(),
                $estoque->getVariacao(),
                $estoque->getQuantidade()
            );

            return $estoqueSalvo;

        } catch (\Exception $e) {
            $this->debug("ERRO no salvamento do estoque", [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function update(Estoque $estoque): bool
    {
        try {

            if ($estoque->getQuantidade() < 0) {
                throw new \RuntimeException("A quantidade não pode ser negativa.");
            }

            if ($estoque->getId() === null) {
                throw new \RuntimeException("ID do estoque não pode ser nulo.");
            }

            $checkStmt = $this->connection->prepare("SELECT * FROM estoque WHERE id = ?");
            $checkStmt->execute([$estoque->getId()]);
            $exists = $checkStmt->fetch(PDO::FETCH_ASSOC);
            

            if (!$exists) {
                try {
                    $this->save($estoque);
                    return true;
                } catch (\Exception $e) {
                    $this->debug("Erro ao criar novo estoque", $e->getMessage());
                    return false;
                }
            }

            $sql = "UPDATE estoque SET variacao = :variacao, quantidade = :quantidade WHERE id = :id";
            $stmt = $this->connection->prepare($sql);
            
            $params = [
                ':variacao' => $estoque->getVariacao(),
                ':quantidade' => $estoque->getQuantidade(),
                ':id' => $estoque->getId()
            ];
            
            $result = $stmt->execute($params);

            if (!$result) {
                $error = $stmt->errorInfo();
                throw new \RuntimeException("Erro ao atualizar o estoque: " . implode(", ", $error));
            }

            $rowsAffected = $stmt->rowCount();

            if ($rowsAffected === 0) {
                try {
                    $this->save($estoque);
                    return true;
                } catch (\Exception $e) {
                    $this->debug("Erro ao criar novo estoque", $e->getMessage());
                    return false;
                }
            }

            $verifyStmt = $this->connection->prepare("SELECT * FROM estoque WHERE id = ?");
            $verifyStmt->execute([$estoque->getId()]);
            $updated = $verifyStmt->fetch(PDO::FETCH_ASSOC);
            
            return true;

        } catch (\Exception $e) {
            $this->debug("ERRO na atualização do estoque", [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    public function updateQuantidade(int $id, int $quantidade): bool
    {
        if ($quantidade < 0) {
            throw new \RuntimeException("A quantidade não pode ser negativa.");
        }

        $stmt = $this->connection->prepare("UPDATE estoque SET quantidade = ? WHERE id = ? AND quantidade >= 0");
        $result = $stmt->execute([$quantidade, $id]);

        if (!$result) {
            throw new \RuntimeException("Erro ao atualizar a quantidade: " . implode(", ", $stmt->errorInfo()));
        }

        if ($stmt->rowCount() === 0) {
            throw new \RuntimeException("Não foi possível atualizar o estoque. Quantidade insuficiente ou ID não encontrado.");
        }

        return true;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->connection->prepare("DELETE FROM estoque WHERE id = ?");
        return $stmt->execute([$id]);
    }
} 