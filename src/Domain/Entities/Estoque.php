<?php

namespace Domain\Entities;

class Estoque
{
    private ?int $id;
    private int $produto_id;
    private ?string $variacao;
    private int $quantidade;

    public function __construct(?int $id, int $produto_id, ?string $variacao, int $quantidade)
    {
        $this->id = $id;
        $this->produto_id = $produto_id;
        $this->variacao = $variacao;
        $this->quantidade = $quantidade;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProdutoId(): int
    {
        return $this->produto_id;
    }

    public function getVariacao(): ?string
    {
        return $this->variacao;
    }

    public function getQuantidade(): int
    {
        return $this->quantidade;
    }

    public function setQuantidade(int $quantidade): void
    {
        $this->quantidade = $quantidade;
    }

    public function setVariacao(?string $variacao): void
    {
        $this->variacao = $variacao;
    }
} 