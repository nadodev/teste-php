<?php

namespace Domain\Interfaces;

use Domain\Entities\Cupom;

interface CupomRepositoryInterface
{
    public function findByCodigo(string $codigo): ?Cupom;
    public function findAll(): array;
    public function save(Cupom $cupom): Cupom;
    public function update(Cupom $cupom): bool;
    public function delete(string $codigo): bool;
} 