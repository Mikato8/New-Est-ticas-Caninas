<?php

declare(strict_types=1);

namespace App\Core;

final class PdoDatabase implements DatabaseInterface
{
    private \PDO $pdo;

    public function __construct(string $url)
    {
        $partes = parse_url($url);
        if ($partes === false || !isset($partes['host'])) {
            throw new DatabaseException('La cadena DATABASE_URL no es válida.');
        }

        parse_str($partes['query'] ?? '', $consulta);
        $base = ltrim((string) ($partes['path'] ?? ''), '/');
        if ($base === '') {
            throw new DatabaseException('DATABASE_URL no incluye el nombre de la base.');
        }

        // channel_binding y otros parámetros de libpq no son aceptados por el DSN de PDO.
        $permitidos = ['sslmode', 'connect_timeout', 'application_name'];
        $dsn = sprintf(
            'pgsql:host=%s;port=%d;dbname=%s',
            $partes['host'],
            (int) ($partes['port'] ?? 5432),
            $base
        );
        foreach ($permitidos as $clave) {
            if (isset($consulta[$clave]) && $consulta[$clave] !== '') {
                $dsn .= ';' . $clave . '=' . $consulta[$clave];
            }
        }
        if (!isset($consulta['connect_timeout'])) {
            $dsn .= ';connect_timeout=10';
        }
        if (!isset($consulta['sslmode'])) {
            $dsn .= ';sslmode=require';
        }

        // Algunos libpq de hosting compartido no negocian SNI con Neon.
        // Neon pide el endpoint explícito en ese caso.
        $endpoint = (string) ($consulta['options'] ?? '');
        if ($endpoint === '' && preg_match('/^(ep-[a-z0-9-]+)\./', (string) $partes['host'], $m)) {
            $endpoint = 'endpoint=' . $m[1];
        }
        if ($endpoint !== '') {
            $dsn .= ';options=' . $endpoint;
        }

        try {
            $this->pdo = new \PDO(
                $dsn,
                urldecode((string) ($partes['user'] ?? '')),
                urldecode((string) ($partes['pass'] ?? '')),
                [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                    // El pooler de Neon trabaja en modo transacción; la emulación
                    // evita depender de sentencias preparadas con nombre.
                    \PDO::ATTR_EMULATE_PREPARES => true,
                ]
            );
        } catch (\PDOException $e) {
            throw new DatabaseException('No fue posible conectar con PostgreSQL: ' . $e->getMessage(), 0, $e);
        }
    }

    public function select(string $sql, array $parametros = []): array
    {
        return $this->consultar($sql, $parametros)->fetchAll();
    }

    public function first(string $sql, array $parametros = []): ?array
    {
        $fila = $this->consultar($sql, $parametros)->fetch();
        return $fila === false ? null : $fila;
    }

    public function value(string $sql, array $parametros = []): mixed
    {
        $fila = $this->first($sql, $parametros);
        return $fila === null ? null : (array_values($fila)[0] ?? null);
    }

    public function execute(string $sql, array $parametros = []): int
    {
        return $this->consultar($sql, $parametros)->rowCount();
    }

    public function insert(string $sql, array $parametros = []): ?array
    {
        $sentencia = $this->consultar($sql, $parametros);
        if ($sentencia->columnCount() === 0) {
            return null;
        }
        $fila = $sentencia->fetch();
        return $fila === false ? null : $fila;
    }

    public function transaction(callable $callback): mixed
    {
        $this->pdo->beginTransaction();
        try {
            $resultado = $callback($this);
            $this->pdo->commit();
            return $resultado;
        } catch (\Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }

    public function driverName(): string
    {
        return 'pdo_pgsql';
    }

    public function ping(): bool
    {
        try {
            return (int) $this->value('select 1') === 1;
        } catch (\Throwable) {
            return false;
        }
    }

    /** @param array<int|string, mixed> $parametros */
    private function consultar(string $sql, array $parametros): \PDOStatement
    {
        try {
            $sentencia = $this->pdo->prepare($sql);
            $valores = array_map(
                static fn (mixed $valor): mixed => is_bool($valor) ? ($valor ? 't' : 'f') : $valor,
                array_values($parametros)
            );
            $sentencia->execute($valores);
            return $sentencia;
        } catch (\PDOException $e) {
            Logger::error('Error de PostgreSQL', ['sql' => $sql, 'error' => $e->getMessage()]);
            throw new DatabaseException('La consulta a la base de datos falló.', 0, $e);
        }
    }
}
