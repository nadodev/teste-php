<?php

namespace Domain\Entities;

class Cupom
{
    private string $codigo;
    private float $valor_desconto;
    private float $valor_minimo;
    private ?string $validade;

    public function __construct(string $codigo, float $valor_desconto, float $valor_minimo, ?string $validade = null)
    {
        $this->codigo = $codigo;
        $this->valor_desconto = $valor_desconto;
        $this->valor_minimo = $valor_minimo;
        $this->validade = $validade;
    }

    public function getCodigo(): string
    {
        return $this->codigo;
    }

    public function getValorDesconto(): float
    {
        return $this->valor_desconto;
    }

    public function getValorMinimo(): float
    {
        return $this->valor_minimo;
    }

    public function isValido(): bool
    {
        if ($this->validade === null) {
            return true;
        }
        return strtotime($this->validade) >= strtotime('today');
    }
} 