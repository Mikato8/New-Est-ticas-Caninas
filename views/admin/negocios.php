<?php
declare(strict_types=1);
?>
<header class="panel__cabecera">
    <div><span class="mono texto-oro">Directorio</span><h1>Fichas de estéticas</h1><p>Revisa que la información sea verificable. No publiques fichas inventadas ni datos sin permiso.</p></div>
</header>

<section class="panel-caja">
    <div class="panel__seccion-titulo"><h2>Todas las fichas</h2><span class="etiqueta"><?= count($negocios) ?></span></div>
    <div class="tabla-scroll">
        <table class="tabla">
            <thead><tr><th>Estética</th><th>Ubicación</th><th>Contacto</th><th>Estado</th><th>Verificada</th><th></th></tr></thead>
            <tbody>
                <?php foreach ($negocios as $negocio): ?>
                    <tr>
                        <td><strong style="font-size:.88rem;"><?= e($negocio['nombre']) ?></strong><?= es_verdadero($negocio['destacado']) ? ' <span class="badge badge--pendiente">Destacada</span>' : '' ?><br><small class="texto-tenue"><?= e(limitar_texto((string) $negocio['descripcion'], 80)) ?></small></td>
                        <td><?= e($negocio['ciudad']) ?><br><small class="texto-tenue"><?= e($negocio['estado_geo']) ?></small></td>
                        <td style="font-size:.8rem;"><?= e((string) ($negocio['telefono'] ?: '—')) ?><br><?= e((string) ($negocio['correo'] ?: '')) ?></td>
                        <td><?= estado_badge($negocio['estado']) ?></td>
                        <td><?= es_verdadero($negocio['verificado']) ? '<span class="verificado">' . icono('escudo') . ' Sí</span>' : '<span class="texto-tenue">No</span>' ?></td>
                        <td class="acciones">
                            <?php if ($negocio['estado'] === 'aprobado'): ?><a class="boton boton--pequeno boton--contorno" href="<?= url('/directorio/' . $negocio['slug']) ?>" target="_blank">Ver</a><?php endif; ?>
                            <?php if ($negocio['estado'] !== 'aprobado'): ?>
                                <form method="post" action="<?= url('/admin/negocios/estado') ?>"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $negocio['id'] ?>"><input type="hidden" name="estado" value="aprobado"><input type="hidden" name="verificado" value="1"><button class="boton boton--pequeno" type="submit">Aprobar</button></form>
                            <?php endif; ?>
                            <?php if ($negocio['estado'] !== 'rechazado'): ?>
                                <form method="post" action="<?= url('/admin/negocios/estado') ?>"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $negocio['id'] ?>"><input type="hidden" name="estado" value="rechazado"><button class="boton boton--pequeno boton--contorno" type="submit">Rechazar</button></form>
                            <?php endif; ?>
                            <form method="post" action="<?= url('/admin/negocios/verificar') ?>"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $negocio['id'] ?>"><input type="hidden" name="verificado" value="<?= es_verdadero($negocio['verificado']) ? '0' : '1' ?>"><button class="boton boton--pequeno boton--contorno" type="submit"><?= es_verdadero($negocio['verificado']) ? 'Quitar verificación' : 'Verificar' ?></button></form>
                            <form method="post" action="<?= url('/admin/negocios/destacar') ?>"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $negocio['id'] ?>"><input type="hidden" name="destacado" value="<?= es_verdadero($negocio['destacado']) ? '0' : '1' ?>"><button class="boton boton--pequeno boton--contorno" type="submit"><?= es_verdadero($negocio['destacado']) ? 'Quitar destacada' : 'Destacar' ?></button></form>
                            <form method="post" action="<?= url('/admin/negocios/eliminar') ?>" data-confirmar="¿Eliminar esta ficha?"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $negocio['id'] ?>"><button class="boton boton--pequeno boton--contorno" type="submit" style="color:var(--coral);">Eliminar</button></form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($negocios === []): ?><tr><td colspan="6" class="tabla-vacia">Sin fichas registradas.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
