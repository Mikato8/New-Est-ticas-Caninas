<?php
declare(strict_types=1);
use App\Domain\Contenido\Guias;
use App\Support\Markdown;
use App\Support\Slug;

$iconos = ['bano' => 'bano', 'corte' => 'tijeras', 'salud' => 'escudo', 'manejo' => 'corazon', 'negocio' => 'dinero'];
$categoriaEtiqueta = Guias::categorias()[$guia['categoria']] ?? $guia['categoria'];
?>
<div class="contenedor">
    <nav class="migas" aria-label="Ruta"><a href="<?= url('/') ?>">Inicio</a><span>/</span><a href="<?= url('/guias') ?>">Guías</a><span>/</span><?= e($categoriaEtiqueta) ?></nav>
    <header class="articulo__cabecera" style="padding-top:26px;">
        <span class="etiqueta etiqueta--teal"><?= e($categoriaEtiqueta) ?></span>
        <h1><?= e($guia['titulo']) ?></h1>
        <p class="texto-suave" style="font-size:1.08rem;max-width:70ch;"><?= e($guia['resumen']) ?></p>
        <div class="articulo__meta">
            <span>Nivel <?= e($guia['nivel']) ?></span>
            <span><?= (int) $guia['minutos'] ?> min de lectura</span>
            <span>Actualizada <?= e(fecha_larga($guia['actualizado'])) ?></span>
            <span>Equipo Mikato</span>
        </div>
    </header>
</div>

<div class="contenedor">
    <div class="articulo__portada">
        <div>
            <strong><?= e($guia['titulo']) ?></strong>
            <p><?= e($guia['resumen']) ?></p>
        </div>
        <?= icono($iconos[$guia['categoria']] ?? 'libro') ?>
    </div>
</div>

<section class="seccion seccion--compacta" style="padding-top:0;">
    <div class="contenedor">
        <div class="articulo">
            <article class="prosa">
                <?php foreach ($guia['secciones'] as $indice => $seccion): ?>
                    <section id="<?= e(Slug::make($seccion['titulo'])) ?>">
                        <h2><?= e($seccion['titulo']) ?></h2>
                        <?= Markdown::toHtml($seccion['md']) ?>
                    </section>
                <?php endforeach; ?>

                <?php if (!empty($guia['faq'])): ?>
                    <h2 id="preguntas-frecuentes">Preguntas frecuentes</h2>
                    <div class="faq">
                        <?php foreach ($guia['faq'] as $pregunta): ?>
                            <details>
                                <summary><?= e($pregunta['p']) ?></summary>
                                <p><?= e($pregunta['r']) ?></p>
                            </details>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="nota nota--teal mt-4">
                    <strong>Recuerda:</strong> esta guía es educativa. No sustituye la valoración de un médico veterinario ni la ficha técnica de tus productos. Si el perro tiene dolor, heridas, fiebre o dificultad para respirar, deriva.
                </div>

                <?php if (!empty($guia['fuentes'])): ?>
                    <h2>Fuentes y lecturas</h2>
                    <ul class="fuentes">
                        <?php foreach ($guia['fuentes'] as $fuente): ?>
                            <li><a href="<?= e($fuente['url']) ?>" target="_blank" rel="noopener noreferrer"><?= e($fuente['titulo']) ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </article>

            <aside class="aside-sticky">
                <div class="aside-caja">
                    <h3>En esta guía</h3>
                    <ul>
                        <?php foreach ($guia['secciones'] as $seccion): ?>
                            <li><a href="#<?= e(Slug::make($seccion['titulo'])) ?>"><?= e($seccion['titulo']) ?></a></li>
                        <?php endforeach; ?>
                        <li><a href="#preguntas-frecuentes">Preguntas frecuentes</a></li>
                    </ul>
                </div>
                <div class="aside-caja" style="background:var(--teal-claro);border-color:#b9d4c9;">
                    <h3>Calcula tu caso</h3>
                    <p style="font-size:.87rem;color:var(--teal);">Pasa de la teoría a tus números con las calculadoras de Mikato.</p>
                    <div style="display:grid;gap:7px;">
                        <a class="chip" href="<?= url('/calculadoras/dilucion') ?>">Dilución de shampoo</a>
                        <a class="chip" href="<?= url('/calculadoras/precio') ?>">Precio de servicio</a>
                        <a class="chip" href="<?= url('/calculadoras/secado') ?>">Tiempo de secado</a>
                    </div>
                </div>
                <?php if ($relacionadas !== []): ?>
                <div class="aside-caja">
                    <h3>Sigue leyendo</h3>
                    <ul>
                        <?php foreach ($relacionadas as $relacionada): ?>
                            <li><a href="<?= url('/guias/' . $relacionada['slug']) ?>"><?= e($relacionada['titulo']) ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
                <div class="aside-caja">
                    <h3>¿Tienes una duda?</h3>
                    <p style="font-size:.87rem;color:var(--tinta-suave);">Pregunta en la comunidad o escríbenos. Las dudas reales mejoran las próximas guías.</p>
                    <a class="boton boton--pequeno boton--bloque" href="<?= url('/comunidad/publicar') ?>">Preguntar en comunidad</a>
                </div>
            </aside>
        </div>
    </div>
</section>

<section class="seccion seccion--compacta" id="galeria">
    <div class="contenedor">
        <div class="encabezado-seccion encabezado-seccion--fila">
            <div>
                <span class="mono texto-oro">Galería de la comunidad</span>
                <h2>Trabajos reales, subidos por estilistas.</h2>
                <p>Comparte una foto o un video de tu trabajo en esta guía. Sirve para mostrar técnica, resolver dudas y inspirar a otras estéticas.</p>
            </div>
        </div>

        <?php if ($media !== []): ?>
            <div class="galeria-media">
                <?php foreach ($media as $trabajo): ?>
                    <article class="tarjeta tarjeta__cuerpo">
                        <div class="galeria-media__archivo">
                            <?php if ($trabajo['tipo'] === 'video'): ?>
                                <?php $extensionVideo = strtolower((string) pathinfo((string) $trabajo['ruta'], PATHINFO_EXTENSION)); $mimeVideo = $extensionVideo === 'webm' ? 'video/webm' : ($extensionVideo === 'mov' ? 'video/quicktime' : 'video/mp4'); ?>
                                <video controls preload="metadata" playsinline>
                                    <source src="<?= e($trabajo['ruta']) ?>" type="<?= e($mimeVideo) ?>">
                                    Tu navegador no reproduce este video. <a href="<?= e($trabajo['ruta']) ?>">Abrir archivo</a>.
                                </video>
                            <?php else: ?>
                                <img src="<?= e($trabajo['ruta']) ?>" alt="<?= e($trabajo['titulo']) ?>" loading="lazy">
                            <?php endif; ?>
                        </div>
                        <div class="galeria-media__texto">
                            <h3><?= e($trabajo['titulo']) ?></h3>
                            <?php if (!empty($trabajo['descripcion'])): ?><p class="texto-suave"><?= e($trabajo['descripcion']) ?></p><?php endif; ?>
                            <span class="texto-tenue" style="font-size:.76rem;">Por <?= e($trabajo['autor']) ?> · <?= e(fecha($trabajo['creado_en'], 'd M Y')) ?></span>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="nota nota--teal">Todavía no hay trabajos en esta guía. Sé la primera persona en compartir una foto o un video.</div>
        <?php endif; ?>

        <?php if ($puedeSubir): ?>
            <form class="tarjeta tarjeta__cuerpo formulario mt-4" method="post" action="<?= url('/guias/' . $guia['slug'] . '/media') ?>" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <h3 style="font-size:1.35rem;">Sube tu trabajo a esta guía</h3>
                <p class="texto-suave" style="font-size:.88rem;">Una imagen JPG, PNG o WebP de hasta 3 MB, o un video MP4, WebM o MOV de hasta 40 MB. Se publica con tu nombre de cuenta.</p>
                <div class="rejilla-campos">
                    <div class="campo campo--6"><label for="titulo-media">Título del trabajo *</label><input id="titulo-media" name="titulo" type="text" required minlength="3" maxlength="120" placeholder="Ej. Corte comercial en manto doble"></div>
                    <div class="campo campo--6"><label for="archivo-media">Foto o video *</label><input id="archivo-media" name="archivo" type="file" required accept="image/jpeg,image/png,image/webp,video/mp4,video/webm,video/quicktime"></div>
                    <div class="campo"><label for="descripcion-media">Descripción breve</label><textarea id="descripcion-media" name="descripcion" maxlength="400" placeholder="Técnica, producto, tipo de manto o lo que quieras destacar."></textarea></div>
                </div>
                <div class="formulario__pie"><button class="boton" type="submit"><?= icono('subir') ?> Publicar en la galería</button></div>
            </form>
        <?php else: ?>
            <div class="nota mt-4"><strong>¿Quieres compartir tu trabajo?</strong> <a href="<?= url('/entrar?destino=' . rawurlencode('/guias/' . $guia['slug'] . '#galeria')) ?>">Entra a tu cuenta</a> o <a href="<?= url('/registro') ?>">crea una gratis</a> para subir fotos y videos.</div>
        <?php endif; ?>
    </div>
</section>

<section class="seccion seccion--compacta">
    <div class="contenedor">
        <div class="flex-entre" style="border-top:1px solid var(--borde);padding-top:24px;">
            <div>
                <?php if ($anterior !== null): ?>
                    <a class="enlace-flecha" href="<?= url('/guias/' . $anterior['slug']) ?>">← <?= e($anterior['titulo']) ?></a>
                <?php endif; ?>
            </div>
            <div>
                <?php if ($siguiente !== null): ?>
                    <a class="enlace-flecha" href="<?= url('/guias/' . $siguiente['slug']) ?>"><?= e($siguiente['titulo']) ?> →</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
