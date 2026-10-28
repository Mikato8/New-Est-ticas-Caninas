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
use App\Repositories\ClienteRepository;
use App\Repositories\MascotaRepository;
use App\Repositories\VentaRepository;
use App\Support\Upload;
use App\Support\Validator;

final class MiCuentaController
{
    public function index(Request $peticion): Response
    {
        Auth::requiereLogin();
        if (Auth::esPersonal()) {
            return redirigir('/panel');
        }
        $usuario = Auth::usuario();
        $clienteId = ClienteRepository::asegurarParaUsuario((int) $usuario['id']);
        $cliente = ClienteRepository::porId($clienteId);
        $mascotas = MascotaRepository::porCliente($clienteId);
        $citas = CitaRepository::porUsuario((int) $usuario['id'], 20);
        $proximas = array_values(array_filter($citas, static fn (array $c): bool => $c['fecha'] >= date('Y-m-d') && in_array($c['estado'], ['pendiente', 'confirmada'], true)));

        $historial = [];
        foreach ($mascotas as $mascota) {
            $historial[(int) $mascota['id']] = MascotaRepository::historial((int) $mascota['id'], 3);
        }

        return Response::html(view('paginas/mi-cuenta', [
            'titulo' => 'Mi cuenta',
            'descripcion' => 'Tus mascotas, próximas citas e historial de estética en Mikato.',
            'usuario' => $usuario,
            'cliente' => $cliente,
            'mascotas' => $mascotas,
            'proximas' => $proximas,
            'citas' => $citas,
            'historial' => $historial,
            'compras' => VentaRepository::porCliente($clienteId, 6),
        ]));
    }

    public function mascotas(Request $peticion): Response
    {
        Auth::requiereLogin();
        if (Auth::esPersonal()) {
            return redirigir('/panel/mascotas');
        }
        $usuario = Auth::usuario();
        $clienteId = ClienteRepository::asegurarParaUsuario((int) $usuario['id']);
        $editar = null;
        if ($peticion->entero('editar') > 0) {
            $editar = MascotaRepository::porId($peticion->entero('editar'));
            if ($editar === null || (int) $editar['cliente_id'] !== $clienteId) {
                throw new HttpException('Esa mascota no está en tu cuenta.', 404);
            }
        }

        return Response::html(view('paginas/mis-mascotas', [
            'titulo' => 'Mis mascotas',
            'descripcion' => 'Agrega o actualiza los datos de tus perros para un servicio más seguro y personalizado.',
            'mascotas' => MascotaRepository::porCliente($clienteId),
            'editar' => $editar,
        ]));
    }

    public function guardarMascota(Request $peticion): Response
    {
        Csrf::verificar($peticion);
        Auth::requiereLogin();
        $usuario = Auth::usuario();
        $clienteId = ClienteRepository::asegurarParaUsuario((int) $usuario['id']);

        $datos = [
            'nombre' => $peticion->cadena('nombre'),
            'especie' => $peticion->cadena('especie', 'perro'),
            'raza' => $peticion->cadena('raza'),
            'tamano' => $peticion->cadena('tamano', 'mediano'),
            'sexo' => $peticion->cadena('sexo'),
            'nacimiento' => $peticion->cadena('nacimiento'),
            'peso' => $peticion->cadena('peso'),
            'temperamento' => $peticion->cadena('temperamento'),
            'alergias' => $peticion->cadena('alergias'),
            'notas' => $peticion->cadena('notas'),
        ];
        $errores = Validator::validar($datos, [
            'nombre' => 'required|min:2|max:80',
            'peso' => 'numeric',
            'notas' => 'max:1000',
        ]);
        if (!in_array($datos['tamano'], ['chico', 'mediano', 'grande', 'gigante'], true)) {
            $errores['tamano'] = 'Elige un tamaño válido.';
        }
        if ($datos['nacimiento'] !== '' && strtotime($datos['nacimiento']) === false) {
            $errores['nacimiento'] = 'La fecha no es válida.';
        }
        if ($errores !== []) {
            Session::put('_errores', $errores);
            Session::recordarViejo($datos);
            flash('error', 'Revisa los datos de tu mascota.');
            return redirigir('/mi-cuenta/mascotas');
        }

        $foto = null;
        $archivo = $peticion->archivo('foto');
        if ($archivo !== null && (int) ($archivo['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            $subida = Upload::imagen($archivo, 'mascotas');
            if ($subida['ok']) {
                $foto = (string) $subida['ruta'];
            }
        }

        $id = $peticion->entero('id');
        if ($id > 0) {
            $actual = MascotaRepository::porId($id);
            if ($actual === null || (int) $actual['cliente_id'] !== $clienteId) {
                throw new HttpException('Esa mascota no está en tu cuenta.', 404);
            }
            $datos['cliente_id'] = $clienteId;
            $datos['foto'] = $foto;
            MascotaRepository::actualizar($id, $datos);
            flash('exito', 'Datos de ' . $datos['nombre'] . ' actualizados.');
        } else {
            $datos['cliente_id'] = $clienteId;
            $datos['foto'] = $foto;
            MascotaRepository::crear($datos);
            flash('exito', 'Agregamos a ' . $datos['nombre'] . ' a tu cuenta.');
        }
        Session::olvidarViejo();
        return redirigir('/mi-cuenta/mascotas');
    }

    public function eliminarMascota(Request $peticion): Response
    {
        Csrf::verificar($peticion);
        Auth::requiereLogin();
        $usuario = Auth::usuario();
        $clienteId = ClienteRepository::asegurarParaUsuario((int) $usuario['id']);
        $mascota = MascotaRepository::porId($peticion->entero('id'));
        if ($mascota === null || (int) $mascota['cliente_id'] !== $clienteId) {
            throw new HttpException('Esa mascota no está en tu cuenta.', 404);
        }
        MascotaRepository::eliminar((int) $mascota['id']);
        flash('exito', 'Quitamos a ' . $mascota['nombre'] . ' de tu cuenta.');
        return redirigir('/mi-cuenta/mascotas');
    }

    public function citas(Request $peticion): Response
    {
        Auth::requiereLogin();
        if (Auth::esPersonal()) {
            return redirigir('/panel/agenda');
        }
        $usuario = Auth::usuario();
        return Response::html(view('paginas/mis-citas', [
            'titulo' => 'Mis citas',
            'descripcion' => 'Historial y próximas citas de estética canina.',
            'citas' => CitaRepository::porUsuario((int) $usuario['id']),
        ]));
    }

    public function historial(Request $peticion, array $parametros): Response
    {
        Auth::requiereLogin();
        $usuario = Auth::usuario();
        $clienteId = ClienteRepository::asegurarParaUsuario((int) $usuario['id']);
        $mascota = MascotaRepository::porId((int) ($parametros['id'] ?? 0));
        if ($mascota === null || (int) $mascota['cliente_id'] !== $clienteId) {
            throw new HttpException('Esa mascota no está en tu cuenta.', 404);
        }

        return Response::html(view('paginas/mi-historial', [
            'titulo' => 'Historial de ' . $mascota['nombre'],
            'descripcion' => 'Servicios, observaciones y próximas visitas de ' . $mascota['nombre'] . '.',
            'mascota' => $mascota,
            'historial' => MascotaRepository::historial((int) $mascota['id']),
            'citas' => CitaRepository::porCliente($clienteId),
        ]));
    }

    public function cancelarCita(Request $peticion): Response
    {
        Csrf::verificar($peticion);
        Auth::requiereLogin();
        $usuario = Auth::usuario();
        $clienteId = ClienteRepository::asegurarParaUsuario((int) $usuario['id']);
        $cita = CitaRepository::porId($peticion->entero('id'));
        if ($cita === null || (int) $cita['cliente_id'] !== $clienteId) {
            throw new HttpException('Esa cita no está en tu cuenta.', 404);
        }
        if (!in_array($cita['estado'], ['pendiente', 'confirmada'], true)) {
            flash('error', 'Esta cita ya no se puede cancelar desde la web. Llámanos.');
            return redirigir('/mi-cuenta/citas');
        }
        if (strtotime($cita['fecha'] . ' ' . $cita['hora']) < time() + 86400) {
            flash('error', 'Para cancelar con menos de 24 horas, llámanos o escríbenos por WhatsApp.');
            return redirigir('/mi-cuenta/citas');
        }
        CitaRepository::cambiarEstado((int) $cita['id'], 'cancelada');
        flash('exito', 'Cita cancelada. Te esperamos en la próxima.');
        return redirigir('/mi-cuenta/citas');
    }
}
