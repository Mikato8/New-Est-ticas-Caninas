<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\HttpException;
use App\Core\Request;
use App\Core\Response;
use App\Support\Tienda;

/**
 * Imágenes de la tienda servidas desde el propio sitio.
 *
 * Las originales de mikato.com.mx pesan ~1.8 MB; aquí se bajan una vez, se
 * reducen a 480 px de ancho y se guardan en storage/cache. Si no se puede
 * generar, se manda al visitante a la imagen original.
 */
final class MediaController
{
    private const ANCHO = 480;
    private const CALIDAD = 82;

    public function tienda(Request $peticion, array $parametros): Response
    {
        $id = (string) ($parametros['id'] ?? '');
        if (preg_match('/^[0-9a-fA-F-]{36}$/', $id) !== 1) {
            throw new HttpException('Imagen no encontrada.', 404);
        }

        $ruta = BASE_PATH . '/storage/cache/tienda/' . $id . '-' . self::ANCHO . '.webp';
        if (!is_file($ruta)) {
            self::generar($id, $ruta);
        }
        if (!is_file($ruta)) {
            return Response::redirigir(Tienda::baseUrl() . '/api/v1/images/' . rawurlencode($id), 302);
        }

        return Response::texto((string) file_get_contents($ruta), 200, 'image/webp')
            ->conCabecera('Cache-Control', 'public, max-age=2592000, immutable');
    }

    private static function generar(string $id, string $destino): void
    {
        $bytes = Tienda::imagenOriginal($id);
        if ($bytes === null || strlen($bytes) > 12_000_000) {
            return;
        }

        $origen = @imagecreatefromstring($bytes);
        if ($origen === false) {
            return;
        }

        $anchoOriginal = imagesx($origen);
        $altoOriginal = imagesy($origen);
        $ancho = min(self::ANCHO, $anchoOriginal);
        $alto = max(1, (int) round($altoOriginal * ($ancho / max(1, $anchoOriginal))));

        $lienzo = imagecreatetruecolor($ancho, $alto);
        imagefill($lienzo, 0, 0, (int) imagecolorallocate($lienzo, 251, 247, 239));
        imagecopyresampled($lienzo, $origen, 0, 0, 0, 0, $ancho, $alto, $anchoOriginal, $altoOriginal);
        imagedestroy($origen);

        $directorio = dirname($destino);
        if (!is_dir($directorio)) {
            mkdir($directorio, 0775, true);
        }
        imagewebp($lienzo, $destino, self::CALIDAD);
        imagedestroy($lienzo);
    }
}
