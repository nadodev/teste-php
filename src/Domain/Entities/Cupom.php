<?php

namespace Domain\Entities;

class Cupom
{
    private ?int $id;
    private string $codigo;
    private float $valor_desconto;
    private \DateTime $validade;
    private float $valor_minimo;

    public function __construct(
        ?int $id,
        string $codigo,
        float $valor_desconto,
        \DateTime $validade,
        float $valor_minimo
    ) {
        $this->id = $id;
        $this->codigo = $codigo;
        $this->valor_desconto = $valor_desconto;
        $this->validade = $validade;
        $this->valor_minimo = $valor_minimo;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodigo(): string
    {
        return $this->codigo;
    }

    public function getValorDesconto(): float
    {
        return $this->valor_desconto;
    }

    public function getValidade(): \DateTime
    {
        return $this->validade;
    }

    public function getValorMinimo(): float
    {
        return $this->valor_minimo;
    }

    public function isValido(): bool
    {
        return $this->validade >= new \DateTime();
    }
} 