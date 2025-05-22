<?php

namespace Domain\Entities;

class Produto
{
    private int $id;
    private string $nome;
    private float $preco;

    public function __construct(int $id, string $nome, float $preco)
    {
        $this->id = $id;
        $this->nome = $nome;
        $this->preco = $preco;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function getPreco(): float
    {
        return $this->preco;
    }

    public function setNome(string $nome): void
    {
        $this->nome = $nome;
    }

    public function setPreco(float $preco): void
    {
        $this->preco = $preco;
    }
} 