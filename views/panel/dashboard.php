<?php
declare(strict_types=1);
$totalDia = max(1, (int) $resumenHoy['total']);
$pctCompletadas = round(((int) $resumenHoy['completada'] / $totalDia) * 100);
$utilidad = (float) $ventasMes['total'] - (float) $gastosMes;
$maxSerie = 0.01;
foreach ($serie as $dia) { $maxSerie = max($maxSerie, (float) $dia['total']); }
?>
<header class="panel__cabecera">
    <div>
        <span class="mono texto-oro"><?= e(dia_semana($hoy)) ?> <?= e(fecha_larga($hoy)) ?></span>
        <h1>Hola, <?= e(explode(' ', (string) $usuario['nombre'])[0]) ?>.</h1>
        <p>Esto es lo que pasa hoy en la estética.</p>
    </div>
    <div class="panel__acciones">
        <a class="boton boton--pequeno" href="<?= url('/panel/ventas/nueva') ?>"><?= icono('mas') ?> Nueva venta</a>
        <a class="boton boton--oro boton--pequeno" href="<?= url('/panel/agenda') ?>"><?= icono('agenda') ?> Agenda</a>
    </div>
</header>

<div class="panel__rejilla mb-3">
    <div class="panel-caja"><span class="mono">Citas hoy</span><strong class="dato" style="font-size:2rem;"><?= (int) $resumenHoy['total'] ?></strong><span class="texto-tenue" style="font-size:.76rem;"><?= (int) $resumenHoy['completada'] ?> completadas · <?= $pctCompletadas ?> %</span></div>
    <div class="panel-caja"><span class="mono">Ventas del mes</span><strong class="dato" style="font-size:1.7rem;"><?= dinero($ventasMes['total']) ?></strong><span class="texto-tenue" style="font-size:.76rem;"><?= (int) $ventasMes['tickets'] ?> tickets · promedio <?= dinero($ventasMes['ticket']) ?></span></div>
    <div class="panel-caja"><span class="mono">Gastos del mes</span><strong class="dato" style="font-size:1.7rem;color:var(--coral);"><?= dinero($gastosMes) ?></strong><span class="texto-tenue" style="font-size:.76rem;">Utilidad estimada <?= dinero($utilidad) ?></span></div>
    <div class="panel-caja"><span class="mono">Clientes y mascotas</span><strong class="dato" style="font-size:1.7rem;"><?= (int) $clientes ?> / <?= (int) $mascotas ?></strong><span class="texto-tenue" style="font-size:.76rem;">Fichas activas</span></div>
</div>

<div class="panel__rejilla panel__rejilla--2 mb-3">
    <section class="panel-caja">
        <div class="panel__seccion-titulo"><h2>Agenda de hoy</h2><a class="enlace-flecha" href="<?= url('/panel/agenda') ?>">Ver agenda <?= icono('flecha') ?></a></div>
        <?php if ($citasHoy === []): ?>
            <p class="texto-suave" style="font-size:.9rem;">No hay citas para hoy. <a href="<?= url('/panel/agenda') ?>">Agendar una</a> o comparte el enlace de reserva.</p>
        <?php else: ?>
            <div class="agenda-panel">
                <?php foreach (array_slice($citasHoy, 0, 6) as $cita): ?>
                    <div class="agenda-item agenda-item--<?= e($cita['estado']) ?>">
                        <div class="agenda-item__hora"><?= e(substr((string) $cita['hora'], 0, 5)) ?><small><?= e(estado_cita_label($cita['estado'])) ?></small></div>
                        <div class="agenda-item__datos"><strong><?= e($cita['mascota_nombre']) ?></strong><span><?= e($cita['servicio_nombre']) ?> · <?= e($cita['cliente_nombre']) ?></span></div>
                        <div class="agenda-item__acciones">
                            <a class="boton boton--pequeno boton--contorno" href="<?= url('/panel/mascotas/' . $cita['mascota_id']) ?>">Ficha</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <section class="panel-caja">
        <div class="panel__seccion-titulo"><h2>Ventas últimos 7 días</h2><a class="enlace-flecha" href="<?= url('/panel/reportes') ?>">Reportes <?= icono('flecha') ?></a></div>
        <?php if ($serie === []): ?>
            <p class="texto-suave" style="font-size:.9rem;">Aún no hay ventas registradas en la semana.</p>
        <?php else: ?>
            <div class="mini-grafica">
                <?php foreach ($serie as $dia): ?>
                    <div class="mini-grafica__barra" style="height:<?= max(4, round(((float) $dia['total'] / $maxSerie) * 100)) ?>%;" title="<?= e(fecha($dia['fecha'])) ?>: <?= dinero($dia['total']) ?>"><span><?= e(dia_semana($dia['fecha'], true)) ?></span></div>
                <?php endforeach; ?>
            </div>
            <div class="flex-entre mt-3" style="padding-top:12px;border-top:1px solid var(--borde);">
                <span class="texto-tenue" style="font-size:.78rem;">Total 7 días</span>
                <strong><?= dinero(array_sum(array_map(static fn (array $d): float => (float) $d['total'], $serie))) ?></strong>
            </div>
        <?php endif; ?>
    </section>
</div>

<div class="panel__rejilla panel__rejilla--3 mb-3">
    <section class="panel-caja">
        <div class="panel__seccion-titulo"><h2>Próximas citas</h2><a class="enlace-flecha" href="<?= url('/panel/agenda') ?>">Ver todas</a></div>
        <?php if ($proximas === []): ?><p class="texto-suave" style="font-size:.88rem;">Sin citas próximas.</p><?php endif; ?>
        <div style="display:grid;gap:10px;">
            <?php foreach ($proximas as $cita): ?>
                <div class="flex-entre" style="padding-bottom:9px;border-bottom:1px dashed var(--borde);">
                    <div><strong style="font-size:.88rem;"><?= e($cita['mascota_nombre']) ?></strong><br><small class="texto-tenue"><?= e(fecha($cita['fecha'], 'd M')) ?> · <?= e(substr((string) $cita['hora'], 0, 5)) ?> · <?= e($cita['servicio_nombre']) ?></small></div>
                    <?= estado_badge($cita['estado']) ?>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="panel-caja">
        <div class="panel__seccion-titulo"><h2>Stock bajo</h2><a class="enlace-flecha" href="<?= url('/panel/productos') ?>">Productos</a></div>
        <?php if ($bajoStock === []): ?><p class="texto-suave" style="font-size:.88rem;">Todo el inventario está por encima del mínimo.</p><?php endif; ?>
        <div style="display:grid;gap:9px;">
            <?php foreach ($bajoStock as $producto): ?>
                <div class="flex-entre" style="font-size:.86rem;">
                    <span><?= e($producto['nombre']) ?></span>
                    <span class="badge <?= (int) $producto['stock'] <= 0 ? 'badge--cancelada' : 'badge--pendiente' ?>"><?= (int) $producto['stock'] ?> pzas</span>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="panel-caja">
        <div class="panel__seccion-titulo"><h2>Últimas ventas</h2><a class="enlace-flecha" href="<?= url('/panel/ventas') ?>">Ventas</a></div>
        <?php if ($ultimasVentas === []): ?><p class="texto-suave" style="font-size:.88rem;">Sin ventas registradas.</p><?php endif; ?>
        <div style="display:grid;gap:9px;">
            <?php foreach ($ultimasVentas as $venta): ?>
                <div class="flex-entre" style="font-size:.86rem;">
                    <span class="mono" style="font-size:.68rem;"><?= e($venta['folio']) ?><br><span style="font-family:var(--fuente-texto);font-size:.76rem;color:var(--tinta-tenue);"><?= e((string) ($venta['cliente_nombre'] ?? 'Público general')) ?></span></span>
                    <strong><?= dinero($venta['total']) ?></strong>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</div>

<?php if ($cumple !== []): ?>
<section class="panel-caja">
    <div class="panel__seccion-titulo"><h2>Cumpleaños del mes</h2><span class="etiqueta">Detalle para consentir</span></div>
    <div class="grid grid--3">
        <?php foreach ($cumple as $mascota): ?>
            <div class="mascota-tarjeta">
                <span class="avatar avatar--oro"><?= e(iniciales((string) $mascota['nombre'])) ?></span>
                <div><strong><?= e($mascota['nombre']) ?></strong><small><?= e(fecha($mascota['nacimiento'], 'd M')) ?> · <?= e($mascota['cliente_nombre']) ?></small></div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>
