<?php
declare(strict_types=1);
$iconos = ['bano' => 'bano', 'corte' => 'tijeras', 'salud' => 'escudo', 'manejo' => 'corazon', 'negocio' => 'dinero'];
?>
<div class="contenedor">
    <nav class="migas" aria-label="Ruta"><a href="<?= url('/') ?>">Inicio</a><span>/</span>Guías</nav>
    <header class="portada-interior">
        <span class="mono texto-oro">Guías de oficio</span>
        <h1>Aprender estética canina, con las manos y con criterio.</h1>
        <p>Doce guías para el día a día del salón: baño, corte, uñas, oídos, secado, manejo, higiene, razas, precios y marco legal. Escritas para estilistas de México, con fuentes y sin mitos.</p>
    </header>
</div>

<section class="seccion seccion--compacta">
    <div class="contenedor">
        <div class="flex-entre mb-3">
            <div class="filtros">
                <a class="chip <?= $categoria === '' ? 'chip--activo' : '' ?>" href="<?= url('/guias') ?>">Todas</a>
                <?php foreach ($categorias as $clave => $etiqueta): ?>
                    <a class="chip <?= $categoria === $clave ? 'chip--activo' : '' ?>" href="<?= url('/guias?categoria=' . urlencode($clave)) ?>"><?= e($etiqueta) ?></a>
                <?php endforeach; ?>
            </div>
            <form class="buscador" method="get" action="<?= url('/guias') ?>">
                <?= icono('buscar') ?>
                <label class="sr-only" for="q-guias">Buscar en guías</label>
                <input id="q-guias" type="search" name="q" value="<?= e($busqueda) ?>" placeholder="Buscar: secado, uñas, precio…">
                <?php if ($categoria !== ''): ?><input type="hidden" name="categoria" value="<?= e($categoria) ?>"><?php endif; ?>
                <button type="submit">Buscar</button>
            </form>
        </div>

        <div class="grid grid--3">
            <?php foreach ($guias as $guia): ?>
                <article class="tarjeta tarjeta--enlace tarjeta-guia">
                    <div class="tarjeta-guia__portada tarjeta-guia__portada--<?= e($guia['categoria']) ?>">
                        <?= icono($iconos[$guia['categoria']] ?? 'libro') ?>
                        <span class="etiqueta etiqueta--oscura"><?= e($categorias[$guia['categoria']] ?? $guia['categoria']) ?></span>
                    </div>
                    <div class="tarjeta-guia__cuerpo">
                        <h3><a href="<?= url('/guias/' . $guia['slug']) ?>"><?= e($guia['titulo']) ?></a></h3>
                        <p><?= e($guia['resumen']) ?></p>
                        <div class="tarjeta-guia__meta">
                            <span><?= e($guia['nivel']) ?></span>
                            <span><?= (int) $guia['minutos'] ?> min de lectura</span>
                        </div>
                        <a class="enlace-flecha mt-2" href="<?= url('/guias/' . $guia['slug']) ?>">Leer guía <?= icono('flecha') ?></a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <?php if ($guias === []): ?>
            <div class="tarjeta tarjeta__cuerpo texto-centro">
                <h3>No encontramos guías con esa búsqueda</h3>
                <p class="texto-suave">Prueba con “baño”, “uñas”, “secado” o “precio”.</p>
                <a class="boton" href="<?= url('/guias') ?>">Ver todas las guías</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="seccion seccion--crema">
    <div class="contenedor">
        <div class="cta">
            <div>
                <span class="mono texto-oro">Herramientas</span>
                <h2>De la lectura a los números.</h2>
                <p>Calcula la dilución de tu shampoo, el precio de un servicio, el tiempo de secado y la rentabilidad real de tu estética.</p>
            </div>
            <div class="cta__acciones">
                <a class="boton boton--oro boton--grande" href="<?= url('/calculadoras') ?>">Abrir calculadoras</a>
                <a class="boton boton--fantasma boton--grande" href="<?= url('/asistente') ?>">Preguntar al asistente</a>
            </div>
        </div>
    </div>
</section>
