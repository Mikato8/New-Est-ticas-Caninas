<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Support\Slug;

final class UsuarioRepository
{
    /** @return array<string, mixed>|null */
    public static function porLogin(string $login): ?array
    {
        return Database::first(
            'select * from usuarios where lower(correo) = lower(?) or lower(usuario) = lower(?) limit 1',
            [$login, $login]
        );
    }

    /** @return array<string, mixed>|null */
    public static function porId(int $id): ?array
    {
        return Database::first('select * from usuarios where id = ?', [$id]);
    }

    /** @return array<string, mixed>|null */
    public static function porCorreo(string $correo): ?array
    {
        return Database::first('select * from usuarios where lower(correo) = lower(?) limit 1', [$correo]);
    }

    public static function existeCorreo(string $correo): bool
    {
        return (int) Database::value('select count(*) from usuarios where lower(correo) = lower(?)', [$correo]) > 0;
    }

    public static function existeUsuario(string $usuario): bool
    {
        return (int) Database::value('select count(*) from usuarios where lower(usuario) = lower(?)', [$usuario]) > 0;
    }

    /** @param array<string, mixed> $datos */
    public static function crear(array $datos): int
    {
        $fila = Database::insert(
            'insert into usuarios (nombre, usuario, correo, telefono, whatsapp, password_hash, rol, estado, ciudad, estado_geo, bio)
             values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) returning id',
            [
                (string) $datos['nombre'],
                (string) $datos['usuario'],
                (string) $datos['correo'],
                $datos['telefono'] ?? null,
                $datos['whatsapp'] ?? null,
                (string) $datos['password_hash'],
                (string) ($datos['rol'] ?? 'cliente'),
                (string) ($datos['estado'] ?? 'activo'),
                $datos['ciudad'] ?? null,
                $datos['estado_geo'] ?? null,
                $datos['bio'] ?? null,
            ]
        );
        return (int) ($fila['id'] ?? 0);
    }

    public static function usuarioDisponible(string $nombre): string
    {
        return Slug::unico($nombre, static fn (string $candidato): bool => self::existeUsuario($candidato));
    }

    /** @return list<array<string, mixed>> */
    public static function listar(?string $rol = null): array
    {
        if ($rol !== null) {
            return Database::select(
                'select id, nombre, usuario, correo, telefono, rol, estado, ciudad, creado_en, ultimo_acceso
                 from usuarios where rol = ? order by creado_en desc',
                [$rol]
            );
        }
        return Database::select(
            'select id, nombre, usuario, correo, telefono, rol, estado, ciudad, creado_en, ultimo_acceso
             from usuarios order by creado_en desc'
        );
    }

    public static function contar(?string $rol = null): int
    {
        if ($rol !== null) {
            return (int) Database::value('select count(*) from usuarios where rol = ?', [$rol]);
        }
        return (int) Database::value('select count(*) from usuarios');
    }

    /** @param array<string, mixed> $datos */
    public static function actualizarPerfil(int $id, array $datos): void
    {
        Database::execute(
            'update usuarios set nombre = ?, correo = ?, telefono = ?, whatsapp = ?, ciudad = ?, estado_geo = ?, bio = ? where id = ?',
            [
                (string) $datos['nombre'],
                (string) $datos['correo'],
                $datos['telefono'] ?? null,
                $datos['whatsapp'] ?? null,
                $datos['ciudad'] ?? null,
                $datos['estado_geo'] ?? null,
                $datos['bio'] ?? null,
                $id,
            ]
        );
    }

    public static function actualizarAvatar(int $id, string $ruta): void
    {
        Database::execute('update usuarios set avatar = ? where id = ?', [$ruta, $id]);
    }

    public static function actualizarPassword(int $id, string $hash): void
    {
        Database::execute('update usuarios set password_hash = ? where id = ?', [$hash, $id]);
    }

    public static function cambiarEstado(int $id, string $estado): void
    {
        Database::execute('update usuarios set estado = ? where id = ?', [$estado, $id]);
    }

    public static function cambiarRol(int $id, string $rol): void
    {
        Database::execute('update usuarios set rol = ? where id = ?', [$rol, $id]);
    }
}
