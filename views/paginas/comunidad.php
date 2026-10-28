<?php
declare(strict_types=1);
use App\Domain\Contenido\Guias;
?>
<div class="contenedor">
    <nav class="migas" aria-label="Ruta"><a href="<?= url('/') ?>">Inicio</a><span>/</span>Comunidad</nav>
    <header class="portada-interior">
        <span class="mono texto-oro">Comunidad de estilistas</span>
        <h1>El salón también se comparte.</h1>
        <p>Casos, técnicas, dudas y experiencias de estilistas caninos de México. Publicaciones moderadas para que la conversación sea útil y respetuosa.</p>
        <div class="flex-centro mt-3">
            <a class="boton boton--oro" href="<?= url('/comunidad/publicar') ?>"><?= icono('editar') ?> Publicar un caso</a>
            <a class="boton boton--contorno" href="<?= url('/guias') ?>">Leer guías</a>
        </div>
    </header>
</div>

<section class="seccion seccion--compacta">
    <div class="contenedor">
        <div class="flex-entre mb-3">
            <div class="filtros">
                <a class="chip <?= $categoria === '' ? 'chip--activo' : '' ?>" href="<?= url('/comunidad') ?>">Todo</a>
                <?php foreach ($categorias as $clave => $etiqueta): ?>
                    <a class="chip <?= $categoria === $clave ? 'chip--activo' : '' ?>" href="<?= url('/comunidad?categoria=' . urlencode($clave)) ?>"><?= e($etiqueta) ?> <small><?= (int) ($conteos[$clave] ?? 0) ?></small></a>
                <?php endforeach; ?>
            </div>
            <form class="buscador" method="get" action="<?= url('/comunidad') ?>">
                <?= icono('buscar') ?>
                <label class="sr-only" for="q-comunidad">Buscar en la comunidad</label>
                <input id="q-comunidad" type="search" name="q" value="<?= e($busqueda) ?>" placeholder="Buscar casos, razas, técnicas…">
                <button type="submit">Buscar</button>
            </form>
        </div>

        <div class="grid grid--3">
            <?php foreach ($publicaciones as $publicacion): ?>
                <article class="tarjeta tarjeta--enlace tarjeta-post">
                    <?php if ($publicacion['imagen']): ?>
                        <img src="<?= e($publicacion['imagen']) ?>" alt="" style="width:100%;height:170px;object-fit:cover;" loading="lazy">
                    <?php endif; ?>
                    <div class="tarjeta-post__meta">
                        <span class="etiqueta etiqueta--teal"><?= e($categorias[$publicacion['categoria']] ?? $publicacion['categoria']) ?></span>
                        <span><?= e(fecha($publicacion['creado_en'], 'd M Y')) ?></span>
                    </div>
                    <h3><a href="<?= url('/comunidad/' . $publicacion['slug']) ?>"><?= e($publicacion['titulo']) ?></a></h3>
                    <p><?= e(limitar_texto((string) ($publicacion['resumen'] ?: strip_tags($publicacion['contenido'])), 130)) ?></p>
                    <div class="tarjeta-post__pie">
                        <span class="flex-centro"><span class="avatar" style="width:26px;height:26px;font-size:.65rem;"><?= e(iniciales((string) $publicacion['autor_nombre'])) ?></span> <?= e($publicacion['autor_nombre']) ?></span>
                        <span><?= (int) $publicacion['comentarios'] ?> respuestas · <?= (int) $publicacion['vistas'] ?> vistas</span>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <?php if ($publicaciones === []): ?>
            <div class="tarjeta tarjeta__cuerpo texto-centro">
                <h3>Esta categoría está esperando tu caso</h3>
                <p class="texto-suave">Comparte un antes y después, una técnica o una duda. La revisamos y la publicamos.</p>
                <a class="boton" href="<?= url('/comunidad/publicar') ?>">Publicar ahora</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="seccion seccion--crema">
    <div class="contenedor">
        <div class="split">
            <div>
                <span class="mono texto-oro">Cómo funciona</span>
                <h2>Un foro con oficio, no un grupo de Facebook.</h2>
                <ul class="sin-lista" style="display:grid;gap:11px;margin-top:18px;">
                    <li class="flex-centro"><?= icono('check', 'texto-teal') ?> Publica con tu cuenta; revisamos antes de publicar.</li>
                    <li class="flex-centro"><?= icono('check', 'texto-teal') ?> Sin datos personales de clientes ni fotos sin permiso.</li>
                    <li class="flex-centro"><?= icono('check', 'texto-teal') ?> Sin diagnósticos veterinarios ni venta agresiva.</li>
                    <li class="flex-centro"><?= icono('check', 'texto-teal') ?> Respeto: se puede discrepar sin descalificar.</li>
                    <li class="flex-centro"><?= icono('check', 'texto-teal') ?> Si hay riesgo para un perro, se deriva.</li>
                </ul>
                <a class="boton mt-2" href="<?= url('/registro') ?>">Crear cuenta para participar</a>
            </div>
            <div class="tarjeta tarjeta__cuerpo">
                <h3>Guías para empezar</h3>
                <div style="display:grid;gap:10px;">
                    <?php foreach (Guias::destacadas(3) as $guia): ?>
                        <a href="<?= url('/guias/' . $guia['slug']) ?>" class="flex-centro" style="justify-content:space-between;text-decoration:none;border-bottom:1px solid var(--borde);padding-bottom:9px;">
                            <span style="font-weight:600;font-size:.9rem;"><?= e($guia['titulo']) ?></span>
                            <span class="texto-tenue" style="font-size:.75rem;"><?= (int) $guia['minutos'] ?> min</span>
                        </a>
                    <?php endforeach; ?>
                </div>
                <a class="enlace-flecha mt-3" href="<?= url('/guias') ?>">Todas las guías <?= icono('flecha') ?></a>
            </div>
        </div>
    </div>
</section>
