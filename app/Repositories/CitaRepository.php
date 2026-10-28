<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

final class CitaRepository
{
    private const SELECT = 'select ci.*, cl.nombre as cliente_nombre, cl.telefono as cliente_telefono, cl.correo as cliente_correo,
        m.nombre as mascota_nombre, m.raza, m.tamano, m.especie, m.foto as mascota_foto, m.alergias, m.temperamento,
        s.nombre as servicio_nombre, s.duracion_min, s.categoria as servicio_categoria
        from citas ci
        join clientes cl on cl.id = ci.cliente_id
        join mascotas m on m.id = ci.mascota_id
        join servicios s on s.id = ci.servicio_id';

    /** @return list<array<string, mixed>> */
    public static function porFecha(string $fecha): array
    {
        return Database::select(self::SELECT . ' where ci.fecha = ? order by ci.hora', [$fecha]);
    }

    /** @return list<array<string, mixed>> */
    public static function rango(string $desde, string $hasta): array
    {
        return Database::select(self::SELECT . ' where ci.fecha between ? and ? order by ci.fecha, ci.hora', [$desde, $hasta]);
    }

    /** @return array<string, mixed>|null */
    public static function porId(int $id): ?array
    {
        return Database::first(self::SELECT . ' where ci.id = ?', [$id]);
    }

    /** @return array<string, mixed>|null */
    public static function porCodigo(string $codigo): ?array
    {
        return Database::first(self::SELECT . ' where upper(ci.codigo) = upper(?)', [$codigo]);
    }

    /** @return list<array<string, mixed>> */
    public static function porCliente(int $clienteId, int $limite = 50): array
    {
        return Database::select(self::SELECT . ' where ci.cliente_id = ? order by ci.fecha desc, ci.hora desc limit ?', [$clienteId, $limite]);
    }

    /** @return list<array<string, mixed>> */
    public static function porUsuario(int $usuarioId, int $limite = 50): array
    {
        return Database::select(self::SELECT . ' where cl.usuario_id = ? order by ci.fecha desc, ci.hora desc limit ?', [$usuarioId, $limite]);
    }

    /** @return list<array<string, mixed>> */
    public static function proximas(int $limite = 8): array
    {
        return Database::select(
            self::SELECT . " where ci.fecha >= current_date and ci.estado in ('pendiente', 'confirmada', 'en_proceso')
             order by ci.fecha, ci.hora limit ?",
            [$limite]
        );
    }

    /** @return list<array<string, mixed>> */
    public static function proximasDeUsuario(int $usuarioId, int $limite = 5): array
    {
        return Database::select(
            self::SELECT . " where cl.usuario_id = ? and ci.fecha >= current_date and ci.estado in ('pendiente', 'confirmada')
             order by ci.fecha, ci.hora limit ?",
            [$usuarioId, $limite]
        );
    }

    /** @return list<string> */
    public static function horasOcupadas(string $fecha): array
    {
        return array_map(
            static fn (array $fila): string => substr((string) $fila['hora'], 0, 5),
            Database::select(
                "select hora from citas where fecha = ? and estado in ('pendiente', 'confirmada', 'en_proceso')",
                [$fecha]
            )
        );
    }

    public static function slotOcupado(string $fecha, string $hora): bool
    {
        return (int) Database::value(
            "select count(*) from citas where fecha = ? and hora = ? and estado in ('pendiente', 'confirmada', 'en_proceso')",
            [$fecha, $hora]
        ) > 0;
    }

    public static function slotOcupadoExcepto(string $fecha, string $hora, int $exceptoId): bool
    {
        return (int) Database::value(
            "select count(*) from citas where fecha = ? and hora = ? and id <> ? and estado in ('pendiente', 'confirmada', 'en_proceso')",
            [$fecha, $hora, $exceptoId]
        ) > 0;
    }

    /** @param array<string, mixed> $datos */
    public static function crear(array $datos): int
    {
        $codigo = self::codigoUnico();
        $fila = Database::insert(
            'insert into citas (codigo, cliente_id, mascota_id, servicio_id, fecha, hora, estado, precio, notas, origen)
             values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?) returning id',
            [
                $codigo,
                (int) $datos['cliente_id'],
                (int) $datos['mascota_id'],
                (int) $datos['servicio_id'],
                (string) $datos['fecha'],
                (string) $datos['hora'],
                (string) ($datos['estado'] ?? 'pendiente'),
                (float) ($datos['precio'] ?? 0),
                $datos['notas'] ?? null,
                (string) ($datos['origen'] ?? 'web'),
            ]
        );
        return (int) ($fila['id'] ?? 0);
    }

    private static function codigoUnico(): string
    {
        for ($intento = 0; $intento < 8; $intento++) {
            $codigo = 'MK-' . strtoupper(bin2hex(random_bytes(3)));
            if ((int) Database::value('select count(*) from citas where codigo = ?', [$codigo]) === 0) {
                return $codigo;
            }
        }
        return 'MK-' . strtoupper(bin2hex(random_bytes(5)));
    }

    public static function cambiarEstado(int $id, string $estado): void
    {
        Database::execute('update citas set estado = ?, actualizado_en = now() where id = ?', [$estado, $id]);
    }

    public static function reagendar(int $id, string $fecha, string $hora): void
    {
        Database::execute('update citas set fecha = ?, hora = ?, actualizado_en = now() where id = ?', [$fecha, $hora, $id]);
    }

    public static function actualizarPrecio(int $id, float $precio): void
    {
        Database::execute('update citas set precio = ? where id = ?', [$precio, $id]);
    }

    /** @return array<string, int> */
    public static function resumenHoy(): array
    {
        $filas = Database::select(
            "select estado, count(*) as total from citas where fecha = current_date group by estado"
        );
        $resumen = ['total' => 0, 'pendiente' => 0, 'confirmada' => 0, 'en_proceso' => 0, 'completada' => 0, 'cancelada' => 0, 'no_asistio' => 0];
        foreach ($filas as $fila) {
            $estado = (string) $fila['estado'];
            $total = (int) $fila['total'];
            $resumen[$estado] = $total;
            $resumen['total'] += $total;
        }
        return $resumen;
    }

    /** @return list<array<string, mixed>> */
    public static function agendaSemana(string $desde): array
    {
        $hasta = date('Y-m-d', strtotime($desde . ' +6 days'));
        return Database::select(
            "select fecha, count(*) as total,
                    count(*) filter (where estado = 'completada') as completadas,
                    count(*) filter (where estado in ('pendiente','confirmada','en_proceso')) as activas
             from citas where fecha between ? and ? group by fecha order by fecha",
            [$desde, $hasta]
        );
    }

    public static function contarHoy(): int
    {
        return (int) Database::value('select count(*) from citas where fecha = current_date');
    }

    /** @return list<array<string, mixed>> */
    public static function todas(int $limite = 100): array
    {
        return Database::select(self::SELECT . ' order by ci.fecha desc, ci.hora desc limit ?', [$limite]);
    }

    public static function contar(): int
    {
        return (int) Database::value('select count(*) from citas');
    }

    public static function ingresosCompletados(string $desde, string $hasta): float
    {
        return (float) Database::value(
            "select coalesce(sum(precio), 0) from citas where estado = 'completada' and fecha between ? and ?",
            [$desde, $hasta]
        );
    }
}
