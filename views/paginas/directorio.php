<?php
declare(strict_types=1);
?>
<div class="contenedor">
    <nav class="migas" aria-label="Ruta"><a href="<?= url('/') ?>">Inicio</a><span>/</span>Directorio</nav>
    <header class="portada-interior">
        <span class="mono texto-oro">Directorio nacional</span>
        <h1>Estéticas que cuidan como tú.</h1>
        <p>Encuentra estilistas y estéticas caninas por estado. Cada ficha pasa por revisión antes de publicarse. Si tienes una estética, regístrala gratis.</p>
        <div class="flex-centro mt-3">
            <a class="boton boton--oro" href="<?= url('/directorio/registrar') ?>"><?= icono('directorio') ?> Registrar mi estética</a>
            <a class="boton boton--contorno" href="<?= url('/contacto') ?>">Sugerir una estética</a>
        </div>
    </header>
</div>

<section class="seccion seccion--compacta">
    <div class="contenedor">
        <div class="flex-entre mb-3">
            <div class="filtros">
                <a class="chip <?= $estadoGeo === '' ? 'chip--activo' : '' ?>" href="<?= url('/directorio') ?>">Todo México</a>
                <?php foreach ($estados as $estado): ?>
                    <a class="chip <?= $estadoGeo === $estado['estado_geo'] ? 'chip--activo' : '' ?>" href="<?= url('/directorio?estado=' . urlencode((string) $estado['estado_geo'])) ?>"><?= e($estado['estado_geo']) ?> <small><?= (int) $estado['total'] ?></small></a>
                <?php endforeach; ?>
            </div>
            <form class="buscador" method="get" action="<?= url('/directorio') ?>">
                <?= icono('buscar') ?>
                <label class="sr-only" for="q-directorio">Buscar estética</label>
                <input id="q-directorio" type="search" name="q" value="<?= e($busqueda) ?>" placeholder="Ciudad, nombre o servicio…">
                <?php if ($estadoGeo !== ''): ?><input type="hidden" name="estado" value="<?= e($estadoGeo) ?>"><?php endif; ?>
                <button type="submit">Buscar</button>
            </form>
        </div>

        <p class="texto-tenue" style="font-size:.84rem;"><?= (int) $total ?> ficha<?= $total === 1 ? '' : 's' ?> publicada<?= $total === 1 ? '' : 's' ?><?= $pendientes > 0 ? ' · ' . (int) $pendientes . ' en revisión' : '' ?>.</p>

        <div class="grid grid--3 mt-2">
            <?php foreach ($negocios as $negocio): ?>
                <article class="tarjeta tarjeta--enlace tarjeta-negocio">
                    <div class="tarjeta-negocio__cabeza">
                        <?php if ($negocio['logo']): ?>
                            <img src="<?= e($negocio['logo']) ?>" alt="" style="width:52px;height:52px;border-radius:12px;object-fit:cover;">
                        <?php else: ?>
                            <span class="avatar avatar--grande avatar--oro"><?= e(iniciales((string) $negocio['nombre'])) ?></span>
                        <?php endif; ?>
                        <div>
                            <h3><a href="<?= url('/directorio/' . $negocio['slug']) ?>" style="text-decoration:none;"><?= e($negocio['nombre']) ?></a></h3>
                            <span class="tarjeta-negocio__ciudad"><?= e($negocio['ciudad']) ?>, <?= e($negocio['estado_geo']) ?></span>
                        </div>
                    </div>
                    <p><?= e(limitar_texto((string) $negocio['descripcion'], 120)) ?></p>
                    <?php if ($negocio['servicios']): ?>
                        <div class="filtros">
                            <?php foreach (array_slice(explode(',', (string) $negocio['servicios']), 0, 3) as $servicio): ?>
                                <span class="etiqueta"><?= e(trim($servicio)) ?></span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <div class="tarjeta-negocio__pie">
                        <span>
                            <?php if (es_verdadero($negocio['verificado'])): ?><span class="verificado"><?= icono('escudo') ?> Verificada</span><?php endif; ?>
                            <?php if (es_verdadero($negocio['es_demo'])): ?><span class="etiqueta etiqueta--coral">Ejemplo</span><?php endif; ?>
                        </span>
                        <a class="enlace-flecha" href="<?= url('/directorio/' . $negocio['slug']) ?>">Ver ficha <?= icono('flecha') ?></a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <?php if ($negocios === []): ?>
            <div class="tarjeta tarjeta__cuerpo texto-centro">
                <h3>No hay fichas con esos filtros</h3>
                <p class="texto-suave">Prueba con otro estado o registra tu estética. Revisamos cada ficha antes de publicarla.</p>
                <a class="boton" href="<?= url('/directorio/registrar') ?>">Registrar mi estética</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="seccion seccion--crema">
    <div class="contenedor">
        <div class="split">
            <div>
                <span class="mono texto-oro">Para estilistas</span>
                <h2>Tu ficha, tu vitrina.</h2>
                <p class="texto-suave">Publica tus servicios, horario y forma de contacto. Las fichas se revisan para evitar datos falsos y mantener la confianza del directorio.</p>
                <div class="pasos mt-3">
                    <div class="paso"><div><h3>Crea tu cuenta</h3><p>Gratis. La necesitas para administrar tu ficha.</p></div></div>
                    <div class="paso"><div><h3>Llena tus datos</h3><p>Ciudad, servicios, horario y WhatsApp. Sin datos inventados.</p></div></div>
                    <div class="paso"><div><h3>Espera la revisión</h3><p>Verificamos la información. Si falta algo, te escribimos.</p></div></div>
                </div>
            </div>
            <div class="tarjeta tarjeta__cuerpo">
                <h3>Lo que no hacemos</h3>
                <ul class="sin-lista" style="display:grid;gap:9px;">
                    <li class="flex-centro"><?= icono('cerrar', 'texto-coral') ?> Inventar fichas o reseñas.</li>
                    <li class="flex-centro"><?= icono('cerrar', 'texto-coral') ?> Publicar teléfonos sin permiso.</li>
                    <li class="flex-centro"><?= icono('cerrar', 'texto-coral') ?> Cobrar por aparecer en el directorio.</li>
                    <li class="flex-centro"><?= icono('cerrar', 'texto-coral') ?> Vender datos de contacto.</li>
                </ul>
                <div class="nota nota--teal mt-3" style="font-size:.84rem;">Si detectas una ficha falsa o un mal servicio, escríbenos. Revisamos y, si hace falta, la retiramos.</div>
            </div>
        </div>
    </div>
</section>
