<?php
declare(strict_types=1);
?>
<header class="panel__cabecera">
    <div><span class="mono texto-oro">Comunidad</span><h1>Comentarios</h1><p>Los comentarios nuevos esperan aprobación. Revisa que aporten y no incluyan datos personales.</p></div>
</header>

<?php if ($comentarios === []): ?>
    <div class="panel-caja texto-centro"><p class="texto-suave">No hay comentarios pendientes. Buen trabajo.</p></div>
<?php endif; ?>

<div style="display:grid;gap:14px;">
    <?php foreach ($comentarios as $comentario): ?>
        <article class="panel-caja">
            <div class="flex-entre" style="align-items:flex-start;">
                <div class="flex-centro"><span class="avatar avatar--oro"><?= e(iniciales((string) $comentario['autor_nombre'])) ?></span><div><strong style="font-size:.9rem;"><?= e($comentario['autor_nombre']) ?></strong><br><small class="texto-tenue"><?= e(fecha($comentario['creado_en'], 'd M Y · H:i')) ?></small></div></div>
                <a class="etiqueta" href="<?= url('/comunidad/' . $comentario['slug']) ?>" target="_blank"><?= e(limitar_texto((string) $comentario['titulo'], 60)) ?></a>
            </div>
            <p class="texto-suave mt-2" style="font-size:.9rem;"><?= nl2br(e($comentario['contenido'])) ?></p>
            <div class="flex-centro">
                <form method="post" action="<?= url('/admin/comentarios/estado') ?>"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $comentario['id'] ?>"><input type="hidden" name="estado" value="aprobado"><button class="boton boton--pequeno" type="submit">Aprobar</button></form>
                <form method="post" action="<?= url('/admin/comentarios/estado') ?>"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $comentario['id'] ?>"><input type="hidden" name="estado" value="rechazado"><button class="boton boton--pequeno boton--contorno" type="submit">Rechazar</button></form>
                <form method="post" action="<?= url('/admin/comentarios/eliminar') ?>"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $comentario['id'] ?>"><button class="boton boton--pequeno boton--contorno" type="submit" style="color:var(--coral);">Eliminar</button></form>
            </div>
        </article>
    <?php endforeach; ?>
</div>
