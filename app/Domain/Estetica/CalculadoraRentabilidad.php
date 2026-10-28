<?php

declare(strict_types=1);

namespace App\Domain\Estetica;

/**
 * Rentabilidad de un servicio concreto y punto de equilibrio del salón.
 */
final class CalculadoraRentabilidad
{
    /**
     * @param array<string, mixed> $entrada
     * @return array<string, mixed>
     */
    public function calcular(array $entrada): array
    {
        $errores = [];
        $precio = (float) ($entrada['precio'] ?? 350);
        $insumos = (float) ($entrada['insumos'] ?? 45);
        $minutos = (float) ($entrada['minutos'] ?? 90);
        $comision = (float) ($entrada['comision_pct'] ?? 0);
        $fijoMensual = (float) ($entrada['costo_fijo_mensual'] ?? 15000);
        $metaHora = (float) ($entrada['meta_hora'] ?? 180);

        if ($precio <= 0 || $precio > 20000) {
            $errores['precio'] = 'El precio debe ser mayor a cero.';
        }
        if ($insumos < 0 || $insumos > 10000) {
            $errores['insumos'] = 'Los insumos no pueden ser negativos.';
        }
        if ($minutos < 10 || $minutos > 600) {
            $errores['minutos'] = 'El tiempo debe estar entre 10 y 600 minutos.';
        }
        if ($comision < 0 || $comision > 60) {
            $errores['comision_pct'] = 'La comisión debe estar entre 0 y 60 %.';
        }
        if ($fijoMensual < 0) {
            $errores['costo_fijo_mensual'] = 'El costo fijo no puede ser negativo.';
        }
        if ($metaHora < 0 || $metaHora > 5000) {
            $errores['meta_hora'] = 'La meta por hora no es válida.';
        }
        if ($errores !== []) {
            throw new DatosInvalidos($errores);
        }

        $comisionMonto = $precio * ($comision / 100);
        $neto = $precio - $insumos - $comisionMonto;
        $margen = $precio > 0 ? ($neto / $precio) * 100 : 0;
        $horas = $minutos / 60;
        $utilidadHora = $neto / max(0.1, $horas);
        $punto = $neto > 0 ? $fijoMensual / $neto : 0;
        $precioMeta = (($metaHora * $horas) + $insumos) / max(0.1, 1 - ($comision / 100));
        $brecha = $precioMeta - $precio;

        if ($utilidadHora >= $metaHora * 0.95) {
            $semaforo = 'verde';
            $mensaje = 'El servicio alcanza tu meta por hora.';
        } elseif ($utilidadHora >= $metaHora * 0.75) {
            $semaforo = 'ambar';
            $mensaje = 'Está cerca, pero conviene ajustar precio, tiempo o insumos.';
        } else {
            $semaforo = 'rojo';
            $mensaje = 'El servicio no paga tu meta por hora: súbelo, bájale tiempo o revisa insumos.';
        }

        return [
            'precio' => round($precio, 2),
            'insumos' => round($insumos, 2),
            'comision_pct' => $comision,
            'comision_monto' => round($comisionMonto, 2),
            'neto' => round($neto, 2),
            'margen' => round($margen, 1),
            'minutos' => $minutos,
            'horas' => round($horas, 2),
            'utilidad_hora' => round($utilidadHora, 2),
            'meta_hora' => $metaHora,
            'punto_equilibrio' => round($punto, 1),
            'precio_meta' => round($precioMeta, 2),
            'brecha' => round($brecha, 2),
            'semaforo' => $semaforo,
            'mensaje' => $mensaje,
            'recomendaciones' => array_values(array_filter([
                $brecha > 0 ? 'Para llegar a la meta, cobra al menos ' . precio($precioMeta) . ' (brecha de ' . precio($brecha) . ').' : null,
                $comision > 20 ? 'Una comisión de ' . $comision . ' % se come ' . precio($comisionMonto) . ' del precio; verifica que el volumen lo compense.' : null,
                $insumos > $precio * 0.2 ? 'Los insumos representan más del 20 % del precio: negocia compras o ajusta la dilución.' : null,
                $punto > 0 && $punto > 150 ? 'Necesitas ' . ceil($punto) . ' servicios al mes solo para cubrir costos fijos; busca paquetes o sube el ticket.' : null,
            ])),
        ];
    }
}
