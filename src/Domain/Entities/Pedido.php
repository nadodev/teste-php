<?php

namespace Domain\Entities;

class Pedido
{
    private ?int $id;
    private string $email;
    private string $cidade;
    private string $estado;
    private float $subtotal;
    private float $desconto;
    private float $frete;
    private float $total;
    private string $status;
    private string $created_at;
    private array $itens = [];

    public function __construct(
        ?int $id,
        string $email,
        string $cidade,
        string $estado,
        float $subtotal,
        float $desconto,
        float $frete,
        float $total,
        string $status = 'confirmado',
        string $created_at = ''
    ) {
        $this->id = $id;
        $this->email = $email;
        $this->cidade = $cidade;
        $this->estado = $estado;
        $this->subtotal = $subtotal;
        $this->desconto = $desconto;
        $this->frete = $frete;
        $this->total = $total;
        $this->status = $status;
        $this->created_at = $created_at ?: date('Y-m-d H:i:s');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getCidade(): string
    {
        return $this->cidade;
    }

    public function getEstado(): string
    {
        return $this->estado;
    }

    public function getSubtotal(): float
    {
        return $this->subtotal;
    }

    public function getDesconto(): float
    {
        return $this->desconto;
    }

    public function getFrete(): float
    {
        return $this->frete;
    }

    public function getTotal(): float
    {
        return $this->total;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getCreatedAt(): string
    {
        return $this->created_at;
    }

    public function setItens(array $itens): void
    {
        $this->itens = $itens;
    }

    public function getItens(): array
    {
        return $this->itens;
    }
} 