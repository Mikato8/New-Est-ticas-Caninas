<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\HttpException;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Repositories\CitaRepository;
use App\Repositories\ContactoRepository;
use App\Repositories\NegocioRepository;
use App\Repositories\PublicacionRepository;
use App\Repositories\UsuarioRepository;
use App\Support\Slug;
use App\Support\Validator;

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

    /** @return array<string, mixed> */
    private function guardAdmin(): array
    {
        Auth::requiereRol(['admin']);
        return Auth::usuario() ?? [];
    }

    public function usuarios(Request $peticion): Response
    {
        $usuario = $this->guardAdmin();
        return Response::html(view_admin('admin/usuarios', [
            'titulo' => 'Usuarios y accesos',
            'descripcion' => 'Tiempo de uso, roles, estado y altas de usuarios del CRM.',
            'usuario' => $usuario,
            'usuarios' => UsuarioRepository::listar(),
            'resumen' => UsuarioRepository::resumenAccesos(),
            'roles' => ['admin' => 'Administrador', 'editor' => 'Editor', 'estilista' => 'Estilista', 'cliente' => 'Cliente'],
            'dias_default' => max(1, (int) config('acceso.dias_default', 30)),
        ]));
    }

    public function usuarioGuardar(Request $peticion): Response
    {
        $usuario = $this->guardAdmin();
        Csrf::verificar($peticion);
        $datos = [
            'nombre' => $peticion->cadena('nombre'),
            'correo' => mb_strtolower($peticion->cadena('correo')),
            'rol' => $peticion->cadena('rol', 'cliente'),
            'password' => (string) $peticion->input('password', ''),
            'acceso_hasta' => trim($peticion->cadena('acceso_hasta')),
        ];
        $errores = Validator::validar($datos, [
            'nombre' => 'required|min:3|max:120',
            'correo' => 'required|email|max:160',
            'password' => 'required|min:8|max:100',
        ]);
        if (!in_array($datos['rol'], ['admin', 'editor', 'estilista', 'cliente'], true)) {
            $errores['rol'] = 'Elige un rol válido.';
        }
        if ($datos['acceso_hasta'] !== '') {
            $fecha = \DateTimeImmutable::createFromFormat('!Y-m-d', $datos['acceso_hasta']);
            if ($fecha === false || $fecha->format('Y-m-d') !== $datos['acceso_hasta']) {
                $errores['acceso_hasta'] = 'Escribe una fecha válida.';
            }
        }
        if (UsuarioRepository::existeCorreo($datos['correo'])) {
            $errores['correo'] = 'Ese correo ya está registrado.';
        }
        if ($errores !== []) {
            Session::put('_errores', $errores);
            Session::recordarViejo($datos);
            flash('error', 'Revisa los datos del usuario.');
            return redirigir('/admin/usuarios');
        }

        $acceso = $datos['acceso_hasta'];
        if ($datos['rol'] === 'admin') {
            $acceso = null;
        } elseif ($acceso === '') {
            $acceso = date('Y-m-d', strtotime('+' . max(1, (int) config('acceso.dias_default', 30)) . ' days'));
        }
        $base = explode('@', $datos['correo'])[0] ?? 'usuario';
        UsuarioRepository::crear([
            'nombre' => $datos['nombre'],
            'usuario' => Slug::unico($base, static fn (string $c): bool => UsuarioRepository::existeUsuario($c)),
            'correo' => $datos['correo'],
            'password_hash' => password_hash($datos['password'], PASSWORD_DEFAULT),
            'rol' => $datos['rol'],
            'estado' => 'activo',
            'acceso_hasta' => $acceso,
        ]);
        Session::olvidarViejo();
        flash('exito', 'Usuario creado con acceso hasta ' . ($acceso ?? 'sin límite') . '.');
        return redirigir('/admin/usuarios');
    }

    public function usuarioAcceso(Request $peticion): Response
    {
        $this->guardAdmin();
        Csrf::verificar($peticion);
        $id = $peticion->entero('id');
        if (UsuarioRepository::porId($id) === null) {
            flash('error', 'El usuario ya no existe.');
            return redirigir('/admin/usuarios');
        }
        $fecha = trim($peticion->cadena('acceso_hasta'));
        if ($fecha !== '') {
            $valida = \DateTimeImmutable::createFromFormat('!Y-m-d', $fecha);
            if ($valida === false || $valida->format('Y-m-d') !== $fecha) {
                flash('error', 'Escribe una fecha de acceso válida.');
                return redirigir('/admin/usuarios');
            }
        }
        UsuarioRepository::actualizarAcceso($id, $fecha !== '' ? $fecha : null);
        flash('exito', $fecha !== '' ? 'Fecha de acceso actualizada.' : 'Acceso sin fecha de vencimiento.');
        return redirigir('/admin/usuarios');
    }

    public function usuarioEstado(Request $peticion): Response
    {
        $usuario = $this->guardAdmin();
        Csrf::verificar($peticion);
        $id = $peticion->entero('id');
        if ($id === (int) $usuario['id']) {
            flash('error', 'No puedes suspender tu propia cuenta.');
            return redirigir('/admin/usuarios');
        }
        UsuarioRepository::cambiarEstado($id, $peticion->cadena('estado') === 'activo' ? 'activo' : 'suspendido');
        flash('exito', 'Estado del usuario actualizado.');
        return redirigir('/admin/usuarios');
    }

    public function usuarioRol(Request $peticion): Response
    {
        $usuario = $this->guardAdmin();
        Csrf::verificar($peticion);
        $id = $peticion->entero('id');
        $rol = $peticion->cadena('rol');
        if ($id === (int) $usuario['id']) {
            flash('error', 'No puedes cambiar tu propio rol.');
            return redirigir('/admin/usuarios');
        }
        if (!in_array($rol, ['admin', 'editor', 'estilista', 'cliente'], true)) {
            flash('error', 'Rol inválido.');
            return redirigir('/admin/usuarios');
        }
        UsuarioRepository::cambiarRol($id, $rol);
        flash('exito', 'Rol actualizado.');
        return redirigir('/admin/usuarios');
    }

    public function usuariosAccesoMasivo(Request $peticion): Response
    {
        $this->guardAdmin();
        Csrf::verificar($peticion);
        $dias = max(1, min(3650, $peticion->entero('dias') ?: (int) config('acceso.dias_default', 30)));
        $cantidad = UsuarioRepository::asignarAccesoFaltante($dias);
        flash('exito', $cantidad . ' cuenta(s) sin fecha recibieron ' . $dias . ' días de acceso.');
        return redirigir('/admin/usuarios');
    }

}
