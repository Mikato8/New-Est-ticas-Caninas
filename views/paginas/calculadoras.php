<?php
declare(strict_types=1);
?>
<div class="contenedor">
    <nav class="migas" aria-label="Ruta"><a href="<?= url('/') ?>">Inicio</a><span>/</span>Calculadoras</nav>
    <header class="portada-interior">
        <span class="mono texto-oro">Herramientas gratuitas</span>
        <h1>Números claros para trabajar mejor.</h1>
        <p>Cuatro calculadoras para el salón: cuánto shampoo y agua mezclar, cuánto cobrar, cuánto tarda el secado y cuánto ganas de verdad por servicio.</p>
    </header>
</div>

<section class="seccion seccion--compacta">
    <div class="contenedor">
        <div class="grid grid--2">
            <?php foreach ($calculadoras as $clave => $calc): ?>
                <a class="tarjeta tarjeta__cuerpo tarjeta--enlace" href="<?= url('/calculadoras/' . $clave) ?>" style="text-decoration:none;">
                    <div class="flex-centro" style="align-items:flex-start;gap:16px;">
                        <span class="tarjeta-servicio__icono" style="margin:0;flex-shrink:0;"><?= icono($calc['icono']) ?></span>
                        <div>
                            <h3 style="margin-bottom:6px;"><?= e($calc['titulo']) ?></h3>
                            <p class="texto-suave" style="font-size:.92rem;"><?= e($calc['descripcion']) ?></p>
                            <span class="enlace-flecha">Abrir calculadora <?= icono('flecha') ?></span>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="seccion seccion--crema">
    <div class="contenedor">
        <div class="split">
            <div>
                <span class="mono texto-oro">Cómo usarlas</span>
                <h2>Son guías, no promesas.</h2>
                <p class="texto-suave">Cada calculadora parte de supuestos que puedes cambiar. Los resultados son un punto de partida para conversar con tu equipo, tu contador o tu veterinario, no una verdad absoluta.</p>
                <ul class="sin-lista" style="display:grid;gap:9px;margin-top:18px;">
                    <li class="flex-centro"><?= icono('check', 'texto-teal') ?> Ajusta los campos a tus costos reales.</li>
                    <li class="flex-centro"><?= icono('check', 'texto-teal') ?> Revisa precios cada trimestre.</li>
                    <li class="flex-centro"><?= icono('check', 'texto-teal') ?> No incluyas datos personales de clientes.</li>
                    <li class="flex-centro"><?= icono('check', 'texto-teal') ?> Usa la ficha del fabricante para diluir.</li>
                </ul>
            </div>
            <div class="tarjeta tarjeta__cuerpo">
                <h3>¿Buscas una calculadora que no está?</h3>
                <p class="texto-suave">Escríbenos y la evaluamos. Si es útil para varias estéticas, la publicamos con tu crédito.</p>
                <a class="boton boton--bloque" href="<?= url('/contacto') ?>">Sugerir una herramienta</a>
            </div>
        </div>
    </div>
</section>
