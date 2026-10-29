<?php

declare(strict_types=1);

namespace App\Support;

final class Documento
{
    /**
     * Guarda un documento legal fuera de public_html.
     *
     * @param array<string, mixed>|null $archivo
     * @return array{ok: bool, ruta?: string, error?: string}
     */
    public static function guardar(?array $archivo, string $subdirectorio = 'solicitudes'): array
    {
        if ($archivo === null || !isset($archivo['error']) || $archivo['error'] === UPLOAD_ERR_NO_FILE) {
            return ['ok' => false, 'error' => 'No se recibió ningún documento.'];
        }
        if ($archivo['error'] !== UPLOAD_ERR_OK) {
            return ['ok' => false, 'error' => 'El documento no se subió correctamente.'];
        }

        $maximo = (int) config('uploads.documento_max_bytes', 5 * 1024 * 1024);
        if ((int) ($archivo['size'] ?? 0) > $maximo) {
            return ['ok' => false, 'error' => 'El documento supera el máximo de ' . round($maximo / 1048576, 1) . ' MB.'];
        }

        $temporal = (string) ($archivo['tmp_name'] ?? '');
        if ($temporal === '' || !is_uploaded_file($temporal)) {
            return ['ok' => false, 'error' => 'Archivo temporal inválido.'];
        }

        $tipos = [
            'application/pdf' => 'pdf',
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
        ];
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = (string) $finfo->file($temporal);
        if (!isset($tipos[$mime])) {
            return ['ok' => false, 'error' => 'Solo se aceptan PDF, JPG, PNG o WebP.'];
        }

        $subdirectorio = preg_replace('/[^a-z0-9_-]/i', '', $subdirectorio) ?: 'solicitudes';
        $directorio = rtrim(BASE_PATH . '/storage', '/') . '/' . $subdirectorio;
        if (!is_dir($directorio) && !@mkdir($directorio, 0700, true) && !is_dir($directorio)) {
            return ['ok' => false, 'error' => 'No se pudo crear el directorio de documentos.'];
        }

        $nombre = bin2hex(random_bytes(16)) . '.' . $tipos[$mime];
        $destino = $directorio . '/' . $nombre;
        if (!move_uploaded_file($temporal, $destino)) {
            return ['ok' => false, 'error' => 'No se pudo guardar el documento.'];
        }
        @chmod($destino, 0600);

        return ['ok' => true, 'ruta' => $subdirectorio . '/' . $nombre];
    }
}
