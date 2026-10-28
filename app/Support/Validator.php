<?php

declare(strict_types=1);

namespace App\Support;

final class Validator
{
    /**
     * @param array<string, mixed> $datos
     * @param array<string, string> $reglas
     * @return array<string, string>
     */
    public static function validar(array $datos, array $reglas): array
    {
        $errores = [];

        foreach ($reglas as $campo => $regla) {
            $valor = $datos[$campo] ?? null;
            $texto = is_scalar($valor) ? trim((string) $valor) : '';
            $partes = explode('|', $regla);

            foreach ($partes as $parte) {
                [$nombre, $parametro] = array_pad(explode(':', $parte, 2), 2, null);

                if ($nombre === 'required' && $texto === '') {
                    $errores[$campo] = 'Este campo es obligatorio.';
                    break;
                }
                if ($texto === '') {
                    continue;
                }

                switch ($nombre) {
                    case 'email':
                        if (!filter_var($texto, FILTER_VALIDATE_EMAIL)) {
                            $errores[$campo] = 'Escribe un correo válido.';
                        }
                        break;
                    case 'url':
                        if (!filter_var($texto, FILTER_VALIDATE_URL) || !preg_match('#^https?://#i', $texto)) {
                            $errores[$campo] = 'Escribe una URL que empiece con http:// o https://.';
                        }
                        break;
                    case 'min':
                        if (mb_strlen($texto) < (int) $parametro) {
                            $errores[$campo] = 'Debe tener al menos ' . (int) $parametro . ' caracteres.';
                        }
                        break;
                    case 'max':
                        if (mb_strlen($texto) > (int) $parametro) {
                            $errores[$campo] = 'Debe tener como máximo ' . (int) $parametro . ' caracteres.';
                        }
                        break;
                    case 'numeric':
                        if (!is_numeric(str_replace(',', '.', $texto))) {
                            $errores[$campo] = 'Escribe un número válido.';
                        }
                        break;
                    case 'in':
                        $opciones = explode(',', (string) $parametro);
                        if (!in_array($texto, $opciones, true)) {
                            $errores[$campo] = 'Elige una opción válida.';
                        }
                        break;
                    case 'same':
                        if ($texto !== (string) ($datos[(string) $parametro] ?? '')) {
                            $errores[$campo] = 'Los valores no coinciden.';
                        }
                        break;
                }

                if (isset($errores[$campo])) {
                    break;
                }
            }
        }

        return $errores;
    }
}
