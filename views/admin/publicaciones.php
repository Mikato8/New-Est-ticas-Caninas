<?php
declare(strict_types=1);
?>
<header class="panel__cabecera">
    <div><span class="mono texto-oro">Comunidad</span><h1>Publicaciones</h1><p>Aprueba, rechaza, fija o elimina publicaciones. Lo publicado aparece en la comunidad y en el sitemap.</p></div>
</header>

<section class="panel-caja">
    <div class="panel__seccion-titulo"><h2>Todas las publicaciones</h2><span class="etiqueta"><?= count($publicaciones) ?></span></div>
    <div class="tabla-scroll">
        <table class="tabla">
            <thead><tr><th>Título</th><th>Autor</th><th>Categoría</th><th>Estado</th><th>Fecha</th><th class="num">Vistas</th><th></th></tr></thead>
            <tbody>
                <?php foreach ($publicaciones as $pub): ?>
                    <tr>
                        <td><strong style="font-size:.88rem;"><?= e($pub['titulo']) ?></strong><?= es_verdadero($pub['fijado']) ? ' <span class="badge badge--pendiente">Fijada</span>' : '' ?><br><small class="texto-tenue"><?= (int) $pub['comentarios'] ?> respuestas</small></td>
                        <td><?= e($pub['autor_nombre']) ?><br><small class="texto-tenue"><?= e(rol_label((string) $pub['autor_rol'])) ?></small></td>
                        <td><?= e($categorias[$pub['categoria']] ?? $pub['categoria']) ?></td>
                        <td><?= estado_badge($pub['estado']) ?></td>
                        <td><?= e(fecha($pub['creado_en'], 'd M Y')) ?></td>
                        <td class="num"><?= (int) $pub['vistas'] ?></td>
                        <td class="acciones">
                            <?php if ($pub['estado'] === 'publicado'): ?><a class="boton boton--pequeno boton--contorno" href="<?= url('/comunidad/' . $pub['slug']) ?>" target="_blank">Ver</a><?php endif; ?>
                            <?php if ($pub['estado'] !== 'publicado'): ?>
                                <form method="post" action="<?= url('/admin/publicaciones/estado') ?>"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $pub['id'] ?>"><input type="hidden" name="estado" value="publicado"><button class="boton boton--pequeno" type="submit">Publicar</button></form>
                            <?php endif; ?>
                            <?php if ($pub['estado'] !== 'rechazado'): ?>
                                <form method="post" action="<?= url('/admin/publicaciones/estado') ?>"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $pub['id'] ?>"><input type="hidden" name="estado" value="rechazado"><button class="boton boton--pequeno boton--contorno" type="submit">Rechazar</button></form>
                            <?php endif; ?>
                            <form method="post" action="<?= url('/admin/publicaciones/fijar') ?>"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $pub['id'] ?>"><input type="hidden" name="fijado" value="<?= es_verdadero($pub['fijado']) ? '0' : '1' ?>"><button class="boton boton--pequeno boton--contorno" type="submit"><?= es_verdadero($pub['fijado']) ? 'Quitar fijo' : 'Fijar' ?></button></form>
                            <form method="post" action="<?= url('/admin/publicaciones/eliminar') ?>"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $pub['id'] ?>"><button class="boton boton--pequeno boton--contorno" type="submit" style="color:var(--coral);">Eliminar</button></form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($publicaciones === []): ?><tr><td colspan="7" class="tabla-vacia">Sin publicaciones.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
