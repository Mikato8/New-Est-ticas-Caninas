<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\HttpException;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Repositories\PublicacionRepository;
use App\Support\Upload;
use App\Support\Validator;

final class ComunidadController
{
    public function index(Request $peticion): Response
    {
        $categoria = $peticion->cadena('categoria');
        $busqueda = $peticion->cadena('q');
        $publicaciones = PublicacionRepository::publicadas($categoria, $busqueda, 30);

        $conteos = [];
        foreach (PublicacionRepository::categorias() as $clave => $etiqueta) {
            $conteos[$clave] = count(PublicacionRepository::publicadas($clave, '', 100));
        }

        return Response::html(view('paginas/comunidad', [
            'titulo' => 'Comunidad de estilistas caninos',
            'descripcion' => 'Casos, técnicas, dudas y experiencias de estilistas caninos de México. Un foro moderado y útil para el oficio.',
            'publicaciones' => $publicaciones,
            'categorias' => PublicacionRepository::categorias(),
            'categoria' => $categoria,
            'busqueda' => $busqueda,
            'conteos' => $conteos,
        ]));
    }

    public function show(Request $peticion, array $parametros): Response
    {
        $publicacion = PublicacionRepository::porSlug((string) ($parametros['slug'] ?? ''));
        if ($publicacion === null) {
            throw new HttpException('Esa publicación no existe o aún no está aprobada.', 404);
        }
        PublicacionRepository::incrementarVistas((int) $publicacion['id']);

        return Response::html(view('paginas/publicacion', [
            'titulo' => $publicacion['titulo'],
            'descripcion' => $publicacion['resumen'] ?: limitar_texto(strip_tags($publicacion['contenido']), 160),
            'publicacion' => $publicacion,
            'comentarios' => PublicacionRepository::comentarios((int) $publicacion['id']),
            'relacionadas' => PublicacionRepository::relacionados((string) $publicacion['categoria'], (int) $publicacion['id'], 3),
            'categorias' => PublicacionRepository::categorias(),
        ]));
    }

    public function crear(Request $peticion): Response
    {
        Auth::requiereLogin();

        return Response::html(view('paginas/publicar', [
            'titulo' => 'Publicar en la comunidad',
            'descripcion' => 'Comparte un caso, una técnica o una duda con otros estilistas caninos. La revisamos antes de publicar.',
            'categorias' => PublicacionRepository::categorias(),
        ]));
    }

    public function guardar(Request $peticion): Response
    {
        Csrf::verificar($peticion);
        Auth::requiereLogin();
        if ($peticion->cadena('sitio_web') !== '') {
            return redirigir('/comunidad');
        }

        $limite = (int) config('limites.publicaciones_por_dia', 3);
        [$permitido, $espera] = Session::limitar('publicaciones', $limite, 86400);
        if (!$permitido) {
            flash('error', 'Ya publicaste ' . $limite . ' veces hoy. Intenta de nuevo en ' . ceil($espera / 3600) . ' horas.');
            return redirigir('/comunidad');
        }

        $datos = [
            'titulo' => $peticion->cadena('titulo'),
            'categoria' => $peticion->cadena('categoria', 'dudas'),
            'resumen' => $peticion->cadena('resumen'),
            'contenido' => trim((string) $peticion->input('contenido', '')),
        ];
        $errores = Validator::validar($datos, [
            'titulo' => 'required|min:8|max:140',
            'resumen' => 'max:280',
            'contenido' => 'required|min:40|max:20000',
        ]);
        if (!isset(PublicacionRepository::categorias()[$datos['categoria']])) {
            $errores['categoria'] = 'Elige una categoría válida.';
        }
        if ($errores !== []) {
            Session::put('_errores', $errores);
            Session::recordarViejo($datos);
            flash('error', 'Revisa tu publicación.');
            return redirigir('/comunidad/publicar');
        }

        $imagen = null;
        $archivo = $peticion->archivo('imagen');
        if ($archivo !== null && (int) ($archivo['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            $subida = Upload::imagen($archivo, 'publicaciones');
            if ($subida['ok']) {
                $imagen = (string) $subida['ruta'];
            }
        }

        $estado = Auth::puedeModerar() ? 'publicado' : 'pendiente';
        PublicacionRepository::crear([
            'usuario_id' => (int) Auth::id(),
            'titulo' => $datos['titulo'],
            'categoria' => $datos['categoria'],
            'resumen' => $datos['resumen'] !== '' ? $datos['resumen'] : null,
            'contenido' => $datos['contenido'],
            'imagen' => $imagen,
            'estado' => $estado,
        ]);
        Session::olvidarViejo();
        flash('exito', $estado === 'publicado' ? 'Publicación creada.' : 'Recibimos tu publicación. La revisamos antes de publicarla.');
        return redirigir('/comunidad');
    }

    public function comentar(Request $peticion, array $parametros): Response
    {
        Csrf::verificar($peticion);
        Auth::requiereLogin();
        $publicacion = PublicacionRepository::porSlug((string) ($parametros['slug'] ?? ''));
        if ($publicacion === null) {
            throw new HttpException('Esa publicación no existe.', 404);
        }

        [$permitido, $espera] = Session::limitar('comentarios', (int) config('limites.comentarios_por_hora', 10), 3600);
        if (!$permitido) {
            flash('error', 'Comentaste mucho en poco tiempo. Espera ' . ceil($espera / 60) . ' minutos.');
            return redirigir('/comunidad/' . $publicacion['slug']);
        }

        $contenido = trim((string) $peticion->input('contenido', ''));
        if (mb_strlen($contenido) < 3 || mb_strlen($contenido) > 1500) {
            flash('error', 'El comentario debe tener entre 3 y 1,500 caracteres.');
            return redirigir('/comunidad/' . $publicacion['slug']);
        }

        $aprobado = Auth::puedeModerar();
        PublicacionRepository::comentar(
            (int) $publicacion['id'],
            (int) Auth::id(),
            $contenido,
            $aprobado ? 'aprobado' : 'pendiente'
        );
        flash('exito', $aprobado ? 'Comentario publicado.' : 'Recibimos tu comentario. Aparecerá cuando lo aprobemos.');
        return redirigir('/comunidad/' . $publicacion['slug']);
    }
}
