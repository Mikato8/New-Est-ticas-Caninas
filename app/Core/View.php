<?php

declare(strict_types=1);

namespace App\Core;

final class View
{
    private static string $base = '';

    public static function setBase(string $ruta): void
    {
        self::$base = rtrim($ruta, '/');
    }

    /** @param array<string, mixed> $datos */
    public static function render(string $plantilla, array $datos = [], string $layout = 'layout'): string
    {
        $datos['titulo'] ??= (string) config('name', 'Electricistas MX');
        $datos['descripcion'] ??= (string) config('tagline', '');
        $contenido = self::capturar(self::$base . '/' . $plantilla . '.php', $datos);

        if ($layout === '') {
            return $contenido;
        }

        $datos['contenido'] = $contenido;
        return self::capturar(self::$base . '/' . $layout . '.php', $datos);
    }

    /** @param array<string, mixed> $datos */
    public static function parcial(string $parcial, array $datos = []): void
    {
        echo self::capturar(self::$base . '/partials/' . $parcial . '.php', $datos);
    }

    /** @param array<string, mixed> $datos */
    private static function capturar(string $archivo, array $datos): string
    {
        if (!is_file($archivo)) {
            throw new \RuntimeException('No existe la vista: ' . $archivo);
        }

        $__vista = $datos;
        extract($__vista, EXTR_SKIP);
        // 'datos' es un nombre de variable habitual en los formularios; EXTR_SKIP
        // lo dejaría apuntando al arreglo completo de la vista.
        if (isset($__vista['datos']) && is_array($__vista['datos'])) {
            $datos = $__vista['datos'];
        }
        ob_start();
        try {
            require $archivo;
        } catch (\Throwable $e) {
            ob_end_clean();
            throw $e;
        }
        return (string) ob_get_clean();
    }
}
