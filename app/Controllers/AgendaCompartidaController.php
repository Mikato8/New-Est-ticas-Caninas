<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\HttpException;
use App\Core\Request;
use App\Core\Response;
use App\Repositories\CitaRepository;
use App\Support\AgendaShare;

final class AgendaCompartidaController
{
    public function ver(Request $peticion, array $parametros): Response
    {
        $fecha = AgendaShare::fecha((string) ($parametros['token'] ?? ''));
        if ($fecha === null) {
            throw new HttpException('Este enlace de agenda venció o no es válido.', 404);
        }
        $citas = CitaRepository::porFecha($fecha);

        return Response::html(view('paginas/agenda-compartida', [
            'titulo' => 'Agenda del ' . fecha_larga($fecha),
            'descripcion' => 'Agenda de citas compartida desde el panel de Mikato.',
            'noindex' => true,
            'fecha' => $fecha,
            'citas' => $citas,
            'token' => (string) ($parametros['token'] ?? ''),
        ]));
    }

    public function csv(Request $peticion, array $parametros): Response
    {
        $fecha = AgendaShare::fecha((string) ($parametros['token'] ?? ''));
        if ($fecha === null) {
            throw new HttpException('Este enlace de agenda venció o no es válido.', 404);
        }
        $citas = CitaRepository::porFecha($fecha);
        $csv = AgendaShare::csv($fecha, $citas);

        return Response::texto($csv, 200, 'text/csv; charset=UTF-8')
            ->conCabecera('Content-Disposition', 'attachment; filename="agenda-' . $fecha . '.csv"');
    }
}
