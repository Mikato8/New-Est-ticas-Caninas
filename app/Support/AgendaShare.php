<?php

declare(strict_types=1);

namespace App\Support;

final class AgendaShare
{
    private const DIAS = 7;

    public static function token(string $fecha): string
    {
        $payload = self::base64Url((string) json_encode([
            'fecha' => $fecha,
            'exp' => time() + self::DIAS * 86400,
        ], JSON_UNESCAPED_UNICODE));
        $firma = hash_hmac('sha256', $payload, (string) config('app.key'));
        return $payload . '.' . $firma;
    }

    public static function fecha(string $token): ?string
    {
        $partes = explode('.', $token, 2);
        if (count($partes) !== 2 || $partes[1] === '') {
            return null;
        }
        $firma = hash_hmac('sha256', $partes[0], (string) config('app.key'));
        if (!hash_equals($firma, $partes[1])) {
            return null;
        }
        $json = self::base64UrlDecode($partes[0]);
        $datos = $json !== null ? json_decode($json, true) : null;
        if (!is_array($datos) || !is_string($datos['fecha'] ?? null) || !is_int($datos['exp'] ?? null)) {
            return null;
        }
        if ($datos['exp'] < time() || preg_match('/^\d{4}-\d{2}-\d{2}$/', $datos['fecha']) !== 1) {
            return null;
        }
        return $datos['fecha'];
    }

    public static function url(string $fecha): string
    {
        return url('/agenda/compartida/' . self::token($fecha));
    }

    /** @param list<array<string, mixed>> $citas */
    public static function csv(string $fecha, array $citas): string
    {
        $lineas = ["\xEF\xBB\xBF" . 'Fecha,Hora,Mascota,Cliente,Teléfono,Servicio,Estado,Precio,Notas'];
        foreach ($citas as $cita) {
            $campos = [
                $fecha,
                substr((string) ($cita['hora'] ?? ''), 0, 5),
                (string) ($cita['mascota_nombre'] ?? ''),
                (string) ($cita['cliente_nombre'] ?? ''),
                (string) ($cita['cliente_telefono'] ?? ''),
                (string) ($cita['servicio_nombre'] ?? ''),
                (string) ($cita['estado'] ?? ''),
                (string) ($cita['precio'] ?? ''),
                (string) ($cita['notas'] ?? ''),
            ];
            $lineas[] = implode(',', array_map(static fn (string $valor): string => self::campoCsv($valor), $campos));
        }
        return implode("\r\n", $lineas) . "\r\n";
    }

    private static function campoCsv(string $valor): string
    {
        if ($valor !== '' && in_array($valor[0], ['=', '+', '-', '@'], true)) {
            $valor = "'" . $valor;
        }
        return '"' . str_replace('"', '""', $valor) . '"';
    }

    private static function base64Url(string $texto): string
    {
        return rtrim(strtr(base64_encode($texto), '+/', '-_'), '=');
    }

    private static function base64UrlDecode(string $texto): ?string
    {
        $texto = strtr($texto, '-_', '+/');
        $resto = strlen($texto) % 4;
        if ($resto !== 0) {
            $texto .= str_repeat('=', 4 - $resto);
        }
        $binario = base64_decode($texto, true);
        return $binario === false ? null : $binario;
    }
}
