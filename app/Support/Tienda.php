<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Catálogo público de la tienda mikato.com.mx.
 *
 * La página /productos no guarda copia propia: lee la API de la tienda y deja
 * el resultado en storage/cache para no pegarle a la tienda en cada visita.
 * Si la tienda no responde se usa el último catálogo conocido.
 */
final class Tienda
{
    private const CACHE_TTL = 600; // 10 minutos
    private const CACHE = '/storage/cache/tienda-productos.json';

    /** @return list<array<string, mixed>> */
    public static function productos(): array
    {
        $cache = self::leerCache();
        if (is_array($cache) && (int) ($cache['expira'] ?? 0) > time()) {
            return $cache['productos'];
        }

        $productos = self::consultar();
        if ($productos === null) {
            return is_array($cache) ? $cache['productos'] : [];
        }

        self::guardarCache($productos);
        return $productos;
    }

    /** @return list<array<string, mixed>>|null */
    private static function consultar(): ?array
    {
        $respuesta = self::pedir(self::baseUrl() . '/api/v1/products', 'application/json');
        if ($respuesta === null) {
            return null;
        }

        $json = json_decode($respuesta, true);
        $filas = is_array($json) ? ($json['data'] ?? null) : null;
        if (!is_array($filas)) {
            return null;
        }

        $productos = [];
        foreach ($filas as $fila) {
            if (!is_array($fila) || ($fila['active'] ?? true) === false) {
                continue;
            }
            $id = trim((string) ($fila['id'] ?? ''));
            if ($id === '') {
                continue;
            }
            $categoria = is_array($fila['categories'] ?? null)
                ? trim((string) ($fila['categories']['name'] ?? ''))
                : '';
            // El id de la imagen no es el id del producto: viene en image_url.
            $imagenId = basename((string) (parse_url((string) ($fila['image_url'] ?? ''), PHP_URL_PATH) ?: ''));

            $productos[] = [
                'id' => $id,
                'nombre' => trim((string) ($fila['name'] ?? '')),
                'descripcion' => trim((string) ($fila['description'] ?? '')),
                'precio' => (float) ($fila['price'] ?? 0),
                'categoria' => $categoria !== '' ? $categoria : 'Productos Mikato',
                'agotado' => (int) ($fila['stock'] ?? 0) <= 0,
                'url' => self::baseUrl() . '/producto/' . rawurlencode($id),
                'imagen' => preg_match('/^[0-9a-fA-F-]{36}$/', $imagenId) === 1
                    ? url('/media/tienda/' . rawurlencode($imagenId))
                    : '',
            ];
        }

        return $productos;
    }

    /** Bytes de la imagen original en la tienda; null si no se pudo bajar. */
    public static function imagenOriginal(string $id): ?string
    {
        return self::pedir(self::baseUrl() . '/api/v1/images/' . rawurlencode($id), 'image/*');
    }

    public static function baseUrl(): string
    {
        return rtrim((string) env('TIENDA_URL', 'https://mikato.com.mx'), '/');
    }

    private static function pedir(string $url, string $aceptar): ?string
    {
        $curl = curl_init($url);
        if ($curl === false) {
            return null;
        }
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 3,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => 12,
            CURLOPT_HTTPHEADER => ['Accept: ' . $aceptar],
        ]);
        $cuerpo = curl_exec($curl);
        $estado = (int) curl_getinfo($curl, CURLINFO_RESPONSE_CODE);

        return ($cuerpo === false || $estado !== 200) ? null : (string) $cuerpo;
    }

    /** @return array{expira: int, productos: list<array<string, mixed>>}|null */
    private static function leerCache(): ?array
    {
        $ruta = BASE_PATH . self::CACHE;
        if (!is_file($ruta)) {
            return null;
        }
        $datos = json_decode((string) file_get_contents($ruta), true);

        return is_array($datos) && is_array($datos['productos'] ?? null) ? $datos : null;
    }

    /** @param list<array<string, mixed>> $productos */
    private static function guardarCache(array $productos): void
    {
        $directorio = dirname(BASE_PATH . self::CACHE);
        if (!is_dir($directorio)) {
            mkdir($directorio, 0775, true);
        }
        file_put_contents(BASE_PATH . self::CACHE, json_encode([
            'expira' => time() + self::CACHE_TTL,
            'productos' => $productos,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), LOCK_EX);
    }
}
