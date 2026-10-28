<?php
declare(strict_types=1);
$secciones = [
    'guias' => ['titulo' => 'Guías', 'etiqueta' => 'Guía', 'icono' => 'libro'],
    'publicaciones' => ['titulo' => 'Comunidad', 'etiqueta' => 'Caso', 'icono' => 'comunidad'],
    'negocios' => ['titulo' => 'Directorio', 'etiqueta' => 'Estética', 'icono' => 'directorio'],
    'servicios' => ['titulo' => 'Servicios', 'etiqueta' => 'Servicio', 'icono' => 'tijeras'],
    'productos' => ['titulo' => 'Productos', 'etiqueta' => 'Producto', 'icono' => 'producto'],
];
?>
<div class="contenedor">
    <nav class="migas" aria-label="Ruta"><a href="<?= url('/') ?>">Inicio</a><span>/</span>Buscar</nav>
    <header class="portada-interior">
        <span class="mono texto-oro">Buscador</span>
        <h1><?= $q !== '' ? 'Resultados para «' . e($q) . '»' : '¿Qué estás buscando?' ?></h1>
        <p><?= $q !== '' ? (int) $total . ' resultado' . ($total === 1 ? '' : 's') . ' en guías, comunidad, directorio, servicios y productos.' : 'Busca por técnica, raza, ciudad, servicio o producto. Ejemplos: “secado”, “uñas”, “Poodle”, “Guadalajara”.' ?></p>
        <form class="buscador mt-3" method="get" action="<?= url('/buscar') ?>" style="max-width:560px;">
            <?= icono('buscar') ?>
            <label class="sr-only" for="q-buscar">Buscar</label>
            <input id="q-buscar" type="search" name="q" value="<?= e($q) ?>" placeholder="Buscar en todo el portal…" autofocus>
            <button type="submit">Buscar</button>
        </form>
    </header>
</div>

<section class="seccion seccion--compacta">
    <div class="contenedor">
        <?php if ($q === ''): ?>
            <div class="grid grid--3">
                <div class="tarjeta tarjeta__cuerpo"><h3>Técnicas</h3><p class="texto-suave" style="font-size:.9rem;">Baño, corte, uñas, oídos, secado, deslanado.</p><a class="enlace-flecha" href="<?= url('/guias') ?>">Guías <?= icono('flecha') ?></a></div>
                <div class="tarjeta tarjeta__cuerpo"><h3>Ciudades</h3><p class="texto-suave" style="font-size:.9rem;">Encuentra estéticas en tu estado.</p><a class="enlace-flecha" href="<?= url('/directorio') ?>">Directorio <?= icono('flecha') ?></a></div>
                <div class="tarjeta tarjeta__cuerpo"><h3>Herramientas</h3><p class="texto-suave" style="font-size:.9rem;">Dilución, precio, secado, rentabilidad.</p><a class="enlace-flecha" href="<?= url('/calculadoras') ?>">Calculadoras <?= icono('flecha') ?></a></div>
            </div>
        <?php else: ?>
            <?php foreach ($secciones as $clave => $meta): ?>
                <?php if (empty($resultados[$clave])) continue; ?>
                <section class="mb-4">
                    <div class="flex-entre mb-2">
                        <h2 style="font-size:1.35rem;margin:0;"><?= e($meta['titulo']) ?></h2>
                        <span class="etiqueta"><?= count($resultados[$clave]) ?> resultado<?= count($resultados[$clave]) === 1 ? '' : 's' ?></span>
                    </div>
                    <div class="tarjeta" style="padding:0 20px;">
                        <?php foreach ($resultados[$clave] as $item): ?>
                            <?php
                            $url = match ($clave) {
                                'guias' => url('/guias/' . $item['slug']),
                                'publicaciones' => url('/comunidad/' . $item['slug']),
                                'negocios' => url('/directorio/' . $item['slug']),
                                'servicios' => url('/servicios'),
                                'productos' => url('/productos'),
                                default => url('/'),
                            };
                            $titulo = $item['titulo'] ?? $item['nombre'];
                            $texto = $item['resumen'] ?? $item['descripcion'] ?? ($item['ciudad'] ?? '');
                            ?>
                            <div class="resultado-busca">
                                <span class="etiqueta <?= $clave === 'negocios' ? 'etiqueta--teal' : '' ?>"><?= icono($meta['icono']) ?> <?= e($meta['etiqueta']) ?></span>
                                <div>
                                    <h3><a href="<?= e($url) ?>"><?= e($titulo) ?></a></h3>
                                    <p><?= e(limitar_texto((string) $texto, 160)) ?></p>
                                    <?php if ($clave === 'negocios'): ?><p class="texto-tenue" style="font-size:.78rem;"><?= e($item['ciudad']) ?>, <?= e($item['estado_geo']) ?></p><?php endif; ?>
                                    <?php if ($clave === 'servicios' || $clave === 'productos'): ?><p class="texto-tenue" style="font-size:.8rem;"><?= precio($item['precio']) ?></p><?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endforeach; ?>

            <?php if ($total === 0): ?>
                <div class="tarjeta tarjeta__cuerpo texto-centro">
                    <h3>Sin resultados</h3>
                    <p class="texto-suave">Prueba con otra palabra o explora las secciones principales.</p>
                    <div class="flex-centro" style="justify-content:center;">
                        <a class="boton" href="<?= url('/guias') ?>">Ver guías</a>
                        <a class="boton boton--contorno" href="<?= url('/directorio') ?>">Ver directorio</a>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>
