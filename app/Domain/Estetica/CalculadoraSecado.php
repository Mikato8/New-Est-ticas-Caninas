<?php

declare(strict_types=1);

namespace App\Domain\Estetica;

/**
 * Estimación de tiempo y costo de secado.
 * El tiempo es una guía de planeación; la seguridad manda: temperatura,
 * distancia y pausas se ajustan al perro, no al reloj.
 */
final class CalculadoraSecado
{
    private const PELO = [
        'corto' => ['factor' => 0.85, 'etiqueta' => 'Corto'],
        'medio' => ['factor' => 1.0, 'etiqueta' => 'Medio'],
        'largo' => ['factor' => 1.35, 'etiqueta' => 'Largo'],
        'doble' => ['factor' => 1.55, 'etiqueta' => 'Doble capa'],
    ];

    private const EQUIPO = [
        'turbo' => ['factor' => 1.0, 'watts' => 2200, 'etiqueta' => 'Secadora turbo de pie'],
        'soplo' => ['factor' => 1.15, 'watts' => 1600, 'etiqueta' => 'Soplo / secadora de mano'],
        'jaula' => ['factor' => 1.4, 'watts' => 900, 'etiqueta' => 'Secadora de jaula'],
    ];

    /**
     * @param array<string, mixed> $entrada
     * @return array<string, mixed>
     */
    public function calcular(array $entrada): array
    {
        $errores = [];
        $peso = (float) ($entrada['peso_kg'] ?? 10);
        $pelo = (string) ($entrada['pelo'] ?? 'medio');
        $equipo = (string) ($entrada['equipo'] ?? 'turbo');
        $tarifa = (float) ($entrada['tarifa_kwh'] ?? 3.5);

        if ($peso < 0.5 || $peso > 90) {
            $errores['peso_kg'] = 'El peso debe estar entre 0.5 y 90 kg.';
        }
        if (!isset(self::PELO[$pelo])) {
            $errores['pelo'] = 'Elige un tipo de pelo válido.';
        }
        if (!isset(self::EQUIPO[$equipo])) {
            $errores['equipo'] = 'Elige un equipo válido.';
        }
        if ($tarifa < 0 || $tarifa > 20) {
            $errores['tarifa_kwh'] = 'La tarifa por kWh no es válida.';
        }
        if ($errores !== []) {
            throw new DatosInvalidos($errores);
        }

        $minutos = (int) round(pow($peso, 0.55) * 7 * self::PELO[$pelo]['factor'] * self::EQUIPO[$equipo]['factor']);
        $minutos = max(10, min(180, $minutos));
        $rango = [max(8, (int) round($minutos * 0.8)), (int) round($minutos * 1.2)];
        $watts = self::EQUIPO[$equipo]['watts'];
        $kwh = ($watts / 1000) * ($minutos / 60);
        $costo = $kwh * $tarifa;
        $pausas = $minutos >= 40 ? (int) floor($minutos / 20) - 1 : 0;
        $pesoRiesgo = $peso >= 25;

        $alertas = [];
        if ($pelo === 'doble') {
            $alertas[] = 'En doble capa no uses jaula de secado con el perro mojado: el aire caliente queda atrapado y hay riesgo de golpe de calor.';
            $alertas[] = 'Seca primero la capa interna con soplo a temperatura media y luego alisa la capa externa.';
        }
        if ($pesoRiesgo) {
            $alertas[] = 'Perro grande o gigante: divide el secado en dos sesiones y revisa la temperatura de la piel cada 10 minutos.';
        }
        if ($minutos >= 45) {
            $alertas[] = 'Programa ' . max(1, $pausas) . ' pausa(s) para que el perro se enfríe, tome agua y descansemos el equipo.';
        }
        if ($equipo === 'jaula') {
            $alertas[] = 'La secadora de jaula nunca debe usarse sin supervisión ni con el perro jadeando. Si jadea, para de inmediato.';
        }

        return [
            'minutos' => $minutos,
            'min_pausas' => $pausas,
            'rango' => $rango[0] . ' a ' . $rango[1] . ' min',
            'peso_kg' => $peso,
            'pelo' => self::PELO[$pelo]['etiqueta'],
            'equipo' => self::EQUIPO[$equipo]['etiqueta'],
            'watts' => $watts,
            'kwh' => round($kwh, 3),
            'costo_energia' => round($costo, 2),
            'tarifa_kwh' => $tarifa,
            'alertas' => $alertas,
            'checklist' => [
                'Temperatura del aire: tibia, no caliente. Prueba en tu muñeca antes de apuntar al perro.',
                'Distancia: 20 a 30 cm de la piel; mueve la boquilla, no la dejes fija.',
                'Zonas sensibles: cara, orejas, axilas, ingle y abdomen con aire tibio y poca presión.',
                'Nunca apuntes el aire a los ojos, nariz ni ano.',
                'Si el perro jadea, se echa o intenta escapar: apaga el equipo y deja que se recupere.',
            ],
        ];
    }

    /** @return array<string, string> */
    public static function pelos(): array
    {
        return array_map(static fn (array $dato): string => $dato['etiqueta'], self::PELO);
    }

    /** @return array<string, string> */
    public static function equipos(): array
    {
        return array_map(static fn (array $dato): string => $dato['etiqueta'], self::EQUIPO);
    }
}
