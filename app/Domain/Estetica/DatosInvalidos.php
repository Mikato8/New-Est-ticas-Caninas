<?php

declare(strict_types=1);

namespace App\Domain\Estetica;

final class DatosInvalidos extends \InvalidArgumentException
{
    /** @var array<string, string> */
    private array $errores;

    /** @param array<string, string> $errores */
    public function __construct(array $errores)
    {
        $this->errores = $errores;
        parent::__construct('Revisa los datos: ' . implode(' ', $errores));
    }

    /** @return array<string, string> */
    public function errores(): array
    {
        return $this->errores;
    }
}
