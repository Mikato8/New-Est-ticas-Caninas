<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\HttpException;
use App\Core\Request;
use App\Core\Response;
use App\Domain\Contenido\Guias;
use App\Repositories\GuiaMediaRepository;
use App\Support\Upload;

final class GuiaController
{
    public function index(Request $peticion): Response
    {
        $categoria = $peticion->cadena('categoria');
        $busqueda = mb_strtolower($peticion->cadena('q'));
        $guias = Guias::catalogo();

        if ($categoria !== '' && isset(Guias::categorias()[$categoria])) {
            $guias = array_values(array_filter($guias, static fn (array $g): bool => $g['categoria'] === $categoria));
        }
        if ($busqueda !== '') {
            $guias = array_values(array_filter($guias, static function (array $g) use ($busqueda): bool {
                $texto = mb_strtolower($g['titulo'] . ' ' . $g['resumen'] . ' ' . implode(' ', array_column($g['secciones'], 'titulo')));
                return str_contains($texto, $busqueda);
            }));
        }

        return Response::html(view('paginas/guias', [
            'titulo' => 'Guías de estética canina',
            'descripcion' => 'Baño, corte, uñas, oídos, secado, manejo y negocio: guías de oficio escritas para estilistas caninos de México.',
            'guias' => $guias,
            'categoria' => $categoria,
            'busqueda' => $peticion->cadena('q'),
            'categorias' => Guias::categorias(),
        ]));
    }

    public function show(Request $peticion, array $parametros): Response
    {
        $guia = Guias::porSlug((string) ($parametros['slug'] ?? ''));
        if ($guia === null) {
            throw new \App\Core\HttpException('Esa guía no existe o cambió de dirección.', 404);
        }

        $relacionadas = array_values(array_filter(
            Guias::porCategoria($guia['categoria']),
            static fn (array $g): bool => $g['slug'] !== $guia['slug']
        ));
        $relacionadas = array_slice($relacionadas, 0, 3);

        $indice = null;
        $catalogo = Guias::catalogo();
        foreach ($catalogo as $i => $item) {
            if ($item['slug'] === $guia['slug']) {
                $indice = $i;
                break;
            }
        }
        $anterior = $indice !== null && isset($catalogo[$indice - 1]) ? $catalogo[$indice - 1] : null;
        $siguiente = $indice !== null && isset($catalogo[$indice + 1]) ? $catalogo[$indice + 1] : null;

        return Response::html(view('paginas/guia', [
            'titulo' => $guia['titulo'],
            'descripcion' => $guia['resumen'],
            'guia' => $guia,
            'relacionadas' => $relacionadas,
            'anterior' => $anterior,
            'siguiente' => $siguiente,
            'categorias' => Guias::categorias(),
            'media' => GuiaMediaRepository::porGuia((string) $guia['slug'], 12),
            'puedeSubir' => Auth::check(),
        ]));
    }

    public function subirMedia(Request $peticion, array $parametros): Response
    {
        Csrf::verificar($peticion);
        Auth::requiereLogin();

        $slug = (string) ($parametros['slug'] ?? '');
        if (Guias::porSlug($slug) === null) {
            throw new HttpException('Esa guía no existe.', 404);
        }

        $usuarioId = (int) Auth::id();
        if (GuiaMediaRepository::contarHoy($usuarioId) >= (int) config('limites.guia_media_por_dia', 5)) {
            flash('error', 'Ya compartiste varios trabajos hoy. Intenta mañana o edita uno existente.');
            return redirigir('/guias/' . $slug . '#galeria');
        }

        $titulo = trim($peticion->cadena('titulo'));
        $descripcion = trim($peticion->cadena('descripcion'));
        if (mb_strlen($titulo) < 3 || mb_strlen($titulo) > 120) {
            flash('error', 'Escribe un título de 3 a 120 caracteres para tu trabajo.');
            return redirigir('/guias/' . $slug . '#galeria');
        }
        if (mb_strlen($descripcion) > 400) {
            flash('error', 'La descripción no puede pasar de 400 caracteres.');
            return redirigir('/guias/' . $slug . '#galeria');
        }

        $archivo = $peticion->archivo('archivo');
        if ($archivo === null || (int) ($archivo['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            flash('error', 'Elige una imagen o un video para compartir.');
            return redirigir('/guias/' . $slug . '#galeria');
        }

        $temporal = (string) ($archivo['tmp_name'] ?? '');
        $mime = $temporal !== '' && is_file($temporal) ? (string) (new \finfo(FILEINFO_MIME_TYPE))->file($temporal) : '';
        if (str_starts_with($mime, 'image/')) {
            $tipo = 'imagen';
            $subida = Upload::imagen($archivo, 'guias');
        } elseif (str_starts_with($mime, 'video/')) {
            $tipo = 'video';
            $subida = Upload::video($archivo, 'guias');
        } else {
            flash('error', 'El archivo debe ser una imagen JPG/PNG/WebP o un video MP4/WebM/MOV.');
            return redirigir('/guias/' . $slug . '#galeria');
        }

        if (!($subida['ok'] ?? false)) {
            flash('error', (string) ($subida['error'] ?? 'No se pudo subir el archivo.'));
            return redirigir('/guias/' . $slug . '#galeria');
        }

        GuiaMediaRepository::crear($usuarioId, $slug, $tipo, (string) $subida['ruta'], $titulo, $descripcion);
        flash('exito', 'Tu trabajo ya se ve en la galería de esta guía.');
        return redirigir('/guias/' . $slug . '#galeria');
    }
}
