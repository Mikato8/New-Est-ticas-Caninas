<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

final class ClienteRepository
{
    /** @return list<array<string, mixed>> */
    public static function listar(string $busqueda = '', int $limite = 200): array
    {
        $busqueda = trim($busqueda);
        if ($busqueda !== '') {
            $termino = '%' . mb_strtolower($busqueda) . '%';
            return Database::select(
                'select c.*, u.correo as correo_usuario,
                        (select count(*) from mascotas m where m.cliente_id = c.id) as mascotas
                 from clientes c left join usuarios u on u.id = c.usuario_id
                 where lower(c.nombre) like ? or lower(coalesce(c.correo, \'\')) like ? or coalesce(c.telefono, \'\') like ?
                 order by c.nombre limit ?',
                [$termino, $termino, '%' . $busqueda . '%', $limite]
            );
        }
        return Database::select(
            'select c.*, u.correo as correo_usuario,
                    (select count(*) from mascotas m where m.cliente_id = c.id) as mascotas,
                    (select max(fecha) from citas ci where ci.cliente_id = c.id) as ultima_cita
             from clientes c left join usuarios u on u.id = c.usuario_id
             order by c.nombre limit ?',
            [$limite]
        );
    }

    /** @return array<string, mixed>|null */
    public static function porId(int $id): ?array
    {
        return Database::first(
            'select c.*, u.correo as correo_usuario, u.rol
             from clientes c left join usuarios u on u.id = c.usuario_id where c.id = ?',
            [$id]
        );
    }

    /** @return array<string, mixed>|null */
    public static function porUsuario(int $usuarioId): ?array
    {
        return Database::first('select * from clientes where usuario_id = ? order by id limit 1', [$usuarioId]);
    }

    /** @return array<string, mixed>|null */
    public static function porContacto(string $telefono, string $correo): ?array
    {
        $telefono = trim($telefono);
        $correo = trim($correo);
        if ($telefono === '' && $correo === '') {
            return null;
        }
        return Database::first(
            "select * from clientes
             where (correo <> '' and lower(correo) = lower(?))
                or (telefono <> '' and regexp_replace(telefono, '[^0-9]', '', 'g') = regexp_replace(?, '[^0-9]', '', 'g'))
             order by id limit 1",
            [$correo, $telefono]
        );
    }

    public static function asegurarParaUsuario(int $usuarioId): int
    {
        $existente = self::porUsuario($usuarioId);
        if ($existente !== null) {
            return (int) $existente['id'];
        }

        $usuario = UsuarioRepository::porId($usuarioId);
        if ($usuario === null) {
            return 0;
        }

        $fila = Database::insert(
            'insert into clientes (usuario_id, nombre, telefono, correo, direccion) values (?, ?, ?, ?, ?) returning id',
            [$usuarioId, (string) $usuario['nombre'], $usuario['telefono'] ?? null, $usuario['correo'] ?? null, null]
        );
        return (int) ($fila['id'] ?? 0);
    }

    /** @param array<string, mixed> $datos */
    public static function crear(array $datos): int
    {
        $fila = Database::insert(
            'insert into clientes (usuario_id, nombre, telefono, correo, direccion, notas)
             values (?, ?, ?, ?, ?, ?) returning id',
            [
                !empty($datos['usuario_id']) ? (int) $datos['usuario_id'] : null,
                (string) $datos['nombre'],
                $datos['telefono'] ?? null,
                $datos['correo'] ?? null,
                $datos['direccion'] ?? null,
                $datos['notas'] ?? null,
            ]
        );
        return (int) ($fila['id'] ?? 0);
    }

    /** @param array<string, mixed> $datos */
    public static function actualizar(int $id, array $datos): void
    {
        Database::execute(
            'update clientes set nombre = ?, telefono = ?, correo = ?, direccion = ?, notas = ? where id = ?',
            [
                (string) $datos['nombre'],
                $datos['telefono'] ?? null,
                $datos['correo'] ?? null,
                $datos['direccion'] ?? null,
                $datos['notas'] ?? null,
                $id,
            ]
        );
    }

    public static function eliminar(int $id): void
    {
        Database::execute('delete from clientes where id = ?', [$id]);
    }

    public static function contar(): int
    {
        return (int) Database::value('select count(*) from clientes');
    }

    /** @return list<array<string, mixed>> */
    public static function opciones(): array
    {
        return Database::select('select id, nombre, telefono from clientes order by nombre');
    }
}
