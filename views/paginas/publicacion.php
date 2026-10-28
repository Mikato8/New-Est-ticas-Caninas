<?php
declare(strict_types=1);
use App\Core\Auth;
use App\Support\Markdown;
$usuario = Auth::usuario();
$errores = errores();
?>
<div class="contenedor">
    <nav class="migas" aria-label="Ruta"><a href="<?= url('/') ?>">Inicio</a><span>/</span><a href="<?= url('/comunidad') ?>">Comunidad</a><span>/</span><?= e($publicacion['titulo']) ?></nav>
    <header style="padding-top:26px;">
        <span class="etiqueta etiqueta--teal"><?= e($categorias[$publicacion['categoria']] ?? $publicacion['categoria']) ?></span>
        <h1 style="font-size:clamp(2rem,4.4vw,3.4rem);margin:12px 0;"><?= e($publicacion['titulo']) ?></h1>
        <div class="articulo__meta">
            <span>Por <?= e($publicacion['autor_nombre']) ?></span>
            <span><?= e(fecha_larga($publicacion['creado_en'])) ?></span>
            <span><?= (int) $publicacion['vistas'] ?> vistas</span>
            <span><?= count($comentarios) ?> respuestas</span>
        </div>
    </header>
</div>

<section class="seccion seccion--compacta" style="padding-top:18px;">
    <div class="contenedor">
        <div class="articulo">
            <article>
                <?php if ($publicacion['imagen']): ?>
                    <img src="<?= e($publicacion['imagen']) ?>" alt="" style="width:100%;max-height:460px;object-fit:cover;border-radius:var(--radio);margin-bottom:22px;" loading="lazy">
                <?php endif; ?>
                <div class="prosa">
                    <?= Markdown::toHtml((string) $publicacion['contenido']) ?>
                </div>

                <div class="nota mt-4" style="display:flex;gap:12px;align-items:flex-start;">
                    <?= icono('escudo') ?>
                    <div><strong>Contenido de la comunidad.</strong> Las opiniones son de quien publica. No sustituyen la valoración de un médico veterinario. Si detectas una práctica de riesgo, repórtala con el equipo de Mikato.</div>
                </div>

                <h2 class="mt-4" id="comentarios" style="font-size:1.6rem;">Respuestas (<?= count($comentarios) ?>)</h2>
                <div style="display:grid;gap:14px;">
                    <?php foreach ($comentarios as $comentario): ?>
                        <div class="tarjeta tarjeta__cuerpo">
                            <div class="flex-entre" style="align-items:flex-start;">
                                <div class="flex-centro"><span class="avatar avatar--oro"><?= e(iniciales((string) $comentario['autor_nombre'])) ?></span><div><strong style="font-size:.92rem;"><?= e($comentario['autor_nombre']) ?></strong><br><small class="texto-tenue"><?= e(fecha($comentario['creado_en'], 'd M Y · H:i')) ?></small></div></div>
                                <?php if (($comentario['autor_rol'] ?? '') === 'admin' || ($comentario['autor_rol'] ?? '') === 'editor'): ?><span class="etiqueta etiqueta--oro">Equipo Mikato</span><?php endif; ?>
                            </div>
                            <p class="texto-suave mt-2 mb-0" style="font-size:.92rem;"><?= nl2br(e($comentario['contenido'])) ?></p>
                        </div>
                    <?php endforeach; ?>
                    <?php if ($comentarios === []): ?>
                        <p class="texto-tenue">Todavía no hay respuestas. Sé la primera persona en aportar.</p>
                    <?php endif; ?>
                </div>

                <div class="tarjeta tarjeta__cuerpo mt-3">
                    <?php if ($usuario !== null): ?>
                        <h3 style="font-size:1.05rem;">Responder</h3>
                        <form class="formulario" method="post" action="<?= url('/comunidad/' . $publicacion['slug'] . '/comentar') ?>">
                            <?= csrf_field() ?>
                            <div class="campo<?= isset($errores['contenido']) ? ' campo--error' : '' ?>">
                                <label for="contenido" class="sr-only">Tu respuesta</label>
                                <textarea id="contenido" name="contenido" required minlength="3" maxlength="1500" placeholder="Aporta algo útil: qué funcionó, qué cambió, qué harías distinto…"></textarea>
                            </div>
                            <div class="formulario__pie">
                                <button class="boton boton--pequeno" type="submit">Publicar respuesta</button>
                                <span class="texto-tenue" style="font-size:.76rem;">Se revisa antes de aparecer.</span>
                            </div>
                        </form>
                    <?php else: ?>
                        <p class="texto-suave mb-2">Entra a tu cuenta para responder.</p>
                        <a class="boton boton--pequeno" href="<?= url('/entrar?destino=' . urlencode('/comunidad/' . $publicacion['slug'])) ?>">Entrar y responder</a>
                    <?php endif; ?>
                </div>
            </article>

            <aside class="aside-sticky">
                <div class="aside-caja">
                    <h3>Sobre el autor</h3>
                    <div class="flex-centro"><span class="avatar avatar--grande"><?= e(iniciales((string) $publicacion['autor_nombre'])) ?></span><div><strong><?= e($publicacion['autor_nombre']) ?></strong><br><small class="texto-tenue"><?= e(rol_label((string) $publicacion['autor_rol'])) ?></small></div></div>
                </div>
                <div class="aside-caja" style="background:var(--teal-claro);border-color:#b9d4c9;">
                    <h3>Participa</h3>
                    <p style="font-size:.87rem;color:var(--teal);">Comparte un caso, una técnica o una duda. La comunidad crece con lo que sabes.</p>
                    <a class="boton boton--pequeno boton--bloque" href="<?= url('/comunidad/publicar') ?>">Publicar</a>
                </div>
                <?php if ($relacionadas !== []): ?>
                <div class="aside-caja">
                    <h3>Más de <?= e($categorias[$publicacion['categoria']] ?? 'la comunidad') ?></h3>
                    <ul>
                        <?php foreach ($relacionadas as $relacionada): ?>
                            <li><a href="<?= url('/comunidad/' . $relacionada['slug']) ?>"><?= e($relacionada['titulo']) ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
            </aside>
        </div>
    </div>
</section>
