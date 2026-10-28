<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

final class VentaRepository
{
    /**
     * @param array<string, mixed> $venta
     * @param list<array<string, mixed>> $items
     */
    public static function crearConItems(array $venta, array $items): int
    {
        return (int) Database::transaction(static function () use ($venta, $items): int {
            $subtotal = 0.0;
            foreach ($items as $item) {
                $subtotal += (float) $item['importe'];
            }
            $descuento = (float) ($venta['descuento'] ?? 0);
            $total = max(0, $subtotal - $descuento);

            $fila = Database::insert(
                'insert into ventas (folio, cliente_id, fecha, subtotal, descuento, total, metodo_pago, estado, notas, registrado_por)
                 values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?) returning id',
                [
                    self::folioUnico(),
                    !empty($venta['cliente_id']) ? (int) $venta['cliente_id'] : null,
                    !empty($venta['fecha']) ? $venta['fecha'] : date('Y-m-d'),
                    $subtotal,
                    $descuento,
                    $total,
                    (string) ($venta['metodo_pago'] ?? 'efectivo'),
                    (string) ($venta['estado'] ?? 'pagada'),
                    $venta['notas'] ?? null,
                    !empty($venta['registrado_por']) ? (int) $venta['registrado_por'] : null,
                ]
            );
            $ventaId = (int) ($fila['id'] ?? 0);

            foreach ($items as $item) {
                Database::execute(
                    'insert into venta_items (venta_id, tipo, referencia_id, descripcion, cantidad, precio_unitario, importe)
                     values (?, ?, ?, ?, ?, ?, ?)',
                    [
                        $ventaId,
                        (string) $item['tipo'],
                        !empty($item['referencia_id']) ? (int) $item['referencia_id'] : null,
                        (string) $item['descripcion'],
                        (int) $item['cantidad'],
                        (float) $item['precio_unitario'],
                        (float) $item['importe'],
                    ]
                );
                if (($item['tipo'] ?? '') === 'producto' && !empty($item['referencia_id'])) {
                    Database::execute(
                        'update productos set stock = greatest(0, stock - ?) where id = ?',
                        [(int) $item['cantidad'], (int) $item['referencia_id']]
                    );
                }
            }

            return $ventaId;
        });
    }

    private static function folioUnico(): string
    {
        for ($intento = 0; $intento < 10; $intento++) {
            $folio = 'V-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(2)));
            if ((int) Database::value('select count(*) from ventas where folio = ?', [$folio]) === 0) {
                return $folio;
            }
        }
        return 'V-' . date('Ymd-His') . '-' . strtoupper(bin2hex(random_bytes(2)));
    }

    /** @return list<array<string, mixed>> */
    public static function listar(string $desde = '', string $hasta = '', int $limite = 200): array
    {
        if ($desde !== '' && $hasta !== '') {
            return Database::select(
                'select v.*, c.nombre as cliente_nombre, (select count(*) from venta_items vi where vi.venta_id = v.id) as items
                 from ventas v left join clientes c on c.id = v.cliente_id
                 where v.fecha between ? and ? order by v.fecha desc, v.id desc limit ?',
                [$desde, $hasta, $limite]
            );
        }
        return Database::select(
            'select v.*, c.nombre as cliente_nombre, (select count(*) from venta_items vi where vi.venta_id = v.id) as items
             from ventas v left join clientes c on c.id = v.cliente_id
             order by v.fecha desc, v.id desc limit ?',
            [$limite]
        );
    }

    /** @return array<string, mixed>|null */
    public static function porId(int $id): ?array
    {
        return Database::first(
            'select v.*, c.nombre as cliente_nombre, c.telefono as cliente_telefono, u.nombre as registrado_nombre
             from ventas v left join clientes c on c.id = v.cliente_id left join usuarios u on u.id = v.registrado_por
             where v.id = ?',
            [$id]
        );
    }

    /** @return list<array<string, mixed>> */
    public static function items(int $ventaId): array
    {
        return Database::select('select * from venta_items where venta_id = ? order by id', [$ventaId]);
    }

    public static function anular(int $id): void
    {
        Database::execute("update ventas set estado = 'cancelada' where id = ?", [$id]);
    }

    /** @return array{total: float, tickets: int, ticket: float} */
    public static function resumen(string $desde, string $hasta): array
    {
        $fila = Database::first(
            "select coalesce(sum(total), 0) as total, count(*) as tickets
             from ventas where estado = 'pagada' and fecha between ? and ?",
            [$desde, $hasta]
        );
        $total = (float) ($fila['total'] ?? 0);
        $tickets = (int) ($fila['tickets'] ?? 0);
        return ['total' => $total, 'tickets' => $tickets, 'ticket' => $tickets > 0 ? $total / $tickets : 0.0];
    }

    /** @return list<array<string, mixed>> */
    public static function serieDias(string $desde, string $hasta): array
    {
        return Database::select(
            "select fecha, sum(total) as total, count(*) as tickets
             from ventas where estado = 'pagada' and fecha between ? and ? group by fecha order by fecha",
            [$desde, $hasta]
        );
    }

    /** @return list<array<string, mixed>> */
    public static function topConceptos(string $desde, string $hasta, int $limite = 6): array
    {
        return Database::select(
            'select vi.descripcion, sum(vi.cantidad) as cantidad, sum(vi.importe) as total
             from venta_items vi join ventas v on v.id = vi.venta_id
             where v.estado = \'pagada\' and v.fecha between ? and ?
             group by vi.descripcion order by total desc limit ?',
            [$desde, $hasta, $limite]
        );
    }

    /** @return list<array<string, mixed>> */
    public static function ultimas(int $limite = 8): array
    {
        return Database::select(
            'select v.*, c.nombre as cliente_nombre from ventas v left join clientes c on c.id = v.cliente_id
             where v.estado = \'pagada\' order by v.fecha desc, v.id desc limit ?',
            [$limite]
        );
    }


    /** @return list<array<string, mixed>> */
    public static function porCliente(int $clienteId, int $limite = 30): array
    {
        return Database::select(
            'select v.*, (select count(*) from venta_items vi where vi.venta_id = v.id) as items
             from ventas v where v.cliente_id = ? order by v.fecha desc, v.id desc limit ?',
            [$clienteId, $limite]
        );
    }

    public static function contar(): int
    {
        return (int) Database::value('select count(*) from ventas');
    }
}
