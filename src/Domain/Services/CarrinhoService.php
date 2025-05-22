<?php

namespace Domain\Services;

use Domain\Entities\Produto;
use Domain\Entities\Cupom;
use Infrastructure\Repositories\CupomRepository;
use Infrastructure\Repositories\ProdutoRepository;
use Helpers\CarrinhoSessionStorage;

class CarrinhoService
{
    private array $items = [];
    private float $subtotal = 0;
    private float $frete = 0;
    private float $total = 0;
    private ?string $cupom_codigo = null;
    private float $desconto = 0;
    private FreteService $freteService;
    private CupomRepository $cupomRepository;
    private ProdutoRepository $produtoRepository;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->freteService = new FreteService();
        $this->cupomRepository = new CupomRepository();
        $this->produtoRepository = new ProdutoRepository();
        $this->items = $_SESSION['carrinho'] ?? [];
        $this->cupom_codigo = $_SESSION['cupom_codigo'] ?? null;
        
        if ($this->cupom_codigo) {
            $cupom = $this->cupomRepository->findByCodigo($this->cupom_codigo);
            if ($cupom && $cupom->isValido()) {
                $this->desconto = $cupom->getValorDesconto();
            } else {
                $this->cupom_codigo = null;
                $this->desconto = 0;
                unset($_SESSION['cupom_codigo']);
            }
        }
    }

    private function salvar(): void
    {
        $_SESSION['carrinho'] = $this->items;
    }

    public function adicionarItem(int $produto_id, int $quantidade): void
    {
        if (isset($this->items[$produto_id])) {
            $this->items[$produto_id]['quantidade'] += $quantidade;
        } else {
            $this->items[$produto_id] = [
                'produto_id' => $produto_id,
                'quantidade' => $quantidade
            ];
        }
        
        $this->calcularTotais();
        $this->salvar();
    }

    public function removerItem(int $produto_id): void
    {
        if (isset($this->items[$produto_id])) {
            unset($this->items[$produto_id]);
            $this->calcularTotais();
            $this->salvar();
        }
    }

    public function atualizarQuantidade(int $produto_id, int $quantidade): void
    {
        if (isset($this->items[$produto_id])) {
            $this->items[$produto_id]['quantidade'] = $quantidade;
            $this->calcularTotais();
            $this->salvar();
        }
    }

    public function obterCarrinho(): int
    {

        $quantidadeTotal = 0;
        foreach ($this->items as $item) {
            $quantidadeTotal += $item['quantidade'];
        }
        return $quantidadeTotal;
    }

    public function aplicarCupom(?Cupom $cupom): void
    {
        if ($cupom && $cupom->isValido()) {
            $this->cupom_codigo = $cupom->getCodigo();
            $this->desconto = $cupom->getValorDesconto();
            $_SESSION['cupom_codigo'] = $cupom->getCodigo();
        } else {
            $this->cupom_codigo = null;
            $this->desconto = 0;
            unset($_SESSION['cupom_codigo']);
        }
    }

    private function calcularTotais(): void
    {
        $this->subtotal = 0;
        foreach ($this->items as $produto_id => $item) {
            $produto = $this->produtoRepository->findById($produto_id);
            if ($produto) {
                $this->subtotal += $produto->getPreco() * $item['quantidade'];
            }
        }

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
        if (!$this->cupom_codigo) {
            return null;
        }
        return $this->cupomRepository->findByCodigo($this->cupom_codigo);
    }

    public function limpar(): void
    {
        $this->items = [];
        $this->cupom_codigo = null;
        $this->desconto = 0;
        unset($_SESSION['carrinho'], $_SESSION['cupom_codigo']);
    }

    public function finalizarCompra(): void
    {
        $this->limpar();
    }

    public function calcularSubtotal(array $produtos): float
    {
        $subtotal = 0;
        foreach ($this->items as $produto_id => $item) {
            if (isset($produtos[$produto_id])) {
                $produto = $produtos[$produto_id];
                $subtotal += $produto->getPreco() * $item['quantidade'];
            }
        }
        return $subtotal;
    }

    public function calcularDesconto(float $subtotal): float
    {
        $cupom = $this->getCupom();
        if ($cupom && $cupom->isValido()) {
            if ($subtotal >= $cupom->getValorMinimo()) {
                return $cupom->getValorDesconto();
            }
        }
        return 0;
    }
} 