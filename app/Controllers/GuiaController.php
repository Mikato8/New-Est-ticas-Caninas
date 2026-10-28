<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Domain\Contenido\Guias;

final class GuiaController
{
    public function index(Request $peticion): Response
    {
        $categoria = $peticion->cadena('categoria');
        $busqueda = mb_strtolower($peticion->cadena('q'));
        $guias = Guias::catalogo();

        if ($categoria !== '' && isset(Guias::categorias()[$categoria])) {
            $guias = array_values(array_filter($guias, static fn (array $g): bool => $g['categoria'] === $categoria));
        }
        if ($busqueda !== '') {
            $guias = array_values(array_filter($guias, static function (array $g) use ($busqueda): bool {
                $texto = mb_strtolower($g['titulo'] . ' ' . $g['resumen'] . ' ' . implode(' ', array_column($g['secciones'], 'titulo')));
                return str_contains($texto, $busqueda);
            }));
        }

        return Response::html(view('paginas/guias', [
            'titulo' => 'Guías de estética canina',
            'descripcion' => 'Baño, corte, uñas, oídos, secado, manejo y negocio: guías de oficio escritas para estilistas caninos de México.',
            'guias' => $guias,
            'categoria' => $categoria,
            'busqueda' => $peticion->cadena('q'),
            'categorias' => Guias::categorias(),
        ]));
    }

    public function show(Request $peticion, array $parametros): Response
    {
        $guia = Guias::porSlug((string) ($parametros['slug'] ?? ''));
        if ($guia === null) {
            throw new \App\Core\HttpException('Esa guía no existe o cambió de dirección.', 404);
        }

        $relacionadas = array_values(array_filter(
            Guias::porCategoria($guia['categoria']),
            static fn (array $g): bool => $g['slug'] !== $guia['slug']
        ));
        $relacionadas = array_slice($relacionadas, 0, 3);

        $indice = null;
        $catalogo = Guias::catalogo();
        foreach ($catalogo as $i => $item) {
            if ($item['slug'] === $guia['slug']) {
                $indice = $i;
                break;
            }
        }
        $anterior = $indice !== null && isset($catalogo[$indice - 1]) ? $catalogo[$indice - 1] : null;
        $siguiente = $indice !== null && isset($catalogo[$indice + 1]) ? $catalogo[$indice + 1] : null;

        return Response::html(view('paginas/guia', [
            'titulo' => $guia['titulo'],
            'descripcion' => $guia['resumen'],
            'guia' => $guia,
            'relacionadas' => $relacionadas,
            'anterior' => $anterior,
            'siguiente' => $siguiente,
            'categorias' => Guias::categorias(),
        ]));
    }
}
