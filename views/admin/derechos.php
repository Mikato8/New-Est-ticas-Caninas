<?php
declare(strict_types=1);
$estados = ['' => 'Todas', 'recibida' => 'Recibidas', 'en_revision' => 'En revisión', 'resuelta' => 'Resueltas', 'rechazada' => 'Rechazadas'];
$colores = ['recibida' => 'etiqueta--coral', 'en_revision' => 'etiqueta--oro', 'resuelta' => 'etiqueta--teal', 'rechazada' => ''];
?>
<header class="panel__cabecera">
    <div><span class="mono texto-oro">Legal</span><h1>Derechos ARCO</h1><p>Solicitudes de rectificación de datos personales y su seguimiento.</p></div>
    <div class="panel__acciones"><span class="etiqueta"><?= (int) $nuevas ?> nueva(s)</span></div>
</header>

<div class="panel-caja mb-3">
    <div class="flex-centro" style="gap:6px;flex-wrap:wrap;">
        <?php foreach ($estados as $clave => $texto): ?>
            <a class="chip <?= $estado === $clave ? 'chip--activo' : '' ?>" href="<?= url('/admin/derechos' . ($clave !== '' ? '?estado=' . urlencode($clave) : '')) ?>"><?= e($texto) ?></a>
        <?php endforeach; ?>
    </div>
</div>

<section class="panel-caja">
    <div class="panel__seccion-titulo"><h2>Solicitudes</h2><span class="etiqueta"><?= count($solicitudes) ?></span></div>
    <div class="tabla-scroll">
        <table class="tabla">
            <thead><tr><th>Folio</th><th>Titular</th><th>Correo</th><th>Recibida</th><th>Estado</th><th></th></tr></thead>
            <tbody>
                <?php foreach ($solicitudes as $solicitud): ?>
                    <tr>
                        <td class="mono"><?= e($solicitud['folio']) ?></td>
                        <td><strong><?= e($solicitud['nombre_titular']) ?></strong></td>
                        <td><?= e($solicitud['correo']) ?></td>
                        <td><?= e(fecha($solicitud['creado_en'], 'd M Y H:i')) ?></td>
                        <td><span class="etiqueta <?= e($colores[(string) $solicitud['estado']] ?? '') ?>"><?= e(ucfirst(str_replace('_', ' ', (string) $solicitud['estado']))) ?></span></td>
                        <td><a class="boton boton--pequeno boton--contorno" href="<?= url('/admin/derechos/' . $solicitud['id']) ?>">Ver</a></td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($solicitudes === []): ?><tr><td colspan="6" class="tabla-vacia">No hay solicitudes en este filtro.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
