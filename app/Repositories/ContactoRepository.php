<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

final class ContactoRepository
{
    /** @param array<string, mixed> $datos */
    public static function crear(array $datos): int
    {
        $fila = Database::insert(
            'insert into contactos (nombre, correo, telefono, asunto, mensaje) values (?, ?, ?, ?, ?) returning id',
            [
                (string) $datos['nombre'],
                $datos['correo'] ?? null,
                $datos['telefono'] ?? null,
                (string) ($datos['asunto'] ?? 'general'),
                (string) $datos['mensaje'],
            ]
        );
        return (int) ($fila['id'] ?? 0);
    }

    /** @return list<array<string, mixed>> */
    public static function listar(int $limite = 100): array
    {
        return Database::select('select * from contactos order by creado_en desc limit ?', [$limite]);
    }

    public static function marcarAtendido(int $id): void
    {
        Database::execute("update contactos set estado = 'atendido' where id = ?", [$id]);
    }

    public static function eliminar(int $id): void
    {
        Database::execute('delete from contactos where id = ?', [$id]);
    }

    public static function contarNuevos(): int
    {
        return (int) Database::value("select count(*) from contactos where estado = 'nuevo'");
    }

    public static function suscribir(string $correo): void
    {
        Database::execute('insert into boletin (correo) values (?) on conflict do nothing', [$correo]);
    }

    public static function contarBoletin(): int
    {
        return (int) Database::value('select count(*) from boletin');
    }
}
