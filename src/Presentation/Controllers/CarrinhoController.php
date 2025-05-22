<?php

namespace Presentation\Controllers;

use Domain\Services\CarrinhoService;
use Infrastructure\Repositories\ProdutoRepository;

class CarrinhoController
{
    private CarrinhoService $carrinho;
    private ProdutoRepository $produtoRepository;

    public function __construct()
    {
        $this->carrinho = $_SESSION['carrinho'];
        $this->produtoRepository = new ProdutoRepository();
    }

    public function index(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            
            switch ($action) {
                case 'adicionar':
                    $produto_id = (int) ($_POST['produto_id'] ?? 0);
                    $quantidade = (int) ($_POST['quantidade'] ?? 1);
                    $produto = $this->produtoRepository->findById($produto_id);
                    
                    if ($produto) {
                        $this->carrinho->adicionarProduto($produto, $quantidade);
                    }
                    break;

                case 'remover':
                    $produto_id = (int) ($_POST['produto_id'] ?? 0);
                    $this->carrinho->removerProduto($produto_id);
                    break;

                case 'atualizar':
                    $produto_id = (int) ($_POST['produto_id'] ?? 0);
                    $quantidade = (int) ($_POST['quantidade'] ?? 1);
                    $this->carrinho->atualizarQuantidade($produto_id, $quantidade);
                    break;

                case 'limpar':
                    $this->carrinho->limpar();
                    break;
            }

            header('Location: ?route=carrinho');
            exit;
        }

        require_once __DIR__ . '/../Views/carrinho/index.php';
    }
} 