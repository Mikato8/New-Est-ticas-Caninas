<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

final class PasswordResetRepository
{
    public static function crear(int $usuarioId, string $tokenHash, string $ip, string $userAgent): void
    {
        Database::execute(
            'delete from password_resets where usuario_id = ? and usado_en is null',
            [$usuarioId]
        );
        Database::execute(
            'insert into password_resets (usuario_id, token_hash, expira_en, ip, user_agent)
             values (?, ?, now() + interval \'1 hour\', ?, ?)',
            [$usuarioId, $tokenHash, mb_substr($ip, 0, 60), mb_substr($userAgent, 0, 255)]
        );
    }

    /** @return array<string, mixed>|null */
    public static function porHash(string $tokenHash): ?array
    {
        return Database::first(
            "select pr.id as reset_id, pr.expira_en, u.id, u.nombre, u.correo, u.estado
             from password_resets pr
             join usuarios u on u.id = pr.usuario_id
             where pr.token_hash = ? and pr.usado_en is null and pr.expira_en > now() and u.estado = 'activo'
             limit 1",
            [$tokenHash]
        );
    }

    public static function contarRecientes(int $usuarioId, int $minutos = 15): int
    {
        return (int) Database::value(
            'select count(*) from password_resets where usuario_id = ? and creado_en > now() - (? || \' minutes\')::interval',
            [$usuarioId, (string) max(1, $minutos)]
        );
    }

    public static function marcarUsado(int $resetId): void
    {
        Database::execute(
            'update password_resets set usado_en = now() where id = ? and usado_en is null',
            [$resetId]
        );
    }

    public static function invalidarTodos(int $usuarioId): void
    {
        Database::execute(
            'update password_resets set usado_en = now() where usuario_id = ? and usado_en is null',
            [$usuarioId]
        );
    }

    public static function limpiar(): void
    {
        Database::execute("delete from password_resets where expira_en < now() - interval '7 days'");
    }
}
