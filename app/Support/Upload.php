<?php

declare(strict_types=1);

namespace App\Support;

final class Upload
{
    private const TIPOS = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    /**
     * @param array<string, mixed>|null $archivo
     * @return array{ok: bool, ruta?: string, error?: string}
     */
    public static function imagen(?array $archivo, string $subdirectorio = 'posts'): array
    {
        if ($archivo === null || !isset($archivo['error']) || $archivo['error'] === UPLOAD_ERR_NO_FILE) {
            return ['ok' => false, 'error' => 'No se recibió ninguna imagen.'];
        }
        if ($archivo['error'] !== UPLOAD_ERR_OK) {
            return ['ok' => false, 'error' => 'La imagen no se subió correctamente.'];
        }

        $maximo = (int) config('uploads.max_bytes', 3 * 1024 * 1024);
        if ((int) ($archivo['size'] ?? 0) > $maximo) {
            return ['ok' => false, 'error' => 'La imagen supera el máximo de ' . round($maximo / 1048576, 1) . ' MB.'];
        }

        $rutaTemporal = (string) ($archivo['tmp_name'] ?? '');
        if ($rutaTemporal === '' || !is_uploaded_file($rutaTemporal)) {
            return ['ok' => false, 'error' => 'Archivo temporal inválido.'];
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = (string) $finfo->file($rutaTemporal);
        if (!isset(self::TIPOS[$mime])) {
            return ['ok' => false, 'error' => 'Solo se aceptan imágenes JPG, PNG o WebP.'];
        }

        $info = @getimagesize($rutaTemporal);
        if ($info === false || (int) $info[0] < 1 || (int) $info[1] < 1) {
            return ['ok' => false, 'error' => 'La imagen está dañada.'];
        }

        $subdirectorio = preg_replace('/[^a-z0-9_-]/i', '', $subdirectorio) ?: 'posts';
        $directorio = rtrim((string) config('uploads.path'), '/') . '/' . $subdirectorio;
        if (!is_dir($directorio) && !@mkdir($directorio, 0775, true) && !is_dir($directorio)) {
            return ['ok' => false, 'error' => 'No se pudo crear el directorio de imágenes.'];
        }

        $nombre = bin2hex(random_bytes(10)) . '.' . self::TIPOS[$mime];
        $destino = $directorio . '/' . $nombre;
        if (!move_uploaded_file($rutaTemporal, $destino)) {
            return ['ok' => false, 'error' => 'No se pudo guardar la imagen.'];
        }

        @chmod($destino, 0644);

        return ['ok' => true, 'ruta' => '/uploads/' . $subdirectorio . '/' . $nombre];
    }
}
