<?php

namespace Presentation\Controllers;

use Domain\Entities\Produto;
use Domain\Services\FreteService;
use Domain\Services\CarrinhoService;
use Infrastructure\Repositories\ProdutoRepository;
use Infrastructure\Repositories\EstoqueRepository;
use Infrastructure\Repositories\CupomRepository;

class CarrinhoController
{
    private ProdutoRepository $produtoRepository;
    private EstoqueRepository $estoqueRepository;
    private CupomRepository $cupomRepository;
    private FreteService $freteService;
    private CarrinhoService $carrinhoService;

    public function __construct()
    {
        $this->produtoRepository = new ProdutoRepository();
        $this->estoqueRepository = new EstoqueRepository();
        $this->cupomRepository = new CupomRepository();
        $this->freteService = new FreteService();
        $this->carrinhoService = new CarrinhoService();
    }

    public function index(): void
    {
        $itens = [];
        $produtos = [];
        $subtotal = 0;

        // Carregar produtos
        foreach ($this->carrinhoService->getItems() as $item) {
            $produto = $this->produtoRepository->findById($item['produto_id']);
            if ($produto) {
                $produtos[$produto->getId()] = $produto;
                $estoque = $this->estoqueRepository->findByProdutoId($produto->getId())[0] ?? null;
                $quantidade = $item['quantidade'];
                $subtotal += $produto->getPreco() * $quantidade;

                $itens[] = [
                    'produto' => $produto,
                    'estoque' => $estoque,
                    'quantidade' => $quantidade,
                    'subtotal' => $produto->getPreco() * $quantidade
                ];
            }
        }

        // Calcular valores
        $cupom = $this->carrinhoService->getCupom();
        $desconto = $cupom && $cupom->isValido() ? $cupom->getValorDesconto() : 0;
        $subtotalComDesconto = max(0, $subtotal - $desconto);
        
        $frete = $this->freteService->calcularFrete($subtotalComDesconto);
        $descricaoFrete = $this->freteService->getDescricaoFrete($subtotalComDesconto);
        $valorRestanteFreteGratis = $this->freteService->getValorRestanteParaFreteGratis($subtotalComDesconto);
        $total = $subtotalComDesconto + $frete;

        require_once __DIR__ . '/../Views/carrinho/index.php';
    }

    public function adicionar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?route=carrinho');
            exit;
        }

        $produto_id = (int) ($_POST['produto_id'] ?? 0);
        $quantidade = (int) ($_POST['quantidade'] ?? 1);

        // Validar produto
        $produto = $this->produtoRepository->findById($produto_id);
        if (!$produto) {
            $_SESSION['message'] = [
                'type' => 'danger',
                'text' => 'Produto não encontrado.'
            ];
            header('Location: ?route=produtos');
            exit;
        }

        // Validar estoque
        $estoque = $this->estoqueRepository->findByProdutoId($produto->getId())[0] ?? null;
        if (!$estoque || $estoque->getQuantidade() < $quantidade) {
            $_SESSION['message'] = [
                'type' => 'danger',
                'text' => 'Quantidade indisponível em estoque.'
            ];
            header('Location: ?route=produtos');
            exit;
        }

        // Adicionar ao carrinho
        $this->carrinhoService->adicionarItem($produto_id, $quantidade);

        $_SESSION['message'] = [
            'type' => 'success',
            'text' => 'Produto adicionado ao carrinho!'
        ];
        header('Location: ?route=carrinho');
        exit;
    }

    public function remover(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?route=carrinho');
            exit;
        }

        $produto_id = (int) ($_POST['produto_id'] ?? 0);
        $this->carrinhoService->removerItem($produto_id);

        $_SESSION['message'] = [
            'type' => 'success',
            'text' => 'Produto removido do carrinho!'
        ];
        header('Location: ?route=carrinho');
        exit;
    }

    public function atualizar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?route=carrinho');
            exit;
        }

        $produto_id = (int) ($_POST['produto_id'] ?? 0);
        $quantidade = (int) ($_POST['quantidade'] ?? 1);

        if ($quantidade < 1) {
            $_SESSION['message'] = [
                'type' => 'danger',
                'text' => 'Quantidade inválida.'
            ];
            header('Location: ?route=carrinho');
            exit;
        }

        // Validar produto e estoque
        $produto = $this->produtoRepository->findById($produto_id);
        $estoque = $this->estoqueRepository->findByProdutoId($produto_id)[0] ?? null;

        if (!$produto || !$estoque) {
            $_SESSION['message'] = [
                'type' => 'danger',
                'text' => 'Produto não encontrado.'
            ];
            header('Location: ?route=carrinho');
            exit;
        }

        if ($quantidade > $estoque->getQuantidade()) {
            $_SESSION['message'] = [
                'type' => 'danger',
                'text' => 'Quantidade indisponível em estoque.'
            ];
            header('Location: ?route=carrinho');
            exit;
        }

        // Atualizar quantidade
        $this->carrinhoService->atualizarQuantidade($produto_id, $quantidade);

        $_SESSION['message'] = [
            'type' => 'success',
            'text' => 'Carrinho atualizado!'
        ];
        header('Location: ?route=carrinho');
        exit;
    }

    public function aplicarCupom(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?route=carrinho');
            exit;
        }

        $codigo = $_POST['codigo'] ?? '';
        $cupom = $this->cupomRepository->findByCodigo($codigo);

        if (!$cupom) {
            $_SESSION['message'] = [
                'type' => 'danger',
                'text' => 'Cupom não encontrado.'
            ];
            header('Location: ?route=carrinho');
            exit;
        }

        if (!$cupom->isValido()) {
            $_SESSION['message'] = [
                'type' => 'danger',
                'text' => 'Este cupom está expirado.'
            ];
            header('Location: ?route=carrinho');
            exit;
        }

        // Verificar valor mínimo
        $subtotal = $this->carrinhoService->calcularSubtotal(
            $this->getProdutosFromCarrinho()
        );

        if ($subtotal < $cupom->getValorMinimo()) {
            $_SESSION['message'] = [
                'type' => 'danger',
                'text' => sprintf(
                    'O valor mínimo para este cupom é R$ %s',
                    number_format($cupom->getValorMinimo(), 2, ',', '.')
                )
            ];
            header('Location: ?route=carrinho');
            exit;
        }

        $this->carrinhoService->aplicarCupom($cupom);

        $_SESSION['message'] = [
            'type' => 'success',
            'text' => sprintf(
                'Cupom aplicado! Desconto de R$ %s',
                number_format($cupom->getValorDesconto(), 2, ',', '.')
            )
        ];
        header('Location: ?route=carrinho');
        exit;
    }

    public function removerCupom(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?route=carrinho');
            exit;
        }

        $this->carrinhoService->removerCupom();

        $_SESSION['message'] = [
            'type' => 'success',
            'text' => 'Cupom removido com sucesso!'
        ];
        header('Location: ?route=carrinho');
        exit;
    }

    public function finalizar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?route=carrinho');
            exit;
        }

        // Aqui você pode adicionar validações adicionais
        // como verificar se há itens no carrinho, se o pagamento foi processado, etc.

        $this->carrinhoService->finalizarCompra();

        $_SESSION['message'] = [
            'type' => 'success',
            'text' => 'Compra finalizada com sucesso! Obrigado pela preferência.'
        ];
        
        header('Location: ?route=produtos');
        exit;
    }

    private function getProdutosFromCarrinho(): array
    {
        $produtos = [];
        foreach ($this->carrinhoService->getItems() as $item) {
            $produto = $this->produtoRepository->findById($item['produto_id']);
            if ($produto) {
                $produtos[$produto->getId()] = $produto;
            }
        }
        return $produtos;
    }
} 