<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Licencias de Mikato CRM.
 *
 * La app de escritorio pide un token firmado a este servicio. El token es
 * `base64url(payload).base64url(firma Ed25519)`; la app lo verifica sin red con
 * la llave pública embebida. Aquí solo se guarda el registro de claves y se
 * firma; nunca se guardan datos del negocio del cliente.
 *
 * La llave privada de demostración está en `config/licencias/demo-privada.pem`.
 * Antes de vender el programa hay que generar un par propio con
 * `php bin/licencias.php llaves` y reemplazar la llave pública en la app.
 */
final class Licencias
{
    public const PATRON = '/^MKTO-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}$/';

    private const REGISTRO = '/storage/licencias.json';
    private const SEMILLA = '/config/licencias/demo.json';
    private const LLAVE_DEMO = '/config/licencias/demo-privada.pem';
    private const INTENTOS = '/storage/cache/licencias-intentos.json';

    /** @return array{0:int,1:array<string,mixed>} */
    public static function activar(array $datos, string $ip): array
    {
        $clave = strtoupper(trim((string) ($datos['clave'] ?? '')));
        $equipo = trim((string) ($datos['equipo'] ?? ''));
        $version = trim((string) ($datos['version'] ?? ''));

        if (preg_match(self::PATRON, $clave) !== 1) {
            return [400, self::error('clave_invalida', 'La clave debe tener el formato MKTO-XXXX-XXXX-XXXX.')];
        }
        if ($equipo === '' || strlen($equipo) > 80) {
            return [400, self::error('equipo_requerido', 'Falta el identificador del equipo que solicita la licencia.')];
        }
        if (strlen($version) > 30) {
            return [400, self::error('solicitud_invalida', 'La versión de la app no es válida.')];
        }
        if (self::demasiadosIntentos($ip)) {
            return [429, self::error('demasiados_intentos', 'Demasiados intentos de activación. Espera un minuto.')];
        }

        $registro = self::leerRegistro();
        $indice = null;
        foreach ($registro['licencias'] as $i => $fila) {
            if (($fila['clave'] ?? '') === $clave) {
                $indice = $i;
                break;
            }
        }
        if ($indice === null) {
            return [404, self::error('clave_no_encontrada', 'Esa clave no existe en el registro de Mikato. Revisa que esté bien escrita o contacta a soporte.')];
        }

        $licencia = $registro['licencias'][$indice];
        if (($licencia['estado'] ?? 'activa') === 'revocada') {
            return [410, self::error('licencia_revocada', 'La licencia fue revocada. Contacta a Mikato para regularizar el uso del software.')];
        }

        $expira = self::expiraSegundos((string) ($licencia['expira_en'] ?? ''));
        if ($expira !== null && $expira <= time()) {
            return [410, self::error('licencia_vencida', 'La licencia venció. Renueva tu suscripción para seguir usando Mikato CRM.')];
        }
        if (($licencia['equipo'] ?? null) !== null && $licencia['equipo'] !== '' && $licencia['equipo'] !== $equipo) {
            return [409, self::error('equipo_en_uso', 'Esta licencia ya está activada en otro equipo. Pide a Mikato que la mueva a este identificador.')];
        }

        $emitidoEn = time();
        $expiraEn = $expira ?? ($emitidoEn + 365 * 86400);
        $payload = [
            'v' => 1,
            'lic' => $clave,
            'plan' => (string) ($licencia['plan'] ?? 'comercial'),
            'titular' => (string) ($licencia['titular'] ?? ''),
            'negocio' => (string) ($licencia['negocio'] ?? ''),
            'equipo' => $equipo,
            'emitido_en' => $emitidoEn,
            'expira_en' => $expiraEn,
            'emisor' => 'mikato-licencias-v1',
        ];

        $token = self::firmar($payload);
        if ($token === null) {
            return [500, self::error('firma_interna', 'El servidor no pudo firmar el token de licencia.')];
        }

        $registro['licencias'][$indice]['equipo'] = $equipo;
        $registro['licencias'][$indice]['activada_en'] = gmdate('c', $emitidoEn);
        self::guardarRegistro($registro);

        return [200, [
            'estado' => 'activa',
            'token' => $token,
            'plan' => $payload['plan'],
            'titular' => $payload['titular'],
            'expira_en' => $expiraEn,
        ]];
    }

    /** @return array<string,mixed> */
    private static function leerRegistro(): array
    {
        $ruta = BASE_PATH . self::REGISTRO;
        if (!is_file($ruta) && is_file(BASE_PATH . self::SEMILLA)) {
            @mkdir(dirname($ruta), 0775, true);
            @copy(BASE_PATH . self::SEMILLA, $ruta);
        }
        $datos = is_file($ruta) ? json_decode((string) file_get_contents($ruta), true) : null;
        if (!is_array($datos) || !is_array($datos['licencias'] ?? null)) {
            return ['licencias' => []];
        }

        return $datos;
    }

    private static function guardarRegistro(array $datos): void
    {
        $ruta = BASE_PATH . self::REGISTRO;
        $json = json_encode($datos, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        if ($json === false) {
            return;
        }
        $temporal = $ruta . '.tmp';
        if (@file_put_contents($temporal, $json, LOCK_EX) !== false) {
            @rename($temporal, $ruta);
        }
    }

    /** @return array<string,mixed>|null */
    private static function llavePrivada(): ?array
    {
        $pem = (string) env('LICENCIA_PRIVATE_KEY', '');
        if ($pem !== '') {
            $decodificado = base64_decode($pem, true);
            if (is_string($decodificado) && str_contains($decodificado, 'PRIVATE KEY')) {
                $pem = $decodificado;
            }
        }
        if (!str_contains($pem, 'PRIVATE KEY')) {
            $ruta = BASE_PATH . self::LLAVE_DEMO;
            $pem = is_file($ruta) ? (string) file_get_contents($ruta) : '';
        }
        if (!str_contains($pem, 'PRIVATE KEY')) {
            return null;
        }

        if (function_exists('sodium_crypto_sign_seed_keypair')) {
            $der = base64_decode((string) preg_replace('/-----[^-]+-----|\s+/', '', $pem), true);
            if (is_string($der) && strlen($der) >= 32) {
                $par = sodium_crypto_sign_seed_keypair(substr($der, -32));

                return [
                    'tipo' => 'sodium',
                    'secreta' => sodium_crypto_sign_secretkey($par),
                    'publica' => sodium_crypto_sign_publickey($par),
                ];
            }
        }

        // Hostinger puede traer OpenSSL sin sodium. OpenSSL firma Ed25519 con
        // algoritmo 0 (sin digest); la firma también mide 64 bytes.
        $llave = openssl_pkey_get_private($pem);
        if ($llave === false) {
            return null;
        }
        $detalles = openssl_pkey_get_details($llave);

        return [
            'tipo' => 'openssl',
            'llave' => $llave,
            'publica' => (string) ($detalles['key'] ?? ''),
        ];
    }

    private static function firmar(array $payload): ?string
    {
        $llaves = self::llavePrivada();
        if ($llaves === null) {
            return null;
        }
        $json = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($json === false) {
            return null;
        }
        $segmento = self::base64url($json);

        if (($llaves['tipo'] ?? '') === 'sodium') {
            $firma = sodium_crypto_sign_detached($segmento, $llaves['secreta']);
            if (!sodium_crypto_sign_verify_detached($firma, $segmento, $llaves['publica'])) {
                return null;
            }
        } else {
            $firma = '';
            if (!openssl_sign($segmento, $firma, $llaves['llave'], 0)) {
                return null;
            }
            $publica = openssl_pkey_get_public($llaves['publica']);
            if ($publica === false || openssl_verify($segmento, $firma, $publica, 0) !== 1) {
                return null;
            }
        }

        return $segmento . '.' . self::base64url($firma);
    }

    private static function base64url(string $dato): string
    {
        return rtrim(strtr(base64_encode($dato), '+/', '-_'), '=');
    }

    private static function expiraSegundos(string $fecha): ?int
    {
        $fecha = trim($fecha);
        if ($fecha === '') {
            return null;
        }
        $marca = strtotime(strlen($fecha) <= 10 ? $fecha . ' 23:59:59 UTC' : $fecha);

        return $marca === false ? null : $marca;
    }

    private static function demasiadosIntentos(string $ip): bool
    {
        $ruta = BASE_PATH . self::INTENTOS;
        $directorio = dirname($ruta);
        if (!is_dir($directorio) || !is_writable($directorio)) {
            return false;
        }
        $llave = hash('sha256', $ip);
        $datos = is_file($ruta) ? json_decode((string) file_get_contents($ruta), true) : [];
        if (!is_array($datos)) {
            $datos = [];
        }
        $ahora = time();
        $ventana = is_array($datos[$llave] ?? null) ? $datos[$llave] : ['desde' => $ahora, 'conteo' => 0];
        if ($ahora - (int) ($ventana['desde'] ?? 0) > 60) {
            $ventana = ['desde' => $ahora, 'conteo' => 0];
        }
        $ventana['conteo'] = (int) ($ventana['conteo'] ?? 0) + 1;
        $datos = array_filter($datos, static fn ($fila): bool => is_array($fila) && $ahora - (int) ($fila['desde'] ?? 0) <= 120);
        $datos[$llave] = $ventana;
        @file_put_contents($ruta, json_encode($datos), LOCK_EX);

        return (int) $ventana['conteo'] > 20;
    }

    /** @return array<string,string> */
    private static function error(string $codigo, string $mensaje): array
    {
        return ['codigo' => $codigo, 'mensaje' => $mensaje];
    }
}
