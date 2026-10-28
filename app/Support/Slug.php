<?php

declare(strict_types=1);

namespace App\Support;

final class Slug
{
    public static function make(string $texto): string
    {
        $texto = trim($texto);
        $texto = strtr($texto, [
            'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ü' => 'u', 'ñ' => 'n',
            'Á' => 'a', 'É' => 'e', 'Í' => 'i', 'Ó' => 'o', 'Ú' => 'u', 'Ü' => 'u', 'Ñ' => 'n',
        ]);
        $texto = strtolower($texto);
        $texto = preg_replace('/[^a-z0-9]+/', '-', $texto) ?? '';
        $texto = trim($texto, '-');
        $texto = preg_replace('/-+/', '-', $texto) ?? '';
        return $texto === '' ? 'articulo' : substr($texto, 0, 90);
    }

    public static function unico(string $base, callable $existe): string
    {
        $slug = self::make($base);
        $candidato = $slug;
        $n = 2;
        while ($existe($candidato)) {
            $candidato = $slug . '-' . $n;
            $n++;
            if ($n > 200) {
                $candidato = $slug . '-' . bin2hex(random_bytes(3));
                break;
            }
        }
        return $candidato;
    }
}
