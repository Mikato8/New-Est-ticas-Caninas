<?php

declare(strict_types=1);

use App\Core\Database;

require dirname(__DIR__) . '/app/bootstrap.php';

$archivo = BASE_PATH . '/database/schema.sql';
if (!is_file($archivo)) {
    fwrite(STDERR, "No existe database/schema.sql\n");
    exit(1);
}

$sql = (string) file_get_contents($archivo);

// El transporte HTTP de Neon solo acepta una sentencia por llamada; se separa
// respetando comillas simples y comentarios de línea.
$sentencias = [];
$actual = '';
$enComilla = false;
$largo = strlen($sql);
for ($i = 0; $i < $largo; $i++) {
    $c = $sql[$i];
    if ($c === "'" ) {
        if ($enComilla && $i + 1 < $largo && $sql[$i + 1] === "'") {
            $actual .= "''";
            $i++;
            continue;
        }
        $enComilla = !$enComilla;
        $actual .= $c;
        continue;
    }
    if ($c === ';' && !$enComilla) {
        $sentencias[] = trim($actual);
        $actual = '';
        continue;
    }
    $actual .= $c;
}
if (trim($actual) !== '') {
    $sentencias[] = trim($actual);
}

$driver = Database::driver();
echo 'Motor: ' . $driver->driverName() . PHP_EOL;
echo 'Sentencias: ' . count($sentencias) . PHP_EOL;

$ejecutadas = 0;
foreach ($sentencias as $sentencia) {
    // Quita líneas que son solo comentario para no enviar basura al endpoint.
    $lineas = [];
    foreach (explode("\n", $sentencia) as $linea) {
        $limpia = trim($linea);
        if ($limpia === '' || str_starts_with($limpia, '--')) {
            continue;
        }
        $lineas[] = $linea;
    }
    $consulta = trim(implode("\n", $lineas));
    if ($consulta === '') {
        continue;
    }

    try {
        Database::execute($consulta);
        $ejecutadas++;
    } catch (\Throwable $e) {
        fwrite(STDERR, 'Fallo en la sentencia ' . ($ejecutadas + 1) . ': ' . substr($consulta, 0, 160) . "\n" . $e->getMessage() . "\n");
        exit(1);
    }
}

echo 'Aplicadas: ' . $ejecutadas . PHP_EOL;
echo 'Base de datos lista.' . PHP_EOL;
