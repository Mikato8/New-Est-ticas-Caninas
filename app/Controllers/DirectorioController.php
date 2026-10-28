<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\HttpException;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Repositories\NegocioRepository;
use App\Support\Upload;
use App\Support\Validator;

final class DirectorioController
{
    public function index(Request $peticion): Response
    {
        $estadoGeo = $peticion->cadena('estado');
        $busqueda = $peticion->cadena('q');
        $negocios = NegocioRepository::aprobados($estadoGeo, $busqueda, 60);

        return Response::html(view('paginas/directorio', [
            'titulo' => 'Directorio de estéticas caninas',
            'descripcion' => 'Encuentra estilistas y estéticas caninas en México por estado. Fichas con servicios, horario y contacto directo.',
            'negocios' => $negocios,
            'estados' => NegocioRepository::estados(),
            'estadoGeo' => $estadoGeo,
            'busqueda' => $busqueda,
            'total' => NegocioRepository::contarAprobados(),
            'pendientes' => NegocioRepository::contarPendientes(),
        ]));
    }

    public function show(Request $peticion, array $parametros): Response
    {
        $negocio = NegocioRepository::porSlug((string) ($parametros['slug'] ?? ''));
        if ($negocio === null) {
            throw new HttpException('Esa ficha no existe o aún no está aprobada.', 404);
        }
        $whatsapp = '';
        if (!empty($negocio['whatsapp'])) {
            $numero = preg_replace('/\D+/', '', (string) $negocio['whatsapp']) ?: '';
            if (strlen($numero) === 10) {
                $numero = '52' . $numero;
            }
            if ($numero !== '') {
                $whatsapp = 'https://wa.me/' . $numero . '?text=' . rawurlencode('Hola, vi su ficha en Mikato Estilistas Caninos y quiero información.');
            }
        }

        return Response::html(view('paginas/negocio', [
            'titulo' => $negocio['nombre'],
            'descripcion' => $negocio['descripcion'] ?: 'Ficha de estética canina en ' . ($negocio['ciudad'] ?? 'México'),
            'negocio' => $negocio,
            'relacionados' => NegocioRepository::relacionados((string) ($negocio['estado_geo'] ?? ''), (int) $negocio['id'], 3),
            'whatsapp' => $whatsapp,
        ]));
    }

    public function crear(Request $peticion): Response
    {
        Auth::requiereLogin();
        $usuario = Auth::usuario();
        $existente = \App\Core\Database::first('select * from negocios where usuario_id = ? order by id desc limit 1', [(int) $usuario['id']]);

        return Response::html(view('paginas/registrar-negocio', [
            'titulo' => 'Registrar mi estética en el directorio',
            'descripcion' => 'Publica tu estética canina en el directorio de Mikato. Revisamos cada ficha antes de aprobarla.',
            'existente' => $existente,
            'usuario' => $usuario,
        ]));
    }

    public function guardar(Request $peticion): Response
    {
        Csrf::verificar($peticion);
        Auth::requiereLogin();
        if ($peticion->cadena('sitio_web') !== '') {
            return redirigir('/directorio');
        }

        $usuario = Auth::usuario();
        $existente = \App\Core\Database::value('select count(*) from negocios where usuario_id = ?', [(int) $usuario['id']]);
        if ((int) $existente > 0) {
            flash('error', 'Ya tienes una ficha registrada. Escríbenos para actualizarla.');
            return redirigir('/directorio');
        }

        $datos = [
            'nombre' => $peticion->cadena('nombre'),
            'descripcion' => $peticion->cadena('descripcion'),
            'ciudad' => $peticion->cadena('ciudad'),
            'estado_geo' => $peticion->cadena('estado_geo'),
            'direccion' => $peticion->cadena('direccion'),
            'telefono' => $peticion->cadena('telefono'),
            'whatsapp' => $peticion->cadena('whatsapp'),
            'correo' => $peticion->cadena('correo'),
            'web' => $peticion->cadena('web'),
            'servicios' => $peticion->cadena('servicios'),
            'horario' => $peticion->cadena('horario'),
        ];
        $errores = Validator::validar($datos, [
            'nombre' => 'required|min:3|max:120',
            'descripcion' => 'required|min:40|max:1200',
            'ciudad' => 'required|max:80',
            'estado_geo' => 'required|max:80',
            'telefono' => 'max:30',
            'correo' => 'email|max:160',
            'web' => 'url|max:200',
            'servicios' => 'max:300',
            'horario' => 'max:200',
        ]);
        if ($errores !== []) {
            Session::put('_errores', $errores);
            Session::recordarViejo($datos);
            flash('error', 'Revisa los datos de tu ficha.');
            return redirigir('/directorio/registrar');
        }

        $logo = null;
        $archivo = $peticion->archivo('logo');
        if ($archivo !== null && (int) ($archivo['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            $subida = Upload::imagen($archivo, 'negocios');
            if ($subida['ok']) {
                $logo = (string) $subida['ruta'];
            }
        }

        NegocioRepository::crear(array_merge($datos, ['usuario_id' => (int) $usuario['id'], 'logo' => $logo]));
        Session::olvidarViejo();
        flash('exito', 'Recibimos tu ficha. La revisamos y te avisamos cuando esté publicada.');
        return redirigir('/directorio');
    }
}
