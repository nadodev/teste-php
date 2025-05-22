<?php

namespace Domain\Services;

use Domain\Entities\Produto;
use Domain\Entities\Cupom;

class CarrinhoService
{
    private array $items = [];
    private float $subtotal = 0;
    private float $frete = 0;
    private float $total = 0;
    private ?Cupom $cupom = null;
    private float $desconto = 0;

    public function adicionarProduto(Produto $produto, int $quantidade = 1): void
    {
        $id = $produto->getId();
        if (isset($this->items[$id])) {
            $this->items[$id]['quantidade'] += $quantidade;
        } else {
            $this->items[$id] = [
                'produto' => $produto,
                'quantidade' => $quantidade
            ];
        }
        $this->calcularTotais();
    }

    public function removerProduto(int $produto_id): void
    {
        if (isset($this->items[$produto_id])) {
            unset($this->items[$produto_id]);
            $this->calcularTotais();
        }
    }

    public function atualizarQuantidade(int $produto_id, int $quantidade): void
    {
        if (isset($this->items[$produto_id])) {
            $this->items[$produto_id]['quantidade'] = $quantidade;
            $this->calcularTotais();
        }
    }

    public function aplicarCupom(?Cupom $cupom): bool
    {
        if ($cupom === null) {
            $this->cupom = null;
            $this->desconto = 0;
            $this->calcularTotais();
            return true;
        }

        if (!$cupom->isValido()) {
            return false;
        }

        if ($this->subtotal < $cupom->getValorMinimo()) {
            return false;
        }

        $this->cupom = $cupom;
        $this->desconto = $cupom->getValorDesconto();
        $this->calcularTotais();
        return true;
    }

    public function calcularFrete(): float
    {
        if ($this->subtotal >= 200) {
            $this->frete = 0; // Frete grátis
        } elseif ($this->subtotal >= 52 && $this->subtotal <= 166.59) {
            $this->frete = 15;
        } else {
            $this->frete = 20;
        }
        
        $this->calcularTotais();
        return $this->frete;
    }

    private function calcularTotais(): void
    {
        $this->subtotal = 0;
        foreach ($this->items as $item) {
            $this->subtotal += $item['produto']->getPreco() * $item['quantidade'];
        }
        
        $this->calcularFrete();
        $this->total = $this->subtotal + $this->frete - $this->desconto;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function getSubtotal(): float
    {
        return $this->subtotal;
    }

    public function getFrete(): float
    {
        return $this->frete;
    }

    public function getTotal(): float
    {
        return $this->total;
    }

    public function getDesconto(): float
    {
        return $this->desconto;
    }

    public function getCupom(): ?Cupom
    {
        return $this->cupom;
    }

    public function limpar(): void
    {
        $this->items = [];
        $this->subtotal = 0;
        $this->frete = 0;
        $this->total = 0;
        $this->cupom = null;
        $this->desconto = 0;
    }
} 