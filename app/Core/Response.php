<?php

declare(strict_types=1);

namespace App\Core;

final class Response
{
    /** @var array<string, string> */
    private array $cabeceras = [];

    public function __construct(
        private string $cuerpo = '',
        private int $estado = 200
    ) {
    }

    public static function html(string $html, int $estado = 200): self
    {
        return (new self($html, $estado))->conCabecera('Content-Type', 'text/html; charset=UTF-8');
    }

    /** @param array<string, mixed>|list<mixed> $datos */
    public static function json(array $datos, int $estado = 200): self
    {
        $cuerpo = json_encode(
            $datos,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR
        );
        return (new self($cuerpo, $estado))->conCabecera('Content-Type', 'application/json; charset=UTF-8');
    }

    public static function redirigir(string $destino, int $estado = 303): self
    {
        return (new self('', $estado))->conCabecera('Location', $destino);
    }

    public static function texto(string $texto, int $estado = 200, string $tipo = 'text/plain; charset=UTF-8'): self
    {
        return (new self($texto, $estado))->conCabecera('Content-Type', $tipo);
    }

    public function conCabecera(string $nombre, string $valor): self
    {
        $this->cabeceras[$nombre] = $valor;
        return $this;
    }

    public function estado(): int
    {
        return $this->estado;
    }

    public function cuerpo(): string
    {
        return $this->cuerpo;
    }

    /** @return array<string, string> */
    public function cabeceras(): array
    {
        return $this->cabeceras;
    }

    public function enviar(): void
    {
        if (!headers_sent()) {
            http_response_code($this->estado);
            foreach ($this->cabeceras as $nombre => $valor) {
                header($nombre . ': ' . $valor);
            }
        }
        echo $this->cuerpo;
    }
}
