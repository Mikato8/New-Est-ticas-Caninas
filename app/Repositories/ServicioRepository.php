<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

final class ServicioRepository
{
    /** @return list<array<string, mixed>> */
    public static function activos(?string $categoria = null): array
    {
        if ($categoria !== null && $categoria !== '') {
            return Database::select(
                'select * from servicios where activo = true and categoria = ? order by orden, nombre',
                [$categoria]
            );
        }
        return Database::select('select * from servicios where activo = true order by orden, nombre');
    }

    /** @return list<array<string, mixed>> */
    public static function todos(): array
    {
        return Database::select('select * from servicios order by activo desc, orden, nombre');
    }

    /** @return array<string, mixed>|null */
    public static function porId(int $id): ?array
    {
        return Database::first('select * from servicios where id = ?', [$id]);
    }

    /** @param array<string, mixed> $datos */
    public static function crear(array $datos): int
    {
        $fila = Database::insert(
            'insert into servicios (nombre, descripcion, categoria, tamano, precio, duracion_min, activo, orden)
             values (?, ?, ?, ?, ?, ?, ?, ?) returning id',
            [
                (string) $datos['nombre'], $datos['descripcion'] ?? null, (string) ($datos['categoria'] ?? 'bano'),
                (string) ($datos['tamano'] ?? 'todos'), (float) ($datos['precio'] ?? 0),
                (int) ($datos['duracion_min'] ?? 60), filter_var($datos['activo'] ?? true, FILTER_VALIDATE_BOOLEAN),
                (int) ($datos['orden'] ?? 0),
            ]
        );
        return (int) ($fila['id'] ?? 0);
    }

    /** @param array<string, mixed> $datos */
    public static function actualizar(int $id, array $datos): void
    {
        Database::execute(
            'update servicios set nombre = ?, descripcion = ?, categoria = ?, tamano = ?, precio = ?, duracion_min = ?, activo = ?, orden = ? where id = ?',
            [
                (string) $datos['nombre'], $datos['descripcion'] ?? null, (string) ($datos['categoria'] ?? 'bano'),
                (string) ($datos['tamano'] ?? 'todos'), (float) ($datos['precio'] ?? 0),
                (int) ($datos['duracion_min'] ?? 60), filter_var($datos['activo'] ?? true, FILTER_VALIDATE_BOOLEAN),
                (int) ($datos['orden'] ?? 0), $id,
            ]
        );
    }

    public static function eliminar(int $id): void
    {
        Database::execute('update servicios set activo = false where id = ?', [$id]);
    }

    public static function contar(): int
    {
        return (int) Database::value('select count(*) from servicios where activo = true');
    }

    /** @return array<string, string> */
    public static function categorias(): array
    {
        return [
            'bano' => 'Baño',
            'corte' => 'Corte',
            'unas' => 'Uñas',
            'oidos' => 'Oídos',
            'spa' => 'Spa',
            'paquete' => 'Paquete',
        ];
    }
}
