<?php
declare(strict_types=1);
$arte = static function (string $categoria): string {
    return match ($categoria) {
        'perfume' => '/assets/img/producto-espray.svg',
        'cepillo' => '/assets/img/producto-cepillo.svg',
        'herramienta' => '/assets/img/producto-herramienta.svg',
        default => '/assets/img/producto-botella.svg',
    };
};
$whatsapp = whatsapp_link();
?>
<div class="contenedor">
    <nav class="migas" aria-label="Ruta"><a href="<?= url('/') ?>">Inicio</a><span>/</span>Productos</nav>
    <header class="portada-interior">
        <span class="mono texto-oro">Fabricantes mexicanos</span>
        <h1>Productos Mikato para el salón.</h1>
        <p>Shampoos, acondicionadores, aromas y herramientas que usan las estéticas. Los elaboramos en Guadalajara, Jalisco y los probamos con estilistas en el día a día del salón.</p>
        <div class="flex-centro mt-3">
            <a class="boton boton--oro" href="<?= url('/contacto') ?>">Pedir catálogo o mayoreo</a>
            <a class="boton boton--contorno" href="https://mikato.com.mx" target="_blank" rel="noopener">Ver tienda mikato.com.mx <?= icono('flecha') ?></a>
            <?php if ($whatsapp !== ''): ?><a class="boton boton--contorno" href="<?= e($whatsapp) ?>" target="_blank" rel="noopener"><?= icono('whatsapp') ?> WhatsApp</a><?php endif; ?>
        </div>
    </header>
</div>

<section class="seccion seccion--compacta">
    <div class="contenedor">
        <div class="grid grid--3">
            <div class="nota nota--teal"><strong>Para estilistas:</strong> precios de mayoreo disponibles. Escríbenos con el volumen que manejas y tu ciudad.</div>
            <div class="nota"><strong>Uso profesional:</strong> respeta la dilución y la ficha técnica de cada producto. No reenvases en envases de alimentos o limpieza.</div>
            <div class="nota nota--coral"><strong>Envíos:</strong> coordinamos entrega en México. El costo se cotiza según destino y volumen.</div>
        </div>
    </div>
</section>

<?php foreach ($agrupados as $categoria => $lista): ?>
<section class="seccion seccion--compacta">
    <div class="contenedor">
        <div class="encabezado-seccion">
            <span class="mono texto-oro"><?= e($categorias[$categoria] ?? ucfirst($categoria)) ?></span>
            <h2><?= e($categorias[$categoria] ?? ucfirst($categoria)) ?></h2>
        </div>
        <div class="grid grid--4">
            <?php foreach ($lista as $producto): ?>
                <article class="tarjeta tarjeta-producto">
                    <div class="tarjeta-producto__imagen">
                        <?php $imagenProducto = trim((string) ($producto['imagen'] ?? '')); ?>
                        <img src="<?= e($imagenProducto !== '' ? $imagenProducto : asset($arte((string) $producto['categoria']))) ?>" alt="<?= e($producto['nombre']) ?>" width="170" height="170" loading="lazy">
                        <?php if (es_verdadero($producto['destacado'])): ?><span class="etiqueta etiqueta--oro" style="position:absolute;top:12px;left:12px;">Destacado</span><?php endif; ?>
                    </div>
                    <div class="tarjeta-producto__cuerpo">
                        <h3><?= e($producto['nombre']) ?></h3>
                        <p><?= e(limitar_texto((string) $producto['descripcion'], 100)) ?></p>
                        <div class="tarjeta-producto__precio">
                            <strong><?= precio($producto['precio']) ?></strong>
                            <small><?= e((string) ($producto['presentacion'] ?? '')) ?></small>
                        </div>
                        <?php if ((float) ($producto['precio_mayoreo'] ?? 0) > 0): ?>
                            <p class="texto-tenue mt-1 mb-0" style="font-size:.75rem;">Mayoreo: <?= precio($producto['precio_mayoreo']) ?></p>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endforeach; ?>

<section class="seccion">
    <div class="contenedor">
        <div class="cta">
            <div>
                <span class="mono texto-oro">Mayoreo y distribución</span>
                <h2>¿Tienes una estética o distribuyes en tu ciudad?</h2>
                <p>Cuéntanos qué necesitas y te armamos una propuesta. Atendemos estilistas, estéticas y distribuidores en México.</p>
            </div>
            <div class="cta__acciones">
                <a class="boton boton--oro boton--grande" href="<?= url('/contacto') ?>">Pedir información</a>
                <a class="boton boton--fantasma boton--grande" href="<?= url('/nosotros') ?>">Conocer Mikato</a>
                <a class="boton boton--contorno boton--grande" href="https://mikato.com.mx" target="_blank" rel="noopener">Comprar en mikato.com.mx</a>
            </div>
        </div>
    </div>
</section>
