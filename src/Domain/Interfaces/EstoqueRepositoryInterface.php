<?php

namespace Domain\Interfaces;

use Domain\Entities\Estoque;

interface EstoqueRepositoryInterface
{
    public function findByProdutoId(int $produto_id): array;
    public function save(Estoque $estoque): Estoque;
    public function update(Estoque $estoque): bool;
    public function updateQuantidade(int $id, int $quantidade): bool;
    public function delete(int $id): bool;
} 