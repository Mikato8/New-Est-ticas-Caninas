<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

final class MascotaRepository
{
    private const CAMPOS = 'm.*, c.nombre as cliente_nombre, c.telefono as cliente_telefono, c.correo as cliente_correo, c.usuario_id as cliente_usuario_id';

    /** @return list<array<string, mixed>> */
    public static function listar(string $busqueda = '', int $limite = 200): array
    {
        $busqueda = trim($busqueda);
        if ($busqueda !== '') {
            $termino = '%' . mb_strtolower($busqueda) . '%';
            return Database::select(
                'select ' . self::CAMPOS . ' from mascotas m join clientes c on c.id = m.cliente_id
                 where lower(m.nombre) like ? or lower(coalesce(m.raza, \'\')) like ? or lower(c.nombre) like ?
                 order by m.nombre limit ?',
                [$termino, $termino, $termino, $limite]
            );
        }
        return Database::select(
            'select ' . self::CAMPOS . ' from mascotas m join clientes c on c.id = m.cliente_id
             where m.activo = true order by m.nombre limit ?',
            [$limite]
        );
    }

    /** @return array<string, mixed>|null */
    public static function porId(int $id): ?array
    {
        return Database::first(
            'select ' . self::CAMPOS . ' from mascotas m join clientes c on c.id = m.cliente_id where m.id = ?',
            [$id]
        );
    }

    /** @return list<array<string, mixed>> */
    public static function porCliente(int $clienteId): array
    {
        return Database::select(
            'select * from mascotas where cliente_id = ? and activo = true order by nombre',
            [$clienteId]
        );
    }

    /** @return list<array<string, mixed>> */
    public static function porUsuario(int $usuarioId): array
    {
        return Database::select(
            'select m.*, c.nombre as cliente_nombre
             from mascotas m join clientes c on c.id = m.cliente_id
             where c.usuario_id = ? and m.activo = true order by m.nombre',
            [$usuarioId]
        );
    }

    /** @param array<string, mixed> $datos */
    public static function crear(array $datos): int
    {
        $fila = Database::insert(
            'insert into mascotas (cliente_id, nombre, especie, raza, tamano, sexo, nacimiento, peso, temperamento, alergias, notas, foto)
             values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) returning id',
            [
                (int) $datos['cliente_id'],
                (string) $datos['nombre'],
                (string) ($datos['especie'] ?? 'perro'),
                $datos['raza'] ?? null,
                (string) ($datos['tamano'] ?? 'mediano'),
                $datos['sexo'] ?? null,
                !empty($datos['nacimiento']) ? $datos['nacimiento'] : null,
                !empty($datos['peso']) ? (float) $datos['peso'] : null,
                $datos['temperamento'] ?? null,
                $datos['alergias'] ?? null,
                $datos['notas'] ?? null,
                $datos['foto'] ?? null,
            ]
        );
        return (int) ($fila['id'] ?? 0);
    }

    /** @param array<string, mixed> $datos */
    public static function actualizar(int $id, array $datos): void
    {
        Database::execute(
            'update mascotas set cliente_id = ?, nombre = ?, especie = ?, raza = ?, tamano = ?, sexo = ?, nacimiento = ?,
             peso = ?, temperamento = ?, alergias = ?, notas = ?, foto = coalesce(?, foto) where id = ?',
            [
                (int) $datos['cliente_id'],
                (string) $datos['nombre'],
                (string) ($datos['especie'] ?? 'perro'),
                $datos['raza'] ?? null,
                (string) ($datos['tamano'] ?? 'mediano'),
                $datos['sexo'] ?? null,
                !empty($datos['nacimiento']) ? $datos['nacimiento'] : null,
                !empty($datos['peso']) ? (float) $datos['peso'] : null,
                $datos['temperamento'] ?? null,
                $datos['alergias'] ?? null,
                $datos['notas'] ?? null,
                $datos['foto'] ?? null,
                $id,
            ]
        );
    }

    public static function eliminar(int $id): void
    {
        Database::execute('update mascotas set activo = false where id = ?', [$id]);
    }

    public static function contar(): int
    {
        return (int) Database::value('select count(*) from mascotas where activo = true');
    }

    /** @return list<array<string, mixed>> */
    public static function historial(int $mascotaId, int $limite = 50): array
    {
        return Database::select(
            'select h.*, ci.fecha as cita_fecha
             from historial_estetica h left join citas ci on ci.id = h.cita_id
             where h.mascota_id = ? order by h.fecha desc, h.id desc limit ?',
            [$mascotaId, $limite]
        );
    }

    /** @param array<string, mixed> $datos */
    public static function agregarHistorial(int $mascotaId, array $datos): void
    {
        Database::execute(
            'insert into historial_estetica (mascota_id, cita_id, fecha, servicio, observaciones, proxima_visita, foto)
             values (?, ?, ?, ?, ?, ?, ?)',
            [
                $mascotaId,
                !empty($datos['cita_id']) ? (int) $datos['cita_id'] : null,
                !empty($datos['fecha']) ? $datos['fecha'] : date('Y-m-d'),
                (string) $datos['servicio'],
                $datos['observaciones'] ?? null,
                !empty($datos['proxima_visita']) ? $datos['proxima_visita'] : null,
                $datos['foto'] ?? null,
            ]
        );
    }

    /** @return list<array<string, mixed>> */
    public static function cumpleanerosMes(int $mes): array
    {
        return Database::select(
            'select m.*, c.nombre as cliente_nombre, c.telefono as cliente_telefono
             from mascotas m join clientes c on c.id = m.cliente_id
             where extract(month from m.nacimiento) = ? and m.activo = true order by m.nacimiento',
            [$mes]
        );
    }
}
