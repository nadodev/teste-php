<?php

namespace Domain\Services;

class FreteService
{
    private const FRETE_PADRAO = 20.00;
    private const FRETE_INTERMEDIARIO = 15.00;
    private const FRETE_GRATIS = 0.00;

    private const LIMITE_INFERIOR_INTERMEDIARIO = 52.00;
    private const LIMITE_SUPERIOR_INTERMEDIARIO = 166.59;
    private const LIMITE_FRETE_GRATIS = 200.00;

    public function calcularFrete(float $subtotal): float
    {
        if ($subtotal >= self::LIMITE_FRETE_GRATIS) {
            return self::FRETE_GRATIS;
        }

        if ($subtotal >= self::LIMITE_INFERIOR_INTERMEDIARIO && $subtotal <= self::LIMITE_SUPERIOR_INTERMEDIARIO) {
            return self::FRETE_INTERMEDIARIO;
        }

        return self::FRETE_PADRAO;
    }

    public function getDescricaoFrete(float $subtotal): string
    {
        $frete = $this->calcularFrete($subtotal);

        if ($frete === self::FRETE_GRATIS) {
            return 'Frete Grátis';
        }

        return 'R$ ' . number_format($frete, 2, ',', '.');
    }

    public function getValorMinimoFreteGratis(): float
    {
        return self::LIMITE_FRETE_GRATIS;
    }

    public function getValorRestanteParaFreteGratis(float $subtotal): ?float
    {
        if ($subtotal >= self::LIMITE_FRETE_GRATIS) {
            return null;
        }

        return self::LIMITE_FRETE_GRATIS - $subtotal;
    }
} 