<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

final class SolicitudArcoRepository
{
    /** @param array<string, mixed> $datos */
    public static function crear(array $datos): int
    {
        $fila = Database::insert(
            'insert into solicitudes_arco
                (folio, tipo, nombre_titular, numero_cliente, correo, telefono, ciudad, fecha_solicitud,
                 nombre_representante, tipo_representacion, dato_incorrecto, dato_correcto, documento_acreditativo,
                 notificacion_medio, domicilio, direccion_oficina, nombre_firma,
                 archivo_identificacion, archivo_representacion, archivo_acreditativo, ip, user_agent)
             values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) returning id',
            [
                (string) $datos['folio'],
                'rectificacion',
                (string) $datos['nombre_titular'],
                $datos['numero_cliente'] ?: null,
                (string) $datos['correo'],
                $datos['telefono'] ?: null,
                (string) $datos['ciudad'],
                (string) $datos['fecha_solicitud'],
                $datos['nombre_representante'] ?: null,
                $datos['tipo_representacion'] ?: null,
                (string) $datos['dato_incorrecto'],
                (string) $datos['dato_correcto'],
                $datos['documento_acreditativo'] ?: null,
                (string) $datos['notificacion_medio'],
                $datos['domicilio'] ?: null,
                $datos['direccion_oficina'] ?: null,
                (string) $datos['nombre_firma'],
                $datos['archivo_identificacion'] ?: null,
                $datos['archivo_representacion'] ?: null,
                $datos['archivo_acreditativo'] ?: null,
                $datos['ip'] ?: null,
                $datos['user_agent'] ?: null,
            ]
        );
        return (int) ($fila['id'] ?? 0);
    }

    /** @return list<array<string, mixed>> */
    public static function listar(string $estado = ''): array
    {
        if ($estado !== '') {
            return Database::select(
                'select * from solicitudes_arco where estado = ? order by creado_en desc limit 300',
                [$estado]
            );
        }
        return Database::select('select * from solicitudes_arco order by creado_en desc limit 300');
    }

    /** @return array<string, mixed>|null */
    public static function porId(int $id): ?array
    {
        return Database::first('select * from solicitudes_arco where id = ?', [$id]);
    }

    public static function actualizarEstado(int $id, string $estado, string $respuesta): void
    {
        Database::execute(
            'update solicitudes_arco set estado = ?, respuesta = ?, actualizado_en = now() where id = ?',
            [$estado, $respuesta !== '' ? $respuesta : null, $id]
        );
    }

    public static function contarNuevas(): int
    {
        return (int) Database::value("select count(*) from solicitudes_arco where estado = 'recibida'");
    }
}
