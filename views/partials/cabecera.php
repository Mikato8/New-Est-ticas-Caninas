<?php
declare(strict_types=1);
$usuario = $usuario ?? null;
$whatsapp = whatsapp_link();
$cuentaUrl = $usuario !== null
    ? url((string) $usuario['rol'] === 'cliente' ? '/mi-cuenta' : '/panel')
    : url('/entrar');
?>
<div class="t-utilidad">
    <div class="contenedor">
        <span><span class="t-utilidad__punto" aria-hidden="true"></span>Hecho en Guadalajara, Jalisco</span>
        <span class="t-utilidad__largo"><a href="<?= url('/descargar') ?>">Descarga el CRM para macOS</a></span>
        <?php if ($whatsapp !== ''): ?>
            <a href="<?= e($whatsapp) ?>" target="_blank" rel="noopener">WhatsApp</a>
        <?php endif; ?>
    </div>
</div>
<header class="t-cabecera" id="cabecera">
    <div class="contenedor t-cabecera__fila">
        <a class="t-marca" href="<?= url('/') ?>" aria-label="Mikato Estilistas Caninos, inicio">
            <img src="<?= asset(negocio_logo()) ?>" alt="Mikato">
            <span class="t-marca__texto">
                <strong>Estilistas Caninos</strong>
                <small>CRM · productos · Guadalajara</small>
            </span>
        </a>

        <form class="t-buscador" method="get" action="<?= url('/buscar') ?>" role="search">
            <label class="sr-only" for="buscar-sitio">Buscar en el sitio</label>
            <input id="buscar-sitio" type="search" name="q" placeholder="Buscar en el sitio" autocomplete="off">
            <button type="submit" aria-label="Buscar"><?= icono('buscar') ?></button>
        </form>

        <div class="t-acciones">
            <?php if ($whatsapp !== ''): ?>
                <a class="t-accion t-accion--wa" href="<?= e($whatsapp) ?>" target="_blank" rel="noopener" aria-label="Escribir por WhatsApp">
                    <?= icono('whatsapp') ?><span class="t-accion__texto">WhatsApp</span>
                </a>
            <?php endif; ?>
            <a class="t-btn t-btn--azul t-btn--chico" href="<?= url('/descargar') ?>"><?= icono('descargar') ?> Descargar CRM</a>
            <?php if ($usuario !== null): ?>
                <a class="t-btn t-btn--linea t-btn--chico" href="<?= $cuentaUrl ?>"><?= e(iniciales((string) $usuario['nombre'])) ?></a>
            <?php else: ?>
                <a class="t-btn t-btn--linea t-btn--chico" href="<?= url('/entrar') ?>">Entrar</a>
            <?php endif; ?>
            <a class="t-btn t-btn--oro t-btn--chico t-accion--texto" href="<?= url('/contacto') ?>">Solicitar demo</a>
        </div>
    </div>
    <nav class="t-navcat" aria-label="Navegación principal">
        <div class="contenedor">
            <ul>
                <li><a class="<?= activo('/servicios') ?>" href="<?= url('/servicios') ?>">CRM</a></li>
                <li><a class="<?= activo('/guias') ?>" href="<?= url('/guias') ?>">Guías</a></li>
                <li><a class="<?= activo('/directorio') ?>" href="<?= url('/directorio') ?>">Directorio</a></li>
                <li><a class="<?= activo('/calculadoras') ?>" href="<?= url('/calculadoras') ?>">Calculadoras</a></li>
                <li><a class="<?= activo('/productos') ?>" href="<?= url('/productos') ?>">Productos</a></li>
                <li><a class="<?= activo('/comunidad') ?>" href="<?= url('/comunidad') ?>">Comunidad</a></li>
                <li><a class="<?= activo('/descargar') ?>" href="<?= url('/descargar') ?>">Descargar</a></li>
                <li><a class="<?= activo('/nosotros') ?>" href="<?= url('/nosotros') ?>">Nosotros</a></li>
            </ul>
        </div>
    </nav>
</header>
