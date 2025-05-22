<?php 

namespace Helpers;

class CarrinhoSessionStorage
{
    private const KEY = 'cart_data';

    public function get(): array
    {
        return $_SESSION[self::KEY] ?? [];
    }

    public function set(array $data): void
    {
        $_SESSION[self::KEY] = $data;
    }
}
