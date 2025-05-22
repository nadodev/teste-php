<?php

namespace Infrastructure\Repositories;

use Domain\Entities\Cupom;
use Domain\Interfaces\CupomRepositoryInterface;
use Infrastructure\Database\Connection;
use PDO;

class CupomRepository implements CupomRepositoryInterface
{
    private PDO $connection;

    public function __construct()
    {
        $this->connection = Connection::getInstance();
    }

    public function findByCodigo(string $codigo): ?Cupom
    {
        $stmt = $this->connection->prepare("SELECT * FROM cupons WHERE codigo = ?");
        $stmt->execute([$codigo]);
        $result = $stmt->fetch();

        if (!$result) {
            return null;
        }

        return new Cupom(
            $result['codigo'],
            (float) $result['valor_desconto'],
            (float) $result['valor_minimo'],
            $result['validade']
        );
    }

    public function findAll(): array
    {
        $stmt = $this->connection->query("SELECT * FROM cupons");
        $results = $stmt->fetchAll();
        $cupons = [];

        foreach ($results as $result) {
            $cupons[] = new Cupom(
                $result['codigo'],
                (float) $result['valor_desconto'],
                (float) $result['valor_minimo'],
                $result['validade']
            );
        }

        return $cupons;
    }

    public function save(Cupom $cupom): Cupom
    {
        $stmt = $this->connection->prepare(
            "INSERT INTO cupons (codigo, valor_desconto, valor_minimo, validade) VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([
            $cupom->getCodigo(),
            $cupom->getValorDesconto(),
            $cupom->getValorMinimo(),
            $cupom->getValidade()
        ]);

        return $cupom; 
    }

    public function update(Cupom $cupom): bool
    {
        $stmt = $this->connection->prepare(
            "UPDATE cupons SET valor_desconto = ?, valor_minimo = ?, validade = ? WHERE codigo = ?"
        );
        return $stmt->execute([
            $cupom->getValorDesconto(),
            $cupom->getValorMinimo(),
            $cupom->getValidade(),
            $cupom->getCodigo()
        ]);
    }

    public function delete(string $codigo): bool
    {
        $stmt = $this->connection->prepare("DELETE FROM cupons WHERE codigo = ?");
        return $stmt->execute([$codigo]);
    }
} 