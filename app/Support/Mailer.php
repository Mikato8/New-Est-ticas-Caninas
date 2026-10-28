<?php

declare(strict_types=1);

namespace App\Support;

use App\Core\Logger;

/**
 * Envío de correo transaccional.
 *
 * Usa SMTP autenticado cuando MAIL_HOST está configurado (recomendado para
 * Hostinger) y cae a mail() del hosting solo si no hay host SMTP.
 */
final class Mailer
{
    public static function enviar(string $para, string $asunto, string $cuerpo, ?string $responderA = null): bool
    {
        if (!(bool) config('mail.enabled', false)) {
            Logger::info('Correo desactivado; no se envió el mensaje.', ['para' => $para]);
            return false;
        }

        $de = trim((string) config('mail.from', ''));
        $nombre = trim((string) config('mail.from_name', 'Mikato Estilistas Caninos'));
        if (!filter_var($de, FILTER_VALIDATE_EMAIL) || !filter_var($para, FILTER_VALIDATE_EMAIL)) {
            Logger::error('Correo con remitente o destinatario inválido.', ['de' => $de, 'para' => $para]);
            return false;
        }

        $asunto = str_replace(["\r", "\n"], ' ', $asunto);
        $cuerpo = str_replace(["\r\n", "\r"], "\n", $cuerpo);
        $cuerpo = str_replace("\n", "\r\n", $cuerpo);

        $cabeceras = [
            'Date: ' . date('r'),
            'From: ' . self::cabecera($nombre) . ' <' . $de . '>',
            'To: <' . $para . '>',
            'Subject: ' . self::cabecera($asunto),
            'Message-ID: <' . bin2hex(random_bytes(12)) . '@' . self::dominio() . '>',
            'MIME-Version: 1.0',
            'Content-Type: text/plain; charset=UTF-8',
            'Content-Transfer-Encoding: 8bit',
            'X-Mailer: Mikato',
        ];
        if ($responderA !== null && filter_var($responderA, FILTER_VALIDATE_EMAIL)) {
            $cabeceras[] = 'Reply-To: ' . $responderA;
        }

        $host = trim((string) config('mail.host', ''));
        if ($host !== '') {
            return self::smtp($host, (int) config('mail.port', 465), (string) config('mail.username', $de), (string) config('mail.password', ''), (string) config('mail.encryption', 'ssl'), $de, $para, $cabeceras, $cuerpo);
        }

        $ok = @mail($para, self::cabecera($asunto), $cuerpo, implode("\r\n", $cabeceras));
        if (!$ok) {
            Logger::error('No se pudo enviar correo con mail().', ['para' => $para, 'asunto' => $asunto]);
        }
        return $ok;
    }

    /** @param list<string> $cabeceras */
    private static function smtp(string $host, int $puerto, string $usuario, string $password, string $cifrado, string $de, string $para, array $cabeceras, string $cuerpo): bool
    {
        if ($usuario === '' || $password === '') {
            Logger::error('SMTP configurado sin usuario o contraseña.');
            return false;
        }

        $cifrado = strtolower($cifrado);
        $remoto = ($cifrado === 'ssl' ? 'ssl://' : 'tcp://') . $host . ':' . $puerto;
        $contexto = stream_context_create([
            'ssl' => ['verify_peer' => true, 'verify_peer_name' => true],
        ]);
        $socket = @stream_socket_client($remoto, $errno, $errstr, 15, STREAM_CLIENT_CONNECT, $contexto);
        if (!is_resource($socket)) {
            Logger::error('No se pudo conectar al SMTP.', ['host' => $host, 'puerto' => $puerto, 'error' => $errstr]);
            return false;
        }
        stream_set_timeout($socket, 15);

        $leer = static function () use ($socket): string {
            $respuesta = '';
            while (($linea = fgets($socket, 1024)) !== false) {
                $respuesta .= $linea;
                if (strlen($linea) < 4 || $linea[3] === ' ') {
                    break;
                }
            }
            return $respuesta;
        };
        $esperar = static function (array $codigos) use ($leer): array {
            $respuesta = $leer();
            $codigo = (int) substr($respuesta, 0, 3);
            return [in_array($codigo, $codigos, true), $respuesta];
        };
        $comando = static function (string $comando, array $codigos) use ($socket, $esperar): array {
            if (fwrite($socket, $comando . "\r\n") === false) {
                return [false, 'No se pudo escribir al SMTP.'];
            }
            return $esperar($codigos);
        };
        $fallar = static function (string $respuesta) use ($socket): bool {
            Logger::error('SMTP rechazó la operación.', ['respuesta' => mb_substr(trim($respuesta), 0, 300)]);
            @fclose($socket);
            return false;
        };

        [$ok, $respuesta] = $esperar([220]);
        if (!$ok) {
            return $fallar($respuesta);
        }

        $ehlo = preg_replace('/[^a-zA-Z0-9.-]/', '', (string) gethostname()) ?: 'localhost';
        [$ok, $respuesta] = $comando('EHLO ' . $ehlo, [250]);
        if (!$ok) {
            return $fallar($respuesta);
        }

        if ($cifrado === 'tls') {
            [$ok, $respuesta] = $comando('STARTTLS', [220]);
            if (!$ok) {
                return $fallar($respuesta);
            }
            if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                Logger::error('No se pudo iniciar TLS con el SMTP.');
                @fclose($socket);
                return false;
            }
            [$ok, $respuesta] = $comando('EHLO ' . $ehlo, [250]);
            if (!$ok) {
                return $fallar($respuesta);
            }
        }

        [$ok, $respuesta] = $comando('AUTH LOGIN', [334]);
        if (!$ok) {
            return $fallar($respuesta);
        }
        [$ok, $respuesta] = $comando(base64_encode($usuario), [334]);
        if (!$ok) {
            return $fallar($respuesta);
        }
        [$ok, $respuesta] = $comando(base64_encode($password), [235]);
        if (!$ok) {
            return $fallar($respuesta);
        }

        [$ok, $respuesta] = $comando('MAIL FROM:<' . $de . '>', [250]);
        if (!$ok) {
            return $fallar($respuesta);
        }
        [$ok, $respuesta] = $comando('RCPT TO:<' . $para . '>', [250, 251]);
        if (!$ok) {
            return $fallar($respuesta);
        }
        [$ok, $respuesta] = $comando('DATA', [354]);
        if (!$ok) {
            return $fallar($respuesta);
        }

        $datos = implode("\r\n", $cabeceras) . "\r\n\r\n" . $cuerpo;
        $datos = (string) preg_replace('/^\./m', '..', $datos);
        if (!str_ends_with($datos, "\r\n")) {
            $datos .= "\r\n";
        }
        if (fwrite($socket, $datos . ".\r\n") === false) {
            return $fallar('No se pudo enviar el cuerpo del mensaje.');
        }
        [$ok, $respuesta] = $esperar([250]);
        if (!$ok) {
            return $fallar($respuesta);
        }

        $comando('QUIT', [221]);
        @fclose($socket);
        return true;
    }

    private static function cabecera(string $texto): string
    {
        return '=?UTF-8?B?' . base64_encode(str_replace(["\r", "\n"], ' ', $texto)) . '?=';
    }

    private static function dominio(): string
    {
        $host = parse_url((string) config('url', 'https://mikatoestilistascaninos.com'), PHP_URL_HOST);
        return is_string($host) && $host !== '' ? $host : 'mikatoestilistascaninos.com';
    }
}
