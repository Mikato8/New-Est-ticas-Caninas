<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\HttpException;
use App\Core\Request;
use App\Core\Response;
use App\Domain\Estetica\CalculadoraPrecio;
use App\Domain\Estetica\CalculadoraRentabilidad;
use App\Domain\Estetica\CalculadoraSecado;
use App\Domain\Estetica\CalculadoraShampoo;
use App\Domain\Estetica\DatosInvalidos;

final class CalculadoraController
{
    /** @var array<string, array{clase: class-string, vista: string, titulo: string, descripcion: string, icono: string}> */
    private const CALCULADORAS = [
        'dilucion' => [
            'clase' => CalculadoraShampoo::class,
            'vista' => 'paginas/calculadora-dilucion',
            'titulo' => 'Dilución de shampoo',
            'descripcion' => 'Cuánta agua, cuánto concentrado y cuántos baños te rinde una mezcla.',
            'icono' => 'agua',
        ],
        'precio' => [
            'clase' => CalculadoraPrecio::class,
            'vista' => 'paginas/calculadora-precio',
            'titulo' => 'Precio de un servicio',
            'descripcion' => 'Costo fijo, insumos, mano de obra, comisión y margen para un precio rentable.',
            'icono' => 'dinero',
        ],
        'secado' => [
            'clase' => CalculadoraSecado::class,
            'vista' => 'paginas/calculadora-secado',
            'titulo' => 'Tiempo y costo de secado',
            'descripcion' => 'Minutos estimados, pausas y energía según peso, manto y equipo.',
            'icono' => 'secadora',
        ],
        'rentabilidad' => [
            'clase' => CalculadoraRentabilidad::class,
            'vista' => 'paginas/calculadora-rentabilidad',
            'titulo' => 'Rentabilidad por servicio',
            'descripcion' => 'Tu utilidad por hora, margen real y punto de equilibrio del salón.',
            'icono' => 'grafica',
        ],
    ];

    public function index(Request $peticion): Response
    {
        return Response::html(view('paginas/calculadoras', [
            'titulo' => 'Calculadoras para estilistas caninos',
            'descripcion' => 'Dilución de shampoo, precio, secado y rentabilidad. Herramientas gratuitas para tu estética canina.',
            'calculadoras' => self::CALCULADORAS,
        ]));
    }

    public function dilucion(Request $peticion): Response
    {
        return $this->render('dilucion', $peticion, [
            'concentrado_ml' => 500,
            'ratio' => 10,
            'tamano' => 'mediano',
            'pelo' => 'medio',
            'suciedad' => 'normal',
            'precio_litro' => 450,
        ]);
    }

    public function precio(Request $peticion): Response
    {
        return $this->render('precio', $peticion, [
            'minutos' => 90,
            'insumos' => 45,
            'costo_fijo_mensual' => 15000,
            'servicios_mes' => 120,
            'tarifa_hora' => 120,
            'comision_pct' => 0,
            'margen_pct' => 35,
            'iva_pct' => 0,
        ]);
    }

    public function secado(Request $peticion): Response
    {
        return $this->render('secado', $peticion, [
            'peso_kg' => 10,
            'pelo' => 'medio',
            'equipo' => 'turbo',
            'tarifa_kwh' => 3.5,
        ]);
    }

    public function rentabilidad(Request $peticion): Response
    {
        return $this->render('rentabilidad', $peticion, [
            'precio' => 550,
            'insumos' => 45,
            'minutos' => 90,
            'comision_pct' => 0,
            'costo_fijo_mensual' => 15000,
            'meta_hora' => 180,
        ]);
    }

    public function api(Request $peticion, array $parametros): Response
    {
        $tipo = (string) ($parametros['tipo'] ?? '');
        if (!isset(self::CALCULADORAS[$tipo])) {
            throw new HttpException('Calculadora no encontrada.', 404);
        }

        $clase = self::CALCULADORAS[$tipo]['clase'];
        try {
            $resultado = (new $clase())->calcular($peticion->consulta());
            return Response::json(['ok' => true, 'calculadora' => $tipo, 'resultado' => $resultado]);
        } catch (DatosInvalidos $e) {
            return Response::json(['ok' => false, 'errores' => $e->errores()], 422);
        }
    }

    /** @param array<string, mixed> $defectos */
    private function render(string $tipo, Request $peticion, array $defectos): Response
    {
        $config = self::CALCULADORAS[$tipo];
        $entrada = array_merge($defectos, $peticion->consulta());
        $resultado = null;
        $errores = [];

        if ($peticion->consulta() !== []) {
            $clase = $config['clase'];
            try {
                $resultado = (new $clase())->calcular($entrada);
            } catch (DatosInvalidos $e) {
                $errores = $e->errores();
            }
        }

        return Response::html(view($config['vista'], [
            'titulo' => $config['titulo'] . ' · Calculadora',
            'descripcion' => $config['descripcion'],
            'entrada' => $entrada,
            'resultado' => $resultado,
            'errores' => $errores,
            'calculadora' => $tipo,
        ]));
    }
}
