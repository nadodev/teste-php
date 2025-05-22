<?php

namespace Presentation\Controllers;

use Domain\Entities\Produto;
use Domain\Entities\Estoque;
use Infrastructure\Repositories\ProdutoRepository;
use Infrastructure\Repositories\EstoqueRepository;
use Presentation\View;

class ProdutoController
{
    private ProdutoRepository $produtoRepository;
    private EstoqueRepository $estoqueRepository;
    private View $view;

    public function __construct()
    {
        $this->produtoRepository = new ProdutoRepository();
        $this->estoqueRepository = new EstoqueRepository();
        $this->view = new View();
    }

    public function index(): void
    {
        $produtos = $this->produtoRepository->findAll();
        $estoques = [];
        
        foreach ($produtos as $produto) {
            $estoqueProduto = $this->estoqueRepository->findByProdutoId($produto->getId());
            $estoques[$produto->getId()] = !empty($estoqueProduto) ? $estoqueProduto[0] : null;
        }
        
        $this->view->render('produtos/index', [
            'produtos' => $produtos,
            'estoques' => $estoques
        ]);
    }

    public function create(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nome = $_POST['nome'] ?? '';
            $preco = (float) ($_POST['preco'] ?? 0);
            $variacao = $_POST['variacao'] ?? null;
            $quantidade = (int) ($_POST['quantidade'] ?? 0);

            if ($quantidade < 0) {
                $_SESSION['message'] = [
                    'type' => 'danger',
                    'text' => 'A quantidade não pode ser negativa.'
                ];
                $this->view->render('produtos/form', [
                    'nome' => $nome,
                    'preco' => $preco,
                    'variacao' => $variacao,
                    'quantidade' => $quantidade
                ]);
                return;
            }

            $produto = new Produto(null, $nome, $preco);
            $produto = $this->produtoRepository->save($produto);

            $estoque = new Estoque(null, $produto->getId(), $variacao, $quantidade);
            $this->estoqueRepository->save($estoque);

            $_SESSION['message'] = [
                'type' => 'success',
                'text' => 'Produto criado com sucesso!'
            ];

            header('Location: /produtos');
            exit;
        }

        $this->view->render('produtos/form');
    }

    public function edit(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $produto = $this->produtoRepository->findById($id);
        
        if (!$produto) {
            $_SESSION['message'] = [
                'type' => 'danger',
                'text' => 'Produto não encontrado.'
            ];
            header('Location: /produtos');
            exit;
        }

        $estoqueProduto = $this->estoqueRepository->findByProdutoId($produto->getId());
        $estoque = !empty($estoqueProduto) ? $estoqueProduto[0] : null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nome = $_POST['nome'] ?? '';
            $preco = (float) ($_POST['preco'] ?? 0);
            $variacao = $_POST['variacao'] ?? null;
            $quantidade = (int) ($_POST['quantidade'] ?? 0);

            if ($quantidade < 0) {
                $_SESSION['message'] = [
                    'type' => 'danger',
                    'text' => 'A quantidade não pode ser negativa.'
                ];
                $this->view->render('produtos/form', [
                    'produto' => $produto,
                    'estoque' => $estoque
                ]);
                return;
            }

            try {
                // Atualiza o produto
                $produto->setNome($nome);
                $produto->setPreco($preco);
                $this->produtoRepository->update($produto);

                // Atualiza ou cria o estoque
                if ($estoque) {
                    $estoque->setVariacao($variacao);
                    $estoque->setQuantidade($quantidade);
                    $this->estoqueRepository->update($estoque);
                } else {
                    $estoque = new Estoque(null, $produto->getId(), $variacao, $quantidade);
                    $this->estoqueRepository->save($estoque);
                }

                $_SESSION['message'] = [
                    'type' => 'success',
                    'text' => 'Produto atualizado com sucesso!'
                ];

                header('Location: /produtos');
                exit;

            } catch (\RuntimeException $e) {
                $_SESSION['message'] = [
                    'type' => 'danger',
                    'text' => $e->getMessage()
                ];
                $this->view->render('produtos/form', [
                    'produto' => $produto,
                    'estoque' => $estoque
                ]);
                return;
            }
        }

        $this->view->render('produtos/form', [
            'produto' => $produto,
            'estoque' => $estoque
        ]);
    }

    public function delete(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /produtos');
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

        header('Location: /produtos');
        exit;
    }
} 