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
    private ?array $cupom = null;
    private float $desconto = 0;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function obterCarrinho(): self
    {
        $instance = new self();
        
        // Initialize empty cart if no session data exists
        if (!isset($_SESSION['cart_data'])) {
            $_SESSION['cart_data'] = [
                'items' => [],
                'subtotal' => 0,
                'frete' => 0,
                'total' => 0,
                'cupom' => null,
                'desconto' => 0
            ];
        }

        // Get cart data, handling both serialized and array formats
        $data = $_SESSION['cart_data'];
        if (is_string($data)) {
            try {
                $data = unserialize($data);
            } catch (\Exception $e) {
                // If unserialization fails, start with empty cart
                $data = [
                    'items' => [],
                    'subtotal' => 0,
                    'frete' => 0,
                    'total' => 0,
                    'cupom' => null,
                    'desconto' => 0
                ];
            }
        }

        // Ensure we have an array with all required keys
        $data = array_merge([
            'items' => [],
            'subtotal' => 0,
            'frete' => 0,
            'total' => 0,
            'cupom' => null,
            'desconto' => 0
        ], (array)$data);

        $instance->items = (array)$data['items'];
        $instance->subtotal = (float)$data['subtotal'];
        $instance->frete = (float)$data['frete'];
        $instance->total = (float)$data['total'];
        $instance->cupom = $data['cupom'];
        $instance->desconto = (float)$data['desconto'];

        return $instance;
    }

    private function salvar(): void
    {
        $_SESSION['cart_data'] = [
            'items' => $this->items,
            'subtotal' => $this->subtotal,
            'frete' => $this->frete,
            'total' => $this->total,
            'cupom' => $this->cupom,
            'desconto' => $this->desconto
        ];
    }

    public function adicionarProduto(Produto $produto, int $quantidade = 1): void
    {
        $id = $produto->getId();
        
        $item = [
            'id' => $id,
            'nome' => $produto->getNome(),
            'preco' => $produto->getPreco(),
            'quantidade' => $quantidade
        ];

        if (isset($this->items[$id])) {
            $item['quantidade'] += $this->items[$id]['quantidade'];
        }

        $this->items[$id] = $item;
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

        $this->cupom = [
            'codigo' => $cupom->getCodigo(),
            'valor_desconto' => $cupom->getValorDesconto(),
            'valor_minimo' => $cupom->getValorMinimo()
        ];
        $this->desconto = $cupom->getValorDesconto();
        $this->calcularTotais();
        return true;
    }

    private function calcularTotais(): void
    {
        $this->subtotal = 0;
        foreach ($this->items as $item) {
            $this->subtotal += $item['preco'] * $item['quantidade'];
        }

        // Calculate shipping
        if ($this->subtotal >= 200) {
            $this->frete = 0;
        } elseif ($this->subtotal >= 52 && $this->subtotal <= 166.59) {
            $this->frete = 15;
        } else {
            $this->frete = 20;
        }

        $this->total = $this->subtotal + $this->frete - $this->desconto;
        $this->salvar();
    }

    public function getItems(): array
    {
        $result = [];
        foreach ($this->items as $id => $item) {
            $produto = new Produto(
                $item['id'],
                $item['nome'],
                $item['preco']
            );
            $result[$id] = [
                'produto' => $produto,
                'quantidade' => $item['quantidade']
            ];
        }
        return $result;
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
        if (!is_array($this->cupom)) {
            return null;
        }
        return new Cupom(
            $this->cupom['codigo'],
            $this->cupom['valor_desconto'],
            $this->cupom['valor_minimo']
        );
    }

    public function limpar(): void
    {
        $this->items = [];
        $this->subtotal = 0;
        $this->frete = 0;
        $this->total = 0;
        $this->cupom = null;
        $this->desconto = 0;
        $this->salvar();
    }
} 