<?php
declare(strict_types=1);
$pendientesTotal = array_sum($pendientes);
?>
<header class="panel__cabecera">
    <div><span class="mono texto-oro">Moderación</span><h1>Resumen de administración</h1><p>Comunidad, directorio y mensajes. Todo lo que necesita revisión, en un lugar.</p></div>
    <div class="panel__acciones"><a class="boton boton--pequeno boton--contorno" href="<?= url('/panel') ?>">Ir al panel</a></div>
</header>

<?php if ($pendientesTotal > 0): ?>
<div class="aviso-flash aviso-flash--info" role="status"><?= icono('alerta') ?><div><strong><?= $pendientesTotal ?> elemento<?= $pendientesTotal === 1 ? '' : 's' ?> pendiente<?= $pendientesTotal === 1 ? '' : 's' ?>.</strong> Revisa publicaciones, comentarios, fichas o mensajes.</div></div>
<?php else: ?>
<div class="aviso-flash aviso-flash--exito" role="status"><?= icono('check') ?><div>Todo al día. No hay pendientes de moderación.</div></div>
<?php endif; ?>

<div class="panel__rejilla mb-3">
    <a class="panel-caja" href="<?= url('/admin/publicaciones') ?>" style="text-decoration:none;"><span class="mono">Publicaciones pendientes</span><strong class="dato" style="font-size:1.9rem;color:var(--teal);"><?= (int) $pendientes['publicaciones'] ?></strong><span class="texto-tenue" style="font-size:.75rem;"><?= (int) $publicadas ?> publicadas</span></a>
    <a class="panel-caja" href="<?= url('/admin/comentarios') ?>" style="text-decoration:none;"><span class="mono">Comentarios pendientes</span><strong class="dato" style="font-size:1.9rem;color:var(--teal);"><?= (int) $pendientes['comentarios'] ?></strong><span class="texto-tenue" style="font-size:.75rem;">Moderar respuestas</span></a>
    <a class="panel-caja" href="<?= url('/admin/negocios') ?>" style="text-decoration:none;"><span class="mono">Fichas pendientes</span><strong class="dato" style="font-size:1.9rem;color:var(--teal);"><?= (int) $pendientes['negocios'] ?></strong><span class="texto-tenue" style="font-size:.75rem;"><?= (int) $directorio ?> publicadas</span></a>
    <a class="panel-caja" href="<?= url('/admin/mensajes') ?>" style="text-decoration:none;"><span class="mono">Mensajes nuevos</span><strong class="dato" style="font-size:1.9rem;color:var(--teal);"><?= (int) $pendientes['mensajes'] ?></strong><span class="texto-tenue" style="font-size:.75rem;">Formulario de contacto</span></a>
</div>

<div class="panel__rejilla panel__rejilla--2 mb-3">
    <section class="panel-caja">
        <div class="panel__seccion-titulo"><h2>Publicaciones por revisar</h2><a class="enlace-flecha" href="<?= url('/admin/publicaciones') ?>">Ver todas</a></div>
        <?php foreach ($publicaciones as $pub): ?>
            <div class="flex-entre" style="padding:9px 0;border-bottom:1px dashed var(--borde);gap:10px;">
                <div><strong style="font-size:.88rem;"><?= e($pub['titulo']) ?></strong><br><small class="texto-tenue"><?= e($pub['autor_nombre']) ?> · <?= e(fecha($pub['creado_en'], 'd M')) ?></small></div>
                <a class="boton boton--pequeno boton--contorno" href="<?= url('/admin/publicaciones') ?>">Revisar</a>
            </div>
        <?php endforeach; ?>
        <?php if ($publicaciones === []): ?><p class="texto-suave" style="font-size:.88rem;">Sin publicaciones pendientes.</p><?php endif; ?>
    </section>

    <section class="panel-caja">
        <div class="panel__seccion-titulo"><h2>Fichas por revisar</h2><a class="enlace-flecha" href="<?= url('/admin/negocios') ?>">Ver todas</a></div>
        <?php foreach ($negocios as $negocio): ?>
            <div class="flex-entre" style="padding:9px 0;border-bottom:1px dashed var(--borde);gap:10px;">
                <div><strong style="font-size:.88rem;"><?= e($negocio['nombre']) ?></strong><br><small class="texto-tenue"><?= e($negocio['ciudad']) ?>, <?= e($negocio['estado_geo']) ?></small></div>
                <a class="boton boton--pequeno boton--contorno" href="<?= url('/admin/negocios') ?>">Revisar</a>
            </div>
        <?php endforeach; ?>
        <?php if ($negocios === []): ?><p class="texto-suave" style="font-size:.88rem;">Sin fichas pendientes.</p><?php endif; ?>
    </section>
</div>

<div class="panel__rejilla panel__rejilla--2">
    <section class="panel-caja">
        <div class="panel__seccion-titulo"><h2>Mensajes recientes</h2><a class="enlace-flecha" href="<?= url('/admin/mensajes') ?>">Ver todos</a></div>
        <?php foreach ($contactos as $contacto): ?>
            <div style="padding:9px 0;border-bottom:1px dashed var(--borde);">
                <div class="flex-entre"><strong style="font-size:.86rem;"><?= e($contacto['nombre']) ?></strong><span class="badge <?= $contacto['estado'] === 'nuevo' ? 'badge--pendiente' : 'badge--activo' ?>"><?= e($contacto['estado']) ?></span></div>
                <small class="texto-tenue"><?= e($contacto['asunto']) ?> · <?= e(fecha($contacto['creado_en'], 'd M')) ?></small>
                <p class="texto-suave" style="font-size:.82rem;margin:4px 0 0;"><?= e(limitar_texto((string) $contacto['mensaje'], 120)) ?></p>
            </div>
        <?php endforeach; ?>
        <?php if ($contactos === []): ?><p class="texto-suave" style="font-size:.88rem;">Sin mensajes.</p><?php endif; ?>
    </section>

    <section class="panel-caja">
        <div class="panel__seccion-titulo"><h2>Últimas citas</h2><a class="enlace-flecha" href="<?= url('/admin/citas') ?>">Ver todas</a></div>
        <?php foreach ($citas as $cita): ?>
            <div class="flex-entre" style="padding:9px 0;border-bottom:1px dashed var(--borde);gap:10px;">
                <div><strong style="font-size:.86rem;"><?= e($cita['mascota_nombre']) ?></strong><br><small class="texto-tenue"><?= e(fecha($cita['fecha'], 'd M')) ?> · <?= e($cita['servicio_nombre']) ?></small></div>
                <?= estado_badge($cita['estado']) ?>
            </div>
        <?php endforeach; ?>
    </section>
</div>
