<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Support\Slug;

final class NegocioRepository
{
    /** @return list<array<string, mixed>> */
    public static function aprobados(string $estadoGeo = '', string $busqueda = '', int $limite = 60): array
    {
        $condiciones = ["estado = 'aprobado'"];
        $parametros = [];
        if ($estadoGeo !== '') {
            $condiciones[] = 'estado_geo = ?';
            $parametros[] = $estadoGeo;
        }
        if ($busqueda !== '') {
            $condiciones[] = '(lower(nombre) like ? or lower(coalesce(ciudad, \'\')) like ? or lower(coalesce(servicios, \'\')) like ?)';
            $termino = '%' . mb_strtolower($busqueda) . '%';
            $parametros[] = $termino;
            $parametros[] = $termino;
            $parametros[] = $termino;
        }
        $parametros[] = $limite;
        return Database::select(
            'select * from negocios where ' . implode(' and ', $condiciones) . ' order by destacado desc, verificado desc, nombre limit ?',
            $parametros
        );
    }

    /** @return array<string, mixed>|null */
    public static function porSlug(string $slug): ?array
    {
        return Database::first("select * from negocios where slug = ? and estado = 'aprobado'", [$slug]);
    }

    /** @return array<string, mixed>|null */
    public static function porId(int $id): ?array
    {
        return Database::first('select * from negocios where id = ?', [$id]);
    }

    /** @param array<string, mixed> $datos */
    public static function crear(array $datos): int
    {
        $slug = Slug::unico((string) $datos['nombre'], static fn (string $candidato): bool =>
            (int) Database::value('select count(*) from negocios where slug = ?', [$candidato]) > 0);
        $fila = Database::insert(
            'insert into negocios (usuario_id, nombre, slug, descripcion, ciudad, estado_geo, direccion, telefono, whatsapp, correo, web, servicios, horario, logo, estado)
             values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) returning id',
            [
                !empty($datos['usuario_id']) ? (int) $datos['usuario_id'] : null,
                (string) $datos['nombre'],
                $slug,
                $datos['descripcion'] ?? null,
                $datos['ciudad'] ?? null,
                $datos['estado_geo'] ?? null,
                $datos['direccion'] ?? null,
                $datos['telefono'] ?? null,
                $datos['whatsapp'] ?? null,
                $datos['correo'] ?? null,
                $datos['web'] ?? null,
                $datos['servicios'] ?? null,
                $datos['horario'] ?? null,
                $datos['logo'] ?? null,
                'pendiente',
            ]
        );
        return (int) ($fila['id'] ?? 0);
    }

    /** @return list<array<string, mixed>> */
    public static function pendientes(int $limite = 100): array
    {
        return Database::select(
            "select * from negocios where estado = 'pendiente' order by creado_en asc limit ?",
            [$limite]
        );
    }

    /** @return list<array<string, mixed>> */
    public static function todos(int $limite = 100): array
    {
        return Database::select('select * from negocios order by creado_en desc limit ?', [$limite]);
    }

    public static function cambiarEstado(int $id, string $estado): void
    {
        Database::execute('update negocios set estado = ? where id = ?', [$estado, $id]);
    }

    public static function verificar(int $id, bool $verificado): void
    {
        Database::execute('update negocios set verificado = ? where id = ?', [$verificado, $id]);
    }

    public static function destacar(int $id, bool $destacado): void
    {
        Database::execute('update negocios set destacado = ? where id = ?', [$destacado, $id]);
    }

    public static function eliminar(int $id): void
    {
        Database::execute('delete from negocios where id = ?', [$id]);
    }

    public static function contarAprobados(): int
    {
        return (int) Database::value("select count(*) from negocios where estado = 'aprobado'");
    }

    public static function contarPendientes(): int
    {
        return (int) Database::value("select count(*) from negocios where estado = 'pendiente'");
    }

    /** @return list<array{estado_geo: string, total: int}> */
    public static function estados(): array
    {
        return Database::select(
            "select estado_geo, count(*) as total from negocios where estado = 'aprobado' and estado_geo is not null
             group by estado_geo order by estado_geo"
        );
    }

    /** @return list<array<string, mixed>> */
    public static function relacionados(string $estadoGeo, int $excluir, int $limite = 3): array
    {
        return Database::select(
            "select * from negocios where estado = 'aprobado' and estado_geo = ? and id <> ? order by destacado desc, nombre limit ?",
            [$estadoGeo, $excluir, $limite]
        );
    }
}
