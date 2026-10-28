<?php

declare(strict_types=1);

namespace App\Core;

final class Config
{
    /** @var array<string, mixed> */
    private static array $datos = [];

    /** @param array<string, mixed> $datos */
    public static function set(array $datos): void
    {
        self::$datos = $datos;
    }

    public static function get(string $clave, mixed $porDefecto = null): mixed
    {
        $valor = self::$datos;
        foreach (explode('.', $clave) as $parte) {
            if (!is_array($valor) || !array_key_exists($parte, $valor)) {
                return $porDefecto;
            }
            $valor = $valor[$parte];
        }
        return $valor;
    }

    /** @return array<string, mixed> */
    public static function all(): array
    {
        return self::$datos;
    }
}
