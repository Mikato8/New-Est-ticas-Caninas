<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

final class GastoRepository
{
    /** @return list<array<string, mixed>> */
    public static function listar(string $desde = '', string $hasta = '', int $limite = 200): array
    {
        if ($desde !== '' && $hasta !== '') {
            return Database::select(
                'select * from gastos where fecha between ? and ? order by fecha desc, id desc limit ?',
                [$desde, $hasta, $limite]
            );
        }
        return Database::select('select * from gastos order by fecha desc, id desc limit ?', [$limite]);
    }

    /** @param array<string, mixed> $datos */
    public static function crear(array $datos): int
    {
        $fila = Database::insert(
            'insert into gastos (concepto, categoria, monto, fecha, metodo_pago, notas) values (?, ?, ?, ?, ?, ?) returning id',
            [
                (string) $datos['concepto'],
                (string) ($datos['categoria'] ?? 'insumos'),
                (float) ($datos['monto'] ?? 0),
                !empty($datos['fecha']) ? $datos['fecha'] : date('Y-m-d'),
                (string) ($datos['metodo_pago'] ?? 'efectivo'),
                $datos['notas'] ?? null,
            ]
        );
        return (int) ($fila['id'] ?? 0);
    }

    public static function eliminar(int $id): void
    {
        Database::execute('delete from gastos where id = ?', [$id]);
    }

    public static function total(string $desde, string $hasta): float
    {
        return (float) Database::value('select coalesce(sum(monto), 0) from gastos where fecha between ? and ?', [$desde, $hasta]);
    }

    /** @return list<array<string, mixed>> */
    public static function porCategoria(string $desde, string $hasta): array
    {
        return Database::select(
            'select categoria, sum(monto) as total from gastos where fecha between ? and ? group by categoria order by total desc',
            [$desde, $hasta]
        );
    }

    /** @return array<string, string> */
    public static function categorias(): array
    {
        return [
            'insumos' => 'Insumos y productos',
            'renta' => 'Renta',
            'servicios' => 'Servicios (luz, agua, internet)',
            'nomina' => 'Nómina y comisiones',
            'equipo' => 'Equipo y mantenimiento',
            'marketing' => 'Marketing',
            'otro' => 'Otro',
        ];
    }
}
