<?php

declare(strict_types=1);

namespace App\Core;

final class Session
{
    private static bool $iniciada = false;

    public static function start(): void
    {
        if (self::$iniciada || PHP_SAPI === 'cli') {
            return;
        }
        self::$iniciada = true;

        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        $segura = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');

        $vida = (int) config('session.lifetime', 1209600);
        ini_set('session.gc_maxlifetime', (string) $vida);
        ini_set('session.cookie_lifetime', (string) $vida);

        session_name((string) config('session.name', 'mikato_estetica_sid'));
        session_set_cookie_params([
            'lifetime' => $vida,
            'path' => '/',
            'secure' => $segura,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        session_start();
    }

    public static function get(string $clave, mixed $porDefecto = null): mixed
    {
        return $_SESSION[$clave] ?? $porDefecto;
    }

    public static function put(string $clave, mixed $valor): void
    {
        $_SESSION[$clave] = $valor;
    }

    public static function forget(string $clave): void
    {
        unset($_SESSION[$clave]);
    }

    public static function destruir(): void
    {
        $_SESSION = [];
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }

    public static function flash(string $clave, ?string $valor = null): ?string
    {
        if ($valor !== null) {
            $_SESSION['_flash'][$clave] = $valor;
            return null;
        }
        $mensaje = $_SESSION['_flash'][$clave] ?? null;
        unset($_SESSION['_flash'][$clave]);
        return is_string($mensaje) ? $mensaje : null;
    }

    /** @param array<string, mixed> $datos */
    public static function recordarViejo(array $datos): void
    {
        unset($datos['password'], $datos['password_confirm'], $datos['_token']);
        $_SESSION['_viejo'] = $datos;
    }

    public static function olvidarViejo(): void
    {
        unset($_SESSION['_viejo']);
    }

    public static function csrfToken(): string
    {
        if (empty($_SESSION['_csrf'])) {
            $_SESSION['_csrf'] = bin2hex(random_bytes(32));
        }
        return (string) $_SESSION['_csrf'];
    }

    public static function csrfValido(?string $token): bool
    {
        $esperado = $_SESSION['_csrf'] ?? '';
        return is_string($token) && $esperado !== '' && hash_equals($esperado, $token);
    }

    /** @return array{0: bool, 1: int} */
    public static function limitar(string $accion, int $maximo, int $ventana): array
    {
        $ahora = time();
        $clave = '_rl_' . $accion;
        $marcas = $_SESSION[$clave] ?? [];
        $marcas = array_values(array_filter(
            is_array($marcas) ? $marcas : [],
            static fn (int $t): bool => $t > $ahora - $ventana
        ));

        if (count($marcas) >= $maximo) {
            return [false, max(1, ($marcas[0] ?? $ahora) + $ventana - $ahora)];
        }

        $marcas[] = $ahora;
        $_SESSION[$clave] = $marcas;
        return [true, 0];
    }
}
