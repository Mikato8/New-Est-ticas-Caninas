<?php
declare(strict_types=1);
?>
<div class="contenedor">
    <nav class="migas" aria-label="Ruta"><a href="<?= url('/') ?>">Inicio</a><span>/</span>Herramientas y fuentes</nav>
    <header class="portada-interior">
        <span class="mono texto-oro">Transparencia</span>
        <h1>Herramientas abiertas y fuentes.</h1>
        <p>Este portal se construyó con investigación propia y referencias abiertas. Aquí están los repositorios, datasets y guías que revisamos, con su licencia y para qué sirven.</p>
    </header>
</div>

<section class="seccion seccion--compacta">
    <div class="contenedor">
        <div class="nota nota--teal mb-3">
            <strong>Cómo reutilizamos.</strong> La arquitectura PHP está inspirada en <a href="https://github.com/Mikato8/Electricistas" target="_blank" rel="noopener">Electricistas MX</a> del mismo autor. No copiamos código de proyectos con licencias incompatibles (GPL/AGPL); solo tomamos ideas y enlaces. Cada recurso conserva su licencia.
        </div>

        <div class="grid grid--2">
            <?php foreach ($recursos as $recurso): ?>
                <article class="tarjeta tarjeta__cuerpo">
                    <div class="flex-entre" style="align-items:flex-start;">
                        <div>
                            <span class="etiqueta <?= $recurso['reutilizable'] ? 'etiqueta--teal' : 'etiqueta--coral' ?>"><?= $recurso['reutilizable'] ? 'Reutilizable' : 'Solo referencia' ?></span>
                            <h3 style="font-size:1.1rem;margin:8px 0 2px;"><?= e($recurso['nombre']) ?></h3>
                            <span class="texto-tenue" style="font-size:.78rem;"><?= e($recurso['tipo']) ?> · <?= e($recurso['licencia']) ?></span>
                        </div>
                        <a class="boton boton--pequeno boton--contorno" href="<?= e($recurso['url']) ?>" target="_blank" rel="noopener">Abrir</a>
                    </div>
                    <p class="texto-suave mt-2 mb-0" style="font-size:.88rem;"><?= e($recurso['uso']) ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="seccion seccion--crema">
    <div class="contenedor">
        <div class="split">
            <div>
                <span class="mono texto-oro">Fuentes técnicas</span>
                <h2>Veterinaria y bienestar.</h2>
                <p class="texto-suave">Las guías de salud, secado, uñas, oídos y manejo se contrastaron con estas organizaciones. Donde no hay una cifra oficial (por ejemplo, temperatura exacta de secado), lo decimos y usamos criterios profesionales conservadores.</p>
                <div class="grid grid--2 mt-3">
                    <?php foreach ($fuentes as $fuente): ?>
                        <a class="tarjeta tarjeta__cuerpo tarjeta--enlace" href="<?= e($fuente['url']) ?>" target="_blank" rel="noopener" style="text-decoration:none;">
                            <strong style="font-size:.92rem;"><?= e($fuente['nombre']) ?></strong>
                            <span class="enlace-flecha" style="font-size:.78rem;margin-top:8px;">Visitar <?= icono('flecha') ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="tarjeta tarjeta__cuerpo">
                <h3>Lo que no hacemos</h3>
                <ul class="sin-lista" style="display:grid;gap:9px;font-size:.9rem;color:var(--tinta-suave);">
                    <li class="flex-centro"><?= icono('cerrar', 'texto-coral') ?> Inventar normas o trámites.</li>
                    <li class="flex-centro"><?= icono('cerrar', 'texto-coral') ?> Presentar un modelo pequeño como veterinario.</li>
                    <li class="flex-centro"><?= icono('cerrar', 'texto-coral') ?> Usar datos de clientes para entrenar IA.</li>
                    <li class="flex-centro"><?= icono('cerrar', 'texto-coral') ?> Vender un “certificado NOM” que no existe.</li>
                </ul>
                <div class="nota mt-3" style="font-size:.84rem;">La investigación completa, con fechas de consulta y notas de verificación, está en <a href="<?= url('/buenas-practicas') ?>">buenas prácticas</a> y en el repositorio del proyecto.</div>
            </div>
        </div>
    </div>
</section>
