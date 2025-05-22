<?php

namespace Domain\Interfaces;

use Domain\Entities\Produto;

interface ProdutoRepositoryInterface
{
    public function findById(int $id): ?Produto;
    public function findAll(): array;
    public function save(Produto $produto): Produto;
    public function update(Produto $produto): bool;
    public function delete(int $id): bool;
} 