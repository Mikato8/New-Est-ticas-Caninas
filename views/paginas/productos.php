<?php
declare(strict_types=1);
$whatsapp = whatsapp_link();
$hayCatalogo = $hayCatalogo ?? false;

$visibles = [];
$sinFoto = 0;
foreach ($agrupados as $categoria => $lista) {
    foreach ($lista as $producto) {
        if ((string) ($producto['imagen'] ?? '') !== '') {
            $visibles[(string) $categoria][] = $producto;
        } else {
            $sinFoto++;
        }
    }
}
$totalVisibles = array_sum(array_map('count', $visibles));
?>
<div class="contenedor">
    <nav class="migas" aria-label="Ruta"><a href="<?= url('/') ?>">Inicio</a><span>/</span>Productos</nav>
    <header class="portada-interior">
        <span class="kicker mono texto-oro">Fabricantes mexicanos</span>
        <h1>Productos Mikato para el salón.</h1>
        <p>Shampoos, acondicionadores, aromas y herramientas que usan las estéticas. Los elaboramos en Guadalajara, Jalisco y los probamos con estilistas en el día a día del salón.</p>
        <div class="flex-centro mt-3">
            <a class="t-btn t-btn--oro" href="<?= url('/contacto') ?>">Pedir catálogo o mayoreo</a>
            <a class="t-btn t-btn--linea" href="https://mikato.com.mx" target="_blank" rel="noopener">Ver tienda mikato.com.mx <?= icono('flecha') ?></a>
            <?php if ($whatsapp !== ''): ?><a class="t-btn t-btn--linea" href="<?= e($whatsapp) ?>" target="_blank" rel="noopener"><?= icono('whatsapp') ?> WhatsApp</a><?php endif; ?>
        </div>
    </header>
</div>

<section class="t-seccion t-seccion--ajustada">
    <div class="contenedor">
        <div class="t-guias" style="grid-template-columns:repeat(3,minmax(0,1fr));">
            <div class="t-modulo">
                <div class="t-modulo__icono"><?= icono('caja') ?></div>
                <h3>Para estilistas</h3>
                <p>Precios de mayoreo disponibles. Escríbenos con el volumen que manejas y tu ciudad.</p>
            </div>
            <div class="t-modulo">
                <div class="t-modulo__icono"><?= icono('escudo') ?></div>
                <h3>Uso profesional</h3>
                <p>Respeta la dilución y la ficha técnica de cada producto. No reenvases en envases de alimentos o limpieza.</p>
            </div>
            <div class="t-modulo">
                <div class="t-modulo__icono"><?= icono('pin') ?></div>
                <h3>Envíos en México</h3>
                <p>Coordinamos entrega según destino y volumen. El costo se cotiza con tu pedido.</p>
            </div>
        </div>
    </div>
</section>

<?php if (!$hayCatalogo): ?>
    <section class="t-seccion t-seccion--ajustada">
        <div class="contenedor">
            <div class="nota nota--teal">
                <strong>Catálogo en línea.</strong> En este momento no pudimos leer el catálogo de la tienda.
                Míralo directo en <a href="https://mikato.com.mx" target="_blank" rel="noopener">mikato.com.mx</a>
                o escríbenos por WhatsApp y te lo enviamos.
            </div>
        </div>
    </section>
<?php endif; ?>

<?php if ($totalVisibles > 0): ?>
    <?php
    $listaPlana = [];
    foreach ($visibles as $categoria => $lista) {
        foreach ($lista as $producto) {
            $producto['categoria'] = $categoria;
            $listaPlana[] = $producto;
        }
    }
    ?>
    <section class="t-seccion t-seccion--ajustada">
        <div class="contenedor">
            <div class="t-seccion__cabeza">
                <div>
                    <span class="t-kicker">Catálogo</span>
                    <h2>Lo que tenemos hoy en el salón.</h2>
                    <p>Precios de lista en pesos mexicanos. Para mayoreo, escríbenos con tu volumen y tu ciudad.</p>
                </div>
                <a href="https://mikato.com.mx" target="_blank" rel="noopener">Ver la tienda completa</a>
            </div>
            <div class="t-productos">
                <?php foreach ($listaPlana as $producto): ?>
                    <?php $enlace = (string) $producto['url']; ?>
                    <article class="t-producto">
                        <a class="t-producto__media" href="<?= e($enlace) ?>" target="_blank" rel="noopener">
                            <img src="<?= e((string) $producto['imagen']) ?>" alt="<?= e((string) $producto['nombre']) ?>" width="480" height="480" loading="lazy">
                            <?php if (!empty($producto['agotado'])): ?>
                                <span class="t-insignia" style="position:absolute;top:10px;left:10px;background:var(--t-azul);">Agotado</span>
                            <?php endif; ?>
                        </a>
                        <div class="t-producto__cuerpo">
                            <p class="t-producto__marca"><?= e((string) $producto['categoria']) ?></p>
                            <h3 class="t-producto__nombre"><a href="<?= e($enlace) ?>" target="_blank" rel="noopener"><?= e((string) $producto['nombre']) ?></a></h3>
                            <div class="t-producto__precio"><?= precio((float) $producto['precio']) ?><span>Precio en pesos mexicanos</span></div>
                        </div>
                        <div class="t-producto__accion">
                            <a class="t-btn t-btn--linea t-btn--chico" href="<?= e($enlace) ?>" target="_blank" rel="noopener">Comprar en la tienda</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
            <?php if ($sinFoto > 0): ?>
                <p style="font-size:13px;color:var(--t-tinta-3);margin:18px 0 0;"><?= (int) $sinFoto ?> productos más están disponibles en <a href="https://mikato.com.mx" target="_blank" rel="noopener">mikato.com.mx</a> con su ficha completa.</p>
            <?php endif; ?>
        </div>
    </section>
<?php endif; ?>

<section class="t-seccion">
    <div class="contenedor">
        <div class="t-banda-final">
            <div>
                <h2>¿Tienes una estética o distribuyes en tu ciudad?</h2>
                <p>Cuéntanos qué necesitas y te armamos una propuesta. Atendemos estilistas, estéticas y distribuidores en México.</p>
            </div>
            <div class="t-banda-final__acciones">
                <a class="t-btn t-btn--azul" href="<?= url('/contacto') ?>">Pedir información</a>
                <a class="t-btn t-btn--linea" href="<?= url('/nosotros') ?>">Conocer Mikato</a>
            </div>
        </div>
    </div>
</section>
