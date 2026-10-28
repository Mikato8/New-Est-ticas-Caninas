<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

final class GuiaMediaRepository
{
    /** @return list<array<string, mixed>> */
    public static function porGuia(string $slug, int $limite = 12): array
    {
        return Database::select(
            'select gm.*, u.nombre as autor
             from guia_media gm
             join usuarios u on u.id = gm.usuario_id
             where gm.guia_slug = ? and gm.estado = \'publicado\'
             order by gm.creado_en desc
             limit ' . max(1, min(30, $limite)),
            [$slug]
        );
    }

    /** @return list<array<string, mixed>> */
    public static function recientes(int $limite = 8): array
    {
        return Database::select(
            'select gm.*, u.nombre as autor
             from guia_media gm
             join usuarios u on u.id = gm.usuario_id
             where gm.estado = \'publicado\'
             order by gm.creado_en desc
             limit ' . max(1, min(20, $limite))
        );
    }

    /** @return list<array<string, mixed>> */
    public static function imagenesRecientes(int $limite = 8): array
    {
        return Database::select(
            'select gm.*, u.nombre as autor
             from guia_media gm
             join usuarios u on u.id = gm.usuario_id
             where gm.estado = \'publicado\' and gm.tipo = \'imagen\'
             order by gm.creado_en desc
             limit ' . max(1, min(20, $limite))
        );
    }

    public static function crear(int $usuarioId, string $slug, string $tipo, string $ruta, string $titulo, string $descripcion): int
    {
        return (int) Database::value(
            'insert into guia_media (usuario_id, guia_slug, tipo, ruta, titulo, descripcion, estado)
             values (?, ?, ?, ?, ?, ?, \'publicado\') returning id',
            [$usuarioId, $slug, $tipo, $ruta, $titulo, $descripcion !== '' ? $descripcion : null]
        );
    }

    public static function contarHoy(int $usuarioId): int
    {
        return (int) Database::value(
            'select count(*) from guia_media where usuario_id = ? and creado_en > now() - interval \'24 hours\'',
            [$usuarioId]
        );
    }

    public static function eliminar(int $id, int $usuarioId, bool $moderador = false): bool
    {
        $sql = 'delete from guia_media where id = ?' . ($moderador ? '' : ' and usuario_id = ?');
        $parametros = $moderador ? [$id] : [$id, $usuarioId];
        return Database::execute($sql, $parametros) > 0;
    }
}
