<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Respaldo para hospedajes PHP sin pdo_pgsql: endpoint SQL sobre HTTPS de Neon.
 * Una sentencia por llamada; sin transacciones interactivas.
 */
final class HttpDatabase implements DatabaseInterface
{
    private string $endpoint;
    private string $cadena;

    public function __construct(string $url)
    {
        $partes = parse_url($url);
        if ($partes === false || !isset($partes['host'])) {
            throw new DatabaseException('La cadena de conexión de Neon no es válida.');
        }
        $this->cadena = $url;
        $this->endpoint = 'https://' . $partes['host'] . '/sql';
    }

    public function select(string $sql, array $parametros = []): array
    {
        return $this->peticion($sql, $parametros, true)['rows'];
    }

    public function first(string $sql, array $parametros = []): ?array
    {
        return $this->select($sql, $parametros)[0] ?? null;
    }

    public function value(string $sql, array $parametros = []): mixed
    {
        $fila = $this->first($sql, $parametros);
        return $fila === null ? null : (array_values($fila)[0] ?? null);
    }

    public function execute(string $sql, array $parametros = []): int
    {
        return $this->peticion($sql, $parametros, false)['rowCount'];
    }

    public function insert(string $sql, array $parametros = []): ?array
    {
        return $this->peticion($sql, $parametros, false)['rows'][0] ?? null;
    }

    public function transaction(callable $callback): mixed
    {
        throw new DatabaseException('El transporte HTTPS de Neon no admite transacciones.');
    }

    public function driverName(): string
    {
        return 'neon_http';
    }

    public function ping(): bool
    {
        try {
            return (int) $this->value('select 1') === 1;
        } catch (\Throwable) {
            return false;
        }
    }

    /**
     * @param array<int|string, mixed> $parametros
     * @return array{rows: array<int, array<string, mixed>>, rowCount: int}
     */
    private function peticion(string $sql, array $parametros, bool $soloLectura): array
    {
        $carga = json_encode([
            'query' => self::posicional($sql),
            'params' => array_values($parametros),
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);

        $curl = curl_init($this->endpoint);
        if ($curl === false) {
            throw new DatabaseException('No fue posible inicializar cURL.');
        }
        curl_setopt_array($curl, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $carga,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 8,
            CURLOPT_TIMEOUT => (int) config('db.timeout', 25),
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Accept: application/json',
                'Neon-Connection-String: ' . $this->cadena,
            ],
        ]);

        $cuerpo = curl_exec($curl);
        $estado = (int) curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        $errno = curl_errno($curl);
        $error = curl_error($curl);
        if ($errno !== 0 || $cuerpo === false) {
            Logger::error('Fallo de red con Neon', ['errno' => $errno, 'error' => $error]);
            throw new DatabaseException('No fue posible comunicarse con Neon.');
        }

        $decodificado = json_decode((string) $cuerpo, true);
        if ($estado !== 200 || !is_array($decodificado)) {
            $mensaje = is_array($decodificado) ? (string) ($decodificado['message'] ?? 'Error desconocido') : 'Respuesta inválida';
            Logger::error('Error de Neon', ['estado' => $estado, 'mensaje' => $mensaje]);
            throw new DatabaseException('La consulta a Neon falló: ' . $mensaje);
        }

        return [
            'rows' => is_array($decodificado['rows'] ?? null) ? $decodificado['rows'] : [],
            'rowCount' => (int) ($decodificado['rowCount'] ?? 0),
        ];
    }

    public static function posicional(string $sql): string
    {
        $salida = '';
        $indice = 1;
        $enComilla = false;
        $largo = strlen($sql);

        for ($i = 0; $i < $largo; $i++) {
            $caracter = $sql[$i];
            if ($caracter === "'") {
                if ($enComilla && $i + 1 < $largo && $sql[$i + 1] === "'") {
                    $salida .= "''";
                    $i++;
                    continue;
                }
                $enComilla = !$enComilla;
                $salida .= $caracter;
                continue;
            }
            if ($caracter === '?' && !$enComilla) {
                $salida .= '$' . $indice;
                $indice++;
                continue;
            }
            $salida .= $caracter;
        }
        return $salida;
    }
}
