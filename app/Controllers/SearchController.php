<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Domain\Contenido\Guias;
use App\Repositories\NegocioRepository;
use App\Repositories\ProductoRepository;
use App\Repositories\PublicacionRepository;
use App\Repositories\ServicioRepository;

final class SearchController
{
    public function index(Request $peticion): Response
    {
        $q = trim($peticion->cadena('q'));
        $termino = mb_strtolower($q);
        $resultados = [
            'guias' => [],
            'publicaciones' => [],
            'negocios' => [],
            'servicios' => [],
            'productos' => [],
        ];

        if ($termino !== '') {
            foreach (Guias::catalogo() as $guia) {
                $texto = mb_strtolower($guia['titulo'] . ' ' . $guia['resumen'] . ' ' . implode(' ', array_column($guia['secciones'], 'titulo')));
                if (str_contains($texto, $termino)) {
                    $resultados['guias'][] = $guia;
                }
            }
            $resultados['publicaciones'] = PublicacionRepository::publicadas('', $q, 8);
            $resultados['negocios'] = NegocioRepository::aprobados('', $q, 6);
            foreach (ServicioRepository::activos() as $servicio) {
                $texto = mb_strtolower($servicio['nombre'] . ' ' . (string) $servicio['descripcion']);
                if (str_contains($texto, $termino)) {
                    $resultados['servicios'][] = $servicio;
                }
            }
            foreach (ProductoRepository::activos() as $producto) {
                $texto = mb_strtolower($producto['nombre'] . ' ' . (string) $producto['descripcion']);
                if (str_contains($texto, $termino)) {
                    $resultados['productos'][] = $producto;
                }
            }
        }

        $total = array_sum(array_map('count', $resultados));

        return Response::html(view('paginas/buscar', [
            'titulo' => $q !== '' ? 'Resultados para «' . $q . '»' : 'Buscar',
            'descripcion' => 'Busca guías, casos, servicios, productos y estéticas en Mikato.',
            'q' => $q,
            'resultados' => $resultados,
            'total' => $total,
        ]));
    }

    /** @return array<string, mixed> */
    public function api(Request $peticion): Response
    {
        $q = trim($peticion->cadena('q'));
        if (mb_strlen($q) < 2) {
            return Response::json(['ok' => true, 'q' => $q, 'resultados' => []]);
        }
        $termino = mb_strtolower($q);
        $salida = [];
        foreach (Guias::catalogo() as $guia) {
            $texto = mb_strtolower($guia['titulo'] . ' ' . $guia['resumen']);
            if (str_contains($texto, $termino)) {
                $salida[] = ['tipo' => 'Guía', 'titulo' => $guia['titulo'], 'url' => '/guias/' . $guia['slug'], 'resumen' => $guia['resumen']];
            }
        }
        foreach (PublicacionRepository::publicadas('', $q, 5) as $publicacion) {
            $salida[] = ['tipo' => 'Comunidad', 'titulo' => $publicacion['titulo'], 'url' => '/comunidad/' . $publicacion['slug'], 'resumen' => $publicacion['resumen']];
        }
        foreach (NegocioRepository::aprobados('', $q, 5) as $negocio) {
            $salida[] = ['tipo' => 'Directorio', 'titulo' => $negocio['nombre'], 'url' => '/directorio/' . $negocio['slug'], 'resumen' => $negocio['ciudad'] . ', ' . $negocio['estado_geo']];
        }
        foreach (ServicioRepository::activos() as $servicio) {
            if (str_contains(mb_strtolower($servicio['nombre']), $termino)) {
                $salida[] = ['tipo' => 'Servicio', 'titulo' => $servicio['nombre'], 'url' => '/servicios', 'resumen' => precio($servicio['precio'])];
            }
        }
        return Response::json(['ok' => true, 'q' => $q, 'resultados' => array_slice($salida, 0, 12)]);
    }
}
