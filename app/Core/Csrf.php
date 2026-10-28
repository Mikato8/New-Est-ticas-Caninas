<?php

declare(strict_types=1);

namespace App\Core;

final class Csrf
{
    public static function verificar(Request $peticion): void
    {
        if (!Session::csrfValido($peticion->cadena('_token'))) {
            throw new HttpException('La sesión expiró o el formulario no es válido. Recarga la página e intenta de nuevo.', 419);
        }
    }
}
