<?php

declare(strict_types=1);

namespace App\Core;

interface DatabaseInterface
{
    /** @param array<int|string, mixed> $parametros */
    public function select(string $sql, array $parametros = []): array;

    /** @param array<int|string, mixed> $parametros */
    public function first(string $sql, array $parametros = []): ?array;

    /** @param array<int|string, mixed> $parametros */
    public function value(string $sql, array $parametros = []): mixed;

    /** @param array<int|string, mixed> $parametros */
    public function execute(string $sql, array $parametros = []): int;

    /** @param array<int|string, mixed> $parametros */
    public function insert(string $sql, array $parametros = []): ?array;

    public function transaction(callable $callback): mixed;

    public function driverName(): string;

    public function ping(): bool;
}
