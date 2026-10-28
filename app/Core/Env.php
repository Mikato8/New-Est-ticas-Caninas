<?php

declare(strict_types=1);

namespace App\Core;

final class Env
{
    /** @var array<string, string> */
    private static array $valores = [];

    private static bool $cargado = false;

    /** @param list<string> $rutas */
    public static function load(array $rutas): void
    {
        if (self::$cargado) {
            return;
        }
        self::$cargado = true;

        foreach ($rutas as $ruta) {
            if ($ruta === '' || !is_file($ruta) || !is_readable($ruta)) {
                continue;
            }
            self::parsear((string) file_get_contents($ruta));
        }

        foreach ($_ENV as $clave => $valor) {
            if (is_string($clave) && is_string($valor) && !isset(self::$valores[$clave])) {
                self::$valores[$clave] = $valor;
            }
        }
    }

    private static function parsear(string $contenido): void
    {
        foreach (preg_split('/\R/', $contenido) ?: [] as $linea) {
            $linea = trim($linea);
            if ($linea === '' || str_starts_with($linea, '#') || !str_contains($linea, '=')) {
                continue;
            }

            [$clave, $valor] = explode('=', $linea, 2);
            $clave = trim(preg_replace('/^export\s+/', '', trim($clave)) ?? '');
            if (!preg_match('/^[A-Z0-9_]+$/i', $clave)) {
                continue;
            }

            $valor = trim($valor);
            if (strlen($valor) >= 2) {
                $primero = $valor[0];
                $ultimo = $valor[strlen($valor) - 1];
                if (($primero === '"' && $ultimo === '"') || ($primero === "'" && $ultimo === "'")) {
                    $valor = substr($valor, 1, -1);
                } elseif (str_contains($valor, ' #')) {
                    $valor = trim(substr($valor, 0, (int) strpos($valor, ' #')));
                }
            }

            if (!isset(self::$valores[$clave])) {
                self::$valores[$clave] = $valor;
            }
        }
    }

    public static function get(string $clave, ?string $porDefecto = null): ?string
    {
        return self::$valores[$clave] ?? (getenv($clave) ?: $porDefecto);
    }

    /** @param array<string, string> $datos */
    public static function set(array $datos): void
    {
        self::$valores = array_merge(self::$valores, $datos);
    }
}
