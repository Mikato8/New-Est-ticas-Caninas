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
            'insert into usuarios (nombre, usuario, correo, telefono, whatsapp, password_hash, rol, estado, ciudad, estado_geo, bio, acceso_hasta)
             values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) returning id',
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
                $datos['acceso_hasta'] ?? null,
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
                'select id, nombre, usuario, correo, telefono, rol, estado, ciudad, acceso_hasta, creado_en, ultimo_acceso
                 from usuarios where rol = ? order by creado_en desc',
                [$rol]
            );
        }
        return Database::select(
            'select id, nombre, usuario, correo, telefono, rol, estado, ciudad, acceso_hasta, creado_en, ultimo_acceso
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

    public static function actualizarAcceso(int $id, ?string $fecha): void
    {
        Database::execute('update usuarios set acceso_hasta = ? where id = ?', [$fecha, $id]);
    }

    /** @return array{total: int, sin_fecha: int, vencidos: int, activos: int} */
    public static function resumenAccesos(): array
    {
        $fila = Database::first(
            "select count(*)::int as total,
                    count(*) filter (where rol <> 'admin' and acceso_hasta is null)::int as sin_fecha,
                    count(*) filter (where rol <> 'admin' and acceso_hasta < current_date)::int as vencidos,
                    count(*) filter (where estado = 'activo')::int as activos
             from usuarios"
        ) ?? [];
        return [
            'total' => (int) ($fila['total'] ?? 0),
            'sin_fecha' => (int) ($fila['sin_fecha'] ?? 0),
            'vencidos' => (int) ($fila['vencidos'] ?? 0),
            'activos' => (int) ($fila['activos'] ?? 0),
        ];
    }

    public static function asignarAccesoFaltante(int $dias): int
    {
        return Database::execute(
            "update usuarios set acceso_hasta = current_date + (?::int * interval '1 day')
             where rol <> 'admin' and acceso_hasta is null",
            [max(1, min(3650, $dias))]
        );
    }
}
