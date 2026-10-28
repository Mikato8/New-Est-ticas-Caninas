<?php

declare(strict_types=1);

use App\Core\Database;
use App\Domain\Contenido\Glosario;
use App\Domain\Contenido\Guias;
use App\Support\Markdown;

require dirname(__DIR__) . '/app/bootstrap.php';

/**
 * Corpus del asistente local. Un fragmento por sección de guía, pregunta
 * frecuente, término del glosario, calculadora y servicio/producto.
 */

function limpiar(string $texto): string
{
    $html = Markdown::toHtml($texto);
    $plano = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $plano = preg_replace('/\s+/u', ' ', $plano) ?? $plano;
    return trim($plano);
}

$chunks = [];

foreach (Guias::catalogo() as $guia) {
    $chunks[] = [
        'titulo' => $guia['titulo'],
        'titulo_guia' => $guia['titulo'],
        'texto' => limpiar($guia['resumen']),
        'url' => '/guias/' . $guia['slug'],
        'categoria' => 'guia',
        'tags' => $guia['categoria'] . ' ' . $guia['nivel'] . ' guia',
    ];

    foreach ($guia['secciones'] as $seccion) {
        $chunks[] = [
            'titulo' => $guia['titulo'] . ' · ' . $seccion['titulo'],
            'titulo_guia' => $guia['titulo'],
            'texto' => limpiar($seccion['md']),
            'url' => '/guias/' . $guia['slug'],
            'categoria' => 'guia',
            'tags' => $guia['categoria'] . ' ' . $seccion['titulo'],
        ];
    }

    foreach ($guia['faq'] as $faq) {
        $chunks[] = [
            'titulo' => $faq['p'],
            'titulo_guia' => $guia['titulo'],
            'texto' => limpiar($faq['r']),
            'url' => '/guias/' . $guia['slug'],
            'categoria' => 'faq',
            'tags' => $guia['categoria'] . ' pregunta frecuente ' . $faq['p'],
        ];
    }
}

foreach (Glosario::todos() as $termino) {
    $chunks[] = [
        'titulo' => $termino['termino'],
        'titulo_guia' => 'Glosario',
        'texto' => limpiar($termino['definicion']),
        'url' => '/glosario',
        'categoria' => 'glosario',
        'tags' => $termino['categoria'] . ' glosario ' . $termino['termino'],
    ];
}

$calculadoras = [
    ['dilucion', 'Dilución de shampoo', 'Calcula cuánta agua agregar al shampoo concentrado, cuántos baños rinde y el costo por baño. La ficha del fabricante manda.'],
    ['precio', 'Precio de un servicio', 'Calcula el precio de un servicio con costo fijo, insumos, mano de obra, comisión, margen e IVA.'],
    ['secado', 'Tiempo y costo de secado', 'Estima minutos de secado por peso, manto y equipo, con pausas y costo de energía.'],
    ['rentabilidad', 'Rentabilidad por servicio', 'Calcula utilidad por hora, margen real y punto de equilibrio de la estética.'],
];
foreach ($calculadoras as [$slug, $titulo, $texto]) {
    $chunks[] = [
        'titulo' => $titulo,
        'titulo_guia' => 'Calculadoras',
        'texto' => limpiar($texto),
        'url' => '/calculadoras/' . $slug,
        'categoria' => 'calculadora',
        'tags' => 'calculadora ' . $slug . ' ' . $titulo,
    ];
}

try {
    foreach (\App\Repositories\ServicioRepository::activos() as $servicio) {
        $chunks[] = [
            'titulo' => 'Servicio: ' . $servicio['nombre'],
            'titulo_guia' => 'Servicios',
            'texto' => limpiar('Precio ' . precio($servicio['precio']) . '. Duración aproximada ' . (int) $servicio['duracion_min'] . ' minutos. ' . (string) $servicio['descripcion']),
            'url' => '/servicios',
            'categoria' => 'servicio',
            'tags' => 'servicio ' . $servicio['categoria'] . ' ' . $servicio['nombre'],
        ];
    }
    foreach (\App\Repositories\ProductoRepository::activos() as $producto) {
        $chunks[] = [
            'titulo' => 'Producto: ' . $producto['nombre'],
            'titulo_guia' => 'Productos',
            'texto' => limpiar('Precio ' . precio($producto['precio']) . '. ' . (string) $producto['descripcion']),
            'url' => '/productos',
            'categoria' => 'producto',
            'tags' => 'producto ' . $producto['categoria'] . ' ' . $producto['nombre'],
        ];
    }
} catch (\Throwable $e) {
    fwrite(STDERR, 'Aviso: no se pudieron leer servicios/productos de la base. ' . $e->getMessage() . PHP_EOL);
}

$extras = [
    ['Golpe de calor: señales y primeros auxilios', 'Jadeo intenso, salivación espesa, encías rojas, debilidad, vómito o tambaleo. Llama al veterinario, lleva al perro a un lugar fresco, moja el cuerpo con agua templada, ventila y traslada. No uses hielo. Es una urgencia.', '/guias/secado-seguro', 'emergencia'],
    ['Corte de uñas: el vaso o quick', 'En uñas claras el vaso se ve rosado; en negras no. Corta de 1 a 2 mm por vez y revisa la cara del corte. Si sangra, aplica presión con gasa de 3 a 5 minutos y polvo hemostático. Si no para en 10 minutos, acude al veterinario.', '/guias/corte-de-unas-seguro', 'tecnica'],
    ['Doble capa: no rasurar', 'El manto doble aísla del calor y protege del sol. Rasurarlo puede causar alopecia, quemaduras y problemas de termorregulación. La solución es deslanar y cepillar, no rasurar.', '/guias/cepillado-y-deslanado', 'mito'],
    ['Glándulas anales: no de rutina', 'No se exprimen como parte del baño. Solo si hay signos (se arrastra, se lame, dolor al sentarse) o indicación veterinaria. Forzarlas puede traumatizar e infectar.', '/guias/limpieza-de-oidos-y-pliegues', 'salud'],
    ['Aviso legal: no existe NOM de estética canina', 'No hay una NOM federal específica de estética canina. La NOM-042-SSA2-2006 regula centros antirrábicos y la NOM-033-SAG/ZOO-2014 regula el sacrificio humanitario. Verifica licencia de funcionamiento, uso de suelo y protección civil en tu municipio. La autoridad de datos personales es la Secretaría Anticorrupción y Buen Gobierno, no el INAI.', '/buenas-practicas', 'legal'],
    ['Cómo cobrar un nudo o deslanado', 'Cobra por tiempo y complejidad, no solo por el baño. Calcula los minutos extra, el producto y el riesgo. Un nudo severo puede costar más que el corte completo. Avísalo antes de empezar.', '/guias/como-cotizar-servicios', 'negocio'],
];
foreach ($extras as [$titulo, $texto, $url, $categoria]) {
    $chunks[] = [
        'titulo' => $titulo,
        'titulo_guia' => 'Conceptos clave',
        'texto' => $texto,
        'url' => $url,
        'categoria' => $categoria,
        'tags' => 'concepto ' . $titulo . ' ' . $categoria,
    ];
}

$datos = [
    'generado' => date('c'),
    'version' => '1.0',
    'total' => count($chunks),
    'chunks' => $chunks,
];

$destino = BASE_PATH . '/public/assets/ia/corpus.json';
$json = json_encode($datos, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
if ($json === false || file_put_contents($destino, $json) === false) {
    fwrite(STDERR, "No se pudo escribir {$destino}\n");
    exit(1);
}

echo 'Corpus generado: ' . count($chunks) . ' fragmentos en ' . $destino . PHP_EOL;
