<?php

declare(strict_types=1);

use App\Controllers\AdminController;
use App\Controllers\AgendaController;
use App\Controllers\AuthController;
use App\Controllers\CalculadoraController;
use App\Controllers\ComunidadController;
use App\Controllers\DirectorioController;
use App\Controllers\GuiaController;
use App\Controllers\HomeController;
use App\Controllers\MiCuentaController;
use App\Controllers\PaginasController;
use App\Controllers\PanelController;
use App\Controllers\SearchController;
use App\Core\Request;
use App\Core\Response;
use App\Core\Router;

return static function (Router $router): void {
    $router->get('/api/salud', static function (Request $peticion): Response {
        $base = 'sin conexión';
        try {
            $base = \App\Core\Database::ping() ? 'ok' : 'sin conexión';
        } catch (\Throwable) {
        }
        return Response::json([
            'ok' => true,
            'app' => (string) config('name'),
            'version' => (string) config('version'),
            'base' => $base,
            'driver' => \App\Core\Database::driverName(),
            'hora' => date('c'),
        ]);
    });
    $router->get('/', [HomeController::class, 'index']);
    $router->get('/servicios', [HomeController::class, 'servicios']);
    $router->get('/productos', [HomeController::class, 'productos']);
    $router->get('/nosotros', [HomeController::class, 'nosotros']);
    $router->get('/contacto', [HomeController::class, 'contacto']);
    $router->post('/contacto', [HomeController::class, 'enviarContacto']);
    $router->post('/boletin', [HomeController::class, 'boletin']);
    $router->get('/cuidados', [HomeController::class, 'cuidados']);
    $router->get('/buenas-practicas', [HomeController::class, 'buenasPracticas']);
    $router->get('/glosario', [HomeController::class, 'glosario']);

    $router->get('/guias', [GuiaController::class, 'index']);
    $router->get('/guias/{slug}', [GuiaController::class, 'show']);

    $router->get('/comunidad', [ComunidadController::class, 'index']);
    $router->get('/comunidad/publicar', [ComunidadController::class, 'crear']);
    $router->post('/comunidad', [ComunidadController::class, 'guardar']);
    $router->get('/comunidad/{slug}', [ComunidadController::class, 'show']);
    $router->post('/comunidad/{slug}/comentar', [ComunidadController::class, 'comentar']);

    $router->get('/directorio', [DirectorioController::class, 'index']);
    $router->get('/directorio/registrar', [DirectorioController::class, 'crear']);
    $router->post('/directorio', [DirectorioController::class, 'guardar']);
    $router->get('/directorio/{slug}', [DirectorioController::class, 'show']);

    $router->get('/calculadoras', [CalculadoraController::class, 'index']);
    $router->get('/calculadoras/dilucion', [CalculadoraController::class, 'dilucion']);
    $router->get('/calculadoras/precio', [CalculadoraController::class, 'precio']);
    $router->get('/calculadoras/secado', [CalculadoraController::class, 'secado']);
    $router->get('/calculadoras/rentabilidad', [CalculadoraController::class, 'rentabilidad']);
    $router->get('/api/calculadora/{tipo}', [CalculadoraController::class, 'api']);

    $router->get('/agenda', [AgendaController::class, 'form']);
    $router->post('/agenda', [AgendaController::class, 'guardar']);
    $router->get('/agenda/disponibilidad', [AgendaController::class, 'disponibilidad']);
    $router->get('/agenda/consultar', [AgendaController::class, 'consultar']);
    $router->get('/agenda/gracias/{codigo}', [AgendaController::class, 'gracias']);

    $router->get('/entrar', [AuthController::class, 'form']);
    $router->post('/entrar', [AuthController::class, 'entrar']);
    $router->get('/registro', [AuthController::class, 'registroForm']);
    $router->post('/registro', [AuthController::class, 'registrar']);
    $router->post('/salir', [AuthController::class, 'salir']);
    $router->get('/perfil', [AuthController::class, 'perfil']);
    $router->post('/perfil', [AuthController::class, 'actualizarPerfil']);
    $router->post('/perfil/password', [AuthController::class, 'cambiarPassword']);

    $router->get('/mi-cuenta', [MiCuentaController::class, 'index']);
    $router->get('/mi-cuenta/mascotas', [MiCuentaController::class, 'mascotas']);
    $router->post('/mi-cuenta/mascotas', [MiCuentaController::class, 'guardarMascota']);
    $router->post('/mi-cuenta/mascotas/eliminar', [MiCuentaController::class, 'eliminarMascota']);
    $router->get('/mi-cuenta/citas', [MiCuentaController::class, 'citas']);
    $router->post('/mi-cuenta/citas/cancelar', [MiCuentaController::class, 'cancelarCita']);
    $router->get('/mi-cuenta/historial/{id}', [MiCuentaController::class, 'historial']);

    $router->get('/buscar', [SearchController::class, 'index']);
    $router->get('/api/buscar', [SearchController::class, 'api']);

    $router->get('/herramientas', [PaginasController::class, 'herramientas']);
    $router->get('/fuentes', [PaginasController::class, 'fuentes']);
    $router->get('/privacidad', [PaginasController::class, 'privacidad']);
    $router->get('/terminos', [PaginasController::class, 'terminos']);
    $router->get('/asistente', [PaginasController::class, 'asistente']);
    $router->get('/sitemap.xml', [PaginasController::class, 'sitemap']);

    // Panel de gestión
    $router->get('/panel', [PanelController::class, 'dashboard']);
    $router->get('/panel/agenda', [PanelController::class, 'agenda']);
    $router->post('/panel/agenda', [PanelController::class, 'citaGuardar']);
    $router->post('/panel/cita/estado', [PanelController::class, 'citaEstado']);
    $router->post('/panel/cita/reagendar', [PanelController::class, 'citaReagendar']);
    $router->get('/panel/clientes', [PanelController::class, 'clientes']);
    $router->post('/panel/clientes/guardar', [PanelController::class, 'clienteGuardar']);
    $router->post('/panel/clientes/eliminar', [PanelController::class, 'clienteEliminar']);
    $router->get('/panel/clientes/{id}', [PanelController::class, 'clienteDetalle']);
    $router->get('/panel/mascotas', [PanelController::class, 'mascotas']);
    $router->post('/panel/mascotas/guardar', [PanelController::class, 'mascotaGuardar']);
    $router->post('/panel/mascotas/eliminar', [PanelController::class, 'mascotaEliminar']);
    $router->post('/panel/mascotas/historial', [PanelController::class, 'historialGuardar']);
    $router->get('/panel/mascotas/{id}', [PanelController::class, 'mascotaDetalle']);

    foreach (['servicios', 'productos', 'paquetes'] as $tipo) {
        $router->get('/panel/' . $tipo, static fn (Request $r): Response => (new PanelController())->catalogo($r, ['tipo' => $tipo]));
        $router->post('/panel/' . $tipo . '/guardar', static fn (Request $r): Response => (new PanelController())->catalogoGuardar($r, ['tipo' => $tipo]));
        $router->post('/panel/' . $tipo . '/eliminar', static fn (Request $r): Response => (new PanelController())->catalogoEliminar($r, ['tipo' => $tipo]));
    }

    $router->get('/panel/ventas', [PanelController::class, 'ventas']);
    $router->get('/panel/ventas/nueva', [PanelController::class, 'ventaNueva']);
    $router->post('/panel/ventas/guardar', [PanelController::class, 'ventaGuardar']);
    $router->get('/panel/ventas/{id}', [PanelController::class, 'ventaDetalle']);
    $router->post('/panel/ventas/{id}/anular', [PanelController::class, 'ventaAnular']);
    $router->get('/panel/gastos', [PanelController::class, 'gastos']);
    $router->post('/panel/gastos/guardar', [PanelController::class, 'gastoGuardar']);
    $router->post('/panel/gastos/eliminar', [PanelController::class, 'gastoEliminar']);
    $router->get('/panel/reportes', [PanelController::class, 'reportes']);
    $router->get('/panel/usuarios', [PanelController::class, 'usuarios']);
    $router->post('/panel/usuarios/guardar', [PanelController::class, 'usuarioGuardar']);
    $router->post('/panel/usuarios/estado', [PanelController::class, 'usuarioEstado']);
    $router->post('/panel/usuarios/rol', [PanelController::class, 'usuarioRol']);
    $router->get('/panel/configuracion', [PanelController::class, 'configuracion']);
    $router->post('/panel/configuracion', [PanelController::class, 'configuracionGuardar']);

    // Administración
    $router->get('/admin', [AdminController::class, 'dashboard']);
    $router->get('/admin/publicaciones', [AdminController::class, 'publicaciones']);
    $router->post('/admin/publicaciones/estado', [AdminController::class, 'estadoPublicacion']);
    $router->post('/admin/publicaciones/fijar', [AdminController::class, 'fijarPublicacion']);
    $router->post('/admin/publicaciones/eliminar', [AdminController::class, 'eliminarPublicacion']);
    $router->get('/admin/comentarios', [AdminController::class, 'comentarios']);
    $router->post('/admin/comentarios/estado', [AdminController::class, 'estadoComentario']);
    $router->post('/admin/comentarios/eliminar', [AdminController::class, 'eliminarComentario']);
    $router->get('/admin/negocios', [AdminController::class, 'negocios']);
    $router->post('/admin/negocios/estado', [AdminController::class, 'estadoNegocio']);
    $router->post('/admin/negocios/verificar', [AdminController::class, 'verificarNegocio']);
    $router->post('/admin/negocios/destacar', [AdminController::class, 'destacarNegocio']);
    $router->post('/admin/negocios/eliminar', [AdminController::class, 'eliminarNegocio']);
    $router->get('/admin/mensajes', [AdminController::class, 'mensajes']);
    $router->post('/admin/mensajes/atender', [AdminController::class, 'atenderMensaje']);
    $router->post('/admin/mensajes/eliminar', [AdminController::class, 'eliminarMensaje']);
    $router->get('/admin/citas', [AdminController::class, 'citas']);
};
