<?php

declare(strict_types=1);

use App\Core\Database;
use App\Core\Env;
use App\Domain\Contenido\Glosario;
use App\Domain\Contenido\Guias;

require dirname(__DIR__) . '/app/bootstrap.php';

echo "Mikato Estilistas Caninos · verificación\n";
echo str_repeat('=', 46) . "\n";
echo 'PHP: ' . PHP_VERSION . "\n";
echo 'APP_URL: ' . (string) config('url') . "\n";
echo 'APP_ENV: ' . (string) config('env') . "\n";
echo 'DB_DRIVER solicitado: ' . (string) config('db.driver') . "\n";

try {
    $driver = Database::driver();
    echo 'Driver activo: ' . $driver->driverName() . "\n";
    echo 'Ping: ' . ($driver->ping() ? 'OK' : 'FALLA') . "\n";
    echo 'Usuarios: ' . (int) Database::value('select count(*) from usuarios') . "\n";
    echo 'Clientes: ' . (int) Database::value('select count(*) from clientes') . "\n";
    echo 'Mascotas: ' . (int) Database::value('select count(*) from mascotas where activo = true') . "\n";
    echo 'Citas: ' . (int) Database::value('select count(*) from citas') . "\n";
    echo 'Servicios: ' . (int) Database::value('select count(*) from servicios where activo = true') . "\n";
    echo 'Productos: ' . (int) Database::value('select count(*) from productos where activo = true') . "\n";
    echo 'Ventas: ' . (int) Database::value('select count(*) from ventas') . "\n";
    echo 'Publicaciones: ' . (int) Database::value('select count(*) from publicaciones') . "\n";
    echo 'Negocios: ' . (int) Database::value('select count(*) from negocios') . "\n";

    $admin = Database::first("select usuario, rol, estado from usuarios where rol = 'admin' limit 1");
    echo 'Admin: ' . ($admin === null ? 'NO EXISTE (corre bin/seed.php)' : $admin['usuario'] . ' (' . $admin['estado'] . ')') . "\n";
} catch (\Throwable $e) {
    echo 'Base de datos: ERROR · ' . $e->getMessage() . "\n";
}

echo 'Guías: ' . Guias::total() . "\n";
echo 'Glosario: ' . count(Glosario::todos()) . " términos\n";

$corpus = BASE_PATH . '/public/assets/ia/corpus.json';
echo 'Corpus IA: ' . (is_file($corpus) ? (int) (json_decode((string) file_get_contents($corpus), true)['total'] ?? 0) . ' fragmentos' : 'falta (corre bin/build-ia.php)') . "\n";

echo "Variables clave:\n";
foreach (['APP_URL', 'DATABASE_URL', 'DB_DRIVER', 'ADMIN_USER', 'ADMIN_PASSWORD'] as $clave) {
    $valor = Env::get($clave);
    $mostrar = $valor === null || $valor === '' ? 'FALTA' : ($clave === 'DATABASE_URL' || $clave === 'ADMIN_PASSWORD' ? '(definida)' : $valor);
    echo '  ' . $clave . ': ' . $mostrar . "\n";
}
