<?php

declare(strict_types=1);

namespace App\Support;

use App\Core\Logger;

/**
 * Envío mínimo con mail() del hosting. Se activa cuando existe el buzón
 * del dominio (MAIL_ENABLED=true). No se usa para registros ni recuperación.
 */
final class Mailer
{
    public static function enviar(string $para, string $asunto, string $cuerpo, ?string $responderA = null): bool
    {
        if (!(bool) config('mail.enabled', false)) {
            return false;
        }

        $de = (string) config('mail.from', 'hola@electricistas.mx');
        $nombre = (string) config('mail.from_name', 'Electricistas MX');
        $cabeceras = [
            'From: ' . self::cabecera($nombre) . ' <' . $de . '>',
            'MIME-Version: 1.0',
            'Content-Type: text/plain; charset=UTF-8',
            'Content-Transfer-Encoding: 8bit',
        ];
        if ($responderA !== null && filter_var($responderA, FILTER_VALIDATE_EMAIL)) {
            $cabeceras[] = 'Reply-To: ' . $responderA;
        }

        $asuntoCodificado = '=?UTF-8?B?' . base64_encode($asunto) . '?=';
        $ok = @mail($para, $asuntoCodificado, $cuerpo, implode("\r\n", $cabeceras));
        if (!$ok) {
            Logger::error('No se pudo enviar correo', ['para' => $para, 'asunto' => $asunto]);
        }
        return $ok;
    }

    private static function cabecera(string $texto): string
    {
        return '=?UTF-8?B?' . base64_encode($texto) . '?=';
    }
}
