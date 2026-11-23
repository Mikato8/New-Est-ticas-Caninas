<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Support\Licencias;

/**
 * API pública de activación de Mikato CRM.
 *
 * No toca la sesión ni la base del sitio: solo valida la clave contra el
 * registro de licencias y devuelve un token firmado. La app lo verifica sin
 * red; este endpoint no recibe ni devuelve datos de clientes o mascotas.
 */
final class LicenciaController
{
    public function salud(Request $peticion): Response
    {
        return Response::json([
            'estado' => 'ok',
            'servicio' => 'mikato-licencias',
            'version' => 1,
            'firma' => function_exists('sodium_crypto_sign_detached')
                ? 'sodium'
                : (function_exists('openssl_sign') ? 'openssl' : 'ninguna'),
            'registro' => is_file(BASE_PATH . '/storage/licencias.json')
                || is_file(BASE_PATH . '/config/licencias/demo.json'),
            'llave' => is_file(BASE_PATH . '/config/licencias/demo-privada.pem')
                || (string) env('LICENCIA_PRIVATE_KEY', '') !== '',
        ]);
    }

    public function activar(Request $peticion): Response
    {
        [$estado, $cuerpo] = Licencias::activar($peticion->cuerpo(), $peticion->ip());

        return Response::json($cuerpo, $estado)
            ->conCabecera('Cache-Control', 'no-store');
    }
}
