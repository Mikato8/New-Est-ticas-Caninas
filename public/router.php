<?php
declare(strict_types=1);
$ruta = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$archivo = __DIR__ . $ruta;
if ($ruta !== '/' && is_file($archivo)) {
    return false;
}
require __DIR__ . '/index.php';
