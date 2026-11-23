<?php
declare(strict_types=1);
$ruta = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$archivo = __DIR__ . $ruta;
if ($ruta !== '/' && is_file($archivo)) {
    return false;
}
// Permite previsualizar la PWA en /app/ con el servidor de PHP.
if ($ruta !== '/' && is_dir($archivo) && is_file(rtrim($archivo, '/') . '/index.html')) {
    return false;
}
require __DIR__ . '/index.php';
