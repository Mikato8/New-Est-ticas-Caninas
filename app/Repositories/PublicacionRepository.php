<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Support\Slug;

final class PublicacionRepository
{
    private const SELECT = 'select p.*, u.nombre as autor_nombre, u.avatar as autor_avatar, u.rol as autor_rol,
        (select count(*) from comentarios c where c.publicacion_id = p.id and c.estado = \'aprobado\') as comentarios
        from publicaciones p join usuarios u on u.id = p.usuario_id';

    /** @return list<array<string, mixed>> */
    public static function publicadas(string $categoria = '', string $busqueda = '', int $limite = 30): array
    {
        $condiciones = ["p.estado = 'publicado'"];
        $parametros = [];
        if ($categoria !== '') {
            $condiciones[] = 'p.categoria = ?';
            $parametros[] = $categoria;
        }
        if ($busqueda !== '') {
            $condiciones[] = '(lower(p.titulo) like ? or lower(coalesce(p.resumen, \'\')) like ? or lower(p.contenido) like ?)';
            $termino = '%' . mb_strtolower($busqueda) . '%';
            $parametros[] = $termino;
            $parametros[] = $termino;
            $parametros[] = $termino;
        }
        $parametros[] = $limite;

        return Database::select(
            self::SELECT . ' where ' . implode(' and ', $condiciones) . ' order by p.fijado desc, p.creado_en desc limit ?',
            $parametros
        );
    }

    /** @return array<string, mixed>|null */
    public static function porSlug(string $slug, bool $soloPublicadas = true): ?array
    {
        $sql = self::SELECT . ' where p.slug = ?';
        if ($soloPublicadas) {
            $sql .= " and p.estado = 'publicado'";
        }
        return Database::first($sql, [$slug]);
    }

    /** @return array<string, mixed>|null */
    public static function porId(int $id): ?array
    {
        return Database::first(self::SELECT . ' where p.id = ?', [$id]);
    }

    /** @param array<string, mixed> $datos */
    public static function crear(array $datos): int
    {
        $slug = Slug::unico((string) $datos['titulo'], static fn (string $candidato): bool =>
            (int) Database::value('select count(*) from publicaciones where slug = ?', [$candidato]) > 0);
        $fila = Database::insert(
            'insert into publicaciones (usuario_id, titulo, slug, categoria, resumen, contenido, imagen, estado)
             values (?, ?, ?, ?, ?, ?, ?, ?) returning id',
            [
                (int) $datos['usuario_id'],
                (string) $datos['titulo'],
                $slug,
                (string) ($datos['categoria'] ?? 'casos'),
                $datos['resumen'] ?? null,
                (string) $datos['contenido'],
                $datos['imagen'] ?? null,
                (string) ($datos['estado'] ?? 'pendiente'),
            ]
        );
        return (int) ($fila['id'] ?? 0);
    }

    /** @return list<array<string, mixed>> */
    public static function comentarios(int $publicacionId, bool $soloAprobados = true): array
    {
        $sql = 'select c.*, u.nombre as autor_nombre, u.avatar as autor_avatar, u.rol as autor_rol
                from comentarios c join usuarios u on u.id = c.usuario_id where c.publicacion_id = ?';
        if ($soloAprobados) {
            $sql .= " and c.estado = 'aprobado'";
        }
        return Database::select($sql . ' order by c.creado_en asc', [$publicacionId]);
    }

    public static function comentar(int $publicacionId, int $usuarioId, string $contenido, string $estado = 'pendiente'): int
    {
        $fila = Database::insert(
            'insert into comentarios (publicacion_id, usuario_id, contenido, estado) values (?, ?, ?, ?) returning id',
            [$publicacionId, $usuarioId, $contenido, $estado]
        );
        return (int) ($fila['id'] ?? 0);
    }

    public static function incrementarVistas(int $id): void
    {
        Database::execute('update publicaciones set vistas = vistas + 1 where id = ?', [$id]);
    }

    /** @return list<array<string, mixed>> */
    public static function relacionados(string $categoria, int $excluir, int $limite = 3): array
    {
        return Database::select(
            self::SELECT . " where p.estado = 'publicado' and p.categoria = ? and p.id <> ? order by p.creado_en desc limit ?",
            [$categoria, $excluir, $limite]
        );
    }

    /** @return list<array<string, mixed>> */
    public static function pendientes(int $limite = 50): array
    {
        return Database::select(
            self::SELECT . " where p.estado = 'pendiente' order by p.creado_en asc limit ?",
            [$limite]
        );
    }

    /** @return list<array<string, mixed>> */
    public static function todas(int $limite = 100): array
    {
        return Database::select(self::SELECT . ' order by p.creado_en desc limit ?', [$limite]);
    }

    public static function cambiarEstado(int $id, string $estado): void
    {
        Database::execute('update publicaciones set estado = ?, actualizado_en = now() where id = ?', [$estado, $id]);
    }

    public static function fijar(int $id, bool $fijado): void
    {
        Database::execute('update publicaciones set fijado = ? where id = ?', [$fijado, $id]);
    }

    public static function eliminar(int $id): void
    {
        Database::execute('delete from publicaciones where id = ?', [$id]);
    }

    /** @return list<array<string, mixed>> */
    public static function comentariosPendientes(int $limite = 50): array
    {
        return Database::select(
            'select c.*, u.nombre as autor_nombre, p.titulo, p.slug
             from comentarios c join usuarios u on u.id = c.usuario_id join publicaciones p on p.id = c.publicacion_id
             where c.estado = \'pendiente\' order by c.creado_en asc limit ?',
            [$limite]
        );
    }

    public static function cambiarEstadoComentario(int $id, string $estado): void
    {
        Database::execute('update comentarios set estado = ? where id = ?', [$estado, $id]);
    }

    public static function eliminarComentario(int $id): void
    {
        Database::execute('delete from comentarios where id = ?', [$id]);
    }

    public static function contarPublicadas(): int
    {
        return (int) Database::value("select count(*) from publicaciones where estado = 'publicado'");
    }

    public static function contarPendientes(): int
    {
        return (int) Database::value("select count(*) from publicaciones where estado = 'pendiente'");
    }

    public static function contarComentariosPendientes(): int
    {
        return (int) Database::value("select count(*) from comentarios where estado = 'pendiente'");
    }

    /** @return array<string, string> */
    public static function categorias(): array
    {
        return [
            'casos' => 'Casos y antes/después',
            'tecnicas' => 'Técnicas de corte y baño',
            'negocio' => 'Negocio y precios',
            'salud' => 'Salud y bienestar',
            'herramientas' => 'Herramientas y productos',
            'empleo' => 'Empleo y alianzas',
            'dudas' => 'Dudas de estilistas',
        ];
    }
}
