<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Domain\Contenido\Guias;
use App\Repositories\CitaRepository;
use App\Repositories\ContactoRepository;
use App\Repositories\GuiaMediaRepository;
use App\Repositories\NegocioRepository;
use App\Repositories\ProductoRepository;
use App\Repositories\PublicacionRepository;
use App\Repositories\ServicioRepository;
use App\Support\Mailer;
use App\Support\Validator;

final class HomeController
{
    public function index(Request $peticion): Response
    {
        $servicios = ServicioRepository::activos();
        $productos = ProductoRepository::destacados(6);
        if ($productos === []) {
            $productos = array_slice(ProductoRepository::activos(), 0, 6);
        }

        return Response::html(view('paginas/home', [
            'titulo' => 'CRM y productos para estéticas caninas · Mikato',
            'descripcion' => 'CRM para estéticas caninas: agenda, clientes, mascotas, inventario, ventas y reportes. Productos Mikato elaborados en Guadalajara, Jalisco.',
            'servicios' => $servicios,
            'categoriasServicio' => ServicioRepository::categorias(),
            'productos' => $productos,
            'guias' => Guias::destacadas(4),
            'publicaciones' => PublicacionRepository::publicadas('', '', 3),
            'negocios' => NegocioRepository::aprobados('', '', 3),
            'trabajos' => GuiaMediaRepository::imagenesRecientes(8),
            'stats' => [
                'guias' => Guias::total(),
                'publicaciones' => PublicacionRepository::contarPublicadas(),
                'negocios' => NegocioRepository::contarAprobados(),
                'citas' => CitaRepository::contar(),
            ],
            'usuario' => Auth::usuario(),
        ]));
    }

    public function servicios(Request $peticion): Response
    {
        return Response::html(view('paginas/servicios', [
            'titulo' => 'CRM y productos para estéticas caninas',
            'descripcion' => 'Mikato es un CRM para estéticas caninas y fabricante de productos. Conoce agenda, clientes, inventario, ventas, reportes y cómo funciona la plataforma.',
        ]));
    }

    public function productos(Request $peticion): Response
    {
        $productos = ProductoRepository::activos();
        $agrupados = [];
        foreach ($productos as $producto) {
            $agrupados[(string) $producto['categoria']][] = $producto;
        }

        return Response::html(view('paginas/productos', [
            'titulo' => 'Productos Mikato para estilistas caninos',
            'descripcion' => 'Shampoos, acondicionadores, herramientas y accesorios elaborados en Guadalajara, Jalisco para estéticas caninas.',
            'agrupados' => $agrupados,
            'categorias' => ProductoRepository::categorias(),
        ]));
    }

    public function nosotros(Request $peticion): Response
    {
        return Response::html(view('paginas/nosotros', [
            'titulo' => 'Somos Mikato, fabricantes mexicanos',
            'descripcion' => 'Elaboramos productos para estilistas caninos en Guadalajara, Jalisco y sostenemos una comunidad de oficio con guías, directorio y agenda.',
        ]));
    }

    public function contacto(Request $peticion): Response
    {
        return Response::html(view('paginas/contacto', [
            'titulo' => 'Contacto',
            'descripcion' => 'Escríbenos para una demo del CRM para estéticas caninas, mayoreo de productos Mikato o registro en el directorio.',
            'whatsapp' => whatsapp_link(),
        ]));
    }

    public function enviarContacto(Request $peticion): Response
    {
        Csrf::verificar($peticion);
        if ($peticion->cadena('sitio_web') !== '') {
            return redirigir('/contacto');
        }

        [$permitido, $espera] = Session::limitar('contacto', (int) config('limites.contacto_por_hora', 5), 3600);
        if (!$permitido) {
            flash('error', 'Enviaste varios mensajes seguidos. Espera ' . ceil($espera / 60) . ' minutos o escríbenos por WhatsApp.');
            Session::recordarViejo($peticion->cuerpo());
            return redirigir('/contacto');
        }

        $datos = [
            'nombre' => $peticion->cadena('nombre'),
            'correo' => $peticion->cadena('correo'),
            'telefono' => $peticion->cadena('telefono'),
            'asunto' => $peticion->cadena('asunto', 'general'),
            'mensaje' => $peticion->cadena('mensaje'),
        ];
        $errores = Validator::validar($datos, [
            'nombre' => 'required|min:3|max:120',
            'correo' => 'required|email|max:160',
            'telefono' => 'max:30',
            'mensaje' => 'required|min:15|max:3000',
        ]);

        if ($errores !== []) {
            Session::put('_errores', $errores);
            Session::recordarViejo($datos);
            flash('error', 'Revisa los campos marcados.');
            return redirigir('/contacto');
        }

        ContactoRepository::crear($datos);

        $para = (string) config('mail.to', config('contacto.email', ''));
        if ($para !== '') {
            $cuerpo = "Nuevo mensaje desde el sitio de Mikato\n\n"
                . "Nombre: " . $datos['nombre'] . "\n"
                . "Correo: " . $datos['correo'] . "\n"
                . "Teléfono: " . ($datos['telefono'] !== '' ? $datos['telefono'] : 'No indicado') . "\n"
                . "Asunto: " . $datos['asunto'] . "\n\n"
                . "Mensaje:\n" . $datos['mensaje'] . "\n";
            Mailer::enviar($para, 'Nuevo mensaje de contacto · Mikato', $cuerpo, $datos['correo']);
        }

        Session::olvidarViejo();
        flash('exito', 'Recibimos tu mensaje. Te respondemos en horario de oficina.');
        return redirigir('/contacto');
    }

    public function boletin(Request $peticion): Response
    {
        Csrf::verificar($peticion);
        $correo = $peticion->cadena('correo');
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            flash('error', 'Escribe un correo válido.');
            return redirigir('/');
        }
        ContactoRepository::suscribir($correo);
        flash('exito', 'Listo. Te avisaremos de nuevas guías y productos.');
        return redirigir('/');
    }

    public function cuidados(Request $peticion): Response
    {
        return Response::html(view('paginas/cuidados', [
            'titulo' => 'Antes y después de tu cita',
            'descripcion' => 'Cómo preparar a tu perro para la estética, qué llevar, qué esperar y cómo cuidar el resultado en casa.',
        ]));
    }

    public function buenasPracticas(Request $peticion): Response
    {
        return Response::html(view('paginas/buenas-practicas', [
            'titulo' => 'Buenas prácticas y marco legal',
            'descripcion' => 'Higiene, bienestar animal, datos personales y obligaciones de una estética canina en México, con fuentes oficiales.',
        ]));
    }

    public function glosario(Request $peticion): Response
    {
        return Response::html(view('paginas/glosario', [
            'titulo' => 'Glosario del estilista canino',
            'descripcion' => 'Manto, subpelo, stripping, deslanado, vaso, stripping, carda y más términos del oficio explicados.',
            'terminos' => \App\Domain\Contenido\Glosario::todos(),
            'categorias' => \App\Domain\Contenido\Glosario::categorias(),
        ]));
    }
}
