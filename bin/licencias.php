#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Administración de claves de Mikato CRM.
 *
 * Uso:
 *   php bin/licencias.php listar
 *   php bin/licencias.php crear "Estética Luna" [plan] [YYYY-MM-DD] ["Negocio"]
 *   php bin/licencias.php revocar MKTO-XXXX-XXXX-XXXX
 *
 * El registro vive en storage/licencias.json (fuera de public_html). La llave
 * privada de demostración está en config/licencias/demo-privada.pem; para
 * producción genera un par propio y reemplaza la llave pública de la app.
 */

require dirname(__DIR__) . '/app/bootstrap.php';

$ruta = BASE_PATH . '/storage/licencias.json';
$semilla = BASE_PATH . '/config/licencias/demo.json';

if (!is_file($ruta) && is_file($semilla)) {
    @mkdir(dirname($ruta), 0775, true);
    @copy($semilla, $ruta);
}

$leer = static function () use ($ruta): array {
    $datos = is_file($ruta) ? json_decode((string) file_get_contents($ruta), true) : null;

    return is_array($datos) && is_array($datos['licencias'] ?? null) ? $datos : ['licencias' => []];
};

$guardar = static function (array $datos) use ($ruta): void {
    file_put_contents(
        $ruta,
        json_encode($datos, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT),
        LOCK_EX
    );
};

$orden = $argv[1] ?? 'listar';
$datos = $leer();

switch ($orden) {
    case 'listar':
        if ($datos['licencias'] === []) {
            echo "No hay licencias registradas.\n";
            break;
        }
        printf("%-22s %-11s %-12s %-22s %s\n", 'CLAVE', 'PLAN', 'ESTADO', 'EXPIRA', 'TITULAR / EQUIPO');
        foreach ($datos['licencias'] as $fila) {
            printf(
                "%-22s %-11s %-12s %-22s %s%s\n",
                (string) $fila['clave'],
                (string) ($fila['plan'] ?? ''),
                (string) ($fila['estado'] ?? 'activa'),
                (string) ($fila['expira_en'] ?? 'sin fecha'),
                (string) ($fila['titular'] ?? ''),
                ($fila['equipo'] ?? null) !== null ? ' · ' . $fila['equipo'] : ''
            );
        }
        break;

    case 'crear':
        $titular = trim((string) ($argv[2] ?? ''));
        if ($titular === '') {
            fwrite(STDERR, "Falta el titular. Uso: php bin/licencias.php crear \"Estética Luna\" [plan] [YYYY-MM-DD]\n");
            exit(1);
        }
        $plan = trim((string) ($argv[3] ?? 'comercial'));
        $expira = trim((string) ($argv[4] ?? date('Y-m-d', strtotime('+1 year'))));
        $negocio = trim((string) ($argv[5] ?? 'Mikato Estilistas Caninos'));
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $expira) !== 1) {
            fwrite(STDERR, "La fecha debe ser YYYY-MM-DD.\n");
            exit(1);
        }
        $alfabeto = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $grupo = static function () use ($alfabeto): string {
            $texto = '';
            for ($i = 0; $i < 4; $i++) {
                $texto .= $alfabeto[random_int(0, strlen($alfabeto) - 1)];
            }

            return $texto;
        };
        $clave = sprintf('MKTO-%s-%s-%s', $grupo(), $grupo(), $grupo());
        $datos['licencias'][] = [
            'clave' => $clave,
            'plan' => $plan,
            'titular' => $titular,
            'negocio' => $negocio,
            'expira_en' => $expira,
            'estado' => 'activa',
            'equipo' => null,
            'activada_en' => null,
        ];
        $guardar($datos);
        echo "Clave creada: {$clave}\n";
        echo "Titular: {$titular} · plan {$plan} · vence {$expira}\n";
        break;

    case 'revocar':
        $clave = strtoupper(trim((string) ($argv[2] ?? '')));
        $encontrada = false;
        foreach ($datos['licencias'] as $i => $fila) {
            if (($fila['clave'] ?? '') === $clave) {
                $datos['licencias'][$i]['estado'] = 'revocada';
                $encontrada = true;
            }
        }
        if (!$encontrada) {
            fwrite(STDERR, "No se encontró la clave {$clave}.\n");
            exit(1);
        }
        $guardar($datos);
        echo "Clave revocada: {$clave}\n";
        break;

    default:
        echo "Uso: php bin/licencias.php [listar|crear|revocar]\n";
}
