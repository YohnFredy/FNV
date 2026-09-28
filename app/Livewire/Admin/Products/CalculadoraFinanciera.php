<?php

namespace App\Livewire\Admin\Products;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class CalculadoraFinanciera extends Component
{
    protected array $camposNumericos = [
        'precioPublico',
        'valorProducto',
        'descuentoPrecioPublicoPorcentaje',
        'ivaPorcentaje',
        'tarifaPasarelaPorcentaje',
        'tarifaFijaPasarela',
        'reteIcaPorcentaje',
        'reteRentaPorcentaje',
        'empresaPorcentaje',
        'bonoInicioPorcentaje',
        'valorDolar',
        'binarioPorcentaje',
        'bolsaGlobalPorcentaje',
        'generacionalPorcentaje',
        'rangoPorcentaje',
        'premiosPorcentaje',
    ];

    public float $precioPublico = 60000;

    public float $valorProducto = 18000;

    public float $descuentoPrecioPublicoPorcentaje = 30;

    public float $ivaPorcentaje = 19;

    public float $tarifaPasarelaPorcentaje = 4.19;

    public float $tarifaFijaPasarela = 900;

    public float $reteIcaPorcentaje = 0.414;

    public float $reteRentaPorcentaje = 1.5;

    public float $empresaPorcentaje = 10;

    public float $bonoInicioPorcentaje = 0;

    public float $valorDolar = 4000;

    public float $binarioPorcentaje = 30;

    public float $bolsaGlobalPorcentaje = 30;

    public float $generacionalPorcentaje = 20;

    public float $rangoPorcentaje = 10;

    public float $premiosPorcentaje = 10;

    public float $pts_base = 0;

    public float $pts_bono = 0;

    public float $pts_dist = 0;

    public function updated(string $propertyName, mixed $value): void
    {
        if (in_array($propertyName, $this->camposNumericos)) {
            $this->$propertyName = is_numeric($value) ? floatval($value) : 0;
        }
    }

    private function calcularIva(float $monto, float $ivaPorcentaje): float
    {
        if ($ivaPorcentaje <= 0) {
            return 0;
        }
        $factor = 1 + $ivaPorcentaje / 100;

        return $monto - ($monto / $factor);
    }

    private function calcularPasarela(
        float $monto,
        float $tarifaPorcentaje,
        float $tarifaFija,
        float $reteIcaPorcentaje,
        float $reteRentaPorcentaje
    ): float {
        return ($monto * $tarifaPorcentaje / 100) + $tarifaFija + ($monto * $reteIcaPorcentaje / 100) + ($monto * $reteRentaPorcentaje / 100);
    }

    private function calcularPts(float $saldo, float $valorDolar, float $porcentaje): float
    {
        if ($valorDolar <= 0 || $saldo <= 0 || $porcentaje <= 0) {
            return 0;
        }

        return round((($saldo / $valorDolar) * $porcentaje) / 100, 2);
    }

    public function agregar(): void
    {
        $this->dispatch(
            'calculadora-financiera',
            precioPublico: $this->precioPublico,
            ivaPorcentaje: $this->ivaPorcentaje,
            pts_base: $this->pts_base,
            bonoInicioPorcentaje: $this->bonoInicioPorcentaje,
            pts_bono: $this->pts_bono,
            descuentoPorcentaje: $this->descuentoPrecioPublicoPorcentaje,
            pts_dist: $this->pts_dist
        );
    }

    public function render(): View
    {
        $saldoBase = $this->precioPublico - $this->valorProducto;
        $ivaBase = $this->calcularIva($saldoBase, $this->ivaPorcentaje);
        $pasarelaBase = $this->calcularPasarela(
            $this->precioPublico,
            $this->tarifaPasarelaPorcentaje,
            $this->tarifaFijaPasarela,
            $this->reteIcaPorcentaje,
            $this->reteRentaPorcentaje
        );
        $gananciaEmpresa = ($this->precioPublico * $this->empresaPorcentaje) / 100;
        $bonoInicio = ($this->precioPublico * $this->bonoInicioPorcentaje) / 100;

        $saldoSinBono = $this->precioPublico - $this->valorProducto - $ivaBase - $pasarelaBase - $gananciaEmpresa;
        $saldoConBono = $saldoSinBono - $bonoInicio;

        $precioConDescuento = $this->precioPublico - ($this->precioPublico * $this->descuentoPrecioPublicoPorcentaje) / 100;
        $saldoDescuento = $precioConDescuento - $this->valorProducto;
        $ivaDescuento = $this->calcularIva($saldoDescuento, $this->ivaPorcentaje);
        $pasarelaDescuento = $this->calcularPasarela(
            $precioConDescuento,
            $this->tarifaPasarelaPorcentaje,
            $this->tarifaFijaPasarela,
            $this->reteIcaPorcentaje,
            $this->reteRentaPorcentaje
        );
        $saldoDistribucion = $precioConDescuento - $this->valorProducto - $ivaDescuento - $pasarelaDescuento - $gananciaEmpresa;

        $this->pts_base = $ptsBase = $this->calcularPts($saldoSinBono, $this->valorDolar, $this->binarioPorcentaje);
        $this->pts_bono = $ptsConBono = $this->calcularPts($saldoConBono, $this->valorDolar, $this->binarioPorcentaje);
        $this->pts_dist = $ptsDistribucion = $this->calcularPts($saldoDistribucion, $this->valorDolar, $this->binarioPorcentaje);
        $ptsBolsaGlobal = $this->calcularPts($saldoDistribucion, $this->valorDolar, $this->bolsaGlobalPorcentaje);
        $ptsGeneracional = $this->calcularPts($saldoDistribucion, $this->valorDolar, $this->generacionalPorcentaje);

        return view('livewire.admin.products.calculadora-financiera', [
            'saldoBase' => $saldoBase,
            'ivaBase' => $ivaBase,
            'pasarelaBase' => $pasarelaBase,
            'gananciaEmpresa' => $gananciaEmpresa,
            'bonoInicio' => $bonoInicio,
            'saldoSinBono' => $saldoSinBono,
            'saldoConBono' => $saldoConBono,
            'precioConDescuento' => $precioConDescuento,
            'saldoDescuento' => $saldoDescuento,
            'ivaDescuento' => $ivaDescuento,
            'pasarelaDescuento' => $pasarelaDescuento,
            'saldoDistribucion' => $saldoDistribucion,
            'ptsBase' => $ptsBase,
            'ptsConBono' => $ptsConBono,
            'ptsDistribucion' => $ptsDistribucion,
            'ptsBolsaGlobal' => $ptsBolsaGlobal,
            'ptsGeneracional' => $ptsGeneracional,
        ]);
    }
}
