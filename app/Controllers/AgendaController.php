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
use App\Repositories\ServicioRepository;
use App\Support\Validator;

final class AgendaController
{
    public function form(Request $peticion): Response
    {
        $servicios = ServicioRepository::activos();
        $servicioId = $peticion->entero('servicio');
        $fecha = $peticion->cadena('fecha', date('Y-m-d', strtotime('+1 day')));
        if (strtotime($fecha) === false || $fecha < date('Y-m-d')) {
            $fecha = date('Y-m-d', strtotime('+1 day'));
        }
        $servicio = $servicioId > 0 ? ServicioRepository::porId($servicioId) : null;

        $usuario = Auth::usuario();
        $cliente = $usuario !== null ? ClienteRepository::porUsuario((int) $usuario['id']) : null;
        $mascotas = $cliente !== null ? MascotaRepository::porCliente((int) $cliente['id']) : [];

        return Response::html(view('paginas/agenda', [
            'titulo' => 'Demo de agenda para estéticas caninas',
            'descripcion' => 'Así reservan los clientes de una estética en el CRM de Mikato: servicio, fecha, horario y datos de la mascota.',
            'servicios' => $servicios,
            'servicioId' => $servicioId,
            'servicio' => $servicio,
            'fecha' => $fecha,
            'horas' => $servicio !== null ? $this->horasDisponibles($fecha) : [],
            'usuario' => $usuario,
            'cliente' => $cliente,
            'mascotas' => $mascotas,
            'minFecha' => date('Y-m-d'),
            'maxFecha' => date('Y-m-d', strtotime('+45 days')),
        ]));
    }

    /** @return list<string> */
    private function horasDisponibles(string $fecha): array
    {
        $dia = (int) date('w', strtotime($fecha));
        if ($dia === 0) {
            return [];
        }
        $horas = [];
        for ($hora = 9; $hora <= 18; $hora++) {
            $horas[] = sprintf('%02d:00', $hora);
            if ($hora !== 18) {
                $horas[] = sprintf('%02d:30', $hora);
            }
        }
        $ocupadas = CitaRepository::horasOcupadas($fecha);
        return array_values(array_filter($horas, static fn (string $h): bool => !in_array($h, $ocupadas, true)));
    }

    public function disponibilidad(Request $peticion): Response
    {
        $fecha = $peticion->cadena('fecha');
        if (strtotime($fecha) === false || $fecha < date('Y-m-d') || $fecha > date('Y-m-d', strtotime('+45 days'))) {
            return Response::json(['ok' => false, 'error' => 'Fecha fuera de rango.'], 422);
        }
        $horas = $this->horasDisponibles($fecha);
        return Response::json(['ok' => true, 'fecha' => $fecha, 'horas' => $horas, 'disponible' => $horas !== []]);
    }

    public function guardar(Request $peticion): Response
    {
        Csrf::verificar($peticion);
        if ($peticion->cadena('sitio_web') !== '') {
            return redirigir('/agenda');
        }

        [$permitido, $espera] = Session::limitar('citas', (int) config('limites.citas_por_dia', 3), 86400);
        if (!$permitido) {
            flash('error', 'Ya registramos varias solicitudes desde este dispositivo. Llámanos o escríbenos por WhatsApp.');
            return redirigir('/agenda');
        }

        $datos = [
            'nombre' => $peticion->cadena('nombre'),
            'telefono' => $peticion->cadena('telefono'),
            'correo' => $peticion->cadena('correo'),
            'mascota' => $peticion->cadena('mascota'),
            'raza' => $peticion->cadena('raza'),
            'tamano' => $peticion->cadena('tamano', 'mediano'),
            'servicio_id' => $peticion->entero('servicio_id'),
            'fecha' => $peticion->cadena('fecha'),
            'hora' => $peticion->cadena('hora'),
            'notas' => $peticion->cadena('notas'),
        ];

        $errores = Validator::validar($datos, [
            'nombre' => 'required|min:3|max:120',
            'telefono' => 'required|min:8|max:30',
            'correo' => 'email|max:160',
            'mascota' => 'required|min:2|max:80',
            'fecha' => 'required',
            'hora' => 'required',
        ]);
        if (!in_array($datos['tamano'], ['chico', 'mediano', 'grande', 'gigante'], true)) {
            $errores['tamano'] = 'Elige un tamaño válido.';
        }
        $servicio = ServicioRepository::porId((int) $datos['servicio_id']);
        if ($servicio === null || !es_verdadero($servicio['activo'])) {
            $errores['servicio_id'] = 'Elige un servicio válido.';
        }
        if (strtotime($datos['fecha']) === false || $datos['fecha'] < date('Y-m-d')) {
            $errores['fecha'] = 'Elige una fecha a partir de hoy.';
        }
        if (!preg_match('/^\d{2}:\d{2}$/', $datos['hora'])) {
            $errores['hora'] = 'Elige una hora válida.';
        }
        if ($peticion->cadena('acepto') !== '1') {
            $errores['acepto'] = 'Acepta el aviso de privacidad para continuar.';
        }

        if ($errores === [] && CitaRepository::slotOcupado($datos['fecha'], $datos['hora'])) {
            $errores['hora'] = 'Ese horario acaba de ocuparse. Elige otro.';
        }

        if ($errores !== []) {
            Session::put('_errores', $errores);
            Session::recordarViejo($datos);
            flash('error', 'Revisa los datos de la cita.');
            return redirigir('/agenda?servicio=' . (int) $datos['servicio_id'] . '&fecha=' . urlencode($datos['fecha']));
        }

        $usuario = Auth::usuario();
        $clienteId = 0;
        if ($usuario !== null) {
            $clienteId = ClienteRepository::asegurarParaUsuario((int) $usuario['id']);
        }
        if ($clienteId === 0) {
            $existente = ClienteRepository::porContacto($datos['telefono'], $datos['correo']);
            if ($existente !== null) {
                $clienteId = (int) $existente['id'];
            } else {
                $clienteId = ClienteRepository::crear([
                    'nombre' => $datos['nombre'],
                    'telefono' => $datos['telefono'],
                    'correo' => $datos['correo'] !== '' ? $datos['correo'] : null,
                    'notas' => 'Alta desde la agenda web',
                ]);
            }
        }

        $mascotaId = 0;
        foreach (MascotaRepository::porCliente($clienteId) as $mascota) {
            if (mb_strtolower((string) $mascota['nombre']) === mb_strtolower($datos['mascota'])) {
                $mascotaId = (int) $mascota['id'];
                break;
            }
        }
        if ($mascotaId === 0) {
            $mascotaId = MascotaRepository::crear([
                'cliente_id' => $clienteId,
                'nombre' => $datos['mascota'],
                'raza' => $datos['raza'] !== '' ? $datos['raza'] : null,
                'tamano' => $datos['tamano'],
                'notas' => 'Alta desde la agenda web',
            ]);
        }

        $citaId = CitaRepository::crear([
            'cliente_id' => $clienteId,
            'mascota_id' => $mascotaId,
            'servicio_id' => (int) $datos['servicio_id'],
            'fecha' => $datos['fecha'],
            'hora' => $datos['hora'],
            'precio' => (float) $servicio['precio'],
            'notas' => $datos['notas'] !== '' ? $datos['notas'] : null,
            'origen' => 'web',
        ]);
        $cita = CitaRepository::porId($citaId);
        Session::olvidarViejo();

        return redirigir('/agenda/gracias/' . ($cita['codigo'] ?? ''));
    }

    public function gracias(Request $peticion, array $parametros): Response
    {
        $cita = CitaRepository::porCodigo((string) ($parametros['codigo'] ?? ''));
        if ($cita === null) {
            throw new HttpException('No encontramos esa cita.', 404);
        }

        return Response::html(view('paginas/agenda-gracias', [
            'titulo' => 'Cita recibida',
            'descripcion' => 'Tu solicitud de cita quedó registrada. Te confirmamos por WhatsApp.',
            'cita' => $cita,
            'whatsapp' => whatsapp_link('Hola, quiero confirmar la cita ' . $cita['codigo'] . ' de ' . $cita['mascota_nombre'] . '.'),
        ]));
    }

    public function consultar(Request $peticion): Response
    {
        $codigo = trim($peticion->cadena('codigo'));
        $cita = $codigo !== '' ? CitaRepository::porCodigo($codigo) : null;
        return Response::html(view('paginas/agenda-consultar', [
            'titulo' => 'Consultar mi cita',
            'descripcion' => 'Consulta el estado de tu cita con el código que te dimos al reservar.',
            'codigo' => $codigo,
            'cita' => $cita,
        ]));
    }
}
