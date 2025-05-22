<?php

namespace Domain\Services;

use Domain\Entities\Produto;
use Infrastructure\Repositories\ProdutoRepository;

class ProdutoService
{
    private ProdutoRepository $produtoRepository;

    public function __construct(ProdutoRepository $produtoRepository)
    {
        $this->produtoRepository = $produtoRepository;
    }

    public function buscarPorId(int $id): ?Produto
    {
        return $this->produtoRepository->findById($id);
    }

    public function listarTodos(): array
    {
        return $this->produtoRepository->findAll();
    }

    public function salvar(Produto $produto): void
    {
        $this->produtoRepository->save($produto);
    }

    public function excluir(int $id): void
    {
        $this->produtoRepository->delete($id);
    }
} 