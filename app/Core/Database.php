<?php

declare(strict_types=1);

namespace App\Core;

final class Database
{
    private static ?DatabaseInterface $driver = null;

    /** @var array<string, mixed> */
    private static array $config = [];

    /** @param array<string, mixed> $config */
    public static function configure(array $config): void
    {
        self::$config = $config;
        self::$driver = null;
    }

    public static function driver(): DatabaseInterface
    {
        if (self::$driver !== null) {
            return self::$driver;
        }

        $url = trim((string) (self::$config['url'] ?? ''));
        if ($url === '') {
            throw new DatabaseException('Falta DATABASE_URL en el archivo .env.');
        }

        $solicitado = strtolower((string) (self::$config['driver'] ?? 'auto'));

        if ($solicitado === 'http') {
            return self::$driver = new HttpDatabase($url);
        }

        if ($solicitado === 'pdo') {
            return self::$driver = new PdoDatabase($url);
        }

        if (extension_loaded('pdo_pgsql')) {
            try {
                return self::$driver = new PdoDatabase($url);
            } catch (DatabaseException $e) {
                Logger::info('PDO no disponible; se usa Neon por HTTPS', ['motivo' => $e->getMessage()]);
            }
        }

        return self::$driver = new HttpDatabase($url);
    }

    /** @param array<int|string, mixed> $parametros */
    public static function select(string $sql, array $parametros = []): array
    {
        return self::driver()->select($sql, $parametros);
    }

    /** @param array<int|string, mixed> $parametros */
    public static function first(string $sql, array $parametros = []): ?array
    {
        return self::driver()->first($sql, $parametros);
    }

    /** @param array<int|string, mixed> $parametros */
    public static function value(string $sql, array $parametros = []): mixed
    {
        return self::driver()->value($sql, $parametros);
    }

    /** @param array<int|string, mixed> $parametros */
    public static function execute(string $sql, array $parametros = []): int
    {
        return self::driver()->execute($sql, $parametros);
    }

    /** @param array<int|string, mixed> $parametros */
    public static function insert(string $sql, array $parametros = []): ?array
    {
        return self::driver()->insert($sql, $parametros);
    }

    public static function transaction(callable $callback): mixed
    {
        return self::driver()->transaction($callback);
    }

    public static function ping(): bool
    {
        try {
            return self::driver()->ping();
        } catch (\Throwable) {
            return false;
        }
    }

    public static function driverName(): string
    {
        try {
            return self::driver()->driverName();
        } catch (\Throwable) {
            return 'sin conexión';
        }
    }

    public static function reset(): void
    {
        self::$driver = null;
    }
}
