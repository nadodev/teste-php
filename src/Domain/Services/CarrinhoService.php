<?php

namespace Domain\Services;

use Domain\Entities\Produto;
use Domain\Entities\Cupom;
use Infrastructure\Repositories\CupomRepository;

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

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->freteService = new FreteService();
        $this->cupomRepository = new CupomRepository();
        $this->items = $_SESSION['carrinho'] ?? [];
        $this->cupom_codigo = $_SESSION['cupom_codigo'] ?? null;
        
        // Se tiver um cupom salvo, carrega ele do banco
        if ($this->cupom_codigo) {
            $cupom = $this->cupomRepository->findByCodigo($this->cupom_codigo);
            if ($cupom && $cupom->isValido()) {
                $this->desconto = $cupom->getValorDesconto();
            } else {
                // Se o cupom não for mais válido, remove ele
                $this->cupom_codigo = null;
                $this->desconto = 0;
                unset($_SESSION['cupom_codigo']);
            }
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
        $instance->cupom_codigo = $data['cupom'];
        $instance->desconto = (float)$data['desconto'];

        return $instance;
    }

    private function salvar(): void
    {
        $_SESSION['carrinho'] = $this->items;
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
        foreach ($this->items as &$item) {
            if ($item['produto_id'] === $produto_id) {
                $item['quantidade'] = $quantidade;
                break;
            }
        }
        $this->salvar();
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
        // Aqui você pode adicionar lógica adicional antes de limpar o carrinho
        // como salvar o pedido no banco de dados, processar pagamento, etc.
        $this->limpar();
    }

    public function adicionarItem(int $produto_id, int $quantidade): void
    {
        $encontrado = false;
        foreach ($this->items as &$item) {
            if ($item['produto_id'] === $produto_id) {
                $item['quantidade'] += $quantidade;
                $encontrado = true;
                break;
            }
        }

        if (!$encontrado) {
            $this->items[] = [
                'produto_id' => $produto_id,
                'quantidade' => $quantidade
            ];
        }

        $this->salvar();
    }

    public function removerItem(int $produto_id): void
    {
        foreach ($this->items as $key => $item) {
            if ($item['produto_id'] === $produto_id) {
                unset($this->items[$key]);
                break;
            }
        }
        $this->items = array_values($this->items);
        $this->salvar();
    }

    public function removerCupom(): void
    {
        $this->cupom_codigo = null;
        $this->desconto = 0;
        unset($_SESSION['cupom_codigo']);
    }

    public function calcularSubtotal(array $produtos): float
    {
        $subtotal = 0;
        foreach ($this->items as $item) {
            if (isset($produtos[$item['produto_id']])) {
                $produto = $produtos[$item['produto_id']];
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