<?php

declare(strict_types=1);

spl_autoload_register(static function (string $clase): void {
    $prefijo = 'App\\';
    if (!str_starts_with($clase, $prefijo)) {
        return;
    }
    $ruta = BASE_PATH . '/app/' . str_replace('\\', '/', substr($clase, strlen($prefijo))) . '.php';
    if (is_file($ruta)) {
        require $ruta;
    }
});
