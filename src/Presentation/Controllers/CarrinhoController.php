<?php

namespace Presentation\Controllers;

use Domain\Entities\Produto;
use Domain\Services\FreteService;
use Domain\Services\CarrinhoService;
use Infrastructure\Repositories\ProdutoRepository;
use Infrastructure\Repositories\EstoqueRepository;
use Infrastructure\Repositories\CupomRepository;
use Presentation\View;

class CarrinhoController
{
    private ProdutoRepository $produtoRepository;
    private EstoqueRepository $estoqueRepository;
    private CupomRepository $cupomRepository;
    private FreteService $freteService;
    private CarrinhoService $carrinhoService;
    private View $view;

    public function __construct()
    {
        $this->produtoRepository = new ProdutoRepository();
        $this->estoqueRepository = new EstoqueRepository();
        $this->cupomRepository = new CupomRepository();
        $this->freteService = new FreteService();
        $this->carrinhoService = new CarrinhoService();
        $this->view = new View();
    }

    public function index(): void
    {
        $itens = [];
        $produtos = [];
        $subtotal = 0;

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

        $cupom = $this->carrinhoService->getCupom();
        $desconto = $cupom && $cupom->isValido() ? $cupom->getValorDesconto() : 0;
        $subtotalComDesconto = max(0, $subtotal - $desconto);
        
        $frete = $this->freteService->calcularFrete($subtotalComDesconto);
        $descricaoFrete = $this->freteService->getDescricaoFrete($subtotalComDesconto);
        $valorRestanteFreteGratis = $this->freteService->getValorRestanteParaFreteGratis($subtotalComDesconto);
        $total = $subtotalComDesconto + $frete;

        $this->view->render('carrinho/index', [
            'itens' => $itens,
            'produtos' => $produtos,
            'subtotal' => $subtotal,
            'cupom' => $cupom,
            'desconto' => $desconto,
            'frete' => $frete,
            'descricaoFrete' => $descricaoFrete,
            'valorRestanteFreteGratis' => $valorRestanteFreteGratis,
            'total' => $total
        ]);
    }

    public function adicionar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /carrinho');
            exit;
        }

        $produto_id = (int) ($_POST['produto_id'] ?? 0);
        $quantidade = (int) ($_POST['quantidade'] ?? 1);

        $produto = $this->produtoRepository->findById($produto_id);
        if (!$produto) {
            $_SESSION['message'] = [
                'type' => 'danger',
                'text' => 'Produto não encontrado.'
            ];
            header('Location: /produtos');
            exit;
        }

        $estoque = $this->estoqueRepository->findByProdutoId($produto->getId())[0] ?? null;
        if (!$estoque || $estoque->getQuantidade() < $quantidade) {
            $_SESSION['message'] = [
                'type' => 'danger',
                'text' => 'Quantidade indisponível em estoque.'
            ];
            header('Location: /produtos');
            exit;
        }

        $this->carrinhoService->adicionarItem($produto_id, $quantidade);

        $_SESSION['message'] = [
            'type' => 'success',
            'text' => 'Produto adicionado ao carrinho!'
        ];
        header('Location: /carrinho');
        exit;
    }

    public function remover(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /carrinho');
            exit;
        }

        $produto_id = (int) ($_POST['produto_id'] ?? 0);
        $this->carrinhoService->removerItem($produto_id);

        $_SESSION['message'] = [
            'type' => 'success',
            'text' => 'Produto removido do carrinho!'
        ];
        header('Location: /carrinho');
        exit;
    }

    public function atualizar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /carrinho');
            exit;
        }

        $produto_id = (int) ($_POST['produto_id'] ?? 0);
        $quantidade = (int) ($_POST['quantidade'] ?? 1);

        if ($quantidade < 1) {
            $_SESSION['message'] = [
                'type' => 'danger',
                'text' => 'Quantidade inválida.'
            ];
            header('Location: /carrinho');
            exit;
        }

        $produto = $this->produtoRepository->findById($produto_id);
        $estoque = $this->estoqueRepository->findByProdutoId($produto_id)[0] ?? null;

        if (!$produto || !$estoque) {
            $_SESSION['message'] = [
                'type' => 'danger',
                'text' => 'Produto não encontrado.'
            ];
            header('Location: /carrinho');
            exit;
        }

        if ($quantidade > $estoque->getQuantidade()) {
            $_SESSION['message'] = [
                'type' => 'danger',
                'text' => 'Quantidade indisponível em estoque.'
            ];
            header('Location: /carrinho');
            exit;
        }

        $this->carrinhoService->atualizarQuantidade($produto_id, $quantidade);

        $_SESSION['message'] = [
            'type' => 'success',
            'text' => 'Carrinho atualizado!'
        ];
        header('Location: /carrinho');
        exit;
    }

    public function aplicarCupom(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /carrinho');
            exit;
        }

        $codigo = $_POST['codigo'] ?? '';
        $cupom = $this->cupomRepository->findByCodigo($codigo);

        if (!$cupom) {
            $_SESSION['message'] = [
                'type' => 'danger',
                'text' => 'Cupom não encontrado.'
            ];
            header('Location: /carrinho');
            exit;
        }

        if (!$cupom->isValido()) {
            $_SESSION['message'] = [
                'type' => 'danger',
                'text' => 'Este cupom está expirado.'
            ];
            header('Location: /carrinho');
            exit;
        }

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
            header('Location: /carrinho');
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
        header('Location: /carrinho');
        exit;
    }

    public function removerCupom(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /carrinho');
            exit;
        }

        $this->carrinhoService->removerCupom();

        $_SESSION['message'] = [
            'type' => 'success',
            'text' => 'Cupom removido com sucesso!'
        ];
        header('Location: /carrinho');
        exit;
    }

    public function finalizar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /carrinho');
            exit;
        }

        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        if (!$email) {
            $_SESSION['message'] = [
                'type' => 'danger',
                'text' => 'Por favor, forneça um email válido.'
            ];
            header('Location: /carrinho');
            exit;
        }

        $endereco = [
            'cidade' => htmlspecialchars(trim($_POST['cidade'] ?? ''), ENT_QUOTES, 'UTF-8'),
            'estado' => htmlspecialchars(trim($_POST['estado'] ?? ''), ENT_QUOTES, 'UTF-8')
        ];

        if (empty($endereco['cidade']) || empty($endereco['estado'])) {
            $_SESSION['message'] = [
                'type' => 'danger',
                'text' => 'Por favor, preencha a cidade e o estado.'
            ];
            header('Location: /carrinho');
            exit;
        }

        $estados = [
            'AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS',
            'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC',
            'SP', 'SE', 'TO'
        ];
        if (!in_array($endereco['estado'], $estados)) {
            $_SESSION['message'] = [
                'type' => 'danger',
                'text' => 'Por favor, selecione um estado válido.'
            ];
            header('Location: /carrinho');
            exit;
        }

        $itens = [];
        $produtos = [];
        $subtotal = 0;

        foreach ($this->carrinhoService->getItems() as $item) {
            $produto = $this->produtoRepository->findById($item['produto_id']);
            if ($produto) {
                $produtos[$produto->getId()] = $produto;
                $estoque = $this->estoqueRepository->findByProdutoId($produto->getId())[0] ?? null;
                $quantidade = $item['quantidade'];
                $subtotal += $produto->getPreco() * $quantidade;

                if (!$estoque || $estoque->getQuantidade() < $quantidade) {
                    $_SESSION['message'] = [
                        'type' => 'danger',
                        'text' => "Produto {$produto->getNome()} não possui estoque suficiente."
                    ];
                    header('Location: /carrinho');
                    exit;
                }

                $itens[] = [
                    'produto' => $produto,
                    'estoque' => $estoque,
                    'quantidade' => $quantidade,
                    'subtotal' => $produto->getPreco() * $quantidade
                ];
            }
        }

        if (empty($itens)) {
            $_SESSION['message'] = [
                'type' => 'danger',
                'text' => 'Seu carrinho está vazio.'
            ];
            header('Location: /carrinho');
            exit;
        }

        $cupom = $this->carrinhoService->getCupom();
        $desconto = $cupom && $cupom->isValido() ? $cupom->getValorDesconto() : 0;
        $subtotalComDesconto = max(0, $subtotal - $desconto);
        $frete = $this->freteService->calcularFrete($subtotalComDesconto);
        $total = $subtotalComDesconto + $frete;

        try {
            $this->estoqueRepository->beginTransaction();

            $pedidoRepository = new \Infrastructure\Repositories\PedidoRepository();
            $pedido = new \Domain\Entities\Pedido(
                null,
                $email,
                $endereco['cidade'],
                $endereco['estado'],
                $subtotal,
                0.00, 
                $frete,
                $total
            );
            $pedido = $pedidoRepository->save($pedido, $itens, false);

            foreach ($itens as $item) {
                $estoque = $item['estoque'];
                $novaQuantidade = $estoque->getQuantidade() - $item['quantidade'];
                $estoque->setQuantidade($novaQuantidade);
                $this->estoqueRepository->update($estoque);
            }

            $emailService = new \Domain\Services\EmailService();
            $emailService->enviarDetalhesCompra($email, $itens, $subtotal, $desconto, $frete, $total);

            $this->estoqueRepository->commit();

            $this->carrinhoService->finalizarCompra();

            $_SESSION['pedido_finalizado'] = [
                'pedido_id' => $pedido->getId(),
                'email' => $email,
                'endereco' => $endereco,
                'subtotal' => $subtotal,
                'desconto' => $desconto,
                'frete' => $frete,
                'total' => $total
            ];

            header('Location: /carrinho/sucesso');
            exit;

        } catch (\Exception $e) {
            $this->estoqueRepository->rollback();
            
            $_SESSION['message'] = [
                'type' => 'danger',
                'text' => 'Erro ao processar seu pedido: ' . $e->getMessage()
            ];
            header('Location: /carrinho');
            exit;
        }
    }

    public function sucesso(): void
    {
        if (!isset($_SESSION['pedido_finalizado'])) {
            header('Location: /carrinho');
            exit;
        }

        $dados = $_SESSION['pedido_finalizado'];
        unset($_SESSION['pedido_finalizado']);

        $this->view->render('carrinho/sucesso', $dados);
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