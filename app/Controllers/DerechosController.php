<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Csrf;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Repositories\SolicitudArcoRepository;
use App\Support\Documento;
use App\Support\Mailer;
use App\Support\Validator;

final class DerechosController
{
    public function rectificacionForm(Request $peticion): Response
    {
        return Response::html(view('paginas/derechos-rectificacion', [
            'titulo' => 'Rectificación de datos personales',
            'descripcion' => 'Solicitud para rectificar datos personales inexactos, incompletos o desactualizados conforme a la LFPDPPP.',
            'ciudad' => (string) config('contacto.ciudad', 'Guadalajara'),
            'direccion' => (string) config('contacto.direccion', ''),
            'correo' => (string) config('contacto.email', ''),
        ]));
    }

    public function enviarRectificacion(Request $peticion): Response
    {
        Csrf::verificar($peticion);
        if ($peticion->cadena('sitio_web') !== '') {
            return redirigir('/derechos/rectificacion');
        }

        [$permitido, $espera] = Session::limitar('arco', (int) config('limites.arco_por_hora', 3), 3600);
        if (!$permitido) {
            flash('error', 'Recibimos varias solicitudes seguidas. Espera ' . ceil($espera / 60) . ' minutos o escribe a ' . config('contacto.email') . '.');
            return redirigir('/derechos/rectificacion');
        }

        $datos = [
            'nombre_titular' => $peticion->cadena('nombre_titular'),
            'numero_cliente' => $peticion->cadena('numero_cliente'),
            'correo' => mb_strtolower($peticion->cadena('correo')),
            'telefono' => $peticion->cadena('telefono'),
            'ciudad' => $peticion->cadena('ciudad'),
            'nombre_representante' => $peticion->cadena('nombre_representante'),
            'tipo_representacion' => $peticion->cadena('tipo_representacion'),
            'dato_incorrecto' => $peticion->cadena('dato_incorrecto'),
            'dato_correcto' => $peticion->cadena('dato_correcto'),
            'documento_acreditativo' => $peticion->cadena('documento_acreditativo'),
            'notificacion_medio' => $peticion->cadena('notificacion_medio', 'correo'),
            'domicilio' => $peticion->cadena('domicilio'),
            'direccion_oficina' => $peticion->cadena('direccion_oficina'),
            'nombre_firma' => $peticion->cadena('nombre_firma'),
        ];
        $errores = Validator::validar($datos, [
            'nombre_titular' => 'required|min:3|max:160',
            'numero_cliente' => 'max:80',
            'correo' => 'required|email|max:160',
            'telefono' => 'max:30',
            'ciudad' => 'required|min:2|max:120',
            'nombre_representante' => 'max:160',
            'dato_incorrecto' => 'required|min:3|max:800',
            'dato_correcto' => 'required|min:3|max:800',
            'documento_acreditativo' => 'required|max:300',
            'domicilio' => 'max:300',
            'direccion_oficina' => 'max:300',
            'nombre_firma' => 'required|min:3|max:160',
        ]);
        if (!in_array($datos['notificacion_medio'], ['correo', 'domicilio', 'oficina'], true)) {
            $errores['notificacion_medio'] = 'Elige un medio de notificación válido.';
        }
        if ($datos['notificacion_medio'] === 'domicilio' && mb_strlen(trim($datos['domicilio'])) < 5) {
            $errores['domicilio'] = 'Escribe el domicilio donde recibirás la notificación.';
        }
        if ($datos['tipo_representacion'] !== '' && !in_array($datos['tipo_representacion'], ['poder', 'carta', 'comparecencia'], true)) {
            $errores['tipo_representacion'] = 'Elige el documento de representación.';
        }
        if ($peticion->cadena('acepto') !== '1') {
            $errores['acepto'] = 'Confirma que los datos son verdaderos y acepta el aviso de privacidad.';
        }

        if ($errores !== []) {
            Session::put('_errores', $errores);
            Session::recordarViejo($peticion->cuerpo());
            flash('error', 'Revisa los campos marcados.');
            return redirigir('/derechos/rectificacion');
        }

        $archivos = ['archivo_identificacion' => null, 'archivo_representacion' => null, 'archivo_acreditativo' => null];
        foreach (['identificacion' => 'archivo_identificacion', 'representacion' => 'archivo_representacion', 'acreditativo' => 'archivo_acreditativo'] as $campo => $clave) {
            $archivo = $peticion->archivo($campo);
            if ($archivo === null || (int) ($archivo['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
                continue;
            }
            $subida = Documento::guardar($archivo);
            if (!($subida['ok'] ?? false)) {
                flash('error', (string) ($subida['error'] ?? 'No se pudo guardar el documento.'));
                Session::recordarViejo($datos);
                return redirigir('/derechos/rectificacion');
            }
            $archivos[$clave] = (string) $subida['ruta'];
        }

        $folio = 'ARCO-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
        $id = SolicitudArcoRepository::crear(array_merge($datos, [
            'folio' => $folio,
            'fecha_solicitud' => date('Y-m-d'),
            'archivo_identificacion' => $archivos['archivo_identificacion'],
            'archivo_representacion' => $archivos['archivo_representacion'],
            'archivo_acreditativo' => $archivos['archivo_acreditativo'],
            'ip' => $peticion->ip(),
            'user_agent' => (string) ($_SERVER['HTTP_USER_AGENT'] ?? ''),
        ]));

        $cuerpoAdmin = "Nueva solicitud de rectificación de datos personales\n\n"
            . "Folio: {$folio}\n"
            . "Titular: {$datos['nombre_titular']}\n"
            . "Correo: {$datos['correo']}\n"
            . "Teléfono: " . ($datos['telefono'] !== '' ? $datos['telefono'] : 'No indicado') . "\n"
            . "Ciudad: {$datos['ciudad']}\n"
            . "Representante: " . ($datos['nombre_representante'] !== '' ? $datos['nombre_representante'] : 'No aplica') . "\n"
            . "Dato actual: {$datos['dato_incorrecto']}\n"
            . "Dato solicitado: {$datos['dato_correcto']}\n"
            . "Documento acreditativo: {$datos['documento_acreditativo']}\n"
            . "Notificación: {$datos['notificacion_medio']}\n\n"
            . "Revisa la solicitud en /admin/derechos.";
        Mailer::enviar(
            (string) config('mail.to', config('contacto.email', '')),
            'Solicitud ARCO ' . $folio,
            $cuerpoAdmin,
            $datos['correo']
        );
        Mailer::enviar(
            $datos['correo'],
            'Recibimos tu solicitud de rectificación · ' . $folio,
            "Hola {$datos['nombre_titular']}:\n\n"
            . "Recibimos tu solicitud de rectificación de datos personales con folio {$folio}.\n\n"
            . "Daremos seguimiento por el medio de notificación que elegiste. Si necesitas aclarar algo, responde a este correo o escribe a " . config('contacto.email') . ".\n\n"
            . "Mikato Estilistas Caninos\n" . config('url'),
            (string) config('contacto.email')
        );

        Session::olvidarViejo();
        flash('exito', 'Solicitud recibida. Tu folio es ' . $folio . '. Te contactaremos por el medio que elegiste.');
        return redirigir('/derechos/rectificacion');
    }
}
