<?php

declare(strict_types=1);

namespace App\Core;

final class HttpException extends \RuntimeException
{
    public function __construct(string $mensaje, private readonly int $estado = 404)
    {
        parent::__construct($mensaje);
    }

    public function estado(): int
    {
        return $this->estado;
    }
}
