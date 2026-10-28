<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\HttpException;
use App\Core\Request;
use App\Core\Response;
use App\Repositories\CitaRepository;
use App\Repositories\ContactoRepository;
use App\Repositories\NegocioRepository;
use App\Repositories\PublicacionRepository;

final class AdminController
{
    /** @return array<string, mixed> */
    private function guard(): array
    {
        Auth::requiereRol(['admin', 'editor']);
        return Auth::usuario() ?? [];
    }

    public function dashboard(Request $peticion): Response
    {
        $usuario = $this->guard();

        return Response::html(view_admin('admin/dashboard', [
            'titulo' => 'Administración',
            'descripcion' => 'Moderación de comunidad, directorio y mensajes.',
            'usuario' => $usuario,
            'pendientes' => [
                'publicaciones' => PublicacionRepository::contarPendientes(),
                'comentarios' => PublicacionRepository::contarComentariosPendientes(),
                'negocios' => NegocioRepository::contarPendientes(),
                'mensajes' => ContactoRepository::contarNuevos(),
            ],
            'publicaciones' => PublicacionRepository::pendientes(5),
            'negocios' => NegocioRepository::pendientes(5),
            'comentarios' => PublicacionRepository::comentariosPendientes(5),
            'contactos' => ContactoRepository::listar(5),
            'citas' => CitaRepository::todas(5),
            'publicadas' => PublicacionRepository::contarPublicadas(),
            'directorio' => NegocioRepository::contarAprobados(),
        ]));
    }

    public function publicaciones(Request $peticion): Response
    {
        $usuario = $this->guard();
        return Response::html(view_admin('admin/publicaciones', [
            'titulo' => 'Publicaciones',
            'descripcion' => 'Aprueba, rechaza o fija publicaciones de la comunidad.',
            'usuario' => $usuario,
            'publicaciones' => PublicacionRepository::todas(200),
            'categorias' => PublicacionRepository::categorias(),
        ]));
    }

    public function estadoPublicacion(Request $peticion): Response
    {
        $this->guard();
        Csrf::verificar($peticion);
        $id = $peticion->entero('id');
        $estado = $peticion->cadena('estado');
        if (!in_array($estado, ['pendiente', 'publicado', 'rechazado'], true)) {
            throw new HttpException('Estado inválido.', 400);
        }
        PublicacionRepository::cambiarEstado($id, $estado);
        flash('exito', 'Publicación marcada como ' . $estado . '.');
        return redirigir('/admin/publicaciones');
    }

    public function fijarPublicacion(Request $peticion): Response
    {
        $this->guard();
        Csrf::verificar($peticion);
        PublicacionRepository::fijar($peticion->entero('id'), $peticion->cadena('fijado') === '1');
        flash('exito', 'Publicación actualizada.');
        return redirigir('/admin/publicaciones');
    }

    public function eliminarPublicacion(Request $peticion): Response
    {
        $usuario = $this->guard();
        if (($usuario['rol'] ?? '') !== 'admin') {
            throw new HttpException('Solo administración puede eliminar publicaciones.', 403);
        }
        Csrf::verificar($peticion);
        PublicacionRepository::eliminar($peticion->entero('id'));
        flash('exito', 'Publicación eliminada.');
        return redirigir('/admin/publicaciones');
    }

    public function comentarios(Request $peticion): Response
    {
        $usuario = $this->guard();
        return Response::html(view_admin('admin/comentarios', [
            'titulo' => 'Comentarios',
            'descripcion' => 'Modera las respuestas de la comunidad.',
            'usuario' => $usuario,
            'comentarios' => PublicacionRepository::comentariosPendientes(200),
        ]));
    }

    public function estadoComentario(Request $peticion): Response
    {
        $this->guard();
        Csrf::verificar($peticion);
        $estado = $peticion->cadena('estado') === 'aprobado' ? 'aprobado' : 'rechazado';
        PublicacionRepository::cambiarEstadoComentario($peticion->entero('id'), $estado);
        flash('exito', 'Comentario ' . $estado . '.');
        return redirigir('/admin/comentarios');
    }

    public function eliminarComentario(Request $peticion): Response
    {
        $usuario = $this->guard();
        if (($usuario['rol'] ?? '') !== 'admin') {
            throw new HttpException('Solo administración puede eliminar comentarios.', 403);
        }
        Csrf::verificar($peticion);
        PublicacionRepository::eliminarComentario($peticion->entero('id'));
        flash('exito', 'Comentario eliminado.');
        return redirigir('/admin/comentarios');
    }

    public function negocios(Request $peticion): Response
    {
        $usuario = $this->guard();
        return Response::html(view_admin('admin/negocios', [
            'titulo' => 'Directorio',
            'descripcion' => 'Revisa y publica fichas de estéticas caninas.',
            'usuario' => $usuario,
            'negocios' => NegocioRepository::todos(200),
        ]));
    }

    public function estadoNegocio(Request $peticion): Response
    {
        $this->guard();
        Csrf::verificar($peticion);
        $estado = $peticion->cadena('estado');
        if (!in_array($estado, ['pendiente', 'aprobado', 'rechazado'], true)) {
            throw new HttpException('Estado inválido.', 400);
        }
        $id = $peticion->entero('id');
        NegocioRepository::cambiarEstado($id, $estado);
        if ($estado === 'aprobado') {
            NegocioRepository::verificar($id, $peticion->cadena('verificado') === '1');
        }
        flash('exito', 'Ficha actualizada.');
        return redirigir('/admin/negocios');
    }

    public function verificarNegocio(Request $peticion): Response
    {
        $this->guard();
        Csrf::verificar($peticion);
        NegocioRepository::verificar($peticion->entero('id'), $peticion->cadena('verificado') === '1');
        flash('exito', 'Verificación actualizada.');
        return redirigir('/admin/negocios');
    }

    public function destacarNegocio(Request $peticion): Response
    {
        $this->guard();
        Csrf::verificar($peticion);
        NegocioRepository::destacar($peticion->entero('id'), $peticion->cadena('destacado') === '1');
        flash('exito', 'Ficha actualizada.');
        return redirigir('/admin/negocios');
    }

    public function eliminarNegocio(Request $peticion): Response
    {
        $usuario = $this->guard();
        if (($usuario['rol'] ?? '') !== 'admin') {
            throw new HttpException('Solo administración puede eliminar fichas.', 403);
        }
        Csrf::verificar($peticion);
        NegocioRepository::eliminar($peticion->entero('id'));
        flash('exito', 'Ficha eliminada.');
        return redirigir('/admin/negocios');
    }

    public function mensajes(Request $peticion): Response
    {
        $usuario = $this->guard();
        return Response::html(view_admin('admin/mensajes', [
            'titulo' => 'Mensajes de contacto',
            'descripcion' => 'Solicitudes de servicio, distribución y dudas.',
            'usuario' => $usuario,
            'contactos' => ContactoRepository::listar(200),
        ]));
    }

    public function atenderMensaje(Request $peticion): Response
    {
        $this->guard();
        Csrf::verificar($peticion);
        ContactoRepository::marcarAtendido($peticion->entero('id'));
        flash('exito', 'Mensaje marcado como atendido.');
        return redirigir('/admin/mensajes');
    }

    public function eliminarMensaje(Request $peticion): Response
    {
        $usuario = $this->guard();
        if (($usuario['rol'] ?? '') !== 'admin') {
            throw new HttpException('Solo administración puede eliminar mensajes.', 403);
        }
        Csrf::verificar($peticion);
        ContactoRepository::eliminar($peticion->entero('id'));
        flash('exito', 'Mensaje eliminado.');
        return redirigir('/admin/mensajes');
    }

    public function citas(Request $peticion): Response
    {
        $usuario = $this->guard();
        return Response::html(view_admin('admin/citas', [
            'titulo' => 'Citas',
            'descripcion' => 'Vista general de la agenda.',
            'usuario' => $usuario,
            'citas' => CitaRepository::todas(100),
        ]));
    }
}
