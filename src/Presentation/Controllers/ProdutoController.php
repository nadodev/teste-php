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
        $estoques = [];
        
        foreach ($produtos as $produto) {
            $estoqueProduto = $this->estoqueRepository->findByProdutoId($produto->getId());
            $estoques[$produto->getId()] = !empty($estoqueProduto) ? $estoqueProduto[0] : null;
        }
        
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

    public function edit(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $produto = $this->produtoRepository->findById($id);
        
        if (!$produto) {
            header('Location: ?route=produtos');
            exit;
        }

        $estoqueProduto = $this->estoqueRepository->findByProdutoId($produto->getId());
        $estoque = !empty($estoqueProduto) ? $estoqueProduto[0] : null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nome = $_POST['nome'] ?? '';
            $preco = (float) ($_POST['preco'] ?? 0);
            $variacao = $_POST['variacao'] ?? null;
            $quantidade = (int) ($_POST['quantidade'] ?? 0);

            $produto->setNome($nome);
            $produto->setPreco($preco);
            $this->produtoRepository->update($produto);

            if ($estoque) {
                $estoque->setVariacao($variacao);
                $estoque->setQuantidade($quantidade);
                $this->estoqueRepository->update($estoque);
            } else {
                $estoque = new Estoque(null, $produto->getId(), $variacao, $quantidade);
                $this->estoqueRepository->save($estoque);
            }

            header('Location: ?route=produtos');
            exit;
        }

        require_once __DIR__ . '/../Views/produtos/form.php';
    }

    public function delete(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?route=produtos');
            exit;
        }

        $id = (int) ($_POST['id'] ?? 0);
        $produto = $this->produtoRepository->findById($id);
        
        if ($produto) {
            $estoques = $this->estoqueRepository->findByProdutoId($produto->getId());
            foreach ($estoques as $estoque) {
                $this->estoqueRepository->delete($estoque->getId());
            }
            
            $this->produtoRepository->delete($produto->getId());
            
            $_SESSION['message'] = [
                'type' => 'success',
                'text' => 'Produto excluído com sucesso!'
            ];
        } else {
            $_SESSION['message'] = [
                'type' => 'danger',
                'text' => 'Produto não encontrado.'
            ];
        }

        header('Location: ?route=produtos');
        exit;
    }
} 