<?php

declare(strict_types=1);

namespace App\Core;

final class Logger
{
    /** @param array<string, mixed> $contexto */
    public static function error(string $mensaje, array $contexto = []): void
    {
        self::escribir('ERROR', $mensaje, $contexto);
    }

    /** @param array<string, mixed> $contexto */
    public static function info(string $mensaje, array $contexto = []): void
    {
        self::escribir('INFO', $mensaje, $contexto);
    }

    /** @param array<string, mixed> $contexto */
    private static function escribir(string $nivel, string $mensaje, array $contexto): void
    {
        $directorio = BASE_PATH . '/storage/logs';
        if (!is_dir($directorio)) {
            @mkdir($directorio, 0775, true);
        }

        $linea = sprintf(
            '[%s] %s %s %s',
            date('Y-m-d H:i:s'),
            $nivel,
            $mensaje,
            $contexto === [] ? '' : json_encode($contexto, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );

        @file_put_contents(
            $directorio . '/app-' . date('Y-m-d') . '.log',
            $linea . PHP_EOL,
            FILE_APPEND | LOCK_EX
        );
    }
}
