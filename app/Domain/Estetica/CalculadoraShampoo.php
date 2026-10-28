<?php

declare(strict_types=1);

namespace App\Domain\Estetica;

/**
 * Dilución de shampoo concentrado para baño de mascotas.
 * La dosis de mezcla por tamaño es una guía de trabajo, no una prescripción:
 * ajusta según el largo del pelo, la suciedad y la ficha del fabricante.
 */
final class CalculadoraShampoo
{
    private const DOSIS = [
        'chico' => 120.0,
        'mediano' => 200.0,
        'grande' => 320.0,
        'gigante' => 450.0,
    ];

    private const PELO = [
        'corto' => ['factor' => 0.9, 'etiqueta' => 'Corto'],
        'medio' => ['factor' => 1.0, 'etiqueta' => 'Medio'],
        'largo' => ['factor' => 1.25, 'etiqueta' => 'Largo'],
        'doble' => ['factor' => 1.15, 'etiqueta' => 'Doble capa'],
    ];

    /**
     * @param array<string, mixed> $entrada
     * @return array<string, mixed>
     */
    public function calcular(array $entrada): array
    {
        $errores = [];
        $concentrado = (float) ($entrada['concentrado_ml'] ?? 0);
        $ratio = (float) ($entrada['ratio'] ?? 10);
        $tamano = (string) ($entrada['tamano'] ?? 'mediano');
        $pelo = (string) ($entrada['pelo'] ?? 'medio');
        $precioLitro = (float) ($entrada['precio_litro'] ?? 0);
        $nivelSuciedad = (string) ($entrada['suciedad'] ?? 'normal');

        if ($concentrado <= 0 || $concentrado > 5000) {
            $errores['concentrado_ml'] = 'Escribe entre 1 y 5,000 ml de shampoo concentrado.';
        }
        if ($ratio < 1 || $ratio > 50) {
            $errores['ratio'] = 'La dilución debe estar entre 1:1 y 1:50.';
        }
        if (!isset(self::DOSIS[$tamano])) {
            $errores['tamano'] = 'Elige un tamaño válido.';
        }
        if (!isset(self::PELO[$pelo])) {
            $errores['pelo'] = 'Elige un tipo de pelo válido.';
        }
        if ($precioLitro < 0 || $precioLitro > 100000) {
            $errores['precio_litro'] = 'El precio por litro no es válido.';
        }
        if (!in_array($nivelSuciedad, ['normal', 'grasa', 'mucho'], true)) {
            $errores['suciedad'] = 'Elige un nivel de suciedad válido.';
        }
        if ($errores !== []) {
            throw new DatosInvalidos($errores);
        }

        $factorSuciedad = match ($nivelSuciedad) {
            'grasa' => 1.15,
            'mucho' => 1.3,
            default => 1.0,
        };
        $dosis = self::DOSIS[$tamano] * self::PELO[$pelo]['factor'] * $factorSuciedad;
        $agua = $concentrado * $ratio;
        $total = $concentrado + $agua;
        $banos = (int) floor($total / max(1.0, $dosis));
        $concentradoBano = $dosis / ($ratio + 1);
        $aguaBano = $dosis - $concentradoBano;
        $costoLitro = $precioLitro;
        $costoBano = $costoLitro * ($concentradoBano / 1000);
        $costoMezcla = $costoLitro * ($concentrado / 1000);
        $costoPorMililitro = $total > 0 ? $costoMezcla / $total : 0;

        return [
            'concentrado_ml' => round($concentrado, 1),
            'ratio' => $ratio,
            'agua_ml' => round($agua, 0),
            'total_ml' => round($total, 0),
            'dosis_por_bano' => round($dosis, 0),
            'concentrado_por_bano' => round($concentradoBano, 0),
            'agua_por_bano' => round($aguaBano, 0),
            'banos' => $banos,
            'tamano' => $tamano,
            'tamano_etiqueta' => ucfirst($tamano),
            'pelo' => self::PELO[$pelo]['etiqueta'],
            'suciedad' => $nivelSuciedad,
            'costo_mezcla' => round($costoMezcla, 2),
            'costo_bano' => round($costoBano, 2),
            'costo_por_ml' => round($costoPorMililitro, 4),
            'mezcla' => 'Mezcla ' . number_format($concentrado, 0) . ' ml de shampoo en ' . number_format($agua, 0) . ' ml de agua tibia.',
            'notas' => [
                'Usa agua tibia (entre 35 y 38 °C). Nunca agua caliente: reseca la piel y puede quemar.',
                'Prepara solo lo que vas a usar en el día. Una dilución guardada pierde eficacia y puede contaminarse.',
                'Etiqueta el bote con producto, dilución y fecha. No reutilices envases de limpieza del local.',
                'Enjuaga hasta que el agua salga clara: el residuo de shampoo es la causa más común de comezón.',
            ],
            'alertas' => array_values(array_filter([
                $nivelSuciedad === 'mucho' ? 'Para suciedad pesada, baña en dos pasos: primera pasada con dilución 1:10 y segunda con 1:15.' : null,
                $pelo === 'doble' ? 'En doble capa no satures el pelo: el shampoo debe llegar a la piel, no solo a la capa externa.' : null,
                $banos === 0 ? 'La mezcla no alcanza para un baño completo de este tamaño; prepara más.' : null,
            ])),
        ];
    }

    /** @return array<string, string> */
    public static function tamanos(): array
    {
        return ['chico' => 'Chico', 'mediano' => 'Mediano', 'grande' => 'Grande', 'gigante' => 'Gigante'];
    }

    /** @return array<string, string> */
    public static function pelos(): array
    {
        return ['corto' => 'Corto', 'medio' => 'Medio', 'largo' => 'Largo', 'doble' => 'Doble capa'];
    }
}
