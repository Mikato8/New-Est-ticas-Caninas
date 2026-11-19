<?php
declare(strict_types=1);
$whatsapp = whatsapp_link();
$hayCatalogo = $hayCatalogo ?? false;
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

<?php if (!$hayCatalogo): ?>
<section class="seccion seccion--compacta">
    <div class="contenedor">
        <div class="nota nota--teal">
            <strong>Catálogo en línea.</strong> En este momento no pudimos leer el catálogo de la tienda.
            Míralo directo en <a href="https://mikato.com.mx/tienda" target="_blank" rel="noopener">mikato.com.mx</a>
            o escríbenos por WhatsApp y te lo enviamos.
        </div>
    </div>
</section>
<?php endif; ?>

<?php foreach ($agrupados as $categoria => $lista): ?>
<section class="seccion seccion--compacta">
    <div class="contenedor">
        <div class="encabezado-seccion">
            <span class="mono texto-oro"><?= e((string) $categoria) ?></span>
            <h2><?= e((string) $categoria) ?></h2>
        </div>
        <div class="grid grid--4">
            <?php foreach ($lista as $producto): ?>
                <?php
                $enlace = (string) $producto['url'];
                $imagen = (string) ($producto['imagen'] ?? '');
                $respaldo = asset('/assets/img/producto-botella.svg');
                ?>
                <article class="tarjeta tarjeta-producto">
                    <a class="tarjeta-producto__imagen" href="<?= e($enlace) ?>" target="_blank" rel="noopener">
                        <img src="<?= e($imagen !== '' ? $imagen : $respaldo) ?>" alt="<?= e((string) $producto['nombre']) ?>" width="480" height="480" loading="lazy" onerror="this.onerror=null;this.src='<?= e($respaldo) ?>';">
                        <?php if (!empty($producto['agotado'])): ?>
                            <span class="etiqueta" style="position:absolute;top:12px;left:12px;">Agotado</span>
                        <?php endif; ?>
                    </a>
                    <div class="tarjeta-producto__cuerpo">
                        <h3><a href="<?= e($enlace) ?>" target="_blank" rel="noopener" style="text-decoration:none;"><?= e((string) $producto['nombre']) ?></a></h3>
                        <?php if (trim((string) $producto['descripcion']) !== ''): ?>
                            <p><?= e(limitar_texto((string) $producto['descripcion'], 100)) ?></p>
                        <?php endif; ?>
                        <div class="tarjeta-producto__precio">
                            <strong><?= precio($producto['precio']) ?></strong>
                        </div>
                        <a class="boton boton--contorno boton--grande mt-2" href="<?= e($enlace) ?>" target="_blank" rel="noopener">Comprar en la tienda <?= icono('flecha') ?></a>
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
