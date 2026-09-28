<?php

if (! function_exists('format_price_with_tax')) {
    function format_price_with_tax(float|int|string|null $price, float|int|string|null $taxPercent = 0): string
    {
        $numPrice = (float) ($price ?? 0);
        $numTax = (float) ($taxPercent ?? 0);

        $priceWithTax = round($numPrice * (1 + $numTax / 100), 2);

        return fmod($priceWithTax, 1) == 0
            ? number_format($priceWithTax, 0, ',', '.')
            : number_format($priceWithTax, 2, ',', '.');
    }
}

if (! function_exists('formatear_precio')) {
    function formatear_precio(float|int|string|null $valor): string
    {
        $numValor = (float) ($valor ?? 0);

        return fmod($numValor, 1) == 0
            ? number_format($numValor, 0, ',', '.')
            : number_format($numValor, 2, ',', '.');
    }
}
