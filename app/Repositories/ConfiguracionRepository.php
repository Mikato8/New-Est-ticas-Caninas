<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

final class ConfiguracionRepository
{
    /** @return array<string, string> */
    public static function todas(): array
    {
        $filas = Database::select('select clave, valor from configuracion order by clave');
        $salida = [];
        foreach ($filas as $fila) {
            $salida[(string) $fila['clave']] = (string) ($fila['valor'] ?? '');
        }
        return $salida;
    }

    public static function get(string $clave, string $porDefecto = ''): string
    {
        $valor = Database::value('select valor from configuracion where clave = ?', [$clave]);
        return $valor === null ? $porDefecto : (string) $valor;
    }

    public static function set(string $clave, string $valor): void
    {
        Database::execute(
            'insert into configuracion (clave, valor, actualizado_en) values (?, ?, now())
             on conflict (clave) do update set valor = excluded.valor, actualizado_en = now()',
            [$clave, $valor]
        );
    }

    /** @param array<string, string> $datos */
    public static function setMany(array $datos): void
    {
        foreach ($datos as $clave => $valor) {
            self::set((string) $clave, (string) $valor);
        }
    }
}
