<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

final class ProductoRepository
{
    /** @return list<array<string, mixed>> */
    public static function activos(?string $categoria = null): array
    {
        if ($categoria !== null && $categoria !== '') {
            return Database::select(
                'select * from productos where activo = true and categoria = ? order by destacado desc, nombre',
                [$categoria]
            );
        }
        return Database::select('select * from productos where activo = true order by destacado desc, nombre');
    }

    /** @return list<array<string, mixed>> */
    public static function destacados(int $limite = 6): array
    {
        return Database::select(
            'select * from productos where activo = true and destacado = true order by nombre limit ?',
            [$limite]
        );
    }

    /** @return list<array<string, mixed>> */
    public static function todos(): array
    {
        return Database::select('select * from productos order by activo desc, nombre');
    }

    /** @return array<string, mixed>|null */
    public static function porId(int $id): ?array
    {
        return Database::first('select * from productos where id = ?', [$id]);
    }

    /** @param array<string, mixed> $datos */
    public static function crear(array $datos): int
    {
        $fila = Database::insert(
            'insert into productos (nombre, descripcion, categoria, presentacion, precio, precio_mayoreo, costo, stock, sku, imagen, activo, destacado)
             values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) returning id',
            [
                (string) $datos['nombre'], $datos['descripcion'] ?? null, (string) ($datos['categoria'] ?? 'shampoo'),
                $datos['presentacion'] ?? null, (float) ($datos['precio'] ?? 0),
                !empty($datos['precio_mayoreo']) ? (float) $datos['precio_mayoreo'] : null,
                !empty($datos['costo']) ? (float) $datos['costo'] : null,
                (int) ($datos['stock'] ?? 0), $datos['sku'] ?? null, $datos['imagen'] ?? null,
                filter_var($datos['activo'] ?? true, FILTER_VALIDATE_BOOLEAN),
                filter_var($datos['destacado'] ?? false, FILTER_VALIDATE_BOOLEAN),
            ]
        );
        return (int) ($fila['id'] ?? 0);
    }

    /** @param array<string, mixed> $datos */
    public static function actualizar(int $id, array $datos): void
    {
        Database::execute(
            'update productos set nombre = ?, descripcion = ?, categoria = ?, presentacion = ?, precio = ?, precio_mayoreo = ?,
             costo = ?, stock = ?, sku = ?, imagen = coalesce(?, imagen), activo = ?, destacado = ? where id = ?',
            [
                (string) $datos['nombre'], $datos['descripcion'] ?? null, (string) ($datos['categoria'] ?? 'shampoo'),
                $datos['presentacion'] ?? null, (float) ($datos['precio'] ?? 0),
                !empty($datos['precio_mayoreo']) ? (float) $datos['precio_mayoreo'] : null,
                !empty($datos['costo']) ? (float) $datos['costo'] : null,
                (int) ($datos['stock'] ?? 0), $datos['sku'] ?? null, $datos['imagen'] ?? null,
                filter_var($datos['activo'] ?? true, FILTER_VALIDATE_BOOLEAN),
                filter_var($datos['destacado'] ?? false, FILTER_VALIDATE_BOOLEAN),
                $id,
            ]
        );
    }

    public static function eliminar(int $id): void
    {
        Database::execute('update productos set activo = false where id = ?', [$id]);
    }

    public static function ajustarStock(int $id, int $delta): void
    {
        Database::execute('update productos set stock = greatest(0, stock + ?) where id = ?', [$delta, $id]);
    }

    public static function contar(): int
    {
        return (int) Database::value('select count(*) from productos where activo = true');
    }

    /** @return list<array<string, mixed>> */
    public static function bajoStock(int $umbral = 5): array
    {
        return Database::select(
            'select * from productos where activo = true and stock <= ? order by stock, nombre',
            [$umbral]
        );
    }

    /** @return array<string, string> */
    public static function categorias(): array
    {
        return [
            'shampoo' => 'Shampoo',
            'acondicionador' => 'Acondicionador',
            'perfume' => 'Perfume',
            'cepillo' => 'Cepillo',
            'herramienta' => 'Herramienta',
            'snack' => 'Snack',
            'otro' => 'Otro',
        ];
    }
}
