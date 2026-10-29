<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Database;
use App\Core\HttpException;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Repositories\CitaRepository;
use App\Repositories\ClienteRepository;
use App\Repositories\ConfiguracionRepository;
use App\Repositories\GastoRepository;
use App\Repositories\MascotaRepository;
use App\Repositories\PaqueteRepository;
use App\Repositories\ProductoRepository;
use App\Repositories\ServicioRepository;
use App\Repositories\UsuarioRepository;
use App\Repositories\VentaRepository;
use App\Support\AgendaShare;
use App\Support\Slug;
use App\Support\Upload;
use App\Support\Validator;

final class PanelController
{
    private const CATALOGO = [
        'servicios' => [
            'titulo' => 'Servicios',
            'singular' => 'servicio',
            'descripcion' => 'Baños, cortes, uñas, oídos y spa con precio y duración.',
            'icono' => 'tijeras',
            'campos' => [
                ['nombre' => 'nombre', 'etiqueta' => 'Nombre', 'tipo' => 'text', 'requerido' => true, 'ancho' => '12'],
                ['nombre' => 'categoria', 'etiqueta' => 'Categoría', 'tipo' => 'select', 'opciones' => [], 'ancho' => '6'],
                ['nombre' => 'tamano', 'etiqueta' => 'Tamaño', 'tipo' => 'select', 'opciones' => ['todos' => 'Todos', 'chico' => 'Chico', 'mediano' => 'Mediano', 'grande' => 'Grande', 'gigante' => 'Gigante'], 'ancho' => '6'],
                ['nombre' => 'precio', 'etiqueta' => 'Precio (MXN)', 'tipo' => 'number', 'requerido' => true, 'ancho' => '6', 'paso' => '0.01'],
                ['nombre' => 'duracion_min', 'etiqueta' => 'Duración (min)', 'tipo' => 'number', 'ancho' => '6', 'paso' => '5'],
                ['nombre' => 'orden', 'etiqueta' => 'Orden', 'tipo' => 'number', 'ancho' => '6'],
                ['nombre' => 'activo', 'etiqueta' => 'Activo', 'tipo' => 'checkbox', 'ancho' => '6'],
                ['nombre' => 'descripcion', 'etiqueta' => 'Descripción', 'tipo' => 'textarea', 'ancho' => '12'],
            ],
            'columnas' => ['nombre' => 'Servicio', 'categoria' => 'Categoría', 'tamano' => 'Tamaño', 'precio' => 'Precio', 'duracion_min' => 'Min', 'activo' => 'Activo'],
        ],
        'productos' => [
            'titulo' => 'Productos',
            'singular' => 'producto',
            'descripcion' => 'Catálogo Mikato, inventario y precios de mayoreo.',
            'icono' => 'producto',
            'campos' => [
                ['nombre' => 'nombre', 'etiqueta' => 'Nombre', 'tipo' => 'text', 'requerido' => true, 'ancho' => '12'],
                ['nombre' => 'categoria', 'etiqueta' => 'Categoría', 'tipo' => 'select', 'opciones' => [], 'ancho' => '6'],
                ['nombre' => 'presentacion', 'etiqueta' => 'Presentación', 'tipo' => 'text', 'ancho' => '6'],
                ['nombre' => 'precio', 'etiqueta' => 'Precio (MXN)', 'tipo' => 'number', 'requerido' => true, 'ancho' => '6', 'paso' => '0.01'],
                ['nombre' => 'precio_mayoreo', 'etiqueta' => 'Mayoreo (MXN)', 'tipo' => 'number', 'ancho' => '6', 'paso' => '0.01'],
                ['nombre' => 'costo', 'etiqueta' => 'Costo (MXN)', 'tipo' => 'number', 'ancho' => '6', 'paso' => '0.01'],
                ['nombre' => 'stock', 'etiqueta' => 'Stock', 'tipo' => 'number', 'ancho' => '6'],
                ['nombre' => 'sku', 'etiqueta' => 'SKU', 'tipo' => 'text', 'ancho' => '12'],
                ['nombre' => 'activo', 'etiqueta' => 'Activo', 'tipo' => 'checkbox', 'ancho' => '6'],
                ['nombre' => 'destacado', 'etiqueta' => 'Destacado', 'tipo' => 'checkbox', 'ancho' => '6'],
                ['nombre' => 'descripcion', 'etiqueta' => 'Descripción', 'tipo' => 'textarea', 'ancho' => '12'],
            ],
            'columnas' => ['nombre' => 'Producto', 'presentacion' => 'Presentación', 'precio' => 'Precio', 'stock' => 'Stock', 'activo' => 'Activo'],
        ],
        'paquetes' => [
            'titulo' => 'Paquetes',
            'singular' => 'paquete',
            'descripcion' => 'Combos de servicios con precio cerrado.',
            'icono' => 'caja',
            'campos' => [
                ['nombre' => 'nombre', 'etiqueta' => 'Nombre', 'tipo' => 'text', 'requerido' => true, 'ancho' => '12'],
                ['nombre' => 'precio', 'etiqueta' => 'Precio (MXN)', 'tipo' => 'number', 'requerido' => true, 'ancho' => '6', 'paso' => '0.01'],
                ['nombre' => 'tamano', 'etiqueta' => 'Tamaño', 'tipo' => 'select', 'opciones' => ['todos' => 'Todos', 'chico' => 'Chico', 'mediano' => 'Mediano', 'grande' => 'Grande', 'gigante' => 'Gigante'], 'ancho' => '6'],
                ['nombre' => 'activo', 'etiqueta' => 'Activo', 'tipo' => 'checkbox', 'ancho' => '6'],
                ['nombre' => 'descripcion', 'etiqueta' => 'Descripción', 'tipo' => 'textarea', 'ancho' => '12'],
            ],
            'columnas' => ['nombre' => 'Paquete', 'tamano' => 'Tamaño', 'precio' => 'Precio', 'activo' => 'Activo'],
        ],
    ];

    /** @return array<string, mixed> */
    private function guard(): array
    {
        Auth::requiereRol(['admin', 'editor', 'estilista']);
        return Auth::usuario() ?? [];
    }

    public function dashboard(Request $peticion): Response
    {
        $usuario = $this->guard();
        $hoy = date('Y-m-d');
        $inicioMes = date('Y-m-01');
        $finMes = date('Y-m-t');
        $resumenHoy = CitaRepository::resumenHoy();

        return Response::html(view_panel('panel/dashboard', [
            'titulo' => 'Panel',
            'descripcion' => 'Resumen de la estética: agenda, ventas, inventario y clientes.',
            'usuario' => $usuario,
            'hoy' => $hoy,
            'citasHoy' => CitaRepository::porFecha($hoy),
            'resumenHoy' => $resumenHoy,
            'proximas' => CitaRepository::proximas(6),
            'ventasMes' => VentaRepository::resumen($inicioMes, $finMes),
            'gastosMes' => GastoRepository::total($inicioMes, $finMes),
            'bajoStock' => ProductoRepository::bajoStock(5),
            'clientes' => ClienteRepository::contar(),
            'mascotas' => MascotaRepository::contar(),
            'serie' => VentaRepository::serieDias(date('Y-m-d', strtotime('-6 days')), $hoy),
            'cumple' => MascotaRepository::cumpleanerosMes((int) date('n')),
            'ultimasVentas' => VentaRepository::ultimas(5),
        ]));
    }

    public function agenda(Request $peticion): Response
    {
        $usuario = $this->guard();
        $fecha = $peticion->cadena('fecha', date('Y-m-d'));
        if (strtotime($fecha) === false) {
            $fecha = date('Y-m-d');
        }

        $compartirUrl = AgendaShare::url($fecha);
        $textoWhatsapp = 'Agenda de ' . fecha_larga($fecha) . ': ' . $compartirUrl;
        $whatsappAgenda = 'https://wa.me/?text=' . rawurlencode($textoWhatsapp);

        return Response::html(view_panel('panel/agenda', [
            'titulo' => 'Agenda',
            'descripcion' => 'Citas del día, cambios de estado y nuevas reservas.',
            'usuario' => $usuario,
            'fecha' => $fecha,
            'citas' => CitaRepository::porFecha($fecha),
            'resumen' => CitaRepository::resumenHoy(),
            'servicios' => ServicioRepository::activos(),
            'clientes' => ClienteRepository::opciones(),
            'mascotas' => MascotaRepository::listar('', 300),
            'horas' => $this->horas(),
            'compartirUrl' => $compartirUrl,
            'whatsappAgenda' => $whatsappAgenda,
        ]));
    }

    public function agendaExportar(Request $peticion): Response
    {
        $this->guard();
        $fecha = $peticion->cadena('fecha', date('Y-m-d'));
        if (strtotime($fecha) === false) {
            $fecha = date('Y-m-d');
        }
        $citas = CitaRepository::porFecha($fecha);
        return Response::texto(AgendaShare::csv($fecha, $citas), 200, 'text/csv; charset=UTF-8')
            ->conCabecera('Content-Disposition', 'attachment; filename="agenda-' . $fecha . '.csv"');
    }

    /** @return list<string> */
    private function horas(): array
    {
        $horas = [];
        for ($h = 9; $h <= 18; $h++) {
            $horas[] = sprintf('%02d:00', $h);
            if ($h !== 18) {
                $horas[] = sprintf('%02d:30', $h);
            }
        }
        return $horas;
    }

    public function citaGuardar(Request $peticion): Response
    {
        $usuario = $this->guard();
        Csrf::verificar($peticion);
        $datos = [
            'cliente_id' => $peticion->entero('cliente_id'),
            'mascota_id' => $peticion->entero('mascota_id'),
            'servicio_id' => $peticion->entero('servicio_id'),
            'fecha' => $peticion->cadena('fecha'),
            'hora' => $peticion->cadena('hora'),
            'notas' => $peticion->cadena('notas'),
        ];
        $cliente = ClienteRepository::porId($datos['cliente_id']);
        $mascota = MascotaRepository::porId($datos['mascota_id']);
        $servicio = ServicioRepository::porId($datos['servicio_id']);

        if ($cliente === null || $mascota === null || $servicio === null) {
            flash('error', 'Elige cliente, mascota y servicio.');
            return redirigir('/panel/agenda');
        }
        if ((int) $mascota['cliente_id'] !== (int) $cliente['id']) {
            flash('error', 'La mascota no pertenece a ese cliente.');
            return redirigir('/panel/agenda');
        }
        if (strtotime($datos['fecha']) === false || !in_array($datos['hora'], $this->horas(), true)) {
            flash('error', 'Revisa la fecha y la hora.');
            return redirigir('/panel/agenda');
        }
        if (CitaRepository::slotOcupado($datos['fecha'], $datos['hora'])) {
            flash('error', 'Ese horario ya está ocupado.');
            return redirigir('/panel/agenda?fecha=' . urlencode($datos['fecha']));
        }

        CitaRepository::crear([
            'cliente_id' => $datos['cliente_id'],
            'mascota_id' => $datos['mascota_id'],
            'servicio_id' => $datos['servicio_id'],
            'fecha' => $datos['fecha'],
            'hora' => $datos['hora'],
            'precio' => (float) $servicio['precio'],
            'notas' => $datos['notas'] !== '' ? $datos['notas'] : null,
            'origen' => 'mostrador',
            'estado' => 'confirmada',
        ]);
        flash('exito', 'Cita registrada para ' . $mascota['nombre'] . '.');
        return redirigir('/panel/agenda?fecha=' . urlencode($datos['fecha']));
    }

    public function citaEstado(Request $peticion): Response
    {
        $this->guard();
        Csrf::verificar($peticion);
        $cita = CitaRepository::porId($peticion->entero('id'));
        $estado = $peticion->cadena('estado');
        if ($cita === null || !in_array($estado, ['pendiente', 'confirmada', 'en_proceso', 'completada', 'cancelada', 'no_asistio'], true)) {
            throw new HttpException('Cita o estado inválido.', 400);
        }
        CitaRepository::cambiarEstado((int) $cita['id'], $estado);

        if ($estado === 'completada') {
            $existe = (int) Database::value('select count(*) from historial_estetica where cita_id = ?', [(int) $cita['id']]);
            if ($existe === 0) {
                MascotaRepository::agregarHistorial((int) $cita['mascota_id'], [
                    'cita_id' => (int) $cita['id'],
                    'fecha' => (string) $cita['fecha'],
                    'servicio' => (string) $cita['servicio_nombre'],
                    'observaciones' => 'Servicio completado en el salón.',
                ]);
            }
        }

        flash('exito', 'Cita de ' . $cita['mascota_nombre'] . ' marcada como ' . mb_strtolower(estado_cita_label($estado)) . '.');
        return redirigir('/panel/agenda?fecha=' . urlencode((string) $cita['fecha']));
    }

    public function citaReagendar(Request $peticion): Response
    {
        $this->guard();
        Csrf::verificar($peticion);
        $cita = CitaRepository::porId($peticion->entero('id'));
        $fecha = $peticion->cadena('fecha');
        $hora = $peticion->cadena('hora');
        if ($cita === null || strtotime($fecha) === false || !in_array($hora, $this->horas(), true)) {
            flash('error', 'Revisa la nueva fecha y hora.');
            return redirigir('/panel/agenda');
        }
        if (CitaRepository::slotOcupadoExcepto($fecha, $hora, (int) $cita['id'])) {
            flash('error', 'Ese horario ya está ocupado.');
            return redirigir('/panel/agenda?fecha=' . urlencode($fecha));
        }
        CitaRepository::reagendar((int) $cita['id'], $fecha, $hora);
        flash('exito', 'Cita reagendada.');
        return redirigir('/panel/agenda?fecha=' . urlencode($fecha));
    }

    public function clientes(Request $peticion): Response
    {
        $usuario = $this->guard();
        $editar = $peticion->entero('editar') > 0 ? ClienteRepository::porId($peticion->entero('editar')) : null;

        return Response::html(view_panel('panel/clientes', [
            'titulo' => 'Clientes',
            'descripcion' => 'Directorio de tutores, contacto y mascotas.',
            'usuario' => $usuario,
            'clientes' => ClienteRepository::listar($peticion->cadena('q')),
            'busqueda' => $peticion->cadena('q'),
            'editar' => $editar,
        ]));
    }

    public function clienteGuardar(Request $peticion): Response
    {
        $this->guard();
        Csrf::verificar($peticion);
        $datos = [
            'nombre' => $peticion->cadena('nombre'),
            'telefono' => $peticion->cadena('telefono'),
            'correo' => $peticion->cadena('correo'),
            'direccion' => $peticion->cadena('direccion'),
            'notas' => $peticion->cadena('notas'),
        ];
        $errores = Validator::validar($datos, [
            'nombre' => 'required|min:3|max:120',
            'correo' => 'email|max:160',
            'telefono' => 'max:30',
            'notas' => 'max:1000',
        ]);
        if ($errores !== []) {
            Session::put('_errores', $errores);
            Session::recordarViejo($datos);
            flash('error', 'Revisa los datos del cliente.');
            return redirigir('/panel/clientes');
        }
        $id = $peticion->entero('id');
        if ($id > 0) {
            ClienteRepository::actualizar($id, $datos);
            flash('exito', 'Cliente actualizado.');
        } else {
            ClienteRepository::crear($datos);
            flash('exito', 'Cliente registrado.');
        }
        Session::olvidarViejo();
        return redirigir('/panel/clientes');
    }

    public function clienteEliminar(Request $peticion): Response
    {
        $usuario = $this->guard();
        if (($usuario['rol'] ?? '') === 'estilista') {
            throw new HttpException('Solo administración puede eliminar clientes.', 403);
        }
        Csrf::verificar($peticion);
        $cliente = ClienteRepository::porId($peticion->entero('id'));
        if ($cliente === null) {
            throw new HttpException('Cliente no encontrado.', 404);
        }
        try {
            ClienteRepository::eliminar((int) $cliente['id']);
            flash('exito', 'Cliente eliminado.');
        } catch (\Throwable $e) {
            flash('error', 'No se puede eliminar porque tiene citas o ventas asociadas. Archiva sus datos o cancela las citas primero.');
        }
        return redirigir('/panel/clientes');
    }

    public function clienteDetalle(Request $peticion, array $parametros): Response
    {
        $usuario = $this->guard();
        $cliente = ClienteRepository::porId((int) ($parametros['id'] ?? 0));
        if ($cliente === null) {
            throw new HttpException('Cliente no encontrado.', 404);
        }
        $mascotas = MascotaRepository::porCliente((int) $cliente['id']);
        $historial = [];
        foreach ($mascotas as $mascota) {
            $historial[(int) $mascota['id']] = MascotaRepository::historial((int) $mascota['id'], 5);
        }

        return Response::html(view_panel('panel/cliente', [
            'titulo' => $cliente['nombre'],
            'descripcion' => 'Ficha del cliente, mascotas, citas e historial.',
            'usuario' => $usuario,
            'cliente' => $cliente,
            'mascotas' => $mascotas,
            'citas' => CitaRepository::porCliente((int) $cliente['id'], 30),
            'ventas' => VentaRepository::porCliente((int) $cliente['id'], 10),
            'historial' => $historial,
        ]));
    }

    public function mascotas(Request $peticion): Response
    {
        $usuario = $this->guard();
        $editar = $peticion->entero('editar') > 0 ? MascotaRepository::porId($peticion->entero('editar')) : null;

        return Response::html(view_panel('panel/mascotas', [
            'titulo' => 'Mascotas',
            'descripcion' => 'Fichas de mascotas, tamaño, manto y alertas de salud.',
            'usuario' => $usuario,
            'mascotas' => MascotaRepository::listar($peticion->cadena('q')),
            'busqueda' => $peticion->cadena('q'),
            'clientes' => ClienteRepository::opciones(),
            'editar' => $editar,
        ]));
    }

    public function mascotaGuardar(Request $peticion): Response
    {
        $this->guard();
        Csrf::verificar($peticion);
        $datos = [
            'cliente_id' => $peticion->entero('cliente_id'),
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
            'cliente_id' => 'required|numeric',
            'nombre' => 'required|min:2|max:80',
            'peso' => 'numeric',
            'notas' => 'max:1000',
        ]);
        if (ClienteRepository::porId((int) $datos['cliente_id']) === null) {
            $errores['cliente_id'] = 'Elige un cliente válido.';
        }
        if (!in_array($datos['tamano'], ['chico', 'mediano', 'grande', 'gigante'], true)) {
            $errores['tamano'] = 'Elige un tamaño válido.';
        }
        if ($errores !== []) {
            Session::put('_errores', $errores);
            Session::recordarViejo($datos);
            flash('error', 'Revisa la ficha de la mascota.');
            return redirigir('/panel/mascotas');
        }

        $foto = null;
        $archivo = $peticion->archivo('foto');
        if ($archivo !== null && (int) ($archivo['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            $subida = Upload::imagen($archivo, 'mascotas');
            if ($subida['ok']) {
                $foto = (string) $subida['ruta'];
            }
        }
        $datos['foto'] = $foto;

        $id = $peticion->entero('id');
        if ($id > 0) {
            MascotaRepository::actualizar($id, $datos);
            flash('exito', 'Mascota actualizada.');
        } else {
            $id = MascotaRepository::crear($datos);
            flash('exito', 'Mascota registrada.');
        }
        Session::olvidarViejo();
        return redirigir('/panel/mascotas/' . $id);
    }

    public function mascotaEliminar(Request $peticion): Response
    {
        $this->guard();
        Csrf::verificar($peticion);
        $mascota = MascotaRepository::porId($peticion->entero('id'));
        if ($mascota === null) {
            throw new HttpException('Mascota no encontrada.', 404);
        }
        MascotaRepository::eliminar((int) $mascota['id']);
        flash('exito', 'Mascota archivada.');
        return redirigir('/panel/mascotas');
    }

    public function mascotaDetalle(Request $peticion, array $parametros): Response
    {
        $usuario = $this->guard();
        $mascota = MascotaRepository::porId((int) ($parametros['id'] ?? 0));
        if ($mascota === null) {
            throw new HttpException('Mascota no encontrada.', 404);
        }

        return Response::html(view_panel('panel/mascota', [
            'titulo' => $mascota['nombre'],
            'descripcion' => 'Ficha, historial y próximas citas.',
            'usuario' => $usuario,
            'mascota' => $mascota,
            'historial' => MascotaRepository::historial((int) $mascota['id']),
            'citas' => CitaRepository::porCliente((int) $mascota['cliente_id'], 20),
        ]));
    }

    public function historialGuardar(Request $peticion): Response
    {
        $this->guard();
        Csrf::verificar($peticion);
        $mascota = MascotaRepository::porId($peticion->entero('mascota_id'));
        if ($mascota === null) {
            throw new HttpException('Mascota no encontrada.', 404);
        }
        $datos = [
            'servicio' => $peticion->cadena('servicio'),
            'observaciones' => $peticion->cadena('observaciones'),
            'proxima_visita' => $peticion->cadena('proxima_visita'),
            'fecha' => $peticion->cadena('fecha', date('Y-m-d')),
        ];
        if ($datos['servicio'] === '') {
            flash('error', 'Escribe el servicio realizado.');
            return redirigir('/panel/mascotas/' . $mascota['id']);
        }
        MascotaRepository::agregarHistorial((int) $mascota['id'], $datos);
        flash('exito', 'Historial actualizado.');
        return redirigir('/panel/mascotas/' . $mascota['id']);
    }

    public function catalogo(Request $peticion, array $parametros): Response
    {
        $usuario = $this->guard();
        $tipo = (string) ($parametros['tipo'] ?? 'servicios');
        if (!isset(self::CATALOGO[$tipo])) {
            throw new HttpException('Catálogo no encontrado.', 404);
        }
        $config = self::CATALOGO[$tipo];
        if ($tipo === 'servicios') {
            $config['campos'][1]['opciones'] = ServicioRepository::categorias();
        }
        if ($tipo === 'productos') {
            $config['campos'][1]['opciones'] = ProductoRepository::categorias();
        }
        $items = match ($tipo) {
            'servicios' => ServicioRepository::todos(),
            'productos' => ProductoRepository::todos(),
            'paquetes' => PaqueteRepository::todos(),
            default => [],
        };
        $editar = null;
        if ($peticion->entero('editar') > 0) {
            $editar = $tipo === 'servicios' ? ServicioRepository::porId($peticion->entero('editar'))
                : ($tipo === 'productos' ? ProductoRepository::porId($peticion->entero('editar')) : PaqueteRepository::porId($peticion->entero('editar')));
        }

        return Response::html(view_panel('panel/catalogo', [
            'titulo' => $config['titulo'],
            'descripcion' => $config['descripcion'],
            'usuario' => $usuario,
            'tipo' => $tipo,
            'config' => $config,
            'items' => $items,
            'editar' => $editar,
        ]));
    }

    public function catalogoGuardar(Request $peticion, array $parametros): Response
    {
        $this->guard();
        Csrf::verificar($peticion);
        $tipo = (string) ($parametros['tipo'] ?? '');
        if (!isset(self::CATALOGO[$tipo])) {
            throw new HttpException('Catálogo no encontrado.', 404);
        }
        $config = self::CATALOGO[$tipo];
        $datos = [];
        foreach ($config['campos'] as $campo) {
            $nombre = $campo['nombre'];
            $datos[$nombre] = $campo['tipo'] === 'checkbox'
                ? $peticion->cadena($nombre) === '1'
                : $peticion->cadena($nombre);
        }
        $errores = Validator::validar($datos, ['nombre' => 'required|min:2|max:160']);
        if ((float) ($datos['precio'] ?? 0) < 0) {
            $errores['precio'] = 'El precio no puede ser negativo.';
        }
        if ($errores !== []) {
            Session::put('_errores', $errores);
            Session::recordarViejo($datos);
            flash('error', 'Revisa los datos.');
            return redirigir('/panel/' . $tipo);
        }

        if ($tipo === 'productos') {
            $archivo = $peticion->archivo('imagen_archivo');
            if ($archivo !== null && (int) ($archivo['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
                $subida = Upload::imagen($archivo, 'productos');
                if (!($subida['ok'] ?? false)) {
                    flash('error', (string) ($subida['error'] ?? 'No se pudo subir la imagen del producto.'));
                    return redirigir('/panel/productos');
                }
                $datos['imagen'] = (string) $subida['ruta'];
            }
        }

        $id = $peticion->entero('id');
        $repo = match ($tipo) {
            'servicios' => ServicioRepository::class,
            'productos' => ProductoRepository::class,
            'paquetes' => PaqueteRepository::class,
        };
        if ($id > 0) {
            $repo::actualizar($id, $datos);
            flash('exito', ucfirst($config['singular']) . ' actualizado.');
        } else {
            $repo::crear($datos);
            flash('exito', ucfirst($config['singular']) . ' creado.');
        }
        Session::olvidarViejo();
        return redirigir('/panel/' . $tipo);
    }

    public function catalogoEliminar(Request $peticion, array $parametros): Response
    {
        $usuario = $this->guard();
        if (($usuario['rol'] ?? '') === 'estilista') {
            throw new HttpException('Solo administración puede archivar elementos del catálogo.', 403);
        }
        Csrf::verificar($peticion);
        $tipo = (string) ($parametros['tipo'] ?? '');
        $repo = match ($tipo) {
            'servicios' => ServicioRepository::class,
            'productos' => ProductoRepository::class,
            'paquetes' => PaqueteRepository::class,
            default => throw new HttpException('Catálogo no encontrado.', 404),
        };
        $repo::eliminar($peticion->entero('id'));
        flash('exito', 'Elemento archivado.');
        return redirigir('/panel/' . $tipo);
    }

    public function ventas(Request $peticion): Response
    {
        $usuario = $this->guard();
        $desde = $peticion->cadena('desde', date('Y-m-01'));
        $hasta = $peticion->cadena('hasta', date('Y-m-d'));

        return Response::html(view_panel('panel/ventas', [
            'titulo' => 'Ventas',
            'descripcion' => 'Tickets de servicios y productos, con totales del periodo.',
            'usuario' => $usuario,
            'ventas' => VentaRepository::listar($desde, $hasta),
            'desde' => $desde,
            'hasta' => $hasta,
            'resumen' => VentaRepository::resumen($desde, $hasta),
        ]));
    }

    public function ventaNueva(Request $peticion): Response
    {
        $usuario = $this->guard();
        return Response::html(view_panel('panel/venta-nueva', [
            'titulo' => 'Nueva venta',
            'descripcion' => 'Cobra servicios, productos o paquetes en un solo ticket.',
            'usuario' => $usuario,
            'clientes' => ClienteRepository::opciones(),
            'servicios' => ServicioRepository::activos(),
            'productos' => ProductoRepository::activos(),
            'paquetes' => PaqueteRepository::activos(),
        ]));
    }

    public function ventaGuardar(Request $peticion): Response
    {
        $usuario = $this->guard();
        Csrf::verificar($peticion);
        $cuerpo = $peticion->cuerpo();
        $conceptos = $cuerpo['concepto'] ?? [];
        $cantidades = $cuerpo['cantidad'] ?? [];
        $manualesDesc = $cuerpo['manual_descripcion'] ?? [];
        $manualesCant = $cuerpo['manual_cantidad'] ?? [];
        $manualesPrecio = $cuerpo['manual_precio'] ?? [];

        if (!is_array($conceptos) || !is_array($cantidades)) {
            flash('error', 'No pudimos leer los conceptos de la venta.');
            return redirigir('/panel/ventas/nueva');
        }

        $items = [];
        $total = 0.0;
        foreach ($conceptos as $i => $valor) {
            $valor = trim((string) $valor);
            if ($valor === '') {
                continue;
            }
            $partes = explode(':', $valor, 2);
            $tipo = $partes[0] ?? '';
            $referencia = (int) ($partes[1] ?? 0);
            $cantidad = max(1, min(50, (int) ($cantidades[$i] ?? 1)));
            $descripcion = '';
            $precio = 0.0;
            if ($tipo === 'servicio') {
                $fila = ServicioRepository::porId($referencia);
                $descripcion = (string) ($fila['nombre'] ?? '');
                $precio = (float) ($fila['precio'] ?? 0);
            } elseif ($tipo === 'producto') {
                $fila = ProductoRepository::porId($referencia);
                $descripcion = (string) ($fila['nombre'] ?? '');
                $precio = (float) ($fila['precio'] ?? 0);
            } elseif ($tipo === 'paquete') {
                $fila = PaqueteRepository::porId($referencia);
                $descripcion = (string) ($fila['nombre'] ?? '');
                $precio = (float) ($fila['precio'] ?? 0);
            }
            if ($descripcion === '' || !in_array($tipo, ['servicio', 'producto', 'paquete'], true)) {
                continue;
            }
            $importe = round($cantidad * $precio, 2);
            $items[] = [
                'tipo' => $tipo,
                'referencia_id' => $referencia,
                'descripcion' => mb_substr($descripcion, 0, 200),
                'cantidad' => $cantidad,
                'precio_unitario' => $precio,
                'importe' => $importe,
            ];
            $total += $importe;
        }

        if (is_array($manualesDesc) && is_array($manualesCant) && is_array($manualesPrecio)) {
            foreach ($manualesDesc as $i => $descripcion) {
                $descripcion = trim((string) $descripcion);
                $cantidad = max(0, min(50, (int) ($manualesCant[$i] ?? 0)));
                $precio = max(0.0, (float) ($manualesPrecio[$i] ?? 0));
                if ($descripcion === '' || $cantidad < 1 || $precio <= 0) {
                    continue;
                }
                $importe = round($cantidad * $precio, 2);
                $items[] = [
                    'tipo' => 'otro',
                    'referencia_id' => null,
                    'descripcion' => mb_substr($descripcion, 0, 200),
                    'cantidad' => $cantidad,
                    'precio_unitario' => $precio,
                    'importe' => $importe,
                ];
                $total += $importe;
            }
        }

        if ($items === [] || count($items) > 30) {
            flash('error', 'Agrega al menos un concepto a la venta.');
            return redirigir('/panel/ventas/nueva');
        }

        $clienteId = $peticion->entero('cliente_id') ?: null;
        if ($clienteId !== null && ClienteRepository::porId($clienteId) === null) {
            flash('error', 'El cliente seleccionado ya no existe. Elige otro o deja Público general.');
            return redirigir('/panel/ventas/nueva');
        }
        foreach ($items as $item) {
            if ($item['tipo'] !== 'producto' || empty($item['referencia_id'])) {
                continue;
            }
            $producto = ProductoRepository::porId((int) $item['referencia_id']);
            if ($producto === null) {
                flash('error', 'Uno de los productos ya no existe. Revisa la venta.');
                return redirigir('/panel/ventas/nueva');
            }
            if ((int) $producto['stock'] < (int) $item['cantidad']) {
                flash('error', 'No hay stock suficiente de ' . $producto['nombre'] . ' (quedan ' . (int) $producto['stock'] . ').');
                return redirigir('/panel/ventas/nueva');
            }
        }

        $descuento = max(0.0, min($total, (float) $peticion->cadena('descuento', '0')));
        $ventaId = VentaRepository::crearConItems([
            'cliente_id' => $clienteId,
            'fecha' => $peticion->cadena('fecha', date('Y-m-d')),
            'metodo_pago' => $peticion->cadena('metodo_pago', 'efectivo'),
            'estado' => $peticion->cadena('estado', 'pagada'),
            'descuento' => $descuento,
            'notas' => $peticion->cadena('notas'),
            'registrado_por' => (int) $usuario['id'],
        ], $items);

        flash('exito', 'Venta registrada por ' . dinero($total - $descuento) . '.');
        return redirigir('/panel/ventas/' . $ventaId);
    }

    public function ventaDetalle(Request $peticion, array $parametros): Response
    {
        $usuario = $this->guard();
        $venta = VentaRepository::porId((int) ($parametros['id'] ?? 0));
        if ($venta === null) {
            throw new HttpException('Venta no encontrada.', 404);
        }

        return Response::html(view_panel('panel/venta', [
            'titulo' => 'Venta ' . $venta['folio'],
            'descripcion' => 'Ticket, conceptos y datos de pago.',
            'usuario' => $usuario,
            'venta' => $venta,
            'items' => VentaRepository::items((int) $venta['id']),
        ]));
    }

    public function ventaAnular(Request $peticion, array $parametros): Response
    {
        $usuario = $this->guard();
        if (($usuario['rol'] ?? '') === 'estilista') {
            throw new HttpException('Solo administración puede anular ventas.', 403);
        }
        Csrf::verificar($peticion);
        VentaRepository::anular((int) ($parametros['id'] ?? 0));
        flash('exito', 'Venta anulada.');
        return redirigir('/panel/ventas');
    }

    public function gastos(Request $peticion): Response
    {
        $usuario = $this->guard();
        $desde = $peticion->cadena('desde', date('Y-m-01'));
        $hasta = $peticion->cadena('hasta', date('Y-m-d'));

        return Response::html(view_panel('panel/gastos', [
            'titulo' => 'Gastos',
            'descripcion' => 'Renta, insumos, servicios y equipo del salón.',
            'usuario' => $usuario,
            'gastos' => GastoRepository::listar($desde, $hasta),
            'desde' => $desde,
            'hasta' => $hasta,
            'total' => GastoRepository::total($desde, $hasta),
            'categorias' => GastoRepository::categorias(),
        ]));
    }

    public function gastoGuardar(Request $peticion): Response
    {
        $this->guard();
        Csrf::verificar($peticion);
        $datos = [
            'concepto' => $peticion->cadena('concepto'),
            'categoria' => $peticion->cadena('categoria', 'insumos'),
            'monto' => $peticion->cadena('monto'),
            'fecha' => $peticion->cadena('fecha', date('Y-m-d')),
            'metodo_pago' => $peticion->cadena('metodo_pago', 'efectivo'),
            'notas' => $peticion->cadena('notas'),
        ];
        if ($datos['concepto'] === '' || (float) $datos['monto'] <= 0) {
            flash('error', 'Escribe concepto y monto del gasto.');
            return redirigir('/panel/gastos');
        }
        GastoRepository::crear($datos);
        flash('exito', 'Gasto registrado.');
        return redirigir('/panel/gastos');
    }

    public function gastoEliminar(Request $peticion): Response
    {
        $usuario = $this->guard();
        if (($usuario['rol'] ?? '') === 'estilista') {
            throw new HttpException('Solo administración puede eliminar gastos.', 403);
        }
        Csrf::verificar($peticion);
        GastoRepository::eliminar($peticion->entero('id'));
        flash('exito', 'Gasto eliminado.');
        return redirigir('/panel/gastos');
    }

    public function reportes(Request $peticion): Response
    {
        $usuario = $this->guard();
        $desde = $peticion->cadena('desde', date('Y-m-01'));
        $hasta = $peticion->cadena('hasta', date('Y-m-d'));
        $ventas = VentaRepository::resumen($desde, $hasta);
        $gastos = GastoRepository::total($desde, $hasta);
        $citas = (int) Database::value('select count(*) from citas where fecha between ? and ?', [$desde, $hasta]);
        $completadas = (int) Database::value("select count(*) from citas where fecha between ? and ? and estado = 'completada'", [$desde, $hasta]);

        return Response::html(view_panel('panel/reportes', [
            'titulo' => 'Reportes',
            'descripcion' => 'Ventas, gastos, utilidad y servicios más vendidos.',
            'usuario' => $usuario,
            'desde' => $desde,
            'hasta' => $hasta,
            'ventas' => $ventas,
            'gastos' => $gastos,
            'utilidad' => $ventas['total'] - $gastos,
            'citas' => $citas,
            'completadas' => $completadas,
            'top' => VentaRepository::topConceptos($desde, $hasta, 8),
            'serie' => VentaRepository::serieDias($desde, $hasta),
            'gastosCategoria' => GastoRepository::porCategoria($desde, $hasta),
        ]));
    }

    public function usuarios(Request $peticion): Response
    {
        $usuario = $this->guard();
        if (($usuario['rol'] ?? '') !== 'admin') {
            throw new HttpException('Solo administración puede gestionar usuarios.', 403);
        }

        return Response::html(view_panel('panel/usuarios', [
            'titulo' => 'Usuarios y personal',
            'descripcion' => 'Altas de personal, roles y estado de acceso.',
            'usuario' => $usuario,
            'usuarios' => UsuarioRepository::listar(),
            'roles' => ['admin' => 'Administrador', 'editor' => 'Editor', 'estilista' => 'Estilista', 'cliente' => 'Cliente'],
        ]));
    }

    public function usuarioGuardar(Request $peticion): Response
    {
        $usuario = $this->guard();
        if (($usuario['rol'] ?? '') !== 'admin') {
            throw new HttpException('Solo administración puede gestionar usuarios.', 403);
        }
        Csrf::verificar($peticion);
        $datos = [
            'nombre' => $peticion->cadena('nombre'),
            'correo' => mb_strtolower($peticion->cadena('correo')),
            'rol' => $peticion->cadena('rol', 'estilista'),
            'password' => (string) $peticion->input('password', ''),
        ];
        $errores = Validator::validar($datos, [
            'nombre' => 'required|min:3|max:120',
            'correo' => 'required|email|max:160',
            'password' => 'required|min:8|max:100',
        ]);
        if (!in_array($datos['rol'], ['admin', 'editor', 'estilista', 'cliente'], true)) {
            $errores['rol'] = 'Elige un rol válido.';
        }
        if (UsuarioRepository::existeCorreo($datos['correo'])) {
            $errores['correo'] = 'Ese correo ya está registrado.';
        }
        if ($errores !== []) {
            Session::put('_errores', $errores);
            Session::recordarViejo($datos);
            flash('error', 'Revisa los datos del usuario.');
            return redirigir('/panel/usuarios');
        }
        $base = explode('@', $datos['correo'])[0] ?? 'usuario';
        UsuarioRepository::crear([
            'nombre' => $datos['nombre'],
            'usuario' => Slug::unico($base, static fn (string $c): bool => UsuarioRepository::existeUsuario($c)),
            'correo' => $datos['correo'],
            'password_hash' => password_hash($datos['password'], PASSWORD_DEFAULT),
            'rol' => $datos['rol'],
            'estado' => 'activo',
        ]);
        Session::olvidarViejo();
        flash('exito', 'Usuario creado.');
        return redirigir('/panel/usuarios');
    }

    public function usuarioEstado(Request $peticion): Response
    {
        $usuario = $this->guard();
        if (($usuario['rol'] ?? '') !== 'admin') {
            throw new HttpException('Solo administración puede gestionar usuarios.', 403);
        }
        Csrf::verificar($peticion);
        $id = $peticion->entero('id');
        if ($id === (int) $usuario['id']) {
            flash('error', 'No puedes suspender tu propia cuenta.');
            return redirigir('/panel/usuarios');
        }
        $estado = $peticion->cadena('estado') === 'activo' ? 'activo' : 'suspendido';
        UsuarioRepository::cambiarEstado($id, $estado);
        flash('exito', 'Estado del usuario actualizado.');
        return redirigir('/panel/usuarios');
    }

    public function usuarioRol(Request $peticion): Response
    {
        $usuario = $this->guard();
        if (($usuario['rol'] ?? '') !== 'admin') {
            throw new HttpException('Solo administración puede gestionar usuarios.', 403);
        }
        Csrf::verificar($peticion);
        $id = $peticion->entero('id');
        $rol = $peticion->cadena('rol');
        if ($id === (int) $usuario['id']) {
            flash('error', 'No puedes cambiar tu propio rol.');
            return redirigir('/panel/usuarios');
        }
        if (!in_array($rol, ['admin', 'editor', 'estilista', 'cliente'], true)) {
            flash('error', 'Rol inválido.');
            return redirigir('/panel/usuarios');
        }
        UsuarioRepository::cambiarRol($id, $rol);
        flash('exito', 'Rol actualizado.');
        return redirigir('/panel/usuarios');
    }

    public function usuarioAcceso(Request $peticion): Response
    {
        $usuario = $this->guard();
        if (($usuario['rol'] ?? '') !== 'admin') {
            throw new HttpException('Solo administración puede gestionar usuarios.', 403);
        }
        Csrf::verificar($peticion);
        $id = $peticion->entero('id');
        if (UsuarioRepository::porId($id) === null) {
            flash('error', 'El usuario ya no existe.');
            return redirigir('/panel/usuarios');
        }

        $fecha = trim($peticion->cadena('acceso_hasta'));
        if ($fecha !== '') {
            $valida = \DateTimeImmutable::createFromFormat('!Y-m-d', $fecha);
            if ($valida === false || $valida->format('Y-m-d') !== $fecha) {
                flash('error', 'Escribe una fecha de acceso válida.');
                return redirigir('/panel/usuarios');
            }
        }

        UsuarioRepository::actualizarAcceso($id, $fecha !== '' ? $fecha : null);
        flash('exito', $fecha !== '' ? 'Fecha de acceso actualizada.' : 'Acceso sin fecha de vencimiento.');
        return redirigir('/panel/usuarios');
    }

    public function configuracion(Request $peticion): Response
    {
        $usuario = $this->guard();
        if (($usuario['rol'] ?? '') !== 'admin') {
            throw new HttpException('Solo administración puede cambiar la configuración.', 403);
        }

        return Response::html(view_panel('panel/configuracion', [
            'titulo' => 'Configuración',
            'descripcion' => 'Datos del negocio, contacto y capacidad.',
            'usuario' => $usuario,
            'config' => ConfiguracionRepository::todas(),
        ]));
    }

    public function configuracionGuardar(Request $peticion): Response
    {
        $usuario = $this->guard();
        if (($usuario['rol'] ?? '') !== 'admin') {
            throw new HttpException('Solo administración puede cambiar la configuración.', 403);
        }
        Csrf::verificar($peticion);
        $campos = ['negocio_nombre', 'negocio_telefono', 'negocio_whatsapp', 'negocio_direccion', 'negocio_horario', 'capacidad_diaria', 'anticipo_pct'];
        $datos = [];
        foreach ($campos as $campo) {
            $datos[$campo] = $peticion->cadena($campo);
        }

        $archivo = $peticion->archivo('logo_archivo');
        if ($archivo !== null && (int) ($archivo['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            $subida = Upload::imagen($archivo, 'negocio');
            if (!($subida['ok'] ?? false)) {
                flash('error', (string) ($subida['error'] ?? 'No se pudo subir el logo.'));
                return redirigir('/panel/configuracion');
            }
            $datos['negocio_logo'] = (string) $subida['ruta'];
        } elseif ($peticion->cadena('quitar_logo') === '1') {
            $datos['negocio_logo'] = '';
        }

        ConfiguracionRepository::setMany($datos);
        flash('exito', 'Configuración guardada.');
        return redirigir('/panel/configuracion');
    }
}
