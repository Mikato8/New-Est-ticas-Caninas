<?php

declare(strict_types=1);

namespace App\Core;

final class Auth
{
    private static ?array $usuario = null;

    private static bool $cargado = false;

    /** @return array<string, mixed>|null */
    public static function usuario(): ?array
    {
        if (self::$cargado) {
            return self::$usuario;
        }
        self::$cargado = true;

        $id = Session::get('usuario_id');
        if (!is_int($id) && !is_numeric($id)) {
            return null;
        }

        $fila = Database::first(
            'select id, nombre, usuario, correo, rol, estado, ciudad, estado_geo, telefono, whatsapp, bio, avatar, acceso_hasta, creado_en, ultimo_acceso
             from usuarios where id = ?',
            [(int) $id]
        );

        if ($fila === null || ($fila['estado'] ?? '') !== 'activo') {
            Session::forget('usuario_id');
            return null;
        }
        if (($fila['rol'] ?? '') !== 'admin' && !empty($fila['acceso_hasta']) && (string) $fila['acceso_hasta'] < date('Y-m-d')) {
            Session::forget('usuario_id');
            return null;
        }

        self::$usuario = $fila;
        return self::$usuario;
    }

    /** @return array<string, mixed>|null */
    public static function intentar(string $login, string $password): ?array
    {
        $login = trim($login);
        if ($login === '' || $password === '') {
            return null;
        }

        $fila = Database::first(
            'select * from usuarios where lower(correo) = lower(?) or lower(usuario) = lower(?) limit 1',
            [$login, $login]
        );

        if ($fila === null || !password_verify($password, (string) $fila['password_hash'])) {
            return null;
        }

        if (($fila['estado'] ?? '') !== 'activo') {
            return null;
        }
        if (($fila['rol'] ?? '') !== 'admin' && !empty($fila['acceso_hasta']) && (string) $fila['acceso_hasta'] < date('Y-m-d')) {
            return null;
        }

        if (password_needs_rehash((string) $fila['password_hash'], PASSWORD_DEFAULT)) {
            Database::execute('update usuarios set password_hash = ? where id = ?', [
                password_hash($password, PASSWORD_DEFAULT),
                (int) $fila['id'],
            ]);
        }

        session_regenerate_id(true);
        Session::put('usuario_id', (int) $fila['id']);
        Database::execute('update usuarios set ultimo_acceso = now() where id = ?', [(int) $fila['id']]);

        self::$usuario = $fila;
        self::$cargado = true;

        return $fila;
    }

    public static function check(): bool
    {
        return self::usuario() !== null;
    }

    public static function id(): ?int
    {
        $usuario = self::usuario();
        return $usuario === null ? null : (int) $usuario['id'];
    }

    /** @param list<string> $roles */
    public static function tieneRol(array $roles): bool
    {
        $usuario = self::usuario();
        return $usuario !== null && in_array((string) $usuario['rol'], $roles, true);
    }

    public static function esAdmin(): bool
    {
        return self::tieneRol(['admin']);
    }

    public static function puedeModerar(): bool
    {
        return self::tieneRol(['admin', 'editor']);
    }

    /** Personal del salón: puede entrar al panel de gestión. */
    public static function esPersonal(): bool
    {
        return self::tieneRol(['admin', 'editor', 'estilista']);
    }

    public static function esCliente(): bool
    {
        return self::tieneRol(['cliente']);
    }

    public static function requiereLogin(string $destino = ''): void
    {
        if (self::check()) {
            return;
        }
        $destino = $destino !== '' ? $destino : (string) ($_SERVER['REQUEST_URI'] ?? '/');
        Session::flash('error', 'Entra a tu cuenta para continuar.');
        (new Response('', 303))->conCabecera('Location', url('/entrar?destino=' . urlencode($destino)))->enviar();
        exit;
    }

    /** @param list<string> $roles */
    public static function requiereRol(array $roles): void
    {
        self::requiereLogin();
        if (!self::tieneRol($roles)) {
            throw new HttpException('No tienes permiso para entrar a esta sección.', 403);
        }
    }

    public static function salir(): void
    {
        Session::forget('usuario_id');
        self::$usuario = null;
        self::$cargado = true;
        session_regenerate_id(true);
    }

    public static function refrescar(): void
    {
        self::$usuario = null;
        self::$cargado = false;
    }
}
