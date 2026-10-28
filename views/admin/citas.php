<?php
declare(strict_types=1);
?>
<header class="panel__cabecera">
    <div><span class="mono texto-oro">Agenda</span><h1>Citas</h1><p>Vista general de las últimas 100 citas. Para gestionar el día, usa el <a href="<?= url('/panel/agenda') ?>">panel de agenda</a>.</p></div>
</header>

<section class="panel-caja">
    <div class="panel__seccion-titulo"><h2>Últimas citas</h2><span class="etiqueta"><?= count($citas) ?></span></div>
    <div class="tabla-scroll">
        <table class="tabla">
            <thead><tr><th>Código</th><th>Fecha</th><th>Mascota</th><th>Tutor</th><th>Servicio</th><th>Estado</th><th class="num">Precio</th></tr></thead>
            <tbody>
                <?php foreach ($citas as $cita): ?>
                    <tr>
                        <td class="mono"><?= e($cita['codigo']) ?></td>
                        <td><?= e(fecha($cita['fecha'], 'd M Y')) ?> · <?= e(substr((string) $cita['hora'], 0, 5)) ?></td>
                        <td><?= e($cita['mascota_nombre']) ?></td>
                        <td><?= e($cita['cliente_nombre']) ?><br><small class="texto-tenue"><?= e((string) ($cita['cliente_telefono'] ?: '')) ?></small></td>
                        <td><?= e($cita['servicio_nombre']) ?></td>
                        <td><?= estado_badge($cita['estado']) ?></td>
                        <td class="num"><?= precio($cita['precio']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($citas === []): ?><tr><td colspan="7" class="tabla-vacia">Sin citas registradas.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
