<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

final class PaqueteRepository
{
    /** @return list<array<string, mixed>> */
    public static function activos(): array
    {
        return Database::select(
            'select p.*, (select count(*) from paquete_servicios ps where ps.paquete_id = p.id) as servicios
             from paquetes p where p.activo = true order by p.precio'
        );
    }

    /** @return list<array<string, mixed>> */
    public static function todos(): array
    {
        return Database::select('select * from paquetes order by activo desc, nombre');
    }

    /** @return array<string, mixed>|null */
    public static function porId(int $id): ?array
    {
        return Database::first('select * from paquetes where id = ?', [$id]);
    }

    /** @return list<array<string, mixed>> */
    public static function serviciosDe(int $paqueteId): array
    {
        return Database::select(
            'select s.*, ps.cantidad from paquete_servicios ps join servicios s on s.id = ps.servicio_id
             where ps.paquete_id = ? order by s.orden',
            [$paqueteId]
        );
    }

    /** @param array<string, mixed> $datos */
    public static function crear(array $datos): int
    {
        $fila = Database::insert(
            'insert into paquetes (nombre, descripcion, precio, tamano, activo) values (?, ?, ?, ?, ?) returning id',
            [
                (string) $datos['nombre'], $datos['descripcion'] ?? null, (float) ($datos['precio'] ?? 0),
                (string) ($datos['tamano'] ?? 'todos'), filter_var($datos['activo'] ?? true, FILTER_VALIDATE_BOOLEAN),
            ]
        );
        return (int) ($fila['id'] ?? 0);
    }

    /** @param array<string, mixed> $datos */
    public static function actualizar(int $id, array $datos): void
    {
        Database::execute(
            'update paquetes set nombre = ?, descripcion = ?, precio = ?, tamano = ?, activo = ? where id = ?',
            [
                (string) $datos['nombre'], $datos['descripcion'] ?? null, (float) ($datos['precio'] ?? 0),
                (string) ($datos['tamano'] ?? 'todos'), filter_var($datos['activo'] ?? true, FILTER_VALIDATE_BOOLEAN), $id,
            ]
        );
    }

    public static function eliminar(int $id): void
    {
        Database::execute('update paquetes set activo = false where id = ?', [$id]);
    }

    public static function contar(): int
    {
        return (int) Database::value('select count(*) from paquetes where activo = true');
    }
}
