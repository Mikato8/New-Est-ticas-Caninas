<?php
declare(strict_types=1);
$usuario = $usuario ?? null;
$whatsapp = whatsapp_link();
?>
<div class="aviso-superior">
    <div class="contenedor">
        <span><span class="aviso-superior__punto"></span>Hecho en Guadalajara, Jalisco</span>
        <span class="aviso-superior__derecha">
            <?php if ($whatsapp !== ''): ?>
                <a href="<?= e($whatsapp) ?>" target="_blank" rel="noopener">WhatsApp</a>
            <?php else: ?>
                <a href="<?= url('/servicios') ?>">Demo del CRM</a>
            <?php endif; ?>
            <span>·</span>
            <a href="<?= url('/directorio') ?>">Directorio de estéticas</a>
        </span>
    </div>
</div>
<header class="cabecera" id="cabecera">
    <div class="contenedor cabecera__barra">
        <input type="checkbox" id="menu-principal" class="menu-check" aria-hidden="true">
        <a class="marca" href="<?= url('/') ?>" aria-label="Mikato Estilistas Caninos, inicio">
            <img src="<?= asset(negocio_logo()) ?>" alt="Mikato">
            <span class="marca__texto">
                <strong>Estilistas Caninos</strong>
                <small>CRM · productos · Guadalajara</small>
            </span>
        </a>

        <nav class="nav" id="nav-principal" aria-label="Navegación principal">
            <a class="<?= activo('/servicios') ?>" href="<?= url('/servicios') ?>">Servicios</a>
            <a class="<?= activo('/guias') ?>" href="<?= url('/guias') ?>">Guías</a>
            <a class="<?= activo('/comunidad') ?>" href="<?= url('/comunidad') ?>">Comunidad</a>
            <a class="<?= activo('/directorio') ?>" href="<?= url('/directorio') ?>">Directorio</a>
            <a class="<?= activo('/calculadoras') ?>" href="<?= url('/calculadoras') ?>">Calculadoras</a>
            <a class="<?= activo('/productos') ?>" href="<?= url('/productos') ?>">Productos</a>
            <a class="<?= activo('/nosotros') ?>" href="<?= url('/nosotros') ?>">Nosotros</a>
            <?php if ($usuario !== null): ?>
                <a class="nav__cuenta" href="<?= url((string) $usuario['rol'] === 'cliente' ? '/mi-cuenta' : '/panel') ?>">Mi cuenta</a>
            <?php else: ?>
                <a class="nav__cuenta" href="<?= url('/entrar') ?>">Entrar</a>
            <?php endif; ?>
        </nav>

        <div class="cabecera__acciones">
            <?php if ($usuario !== null): ?>
                <a class="boton boton--contorno boton--pequeno" href="<?= url((string) $usuario['rol'] === 'cliente' ? '/mi-cuenta' : '/panel') ?>">
                    <?= icono('perfil') ?> <?= e(iniciales((string) $usuario['nombre'])) ?>
                </a>
            <?php else: ?>
                <a class="boton boton--contorno boton--pequeno" href="<?= url('/entrar') ?>">Entrar</a>
            <?php endif; ?>
            <a class="boton boton--oro boton--pequeno boton--agenda" href="<?= url('/contacto') ?>"><?= icono('agenda') ?> Solicitar demo</a>
            <label class="menu-boton" for="menu-principal" tabindex="0" aria-label="Abrir menú">
                <?= icono('menu') ?>
            </label>
        </div>
    </div>
</header>
