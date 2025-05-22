<?php

namespace Presentation\Controllers;

use Domain\Entities\Produto;
use Domain\Entities\Estoque;
use Infrastructure\Repositories\ProdutoRepository;
use Infrastructure\Repositories\EstoqueRepository;

class ProdutoController
{
    private ProdutoRepository $produtoRepository;
    private EstoqueRepository $estoqueRepository;

    public function __construct()
    {
        $this->produtoRepository = new ProdutoRepository();
        $this->estoqueRepository = new EstoqueRepository();
    }

    public function index(): void
    {
        $produtos = $this->produtoRepository->findAll();
        require_once __DIR__ . '/../Views/produtos/index.php';
    }

    public function create(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nome = $_POST['nome'] ?? '';
            $preco = (float) ($_POST['preco'] ?? 0);
            $variacao = $_POST['variacao'] ?? null;
            $quantidade = (int) ($_POST['quantidade'] ?? 0);

            $produto = new Produto(null, $nome, $preco);
            $produto = $this->produtoRepository->save($produto);

            $estoque = new Estoque(null, $produto->getId(), $variacao, $quantidade);
            $this->estoqueRepository->save($estoque);

            header('Location: ?route=produtos');
            exit;
        }

        require_once __DIR__ . '/../Views/produtos/form.php';
    }
} 