<?php

declare(strict_types=1);

namespace App\Core;

final class Request
{
    /** @var array<string, mixed> */
    private array $consulta;
    /** @var array<string, mixed> */
    private array $cuerpo;
    /** @var array<string, mixed> */
    private array $archivos;

    public function __construct(
        private readonly string $metodo,
        private readonly string $ruta,
        array $consulta = [],
        array $cuerpo = [],
        array $archivos = [],
        private readonly string $ip = '0.0.0.0'
    ) {
        $this->consulta = $consulta;
        $this->cuerpo = $cuerpo;
        $this->archivos = $archivos;
    }

    public static function desdeGlobales(): self
    {
        $metodo = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
        $ruta = parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH) ?: '/';
        $ruta = '/' . trim(rawurldecode($ruta), '/');

        $cuerpo = $_POST;
        if (str_contains(strtolower((string) ($_SERVER['CONTENT_TYPE'] ?? '')), 'application/json')) {
            $decodificado = json_decode((string) file_get_contents('php://input'), true);
            if (is_array($decodificado)) {
                $cuerpo = $decodificado;
            }
        }

        return new self(
            $metodo,
            $ruta,
            $_GET,
            $cuerpo,
            $_FILES,
            (string) ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0')
        );
    }

    public function metodo(): string
    {
        return $this->metodo;
    }

    public function ruta(): string
    {
        return $this->ruta;
    }

    public function esPost(): bool
    {
        return $this->metodo === 'POST';
    }

    /** @return array<string, mixed> */
    public function todo(): array
    {
        return array_merge($this->consulta, $this->cuerpo);
    }

    public function input(string $clave, mixed $porDefecto = null): mixed
    {
        return $this->cuerpo[$clave] ?? $this->consulta[$clave] ?? $porDefecto;
    }

    public function query(string $clave, mixed $porDefecto = null): mixed
    {
        return $this->consulta[$clave] ?? $porDefecto;
    }

    /** @return array<string, mixed> */
    public function consulta(): array
    {
        return $this->consulta;
    }

    /** @return array<string, mixed> */
    public function cuerpo(): array
    {
        return $this->cuerpo;
    }

    /** @return array<string, mixed> */
    public function archivos(): array
    {
        return $this->archivos;
    }

    public function archivo(string $clave): ?array
    {
        $archivo = $this->archivos[$clave] ?? null;
        return is_array($archivo) ? $archivo : null;
    }

    public function cadena(string $clave, string $porDefecto = ''): string
    {
        $valor = $this->input($clave, $porDefecto);
        return is_scalar($valor) ? trim((string) $valor) : $porDefecto;
    }

    public function numero(string $clave, float $porDefecto = 0.0): float
    {
        $valor = $this->input($clave, $porDefecto);
        if (is_string($valor)) {
            $valor = str_replace(',', '.', trim($valor));
        }
        return is_numeric($valor) ? (float) $valor : $porDefecto;
    }

    public function entero(string $clave, int $porDefecto = 0): int
    {
        return (int) $this->numero($clave, (float) $porDefecto);
    }

    public function booleano(string $clave, bool $porDefecto = false): bool
    {
        return filter_var($this->input($clave, $porDefecto ? '1' : '0'), FILTER_VALIDATE_BOOLEAN);
    }

    public function ip(): string
    {
        return $this->ip;
    }

    public function conRuta(string $ruta): self
    {
        return new self($this->metodo, $ruta, $this->consulta, $this->cuerpo, $this->archivos, $this->ip);
    }

    public function esJson(): bool
    {
        return str_contains(strtolower((string) ($_SERVER['CONTENT_TYPE'] ?? '')), 'application/json')
            || str_contains(strtolower((string) ($_SERVER['HTTP_ACCEPT'] ?? '')), 'application/json');
    }
}
