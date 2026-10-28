<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Confirmación de acciones destructivas sin JavaScript.
 *
 * Intercepta el POST original, muestra una página con el resumen y reenvía
 * los mismos campos cuando la persona confirma. El controlador de destino
 * sigue validando sesión, rol, CSRF y reglas de negocio.
 */
final class Confirmacion
{
    /** @return array{0: string, 1: string}|null [mensaje, ruta de regreso] */
    private static function regla(string $ruta): ?array
    {
        $reglas = [
            '#^/panel/clientes/eliminar$#' => ['¿Eliminar este cliente? Se conservarán sus citas y ventas si la base lo permite.', '/panel/clientes'],
            '#^/panel/mascotas/eliminar$#' => ['¿Archivar esta mascota? Su historial se conservará.', '/panel/mascotas'],
            '#^/panel/servicios/eliminar$#' => ['¿Archivar este servicio? Las ventas pasadas no se modifican.', '/panel/servicios'],
            '#^/panel/productos/eliminar$#' => ['¿Archivar este producto? Las ventas pasadas no se modifican.', '/panel/productos'],
            '#^/panel/paquetes/eliminar$#' => ['¿Archivar este paquete? Las ventas pasadas no se modifican.', '/panel/paquetes'],
            '#^/panel/gastos/eliminar$#' => ['¿Eliminar este gasto? Esta acción no se puede deshacer.', '/panel/gastos'],
            '#^/panel/ventas/\d+/anular$#' => ['¿Anular esta venta? El inventario se repondrá y la venta quedará marcada como anulada.', '/panel/ventas'],
            '#^/admin/publicaciones/eliminar$#' => ['¿Eliminar esta publicación y sus comentarios? Esta acción no se puede deshacer.', '/admin/publicaciones'],
            '#^/admin/comentarios/eliminar$#' => ['¿Eliminar este comentario? Esta acción no se puede deshacer.', '/admin/comentarios'],
            '#^/admin/negocios/eliminar$#' => ['¿Eliminar esta ficha del directorio? Esta acción no se puede deshacer.', '/admin/negocios'],
            '#^/admin/mensajes/eliminar$#' => ['¿Eliminar este mensaje de contacto? Esta acción no se puede deshacer.', '/admin/mensajes'],
            '#^/mi-cuenta/citas/cancelar$#' => ['¿Cancelar esta cita? El horario se liberará para otra persona.', '/mi-cuenta/citas'],
            '#^/mi-cuenta/mascotas/eliminar$#' => ['¿Quitar esta mascota de tu cuenta? Su historial de citas se conservará.', '/mi-cuenta/mascotas'],
        ];
        foreach ($reglas as $patron => $datos) {
            if (preg_match($patron, $ruta) === 1) {
                return $datos;
            }
        }
        return null;
    }

    public static function interceptar(Request $peticion): ?Response
    {
        if ($peticion->metodo() !== 'POST') {
            return null;
        }
        $ruta = '/' . trim($peticion->ruta(), '/');
        $regla = self::regla($ruta);
        if ($regla === null || $peticion->cadena('confirmar') === '1') {
            return null;
        }
        if (!Session::csrfValido($peticion->cadena('_token'))) {
            return null;
        }
        if (str_starts_with($ruta, '/panel/')) {
            Auth::requiereRol(['admin', 'editor', 'estilista']);
        } elseif (str_starts_with($ruta, '/admin/')) {
            Auth::requiereRol(['admin', 'editor']);
        } elseif (str_starts_with($ruta, '/mi-cuenta/')) {
            Auth::requiereLogin();
        }

        $campos = $peticion->cuerpo();
        unset($campos['_token'], $campos['confirmar']);
        $campos = array_filter($campos, static fn (mixed $v): bool => is_scalar($v));

        $datos = [
            'titulo' => 'Confirmar acción',
            'descripcion' => 'Revisa la acción antes de continuar.',
            'mensaje' => $regla[0],
            'destino' => $ruta,
            'volver' => $regla[1],
            'campos' => $campos,
        ];

        if (str_starts_with($ruta, '/panel/')) {
            return Response::html(view_panel('paginas/confirmar', $datos));
        }
        if (str_starts_with($ruta, '/admin/')) {
            return Response::html(view_admin('paginas/confirmar', $datos));
        }
        return Response::html(view('paginas/confirmar', $datos));
    }
}
