<?php

declare(strict_types=1);

namespace App\Domain\Estetica;

/**
 * Precio sugerido de un servicio de estética canina.
 * Parte de costo directo + costo fijo asignado + mano de obra, y aplica
 * comisión y margen antes de IVA. El redondeo es a los $10 siguientes.
 */
final class CalculadoraPrecio
{
    /**
     * @param array<string, mixed> $entrada
     * @return array<string, mixed>
     */
    public function calcular(array $entrada): array
    {
        $errores = [];
        $minutos = (float) ($entrada['minutos'] ?? 90);
        $insumos = (float) ($entrada['insumos'] ?? 40);
        $fijoMensual = (float) ($entrada['costo_fijo_mensual'] ?? 15000);
        $serviciosMes = (float) ($entrada['servicios_mes'] ?? 120);
        $tarifaHora = (float) ($entrada['tarifa_hora'] ?? 120);
        $comision = (float) ($entrada['comision_pct'] ?? 0);
        $margen = (float) ($entrada['margen_pct'] ?? 35);
        $iva = (float) ($entrada['iva_pct'] ?? 0);

        if ($minutos < 10 || $minutos > 600) {
            $errores['minutos'] = 'El tiempo debe estar entre 10 y 600 minutos.';
        }
        if ($insumos < 0 || $insumos > 10000) {
            $errores['insumos'] = 'Los insumos no pueden ser negativos.';
        }
        if ($fijoMensual < 0) {
            $errores['costo_fijo_mensual'] = 'El costo fijo no puede ser negativo.';
        }
        if ($serviciosMes < 1 || $serviciosMes > 5000) {
            $errores['servicios_mes'] = 'Escribe al menos 1 servicio al mes.';
        }
        if ($tarifaHora < 0 || $tarifaHora > 5000) {
            $errores['tarifa_hora'] = 'La tarifa por hora no es válida.';
        }
        if ($comision < 0 || $comision > 60) {
            $errores['comision_pct'] = 'La comisión debe estar entre 0 y 60 %.';
        }
        if ($margen < 0 || $margen > 80) {
            $errores['margen_pct'] = 'El margen debe estar entre 0 y 80 %.';
        }
        if ($margen + $comision >= 90) {
            $errores['margen_pct'] = 'Margen + comisión deben sumar menos de 90 %.';
        }
        if ($iva < 0 || $iva > 30) {
            $errores['iva_pct'] = 'El IVA debe estar entre 0 y 30 %.';
        }
        if ($errores !== []) {
            throw new DatosInvalidos($errores);
        }

        $costoFijo = $fijoMensual / $serviciosMes;
        $manoObra = ($minutos / 60) * $tarifaHora;
        $costoDirecto = $insumos + $costoFijo + $manoObra;
        $factor = 1 - (($margen + $comision) / 100);
        $precioSinIva = $costoDirecto / max(0.1, $factor);
        $precioConIva = $precioSinIva * (1 + $iva / 100);
        $redondeado = ceil($precioConIva / 10) * 10;
        $comisionMonto = $precioSinIva * ($comision / 100);
        $utilidad = $precioSinIva - $costoDirecto - $comisionMonto;
        $margenReal = $precioSinIva > 0 ? ($utilidad / $precioSinIva) * 100 : 0;
        $precioHora = $precioSinIva / max(0.1, $minutos / 60);
        $puntoEquilibrio = $utilidad > 0 ? $fijoMensual / $utilidad : 0;

        return [
            'minutos' => $minutos,
            'horas' => round($minutos / 60, 2),
            'insumos' => round($insumos, 2),
            'costo_fijo' => round($costoFijo, 2),
            'mano_obra' => round($manoObra, 2),
            'costo_directo' => round($costoDirecto, 2),
            'comision_pct' => $comision,
            'margen_pct' => $margen,
            'comision_monto' => round($comisionMonto, 2),
            'precio_sin_iva' => round($precioSinIva, 2),
            'precio_con_iva' => round($precioConIva, 2),
            'precio_sugerido' => round($redondeado, 0),
            'utilidad' => round($utilidad, 2),
            'margen_real' => round($margenReal, 1),
            'precio_hora' => round($precioHora, 2),
            'punto_equilibrio' => round($puntoEquilibrio, 1),
            'desglose' => [
                ['concepto' => 'Insumos (shampoo, toallas, aromas)', 'monto' => round($insumos, 2)],
                ['concepto' => 'Costo fijo asignado por servicio', 'monto' => round($costoFijo, 2)],
                ['concepto' => 'Tu mano de obra (' . round($minutos / 60, 2) . ' h)', 'monto' => round($manoObra, 2)],
                ['concepto' => 'Comisión de quien atiende', 'monto' => round($comisionMonto, 2)],
                ['concepto' => 'Utilidad antes de impuestos', 'monto' => round($utilidad, 2)],
            ],
            'alertas' => array_values(array_filter([
                $margenReal < $margen - 1 ? 'El margen real queda por debajo del margen objetivo: revisa comisión o costo fijo.' : null,
                $precioHora < $tarifaHora ? 'El precio por hora no cubre tu tarifa objetivo de mano de obra.' : null,
                $puntoEquilibrio > $serviciosMes ? 'Necesitas más servicios al mes para cubrir los costos fijos con este precio.' : null,
            ])),
        ];
    }
}
